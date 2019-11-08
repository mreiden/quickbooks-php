<?php declare(strict_types=1);

/**
 * Example QuickBooks SOAP Server / Web Service
 *
 * This is an example Web Service which adds customers to QuickBooks desktop
 * editions via the QuickBooks Web Connector.
 *
 * MAKE SURE YOU READ OUR QUICK-START GUIDE:
 * 	http://wiki.consolibyte.com/wiki/doku.php/quickbooks_integration_php_consolibyte_webconnector_quickstart
 * 	http://wiki.consolibyte.com/wiki/doku.php/quickbooks
 *
 * You should copy this file and use this file as a reference for when you are
 * creating your own Web Service to add, modify, query, or delete data from
 * desktop versions of QuickBooks software.
 *
 * The basic idea behind this method of integration with QuickBooks desktop
 * editions is to host this web service on your server and have the QuickBooks
 * Web Connector connect to it and pass messages to QuickBooks. So, every time
 * that an action occurs on your website which you wish to communicate to
 * QuickBooks, you'll queue up a request (shown below, using the
 * WebConnector\Queue class).
 *
 * You write request handlers which generate qbXML requests for each type of
 * action you queue up. Those qbXML requests will be passed by the Web
 * Connector to QuickBooks, which will process the requests and send back
 * the responses. Your response handler will process the response (you'll
 * probably want to at least store the returned ListID or TxnID of anything you
 * create within QuickBooks) and this pattern will continue until there are no
 * more requests in the queue for QuickBooks to process.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 *
 * @package QuickBooks
 * @subpackage Documentation
 */


use QuickBooksPhpDevKit\{
	Driver,
	Driver\Factory,              // Driver Factory
	Driver\Sql,                  // Sql database driver. Contains table prefix configuration variables.
	PackageInfo,                 // Defines QuickBooks actions, locales, etc and other useful information
	Utilities as QbUtilities,
};
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\Customer,
	QbxmlTestdataGenerator,
};
use QuickBooksPhpDevKit\WebConnector\{
	Handlers,
	Queue as QbQueue,
	Server as QbWebConnectorServer,
};

// ***********************************************************************
// ***** Pick which SOAP Server Adapter to use                       *****
// ***** Use the BuiltinAdapter unless you have good reason not to.  *****
// ***********************************************************************
use QuickBooksPhpDevKit\Adapter\SOAP\Server\{
	BuiltinAdapter as SoapAdapter,
	//PhpExtensionAdapter as SoapAdapter,
};

// Composer's Class Autoloader (This is unnecessary if your project is using Composer)
if (false === class_exists(PackageInfo::class))
{
	require_once(dirname(__FILE__, 3) . '/../vendor/autoload.php');
}



// I always program in E_STRICT error mode, but you do not want to display_errors in production...
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');


$username = 'webconnector';
$password = 'password';


$example = new ExampleWebConnector($username);
$example->qbCreateUser($username, $password);

$return = true;
$debug = true;
$example->qbHandleRequest($return, $debug);



class ExampleWebConnector
{
	// ****************************************************************************************************
	// ***** The Web Connector username and password you'll use in:                                   *****
	// *****   a) Your .QWC file (username)                                                           *****
	// *****   b) The Web Connector program (password)                                                *****
	// *****   c) This QuickBooks framework for Queueing Actions, etc (username)                      *****
	// *****                                                                                          *****
	// ***** NOTE!!!: This has *no relationship* with QuickBooks usernames, Windows usernames, etc.   *****
	// *****          It is *only* used for the Web Connector and SOAP Server!                        *****
	// ****************************************************************************************************
	protected $username = 'webconnector';

	protected $dsn;
	protected $Driver;

	protected $driver_options;
	protected $handler_options;

	protected $qbHooks;
	protected $qbMap;
	protected $qbErrmap;

	protected $qbQueue;
	protected $qbWebConnectorServer;


	public function __construct(?string $username = null)
	{
		if (null !== $username)
		{
			$this->user = $username;
		}

		$this->qbSetup();
	}

	public function qbCreateUser(string $username, string $password): bool
	{
		// Create QuickBooks Web Connector User
		$success = QbUtilities::createUser($this->Driver, $username, $password);

		// Generate and Display QWC File
		//$this->quickbooksQwcAction($username);

		return $success;
	}

	public function qbSetup(?string $username = null): void
	{
		if (!empty($username))
		{
			$this->username = $username;
		}

		// ****************************************************************************************************
		// ***** Database Connection Setup                                                                *****
		// *****                                                                                          *****
		// ***** The database tables will be created for you by Utilities::initialize($dsn) if they do    *****
		// ***** not exist.                                                                               *****
		// *****                                                                                          *****
		// ***** Can also be DSN string like {backend}://{username}:{password}@{host}:{port}/{database}   *****
		// ****************************************************************************************************
		// PostgreSQL Backend
		$this->dsn = [
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
		$this->dsn = [
			'backend' => 'mysqli',
			'host' => 'localhost',
			//'port' => 3306,
			'username' => 'mysql',
			//'password' => '',
			'database' => 'quickbooks',
		];

		// SQLite3 Backend
		$this->dsn = [
			'backend' => 'sqlite3',
			'database' => sys_get_temp_dir() . '/quickbooks.sqlite3',
		];


		// ****************************************************************************************************
		// ***** These should come before any function that uses the dsn.                                 *****
		// ***** Otherwise, a new instance is created based on the dsn and the serialized $driver_options *****
		// *****                                                                                          *****
		// ***** Limits prevent tables from growing endlessly                                             *****
		// ****************************************************************************************************
		$this->driver_options = [
			// See the comments in Driver\Sql\<YOUR DRIVER HERE> class (Mysqli, Pgsql, Sqlite3)
			'max_log_history' => 30000,      // Limit the number of quickbooks_log entries
			'max_queue_history' => 10000,    // Limit the number of *successfully processed* quickbooks_queue entries
			'max_ticket_history' => 10000,   // Limit the number of quickbooks_tickets entries

			// Array of allowed IP Addresses or CIDR Blocks (e.g. Your office IP Address)
			'allow_remote_addr' => [
			],
		];


		$this->handler_options = [
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
		//Sql::$PASSWORD_HASH = 'hash_function_name';
		//Sql::$PASSWORD_SALT = 'hashing_random_salt';

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
		//Sql::$TablePrefix['SQL_MIRROR'] = 'qb_';  // Default is "qb_"

		// Table prefix for Integrator Apps (I'm not sure what an integrator app is)
		//Sql::$TablePrefix['INTEGRATOR'] = 'qb_';  // Default is "qb_"



		// Set the event handler hooks
		$this->qbHooks = $this->qbHookConfiguration();

		// Set the QuickBooks Action to Request/Response Handlers
		$this->qbMap = $this->qbActionMapConfiguration();

		// Set the QuickBooks Error Handlers
		$this->qbErrmap = $this->qbErrorHandlerConfiguration();


		// Make sure database tables have been initialized
		$this->qbInitDatabase();


		// Add a Quickbooks Queue object so we can add additional requests to the queue
		$this->qbQueue = new QbQueue($this->Driver, $this->username);


		// Create the WebConnector server
		$this->qbWebConnectorServer = new QbWebConnectorServer(
			$this->Driver,
			$this->SoapAdapter ?? new SoapAdapter(),
			$this->qbMap,
			$this->qbErrmap,
			$this->qbHooks,
			PackageInfo::$LOGLEVEL,
			$this->handler_options);
	}


	protected function qbHookConfiguration(): array
	{
		// An array of callback hooks that are called when certain events happen within the framework
		return [
			// LOGINSUCCESS runs when the WebConnect user logs in.  This hook should be used to add actions to the Queue such
			// as adding Customers, Invoices, ReceivePayments, etc.
			Handlers::HOOK_LOGINSUCCESS => [$this, 'qbQueueData'],
		];
	}


	protected function qbActionMapConfiguration(): array
	{
		// Set the QuickBooks Action to Request/Response Handlers
		return [
			// Example CustomerAdd Handler
			PackageInfo::Actions['ADD_CUSTOMER'] => [
				[$this, '_quickbooks_customer_request'],
				[$this, '_quickbooks_customer_response']
			],
			PackageInfo::Actions['MOD_CUSTOMER'] => [
				[$this, '_quickbooks_customer_request'],
				[$this, '_quickbooks_customer_response']
			],

			// This is a wildcard handler that handlers any action without its own handler.
			// Most likely you want to use individual action handlers for different QuickBooks objects (Customer, Invoice, etc)
			'*' => [
				[$this, '_quickbooks_request'],
				[$this, '_quickbooks_response']
			],

			// ... Additional Request/Response Handlers would go here ...
		];
	}


	protected function qbErrorHandlerConfiguration(): array
	{
		// This is entirely optional, use it to trigger actions when an error is returned by QuickBooks
		return [
			// Whenever a string is too long to fit in a field, call this method: _quickbooks_error_stringtolong()
			3070 => [$this, '_quickbooks_error_stringtoolong'],

			// Whenever an error occurs while trying to perform an 'AddCustomer' action, call this method: _quickbooks_error_customeradd()
			//PackageInfo::Actions['ADD_CUSTOMER'] => [$this, '_quickbooks_error_customeradd'],

			// Using a key value of '*' will catch any errors which were not caught by another error handler (Wildcard handler)
			// '*' => '_quickbooks_error_catchall',

			// ... Additional error handlers would go here ...
		];
	}




	public function qbQueueData(?int $requestID, string $user, string $hook, ?string &$err, array $hook_data, array $callback_config): bool
	{
		// Set up the QuickBooks framework (Timezone, Password Options, Database connection, create the Queue instance, etc)
		$this->qbSetup($user);

		//  Do something when a WebConnector user successfully logs in...
		//
		//  Find all the things that should be added to the Queue such as:
		//     Customers, Vendors, etc (record has no ListID or EditSequence)
		//     Invoices, ReceivePayments, etc (record has no TxnID or EditSequence)
		//
		//
		//  Use the WeConnector\Queue class to queue up actions to send to QuickBooks.
		//
		//  For instance, a new customer is created in your database and
		//	you want to add them to QuickBooks:
		//      $Queue = new Queue($dsn, $webConnectUsername);
		//      $Queue->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], $primary_key_of_new_customer);
		//
		//  You may also need to queue up the new customer's invoice:
		//      $Queue->enqueue(PackageInfo::Actions['ADD_INVOICE'], $primary_key_of_new_invoice);
		//
		//  Remember that for each action type you queue up, you should have a
		//	request and a response function registered by using the $map parameter
		//	to the WebConnector\Server class. The request function will accept a list
		//	of parameters (one of them is $ID, which will be passed the value of
		//	$primary_key_of_new_customer/order that you passed to the ->enqueue()
		//	method and return a qbXML request. So, your request handler for adding
		//	customers might do something like this:
		//
		//	$arr = mysql_fetch_array(mysql_query("SELECT * FROM my_customer_table WHERE ID = " . (int) $ID));
		//	// build the qbXML CustomerAddRq here
		//	return $qbxml;


		// Configure which types of QuickBooks objects to add.
		$run = [
			'Account' => true,
			'StandardTerms' => true,

			// Class is disabled because QuickBooks comes with class tracking turned off by default.
			// You can still add classes, but you cannot remove them until you turn class tracking on.
			'Class' => false,

			'Item' => true,
			'Customer' => true,
			'Invoice' => true,
			'CreditMemo' => true,
			'Vendor' => true,
			'Bill' => true,
		];

		if ($run['Account'] === true)
		{
			// Add Income, Expense, and Bank Accounts
			$data = [
				// Income Accounts
				[
					[
						'constructorParameters' => [
							'Income',
							'Items',
							'All Items',
						],
					],
					[
						'constructorParameters' => [
							'Income',
							'Items:ServiceItem1',
							'ServiceItem1',
						],
					],
					[
						'constructorParameters' => [
							'Income',
							'Items:ServiceItem2',
							'ServiceItem2',
						],
					],
				],

				// Bank Accounts
				[
					[
						'constructorParameters' => [
							'Bank',
							'Checking Accounts',
							'All Checking Accounts',
						],
					],
					[
						'constructorParameters' => [
							'Bank',
							'Checking Accounts:Operating Account',
							'Checking account for operating expenses',
						],
					],
				],

				// Expense Accounts
				[
					[
						'constructorParameters' => [
							'Expense',
							'Taxes Owed',
							'All The Ridiculous Taxes Businesses Pay',
						],
					],
					[
						'constructorParameters' => [
							'Expense',
							'Taxes Owed:Licensing',
							'Licensing Taxes',
						],
					],
					[
						'constructorParameters' => [
							'Expense',
							'Taxes Owed:Business',
							'Business Taxes',
						],
					],
				],
			];
			for ($i = 1; $i <= count($data); $i++)
			{
				for ($j = 1; $j <= count($data[($i-1)]); $j++)
				{
					$extra = $data[($i-1)][($j-1)];
					//print_r($extra);
					$test = $this->qbQueue->enqueue(PackageInfo::Actions['ADD_ACCOUNT'], 1000+($i*100)+$j, null, $extra, $user);
				}
			}
		}


		if ($run['StandardTerms'] === true)
		{
			$data = [
				[
					'constructorParameters' => [
						'11% 12 Net 31',
						31,
						12,
						4.44,
					],
				],
			];
			for ($i = 1; $i <= count($data); $i++)
			{
				$extra = $data[($i-1)];
				//print_r($extra);
				$test = $this->qbQueue->enqueue(PackageInfo::Actions['ADD_STANDARDTERMS'], 2000+$i, null, $extra, $user);
			}
		}

		if ($run['Class'] === true)
		{
			$data = [
				[
					'constructorParameters' => [
						'QuickBooksClass',
						true,
					],
				],
			];
			for ($i = 1; $i <= count($data); $i++)
			{
				$extra = $data[($i-1)];
				//print_r($extra);
				$test = $this->qbQueue->enqueue(PackageInfo::Actions['ADD_CLASS'], 2100+$i, null, $extra, $user);
			}
		}

		if ($run['Item'] === true)
		{
			$data = [
				[
					'constructorParameters' => [
						'Items',
						'Items',
					],
				],
				[
					'constructorParameters' => [
						'Items:ServiceItem1',
						'Items:ServiceItem1',
					],
				],
				[
					'constructorParameters' => [
						'Items:ServiceItem2',
						'Items:ServiceItem2',
					],
				],
			];
			for ($i = 1; $i <= count($data); $i++)
			{
				$extra = $data[($i-1)];
				//print_r($extra);
				$test = $this->qbQueue->enqueue(PackageInfo::Actions['ADD_SERVICEITEM'], 2200+$i, null, $extra, $user);
			}
		}

		if ($run['Customer'] === true)
		{
			$data = [
				[
					'constructorParameters' => [
						'Parent',
					],
				],
				[
					'constructorParameters' => [
						'My Customer Name -12345',
						'Parent',
					],
				],
				[
					'constructorParameters' => [
						'Freddy Krûegër’s — “Nîghtmåre ¾"',
						'Parent',
					],
				],
				[
					'constructorParameters' => [
						'Test of some UTF8 chars- Á, Æ, Ë, ¾, Õ, ä, ß, ú, ñ',
						'Parent',
						null,
						[
							'CompanyName' => 'Freddy Krûegër’s — “Nîghtmåre ¾"',
							'Contact' => 'Test of some UTF8 chars— Á, Æ, Ë, ¾, Õ, ä, ß, ú, ñ',
							'BillAddress' => [
								'My Customer',          // Address Line 1
								'attn: John Q. Doe',	// Address Line 2
								'123 Billing Street',   // Address Line 3
								'',                     // Address Line 4
								'',                     // Address Line 5
								'Here is the £ pound sign for ££££', // City (should be truncated to 31 characters ending in ££ instead of ££££)
								'NY',                   // State
								'',                     // Province
								'10019',                // Postal Code
								'',                     // Country
								''                      // Note for Address
							],
						],
					],
				],
			];
			for ($i = 1; $i <= count($data); $i++)
			{
				$extra = $data[($i-1)];
				//print_r($extra);
				$test = $this->qbQueue->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], 3000+$i, null, $extra, $user);
			}
		}

		if ($run['Invoice'] === true)
		{
			$data = [
				[
					'constructorParameters' => [
						10500,
						date('Y-m-d'),
						'Parent:My Customer Name -12345',
						'PO12345',
					],
				],
				[
					'constructorParameters' => [
						10750,
						date('Y-m-d'),
						'Parent:My Customer Name -12345',
						'PO54321',
					],
				],
			];
			for ($i = 1; $i <= count($data); $i++)
			{
				$extra = $data[($i-1)];
				//print_r($extra);
				$test = $this->qbQueue->enqueue(PackageInfo::Actions['ADD_INVOICE'], 3300+$i, null, $extra, $user);
			}
		}

		if ($run['CreditMemo'] === true)
		{
			$data = [
				[
					'constructorParameters' => [
						'10500-CM',
						date('Y-m-d'),
						'Parent:My Customer Name -12345',
						'RMA12345',
					],
				],
			];
			for ($i = 1; $i <= count($data); $i++)
			{
				$extra = $data[($i-1)];
				//print_r($extra);
				$test = $this->qbQueue->enqueue(PackageInfo::Actions['ADD_CREDITMEMO'], 3600+$i, null, $extra, $user);
			}
		}

		if ($run['Vendor'] === true)
		{
			$data = [
				[
					'constructorParameters' => [
						'Vendor1',
					],
				],
			];
			for ($i = 1; $i <= count($data); $i++)
			{
				$extra = $data[($i-1)];
				//print_r($extra);
				$test = $this->qbQueue->enqueue(PackageInfo::Actions['ADD_VENDOR'], 4000+$i, null, $extra, $user);
			}
		}

		if ($run['Bill'] === true)
		{
			$data = [
				[
					'constructorParameters' => [
						'Vendor1',
						null,
						'Parent:My Customer Name -12345',
					],
				],
			];
			for ($i = 1; $i <= count($data); $i++)
			{
				$extra = $data[($i-1)];
				//print_r($extra);
				$test = $this->qbQueue->enqueue(PackageInfo::Actions['ADD_BILL'], 5000+$i, null, $extra, $user);
			}
		}

		return true;
	}





	// ****************************************************************************************************
	// ***** QuickBooks Error Handlers                                                                *****
	// ****************************************************************************************************
	/**
	 * Catch and handle a "that string is too long for that field" error (err no. 3070) from QuickBooks
	 *
	 * @param string $requestID
	 * @param string $action
	 * @param mixed $ID
	 * @param mixed $extra
	 * @param string $err
	 * @param string $xml
	 * @param mixed $errnum
	 * @param string $errmsg
	 * @return void
	 */
	public function _quickbooks_error_stringtoolong(int $requestID, string $user, string $action, string $ID, array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg): void
	{
		mail('your-email@your-domain.com',
			'QuickBooks error occured!',
			'QuickBooks thinks that ' . $action . ': ' . $ID . ' has a value which will not fit in a QuickBooks field:  ' . $err);
	}





	// ****************************************************************************************************
	// ***** QuickBooks Action to Request/Response Handlers                                           *****
	// ***** These are application specific and are where most of your application's code will be     *****
	// ****************************************************************************************************
	/**
	 * Generate the qbXML for QuickBooks CustomerAdd/CustomerMod requests.
	 *
	 * Queue up a PackageInfo::Actions['ADD_CUSTOMER'] request with the WebConnector\Queue class:
	 * 	  $Queue = new Queue($dsn, $user);
	 * 	  $Queue->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], $primary_key_of_your_customer);
	 *
	 * Register the request and a response functions in ->qbMapConfiguration() like:
	 *    PackageInfo::Actions['ADD_CUSTOMER'] => ['_quickbooks_customer_request', '_quickbooks_customer_response'],
	 *
	 * Each PackageInfo::Actions['ADD_CUSTOMER'] action in the queue will call ->_quickbooks_customer_request() and
	 * this function will generate a valid qbXML request which will be sent to QuickBooks to be processed.
	 *
	 * QuickBooks will process the request and send a qbXML CustomerAdRs/CustomerRet response to the response
	 * handler (_quickbooks_customer_response) containing all of the data stored for that customer within QuickBooks.
	 *
	 *
	 * @param string $requestID                 The QUEUE.quickbooks_queue_id for this item (including it in the request may help with debugging)
	 * @param string $action                    The QuickBooks action being performed (CustomerAdd in this case)
	 * @param mixed $ID                         The unique identifier for the Customer (most likely a customer ID number in your database)
	 * @param array $extra                      An array of extra data included with the item when it was added to the Queue
	 * @param string $err                       An error message. Assign a value to $err if you want to report an error
	 * @param integer $last_action_time         A unix timestamp (seconds) indicating when the last action of this type was dequeued
	 *                                          (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
	 * @param integer $last_actionident_time    A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued
	 *                                          (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
	 * @param float $version                    The max qbXML version your QuickBooks version supports
	 * @param string $locale					The locale of your QuickBooks version (PackageInfo::Locale['US|CA|UK|AU|OE'])
	 * @return string                           A valid qbXML request
	 */
	public function _quickbooks_customer_request(int $requestID, string $user, string $action, string $ID, array $extra, ?string &$err, ?int $last_action_time, ?int $last_actionident_time, float $version, string $locale): string
	{
		// Not sure why $ID is a string... convert it to an int
		$ID = (int) $ID;

		// You'd probably do some database access here to pull the record with
		//	ID = $ID from your database and build a request to add that particular
		//	customer to QuickBooks.
		//
		// You might also use something you saved in $extra when you queued the request,
		//  though I can't think of an example for an CustomerAdd request.
		//
		// So, when you implement this for your business, you'd probably do
		//	something like this...:

		// Fetch your customer information...
		//$myCustomerData = getCustomerDataById($ID);

		// Here we get a QBXML\Object\Customer from the QBXML\QbxmlTestdataGenerator class and use
		// the constructorParameters saved in $extra when it was added to the Queue.
		$qbCustomer = call_user_func_array(QbxmlTestdataGenerator::class . '::Customer', $extra['constructorParameters'] ?? []);

		if ($action == PackageInfo::Actions['MOD_CUSTOMER'])
		{
			// Set the ListID and EditSequence for a MOD request (which you need to store in your database in the response handler)
			//$qbCustomer->setListID($ListID);
			//$qbCustomer->setEditSequence($EditSequence);
		}
		// Generate a fully useable qbxml for this request.
		$qbXml = $qbCustomer->asCompleteQBXML($action);
		// You can also add limitations for the QBXML version and Locale of the WebConnector client
		//$qbXml = $qbCustomer->asCompleteQBXML($action, 13.0, PackageInfo::Locale['UNITED_STATES']);

		return $qbXml;
	}

	/**
	 * Receive the response from QuickBooks for a CustomerAdd/CustomerMod request
	 *
	 * @param string $requestID					The requestID you passed to QuickBooks previously
	 * @param string $action					The action that was performed (CustomerAdd in this case)
	 * @param mixed $ID							The unique identifier for the Customer (most likely a customer ID number in your database)
	 * @param array $extra						An array of extra data included with the item when it was added to the Queue
	 * @param string $err						An error message, set $err to the error message if you want to report an error
	 * @param integer $last_action_time			A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
	 * @param integer $last_actionident_time	A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
	 * @param string $xml						The complete qbXML response
	 * @param array $idents						An array of identifiers that are contained in the qbXML response
	 * @return void
	 */
	public function _quickbooks_customer_response(int $requestID, string $user, string $action, string $ID, array $extra, ?string &$err, ?int $last_action_time, ?int $last_actionident_time, ?string $xml, array $idents): void
	{
		// Your Customer with id $ID has been added to QuickBooks with:
		//	ListID: $idents['ListID']
		//  EditSequence: $idents['EditSequence']
		//
		// Store the ListID and EditSequence in the database record, so we can use it
		// later for MOD requests.  It also serves as an indicator the record was
		// successfully added to QuickBooks (Records without a ListID should be retried and/or debugged).

		// Not sure why $ID is a string... convert it to an int
		$ID = (int) $ID;

		// Save the ListID and EditSequence for this customer.
		// This example example uses Doctrine, but the point is to save the ListID and EditSequence
		//$company = $this->repCompany->find($ID);
		//$company
		//	->setListID($idents['ListID'])
		//	->setEditSequence($idents['EditSequence'])
		//;
		//$this->em->persist($company);  // Persist is from Doctrine.  This means save t
		//$this->em->flush();

		// For this example, set the msg field of the queue item to a json encoded array of the QuickBooks idents
		$err = 0;
		$errmsg = '';
		$Driver = Factory::create($this->dsn, $this->driver_options);
		$Driver->query(
			'UPDATE '. Sql::$TablePrefix['BASE'] . Sql::$Table['QUEUE'] . "
			SET msg = '" . $Driver->escape(json_encode($idents)) . "'
			WHERE quickbooks_queue_id = $requestID", $errnum, $errmsg);
	}



	/*
	 * @param string $requestID                 You should include this in your qbXML request (it helps with debugging later)
	 * @param string $action                    The QuickBooks action being performed (CustomerAdd in this case)
	 * @param mixed $ID                         The unique identifier for the record (maybe a customer ID number in your database or something)
	 * @param array $extra                      Any extra data you included with the queued item when you queued it up
	 * @param string $err                       An error message, assign a value to $err if you want to report an error
	 * @param integer $last_action_time         A unix timestamp (seconds) indicating when the last action of this type was dequeued
	 *                                          (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
	 * @param integer $last_actionident_time    A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued
	 *                                          (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
	 * @param float $version                    The max qbXML version your QuickBooks version supports
	 * @param string $locale
	 * @return string                           A valid qbXML request
	 */
	public function _quickbooks_request(int $requestID, string $user, string $action, string $ID, array $extra, ?string &$err, ?int $last_action_time, ?int $last_actionident_time, float $version, string $locale): string
	{
		if (1 !== preg_match('/(Mod|Add|Query)$/', $action, $matches))
		{
			throw new \Exception("Could not determine if action \"$action\" is an Add, Mod, or Query action.");
		}
		$actionType = $matches[1];

		$objectType = QbUtilities::actionToObject($action);
		if (null === $objectType)
		{
			throw new \Exception("Could not convert action \"$action\" into a quickbooks object.");
		}

		try
		{
			$qbObject = call_user_func_array(QbxmlTestdataGenerator::class . '::' . $objectType, $extra['constructorParameters'] ?? []);
		}
		catch (\Exception $e)
		{
			throw new \Exception(QbxmlTestdataGenerator::class . ' could not create QBXML Object "' . $objectType . '" for action "' . $action . '"');
		}

		$qbXml = $qbObject->asCompleteQBXML($action);

		return $qbXml;
	}

	/**
	 * Receive a response from QuickBooks
	 *
	 * @param string $requestID                 The requestID you passed to QuickBooks previously
	 * @param string $action                    The action that was performed (CustomerAdd in this case)
	 * @param mixed $ID                         The unique identifier of the record
	 * @param array $extra
	 * @param string $err                       An error message, assign a valid to $err if you want to report an error
	 * @param integer $last_action_time         A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
	 * @param integer $last_actionident_time    A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
	 * @param string $xml                       The complete qbXML response
	 * @param array $idents                     An array of identifiers that are contained in the qbXML response
	 * @return void
	 */
	public function _quickbooks_response(int $requestID, string $user, string $action, string $ID, array $extra, ?string &$err, ?int $last_action_time, ?int $last_actionident_time, ?string $xml, array $idents): void
	{
		// Your Action $action with id $ID was successfully handled by QuickBooks!

		// Not sure why $ID is a string... convert it to an int
		$ID = (int) $ID;

		// Make sure this is an Add, Mod, or Query action
		if (1 !== preg_match('/(Mod|Add|Query)$/', $action, $matches))
		{
			throw new \Exception('Could not determine if action "' . $action . '" is an Add, Mod, or Query action.');
		}
		$actionType = $matches[1];

		// Find the QBXML\Object type for this Action (Object name without namespace)
		$objectType = QbUtilities::actionToObject($action);
		if (null === $objectType)
		{
			throw new \Exception('Could not convert action "' . $action . '" into a quickbooks object.');
		}

		// Find the QuickBooks Ident Name (ListID or TxnID)
		$qbIdentName = QbUtilities::keyForObject($objectType);

		// Store the ListID, TxnID, and EditSequence in the database record if they exist.
		// This way, they can be used later for MOD requests.  It also serves as an indicator the record was
		// successfully added to QuickBooks. Records without a value for ListID or TxnID (depending on the action type)
		// should be retried and/or debugged).
		//    ListID: $idents[ListID] ?? '';
		//    TxnID: $idents['TxnID'] ?? '';
		//    EditSequence: $idents['EditSequence'] ?? '';
		//
		// The following example is a Doctrine snippet, but the point is to save the QuickBooks ident values to the database:
		//$invoice = $this->repInvoice->find($ID);
		//$invoice
		//    ->setListID($idents['ListID'] ?? '')
		//    ->setEditSequence($idents['EditSequence'] ?? '')
		//    ->setTxnID($idents['TxnID'] ?? '')
		//;
		//$this->em->persist($invoice);
		//$this->em->flush();

		// For this example, set the msg field of the queue item to a json encoded array of the QuickBooks idents
		$err = 0;
		$errmsg = '';
		$Driver = Factory::create($this->dsn, $this->driver_options);
		$Driver->query(
			'UPDATE '. Sql::$TablePrefix['BASE'] . Sql::$Table['QUEUE'] . "
			SET msg = '" . $Driver->escape(json_encode($idents)) . "'
			WHERE quickbooks_queue_id = $requestID", $errnum, $errmsg);
	}






	// ****************************************************************************************************
	// ***** Useful Helper Functions                                                                  *****
	// ****************************************************************************************************
	protected function qbInitDatabase(): bool
	{
		$this->Driver = Factory::create($this->dsn, $this->driver_options);
		if (QbUtilities::initialized($this->Driver) === false) {
			// Initialize Quickbooks Web Connector Tables
			QbUtilities::initialize($this->Driver);
		}

		return true;
	}

	public function qbHandleRequest(bool $return = false, bool $debug = false): ?string
	{
		// Quickbooks requires the response be text/xml
		header('Content-Type: text/xml; charset=utf-8');

		// Handle the request
		return $this->qbWebConnectorServer->handle($return, $debug);
	}
}
