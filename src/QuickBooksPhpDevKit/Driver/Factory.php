<?php declare(strict_types=1);

/**
 *
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Driver
 */

namespace QuickBooksPhpDevKit\Driver;

use QuickBooksPhpDevKit\{
	Driver,
	PackageInfo,
	Utilities,
};

/**
 *
 *
 *
 */
class Factory
{
	/**
	 * Create an instance of a driver class from a DSN connection string *or* a connection resource
	 *
	 * You can actually pass in *either* a DSN-style connection string OR an already connected database resource
	 * 	- mysql://user:pass@localhost:port/database
	 * 	- $var (Resource ID #XYZ, valid MySQL connection resource)
	 *
	 * @param mixed $dsn_or_conn	A DSN-style connection string or a PHP resource
	 * @param array $config			An array of configuration options for the driver
	 * @param array $hooks			An array mapping hooks to user-defined hook functions to call
	 * @param integer $log_level
	 * @return object				An instance of a child class of Driver
	 */
	static public function create($dsn_or_conn, array $config = [], array $hooks = [], ?int $log_level = null): Driver
	{
		static $instances = [];

		// Set the logging level. Prefer in order: function parameter $log_level, log_level in $config parameter, static PackageInfo variable, NORMAL
		$log_level = $log_level ?? $config['log_level'] ?? PackageInfo::$LOGLEVEL ?? PackageInfo::LogLevel['NORMAL'];

		if (!empty($config['new_link']))
		{
			// Force a new database connection
			unset($config['new_link']);
			$instances = [];
		}

		// Do not serialize the $hooks because they might contain non-serializeable objects
		if (is_object($dsn_or_conn))
		{
			$key = get_class($dsn_or_conn) . json_encode($config) . $log_level;
		}
		else
		{
			$key = json_encode($dsn_or_conn) . json_encode($config) . $log_level;
		}

		if (!isset($instances[$key]))
		{
			$driver_namespace = __NAMESPACE__ . "\\Sql\\";

			if ($dsn_or_conn instanceof Driver)
			{
				// This is already a Driver object
				$Driver = $dsn_or_conn;
			}
			else
			{
				if (is_resource($dsn_or_conn))
				{
					$scheme = current(explode(' ', get_resource_type($dsn_or_conn)));
				}
				else if (is_object($dsn_or_conn))
				{
					$scheme = get_class($dsn_or_conn);
				}
				else
				{
					$scheme = Utilities::parseDSN($dsn_or_conn, [], 'scheme');
				}

				$scheme = ucfirst(strtolower($scheme));
				if ($scheme == 'Mysql')
				{
					// MySQL was removed in PHP 7.0, so use MySQLi
					$scheme = 'Mysqli';
				}
				else if ($scheme == 'Sqlite')
				{
					// SQLite was removed in PHP 5.4, so use SQLite3
					$scheme = 'Sqlite3';
				}

				$class = $driver_namespace . $scheme;
				//echo "class: $class\n";
				$Driver = new $class($dsn_or_conn, $config);
			}

			$Driver->registerHooks($hooks);
			$Driver->setLogLevel($log_level);

			$instances[$key] = $Driver;
		}

		return $instances[$key];
	}
}
