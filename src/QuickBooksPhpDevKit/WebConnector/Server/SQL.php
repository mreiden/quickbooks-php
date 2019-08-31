<?php declare(strict_types=1);

/**
 * Mirror a QuickBooks database in a query-able SQL database
 *
 * Copyright (c) {2010-04-16} {Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * Essentially, this package tries to import your QuickBooks database into an
 * SQL database of your choice, mapping the QuickBooks schema to SQL tables,
 * and then allowing you to query (and possibly insert/update) the SQL database
 * and keep this consistently syncronized with the original QuickBooks
 * database.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @author Garrett Griffin <grgisme@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Server
 */

namespace QuickBooksPhpDevKit\WebConnector\Server;

use QuickBooksPhpDevKit\Adapter\SOAP\Server\AdapterInterface; // SOAP Server Interface
use QuickBooksPhpDevKit\Callbacks\SQL\{
	Callbacks,      // SQL callbacks (request and response handlers)
	Errors,         // SQL error handlers
};
use QuickBooksPhpDevKit\{
	Driver\Singleton,             // SQL drivers
	PackageInfo,                  // Project constants and config variables
	SQL\AbstractSchemaObject,     // SQL objects (convert qbXML to objects to schema)
	SQL\Schema,                   // SQL schema generation
	Utilities,                    // General Utilities
	WebConnector\Handlers,        // Handlers file (we need this for soem constant declarations)
	WebConnector\Server,          // WebConnector Base Server Class
};

/**
 *
 *
 */
class SQL extends Server
{
	public static $ON_ERROR = 'continueOnError';
	public static $VALUE_CLEAR = '*CLEAR*';

	/**
	 * The priority value to use when re-queueing a request for the next part of an iterator
	 *
	 * @var integer
	 */
	public static $ITERATOR_PRIORITY = 1000;

	/**
	 * The priority value to use when issuing requests from an Error Handler for Add/Mods
	 *
	 * @var integer
	 */
	public static $CONFLICT_QUEUE_PRIORITY = 9999;

	/**
	 * How many records an iterator should grab in a single transaction
	 *
	 * @var integer
	 */
	public static $ITERATOR_MAXRETURNED = 25;



	/**
	 * Read from the QuickBooks database, and write to the SQL database
	 */
	public const MODE_READONLY = 'r';

	/**
	 * Read from the SQL database, and write to the QuickBooks database
	 */
	public const MODE_WRITEONLY = 'w';

	/**
	 * Read and write from both sources, keeping both sources in sync
	 */
	public const MODE_READWRITE = '+';

	public const CONFLICT_LOG = 2;
	public const CONFLICT_NEWER = 4;
	public const CONFLICT_QUICKBOOKS = 8;
	public const CONFLICT_SQL = 16;
	public const CONFLICT_CALLBACK = 32;

	/**
	 * Delete Modes. Decides whether an item actually gets deleted, or just remains marked deleted.
	 *
	 */
	public const DELETE_REMOVE = 2;
	//define('QUICKBOOKS_SERVER_SQL_ON_DELETE_REMOVE', QUICKBOOKS_SERVER_SQL_DELETE_REMOVE);

	public const DELETE_FLAG = 4;
	//define('QUICKBOOKS_SERVER_SQL::ON_DELETE_FLAG', QUICKBOOKS_SERVER_SQL_DELETE_FLAG);

	/**
	 *
	 *
	 * You can run this server in one of three modes:
	 * 	- WebConnector\Server\SQL::MODE_READONLY: Data will only be read from
	 * 		QuickBooks; changes to data in the SQL database will never be
	 * 		pushed back to QuickBooks.
	 * 	- WebConnector\Server\SQL::MODE_WRITEONLY: Data will only be pushed to
	 * 		QuickBooks, and nothing that already exists in QuickBooks will be
	 * 		imported into the SQL database.
	 * 	- WebConnector\Server\SQL::READWRITE: The server will do it's best to
	 * 		try to import all QuickBooks data into the SQL database, and then
	 * 		push changes that occur in either location to the other location.
	 * 		The server will try to syncronise the two locations as much as is
	 * 		possible.
	 *
	 * @param string 			$dsn_or_conn		DSN-style connection string or an already opened connection to the driver
	 * @param int|string 		$how_often			The maximum time we wait between updates/syncs (you can use a number of seconds or any valid string interval: "1 hour", "15 minutes", 60, etc.)
	 * @param char 				$mode				The mode the server should run in (static::MODE_READONLY, static::MODE_WRITEONLY, static::MODE_READWRITE)
	 * @param char 				$conflicts			The steps towards update conflict resolution the server should take (see constants above)
	 * @param int 				$delete				Delete mode (static::DELETE_REMOVE [actually delete] or static::DELETE_FLAG [flag only])
	 * @param mixed 			$users				The WebConnect user (or an array of users) who will be using the SQL server
	 * @param AdapterInterface	$soapAdapter		SOAP server adapter interface (Built-In or PHP Extension)
	 * @param array 			$map
	 * @param array 			$onerror
	 * @param array				$hooks				Hooks
	 * @param int				$log_level			Log level (PackageInfo::LogLevel['NONE'], NORMAL, VERBOSE, DEBUG, DEVELOPER)
	 * @param array 			$handler_options
	 * @param array 			$driver_options
	 * @param array				$sql_options
	 * @param array				$callback_options
	 */
	public function __construct(
		$dsn_or_conn,
		$how_often,
		string $mode,
		int $conflicts,
		int $delete,
		$users = null,
		AdapterInterface $soapAdapter,
		array $map = [],
		array $onerror = [],
		array $hooks = [],
		int $log_level = PackageInfo::LogLevel['NORMAL'],
		array $handler_options = [],
		array $driver_options = [],
		array $sql_options = [],
		array $callback_options = [])
	{
		// Not sure what this is actually for
		$this->_timestamp = microtime(true);

		// Set the default time zone.  Should be set to the WebConnect client's time zone if possible.
		$this->setDefaultTimeZone();

		if (!is_array($users))
		{
			$users = [$users];
		}

		// Make sure $mode is a valid option
		if (!in_array($mode, [static::MODE_READONLY, static::MODE_WRITEONLY, static::MODE_READWRITE])) {
			throw new Exception('$mode must be one of: [static::MODE_READONLY (r), static::MODE_WRITEONLY (w), static::MODE_READWRITE (+)]');
		}

		// Make sure $conflicts is a valid option
		if (!in_array($conflicts, [static::CONFLICT_LOG, static::CONFLICT_NEWER, static::CONFLICT_QUICKBOOKS, static::CONFLICT_SQL, static::CONFLICT_CALLBACK])) {
			throw new Exception('$conflicts must be one of: [static::CONFLICT_LOG (2), static::CONFLICT_NEWER (4), static::CONFLICT_QUICKBOOKS (8), static::CONFLICT_SQL (16), static::CONFLICT_CALLBACK (32)] but is ' . $conflicts);
		}

		// Make sure $delete is a valid option
		if (!in_array($delete, [static::DELETE_REMOVE, static::DELETE_FLAG])) {
			throw new Exception('$mode must be one of: [static::DELETE_REMOVE (2), static::DELETE_FLAG (4)] but is ' . $delete);
		}

		// Map of callback handlers
		$sql_map = [];

		foreach (get_class_methods(Callbacks::class) as $method)
		{
			if (strtolower(substr($method, -7)) == 'request')
			{
				$action = substr($method, 0, -7);

				$sql_map[$action] = [
					Callbacks::class . '::' . $action . 'Request',
					Callbacks::class . '::' . $action . 'Response',
				];
			}
		}
		/*
		$sql_map[PackageInfo::Actions['DERIVE_ITEM']] = [
			'Callbacks::ItemDeriveRequest',
			'Callbacks::ItemDeriveResponse',
		];

		$sql_map[PackageInfo::Actions['DERIVE_CUSTOMER']] = [
			'Callbacks::CustomerDeriveRequest',
			'Callbacks::CustomerDeriveResponse',
		];

		$sql_map[PackageInfo::Actions['DERIVE_INVOICE']] = [
			'Callbacks::InvoiceDeriveRequest',
			'Callbacks::InvoiceDeriveResponse'
		];
		*/

		//print_r($sql_map);
		//exit;

		// Default error handlers
		$sql_onerror = [
			'*' => Errors::class . '::catchall',
		];

		$sql_onerror = $this->_merge($sql_onerror, $onerror, false);

		// Default hooks
		$sql_hooks = [
			// This hook is neccessary for queueing up the appropriate actions to perform the sync 	(use login success so we know user to sync for)
			Handlers::HOOK_LOGINSUCCESS => [Callbacks::class .'::onAuthenticate'],
		];

		// Merge with user-defined hooks
		$sql_hooks = $this->_merge($hooks, $sql_hooks, true);

		// @TODO Prefix these with _ so that people don't accidentally overwrite them
		$sql_callback_options = [
			'hooks' => $sql_hooks,
			'conflicts' => $conflicts,
			'mode' => $mode,
			'delete' => $delete,
			'recur' => Utilities::intervalToSeconds($how_often),
			'map' => $sql_map,
		];

		//print_r($sql_options);
		//exit;

		$defaults = $this->_sqlDefaults($sql_options);

		//$sql_callback_options['_only_query'] = $defaults['only_query'];
		//$sql_callback_options['_dont_query'] = $defaults['dont_query'];
		$sql_callback_options['_only_import'] = $defaults['only_import'];
		$sql_callback_options['_dont_import'] = $defaults['dont_import'];
		$sql_callback_options['_only_add'] = $defaults['only_add'];
		$sql_callback_options['_dont_add'] = $defaults['dont_add'];
		$sql_callback_options['_only_modify'] = $defaults['only_modify'];
		$sql_callback_options['_dont_modify'] = $defaults['dont_modify'];
		$sql_callback_options['_only_misc'] = $defaults['only_misc'];
		$sql_callback_options['_dont_misc'] = $defaults['dont_misc'];

		// Merge default values with passed in values
		//	(in this case, we are *required* to have these values present, so
		//	we make sure that the SQL options override any user-defined options
		$sql_callback_options = $this->_merge($callback_options, $sql_callback_options, false);

		// Initialize the Driver singleton
		$Driver = Singleton::getInstance($dsn_or_conn, $driver_options, $sql_hooks, $log_level);

		//public function __construct($dsn_or_conn, AdapterInterface $soapAdapter, array $map, array $onerror = [], array $hooks = [], int $log_level = PackageInfo::LogLevel['NORMAL'], $handler_options = [], $driver_options = [], $callback_options = [])
		parent::__construct($Driver, $soapAdapter, $sql_map, $sql_onerror, $sql_hooks, $log_level, $handler_options, $driver_options, $sql_callback_options);

		/*
		// TESTING only
		$requestID = null;
		$user = 'quickbooks';
		$hook = Handlers::HOOK_LOGINSUCCESS;
		$err = null;
		$hook_data = [];
		$callback_config = $sql_callback_options;
		Callbacks::onAuthenticate($requestID, $user, $hook, $err, $hook_data, $callback_config);
		*/
	}

	/**
	 * Apply default options to an array of configuration options
	 */
	protected function _sqlDefaults(array $config): array
	{
		$tmp = [
			//'only_query',
			//'dont_query',
			'only_import',
			'dont_import',
			'only_add',
			'dont_add',
			'only_modify',
			'dont_modify',
			'only_misc',
			'dont_misc',
		];

		foreach ($tmp as $filter)
		{
			if (empty($config[$filter]) || !is_array($config[$filter]))
			{
				$config[$filter] = [];
			}
		}

		// Any other configuration defaults go here
		$defaults = [];

		return array_merge($defaults, $config);
	}
}
