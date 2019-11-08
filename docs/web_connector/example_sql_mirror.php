<?php declare(strict_types=1);

/**
 * An example of how to mirror parts (or all of) the QuickBooks database to a database
 *
 *
 * *REALLY FREAKING IMPORTANT* WARNING WARNING WARNING WARNING
 *
 * THE SQL MIRROR CODE IS BETA CODE, AND IS KNOWN TO HAVE BUGS!
 *
 * With that said:
 * - If you are planning on using it in a production environment, you better be ready to do a lot of testing and debugging.
 * - There is absolutely no way I can troubleshoot problems for you without you posting your code. Dumps of the quickbooks_queue and quickbooks_log tables are helpful also.
 *
 * IN ALL LIKELIHOOD, YOU SHOULD *NOT* BE USING THIS CODE. YOU SHOULD INSTEAD
 * LOOK AT THE FOLLOWING SCRIPTS AND IMPLEMENT YOUR REQUEST/RESPONSE HANDLERS
 * YOURSELF:
 * 	docs/example_web_connector.php
 * 	docs/example_web_connector_import.php
 *
 * *REALLY FREAKING IMPORTANT* WARNING WARNING WARNING WARNING
 *
 *
 * The SQL mirror functionality makes it easy to extract information from
 * QuickBooks into an SQL database, and, if so desired, write changes to the
 * SQL records back to QuickBooks automatically.
 *
 * You should look at my wiki for more information about mirroring QuickBooks
 * data into SQL databases:
 * 	http://wiki.consolibyte.com/wiki/doku.php/quickbooks_integration_php_consolibyte_sqlmirror
 *
 * You should also read this forum post before even thinking about using this:
 * 	http://consolibyte.com/forum/viewtopic.php?id=20
 *
 *
 * @package QuickBooks
 * @subpackage Documentation
 */

use QuickBooksPhpDevKit\{
	Driver,
	Driver\Factory,     // Driver Factory
	Driver\Sql,         // Sql database driver. Contains table prefix configuration variables.
	PackageInfo,        // Defines QuickBooks actions, locales, etc and other useful information
	SQL as SqlMirror,   // Defines SQL Mirror WebConnector Hook Names
	Utilities,
};
use QuickBooksPhpDevKit\WebConnector\Server\SQL as WebConnectorServerSQL;

// ***********************************************************************
// ***** Pick which SOAP Server Adapter to use                       *****
// ***** Use the BuiltinAdapter unless you have good reason not to.  *****
// ***********************************************************************
use QuickBooksPhpDevKit\Adapter\SOAP\Server\{
	BuiltinAdapter as SoapAdapter,
	//PhpExtensionAdapter as SoapAdapter,
};


// Composer's Class Autoloader (You can skip this if your project is already using Composer)
if (false === class_exists(PackageInfo::class))
{
	require_once(dirname(__FILE__, 3) . '/../vendor/autoload.php');
}


// I always program in E_STRICT error mode with error reporting turned on...
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');




// The username and password the Web Connector will use to connect with
$username = 'webconnector';
$password = 'password';


// ****************************************************************************************************
// ***** Database Connection Setup                                                                *****
// *****                                                                                          *****
// ***** The database tables will be created for you by Utilities::initialize($dsn) if they do    *****
// ***** not exist.                                                                               *****
// *****                                                                                          *****
// ***** Can also be DSN string like {backend}://{username}:{password}@{host}:{port}/{database}   *****
// ****************************************************************************************************
// PostgreSQL Backend
$dsn = [
	'backend' => 'pgsql',
	'host' => 'localhost',
	//'port' => 5432,
	'username' => 'postgres',
	//'password' => '',
	'database' => 'quickbooks',

	// Create the tables in the PgSchemaName schema in the postgres database named quickbooks
	//'database' => 'quickbooks.PgSchemaName',
];

// MySQL Backend
$dsn = [
	'backend' => 'mysqli',
	'host' => 'localhost',
	//'port' => 3306,
	'username' => 'mysql',
	//'password' => '',
	'database' => 'quickbooks',
];

// SQLite3 Backend
$dsn = [
	'backend' => 'sqlite3',
	'database' => sys_get_temp_dir() . '/quickbooks.sqlite3',
];




// ****************************************************************************************************
// ***** Configure what to sync with QuickBooks.                                                  *****
// ****************************************************************************************************
$ops = [
	PackageInfo::Actions['OBJECT_SALESTAXITEM'],
	PackageInfo::Actions['OBJECT_SALESTAXCODE'],
	PackageInfo::Actions['OBJECT_CUSTOMER'],
	PackageInfo::Actions['OBJECT_VENDOR'],

	PackageInfo::Actions['OBJECT_ITEMINVENTORY'],
	//PackageInfo::Actions['OBJECT_ITEM'],
	//PackageInfo::Actions['OBJECT_ITEMNONINVENTORY'],
	PackageInfo::Actions['OBJECT_ITEMSERVICE'],

	PackageInfo::Actions['OBJECT_TEMPLATE'],

	PackageInfo::Actions['OBJECT_CUSTOMERTYPE'],
	PackageInfo::Actions['OBJECT_VENDORTYPE'],
	PackageInfo::Actions['OBJECT_ESTIMATE'],
	PackageInfo::Actions['OBJECT_INVOICE'],
	PackageInfo::Actions['OBJECT_CLASS'],

	PackageInfo::Actions['OBJECT_INVOICE'],
    PackageInfo::Actions['OBJECT_CREDITMEMO'],

	PackageInfo::Actions['OBJECT_PAYMENTMETHOD'],
	//PackageInfo::Actions['OBJECT_SHIPMETHOD'],
	PackageInfo::Actions['OBJECT_TERMS'],
	//PackageInfo::Actions['OBJECT_PRICELEVEL'],

	PackageInfo::Actions['OBJECT_COMPANY'],
	PackageInfo::Actions['OBJECT_HOST'],
	PackageInfo::Actions['OBJECT_PREFERENCES'],
];

// For fetching inventory levels, deleted transactions, etc.
$ops_misc = [
	PackageInfo::Actions['DERIVE_INVENTORYLEVELS'],
	PackageInfo::Actions['QUERY_DELETEDLISTS'],
	PackageInfo::Actions['QUERY_DELETEDTRANSACTIONS'],
];

//
$sql_options = [
	'only_import' => $ops,
	'only_add' => $ops,
	'only_modify' => $ops,
	'only_misc' => $ops_misc,
];


// ****************************************************************************************************
// ***** How many records should each query return.                                               *****
// ***** Higher values may cause a database or quickbooks timeout                                 *****
// *****                                                                                          *****
// ***** Default Value: 25                                                                        *****
// ***** Recommended Value: 100                                                                   *****
// ****************************************************************************************************
WebConnectorServerSQL::$ITERATOR_MAXRETURNED = 100;


// ****************************************************************************************************
// ***** These should come before any function that uses the dsn.                                 *****
// ***** Otherwise, a new instance is created based on the dsn and the serialized $driver_options *****
// *****                                                                                          *****
// ***** Limits prevent tables from growing endlessly                                             *****
// ****************************************************************************************************
$driver_options = [
	// See the comments in Driver\Sql\<YOUR DRIVER HERE> class (Mysqli, Pgsql, Sqlite3)
    'max_log_history' => 30000,      // Limit the number of quickbooks_log entries
    'max_queue_history' => 10000,    // Limit the number of *successfully processed* quickbooks_queue entries
    'max_ticket_history' => 10000,   // Limit the number of quickbooks_tickets entries

	// Array of allowed IP Addresses or CIDR Blocks (e.g. Your office IP Address)
	'allow_remote_addr' => [
	],
];


$handler_options = [
    // See the comments in the QuickBooks/Server/Handlers.php file
    //'authenticate' => ' *** YOU DO NOT NEED TO PROVIDE THIS CONFIGURATION VARIABLE TO USE THE DEFAULT AUTHENTICATION METHOD FOR THE DRIVER YOU'RE USING (I.E.: MYSQLi) *** '
    //'authenticate' => 'your_function_name_here',
    //'authenticate' => ['YourClassName', 'YourStaticMethod'],
    'deny_concurrent_logins' => false,
    'deny_reallyfast_logins' => false,
];


// Legacy passwords were stored using a hash function (default sha1) and password salt.
// You can remove this line for new installations or if all old passwords have been rehashed.
//
// Recommended Value: false
PackageInfo::$PASSWORD_ALLOW_LEGACY = true;

// This allows old passwords to be rehashed upon a successful login.
// Disabled by default to prevent inadvertent password changes.
// This has no effect if PackageInfo::$PASSWORD_ALLOW_LEGACY = false
//
// Recommended Value: true
//PackageInfo::$PASSWORD_UPGRADE = true;

// If you previously defined your own QUICKBOOKS_HASH or QUICKBOOKS_SALT, enter those values here
// to allow existing WebConnect users to log in.
// PHP password_hash function and the PASSWORD_DEFAULT hashing algorithm.
//SqlMirror::$PASSWORD_HASH = 'hash_function_name';
//SqlMirror::$PASSWORD_SALT = 'hashing_random_salt';

// Controls whether the SOAP handlers ->sendRequestXML() and receiveResponseXML->() will use
// the PHP unserialize function if json_decode fails.
// Needed if the queue has existing records with extra data saved using the PHP serialize function.
//
// Recommended Value: false
PackageInfo::$ALLOW_PHP_UNSERIALIZE_EXTRA_DATA = true;



// ****************************************************************************************************
// ***** TimeZone should match the computer running Quickbooks WebConnector                       *****
// ***** or some installations will complain.                                                     *****
// ****************************************************************************************************
PackageInfo::$TIMEZONE = 'America/New_York';
//PackageInfo::$TIMEZONE = 'America/Chicago';
//PackageInfo::$TIMEZONE = 'America/Denver';
//PackageInfo::$TIMEZONE = 'America/Los_Angeles';



// ****************************************************************************************************
// ***** Set the logging level.  This sets the level of detail that is logged to the              *****
// ***** LOG table (quickbooks_log by default) such as incoming/outgoing QBXML.                   *****
// ****************************************************************************************************
//PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['NONE'];
//PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['NORMAL'];
//PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['VERBOSE'];
//PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['DEBUG'];
PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['DEVELOP'];   // Use this level until you're sure everything works!!!



// ****************************************************************************************************
// ***** SOAPAdapter is declared in the "use" statements (BuiltinAdapter or PhpExtensionAdapter)  *****
// ***** Use the BuiltinAdapter unless you have good reason not to.                               *****
// *****                                                                                          *****
// ***** You can change the WSDL and SOAP options if you need to.                                 *****
// ***** (You do NOT need to if you are questioning if you do or not)                             *****
// ****************************************************************************************************
//$wsdl = PackageInfo::$WSDL;
//$soap_options = [];
//$SoapAdapter = new SoapAdapter($wsdl, $soap_options);



// ****************************************************************************************************
// ***** Advanced Database Settings                                                               *****
// *****                                                                                          *****
// ***** The database tables will be created for you by Utilities::initialize($dsn) if they do    *****
// ***** not exist.                                                                               *****
// *****                                                                                          *****
// ***** Can also be DSN string like {backend}://{username}:{password}@{host}:{port}/{database}   *****
// ****************************************************************************************************
// The number of seconds a ticket (Web Connector "session") remains active
//Sql::$TIMEOUT = 1800;   // Default is 1800 (30 minutes)

// Table prefix for base framework (users, log, queue, config, etc)
//Sql::$TablePrefix['BASE'] = 'quickbooks_';  // Default is "quickbooks_"

// Table prefix for SQL Mirroring (Duplicate QuickBooks to/from database)
// This needs to be short for PostgreSql to be under the maximum 63 character identifier name length
//Sql::$TablePrefix['SQL_MIRROR'] = 'qb_';  // Default is "qb_"

// Table prefix for Integrator Apps (I'm not sure what an integrator app is)
//Sql::$TablePrefix['INTEGRATOR'] = 'qb_';  // Default is "qb_"









// If the database has not been initialized, we need to initialize it (create tables and user)
$Driver = Factory::create($dsn, $driver_options);
if (Utilities::initialized($Driver, $driver_options) === false) {
	header('Content-Type: text/plain; charset=utf-8');

	// This option creates the tables that store the synced QuickBooks items
	$init_options = [
		'quickbooks_sql_enabled' => true,
	];

    // Initialize Quickbooks Web Connector Tables
    Utilities::initialize($Driver, $driver_options, $init_options);

    // Create the WebConnector User
    Utilities::createUser($Driver, $username, $password);
}



// What mode do we want to run the mirror in?
$mode = WebConnectorServerSQL::MODE_READONLY;		// Read from QuickBooks only (no data will be pushed back to QuickBooks)
//$mode = WebConnectorServerSQL::MODE_WRITEONLY;	// Write to QuickBooks only (no data will be copied into the SQL database)
//$mode = WebConnectorServerSQL::MODE_READWRITE;		// Keep both QuickBooks and the database in sync, reading and writing changes back and forth)

// What should we do if a conflict is found? (a record has been changed by another user or process that we're trying to update)
$conflicts = WebConnectorServerSQL::CONFLICT_LOG;

// What should we do with records deleted from QuickBooks?
//$delete = WebConnectorServerSQL::DELETE_REMOVE;	// Delete the record from the database too
$delete = WebConnectorServerSQL::DELETE_FLAG; 		// Just flag it as deleted

// Hooks (optional stuff)
$hooks = [];

/*
// Hooks (optional stuff)
$hook_obj = new MyHookClass2('Keith Palmer');

$hooks = [
	// Register a hook which occurs when we perform an INSERT into the SQL database for a record from QuickBooks
	// SqlMirror::HOOK_SQL_INSERT => 'my_function_name_for_inserts',
	// SqlMirror::HOOK_SQL_INSERT => 'MyHookClass::myMethod',

	// Register a hook which occurs when we perform an UPDATE on the SQL database for a record from QuickBooks
	// SqlMirror::HOOK_SQL_UPDATE => 'my_function_name_for_updates',

	// Example of registering multiple hooks for one hook type
	// SqlMirror::HOOK_PREHANDLE => [
	//     'my_prehandle_function',
	//     [$hook_obj, 'myMethod'],
	// ],
];

class MyHookClass
{
	static public function myMethod($requestID, $user, $hook, &$err, $hook_data, $callback_config)
	{
		// do something here...
		return true;
	}
}

function my_prehandle_function($requestID, $user, $hook, &$err, $hook_data, $callback_config)
{
	//print('here we are!');
	return true;
}

class MyHookClass2
{
	protected $_var;

	public function __construct($var)
	{
		$this->_var = $var;
	}

	public function myMethod($requestID, $user, $hook, &$err, $hook_data, $callback_config)
	{
		//print('variable equals: ' . $this->_var);
		return true;
	}
}
*/



//
//$callback_options = [];

// Create the SQL Mirror WebConnector Server
$Server = new WebConnectorServerSQL(
    $Driver,                            // $dsn_or_conn     DSN-style connection string or an already opened connection to the driver
    '1 minute',                         // $how_often       The maximum time we wait between updates/syncs (you can use a number of seconds or any valid string interval: "1 hour", "15 minutes", 60, etc.)
    $mode,                              // $mode            The mode the server should run in (The mode the server should run in (WebConnectorServerSQL::MODE_READONLY, WebConnectorServerSQL::MODE_WRITEONLY, WebConnectorServerSQL::MODE_READWRITE))
    $conflicts,                         // $conflicts       The steps towards update conflict resolution the server should take (see constants above)
    $delete,                            // $delete          Delete mode (static::DELETE_REMOVE [actually delete] or static::DELETE_FLAG [flag only])
    $username,                          //
    $SoapAdapter ?? new SoapAdapter(),  // $SoapAdapter     SOAP server adapter interface (Built-In or PHP Extension)
    [],                                 // $map
    [],                                 // $onerror
    $hooks,                             // $hooks
    PackageInfo::LogLevel['DEVELOP'],   // $log_level
    $handler_options,                   // $handler_options
    $driver_options,                    // $driver_options
    $sql_options//,                       // $sql_options
    //$callback_options                   // $callback_options
);

// Handle the WebConnector Request
$Server->handle(true, true);
