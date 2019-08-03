<?php declare(strict_types=1);

/**
 * MySQLi backend for the QuickBooks SOAP server
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * This backend driver is for a MySQL database, using the PHP MySQLi extension.
 * You can use the {@see Utilities} class to initalize the tables in
 * the MySQL database.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Driver
 */

namespace QuickBooksPhpDevKit\Driver\Sql;

use \mysqli as PhpMysqli;
use QuickBooksPhpDevKit\Driver\Sql;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;

/**
 * QuickBooks MySQLi back-end driver
 */
class Mysqli extends Sql
{

	/**
	 * MySQLi connection resource
	 *
	 * @var resource
	 */
	protected $_conn;

	/**
	 * MySQLi query result
	 *
	 * @var result
	 */
	protected $_res;

	/**
	 * Last error message that occurred
	 *
	 * @var integer
	 */
	public $_last_error;

	/**
	 * Database name
	 */
	protected $_dbname;

	/**
	 * Create a new MySQLi back-end driver
	 *
	 * @param resource|array|string	$dsn	A database connection resource, configuration array, or a DSN-style connection string (i.e.: "mysqli://your-mysql-username:your-mysql-password@your-mysql-host:port/your-mysql-database")
	 * @param array 					$config	Configuration options for the driver (not currently supported)
	 */
	public function __construct($dsn_or_conn, array $config)
	{
		$config = $this->_defaults($config);

		// Use the log level if it was provided in config
		if (isset($config['log_level']))
		{
			$this->setLogLevel($config['log_level']);
		}

		if (is_resource($dsn_or_conn) || ($dsn_or_conn instanceof PhpMysqli))
		{
			$this->_conn = $dsn_or_conn;
		}
		else
		{
			$defaults = [
				'backend' => 'mysqli',
				'host' => 'localhost',
				'port' => 3306,
				'username' => 'root',
				'password' => '',
				'database' => 'quickbooks',
			];

			$parse = Utilities::parseDSN($dsn_or_conn, $defaults);

			// Store this for debugging
			$this->_dbname = $parse['database'];

			$this->_connect($parse['host'], $parse['port'], $parse['username'], $parse['password'], $parse['database'], $config['new_link'], $config['client_flags']);
		}

		// Call the parent constructor too
		parent::__construct($dsn_or_conn, $config);
	}

	/**
	 * Merge an array of configuration options with the defaults
	 */
	protected function _defaults(array $config): array
	{
		$defaults = [
			'log_level' => PackageInfo::LogLevel['NORMAL'],
			'client_flags' => 0,
			'new_link' => true,
		];

		return array_merge($defaults, $config);
	}

	/**
	 * Tell whether or not the SQL database has been initialized
	 */
	protected function _initialized(): bool
	{
		$required = [
			//$this->_mapTableName(static::$Table['IDENT']) => false,
			$this->_mapTableName(static::$Table['TICKET']) => false,
			$this->_mapTableName(static::$Table['USER']) => false,
			$this->_mapTableName(static::$Table['RECUR']) => false,
			$this->_mapTableName(static::$Table['QUEUE']) => false,
			$this->_mapTableName(static::$Table['LOG']) => false,
			$this->_mapTableName(static::$Table['CONFIG']) => false,
			//$this->_mapTableName(static::$Table['NOTIFY']) => false,
			//$this->_mapTableName(static::$Table['CONNECTION']) => false,
		];

		$numRequiredTables = count($required);
		$errnum = 0;
		$errmsg = '';
		$res = $this->_query("SHOW TABLES ", $errnum, $errmsg);
		while ($arr = $this->_fetch($res))
		{
			$table = current($arr);

			if (isset($required[$table]))
			{
				$numRequiredTables--;
			}
		}

		return 0 === $numRequiredTables;
	}

	/**
	 * Connect to the database
	 *
	 * @param string $host				The hostname the database is located at
	 * @param integer $port				The port the database is at
	 * @param string $user				Username for connecting
	 * @param string $pass				Password for connecting
	 * @param string $db				The database name
	 * @param boolean $new_link			TRUE for establishing a new link to the database, FALSE to re-use an existing one
	 * @param integer $client_flags		Database connection flags (see the PHP/MySQLi documentation)
	 * @return boolean
	 */
	protected function _connect(string $host, $port, string $user, string $pass, string $db, bool $new_link, ?int $client_flags = null): bool
	{
		$port = filter_var($port, FILTER_VALIDATE_INT);
		$port = $port !== false ? $port : 3306;

		// Throw errors for all errors except for missing index warnings
		$report_mode = MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX;
		\mysqli_report($report_mode);

		$this->_conn = new PhpMysqli($host, $user, $pass, $db, $port);
		if (!$this->_conn)
		{
			throw new \Exception('Mysqli Connection Failure using host: ' . $host . ', user: ' . $user . ', pass: ' . $pass . ' mysqli_error: ' . $this->_conn->connect_error);
		}
		$this->_conn->report_mode = $report_mode;

		return true;
	}

	/**
	 * Fetch a record from a result set
	 *
	 * @param resource $res
	 * @return array
	 */
	public function fetch($res): array
	{
		return $this->_fetch($res);
	}

	/**
	 * Fetch an array from a database result set
	 */
	protected function _fetch($res): array
	{
		// returns [] if no rows are fetched
		$arr = $res->fetch_assoc();

		return $arr ?? [];
	}

	/**
	 * Query the database
	 *
	 * @param string $sql
	 * @return resource
	 */
	protected function _query(string $sql, ?int &$errnum, ?string &$errmsg, ?int $offset = 0, ?int $limit = null)
	{
		if ($limit)
		{
			if ($offset)
			{
				$sql .= " LIMIT " . (int) $offset . ", " . (int) $limit;
			}
			else
			{
				$sql .= " LIMIT " . (int) $limit;
			}
		}
		else if ($offset)
		{
			// @todo Should this be implemented...?
		}

		$res = $this->_conn->query($sql);

		$this->_last_error = '';
		if (!$res)
		{
			$errnum = $this->_conn->errno;
			$errmsg = $this->_conn->error;
			$this->_last_error = $this->_conn->error;

			//print($sql);

			throw new \Exception('Error Num.: ' . $errnum . "\n" . 'Error Msg.:' . $errmsg . "\n" . 'SQL: ' . $sql . "\n" . 'Database: ' . $this->_dbname);
			return false;
		}

		return $res;
	}

	/**
	 * Issue a query to the SQL server
	 *
	 * @param string $sql
	 * @param integer $errnum
	 * @param string $errmsg
	 * @return resource
	 */
	/*public function query($sql, &$errnum, &$errmsg, $offset = 0, $limit = null)
	{
		return $this->_query($sql, $errnum, $errmsg, $offset, $limit);
	}*/

	/**
	 * Tell the number of rows the last run query affected
	 */
	public function affected(): int
	{
		return $this->_conn->affected_rows;
	}

	/**
	 * Tell the last inserted AUTO_INCREMENT value
	 */
	public function last(): int
	{
		return $this->_conn->insert_id;
	}

	/**
	 * Escape a string for the database
	 */
	public function escape(string $str): string
	{
		return $this->_escape($str);
	}

	/**
	 * Escape a string for the database
	 */
	protected function _escape(string $str): string
	{
		return $this->_conn->real_escape_string($str);
	}


	/**
	 * Tell the number of records in a result resource
	 */
	public function count($res): int
	{
		return $this->_count($res);
	}

	/**
	 * Count the number of rows returned from the database
	 */
	protected function _count($res): int
	{
		return $res->num_rows;
	}

	/**
	 * Rewind the result set
	 */
	public function rewind($res): bool
	{
		if ($res and $res->num_rows > 0)
		{
			return $res->data_seek(0);
		}

		return true;
	}

	/**
	 *
	 */
	protected function _fields(string $table): array
	{
		$sql = 'SHOW FIELDS FROM ' . $table;

		$list = [];

		$errnum = 0;
		$errmsg = '';
		$res = $this->_query($sql, $errnum, $errmsg);
		while ($arr = $this->_fetch($res))
		{
			$list[] = current($arr);
		}

		return $list;
	}

	/**
	 * Override for the default SQL generation functions, MySQLi-specific field generation function
	 */
	protected function _generateFieldSchema(string $name, array $def): string
	{
		switch ($def[0])
		{
			case static::DataType['SERIAL']:
				$sql = $name . ' INT(10) UNSIGNED NOT NULL '; // AUTO_INCREMENT
				return $sql;

			case static::DataType['TIMESTAMP']:
			case static::DataType['TIMESTAMP_ON_INSERT_OR_UPDATE']:
				$sql = $name . ' TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ';
				return $sql;

			case static::DataType['TIMESTAMP_ON_UPDATE']:
				$sql = $name . ' TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP ';
				return $sql;

			case static::DataType['TIMESTAMP_ON_INSERT']:
				$sql = $name . ' TIMESTAMP DEFAULT CURRENT_TIMESTAMP ';
				return $sql;

			case static::DataType['BOOLEAN']:
				$sql = $name . ' tinyint(1) ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else if ($def[2])
					{
						$sql .= ' DEFAULT 1 ';
					}
					else
					{
						$sql .= ' DEFAULT 0 ';
					}
				}
				return $sql;

			case static::DataType['INTEGER']:
				$sql = $name . ' int(10) unsigned ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else
					{
						$sql .= ' DEFAULT ' . (int) $def[2];
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				return $sql;

			default:
				return parent::_generateFieldSchema($name, $def);
		}
	}

	/**
	 * Override for the default SQL generation functions, MySQL-specific field generation function
	 */
	protected function _generateCreateTable(string $name, array $arr, $primary = [], array $keys = [], array $uniques = [], bool $if_not_exists = true): array
	{
		$arr_sql = parent::_generateCreateTable($name, $arr, $primary, $keys, $uniques, $if_not_exists);

		if (is_array($primary) and count($primary) == 1)
		{
			$primary = current($primary);
		}

		if (is_array($primary))
		{
			//ALTER TABLE  `quickbooks_ident` ADD PRIMARY KEY (  `qb_action` ,  `unique_id` )
			$arr_sql[] = 'ALTER TABLE ' . $name . ' ADD PRIMARY KEY ( ' . implode(', ', $primary) . ' ) ';
		}
		else if ($primary)
		{
			$arr_sql[] = 'ALTER TABLE ' . $name . ' ADD PRIMARY KEY(' . $primary . '); ';

			if ($arr[$primary][0] == static::DataType['SERIAL'])
			{
				// add the auto-increment
				$arr_sql[] = 'ALTER TABLE ' . $name . ' CHANGE ' . $primary . ' ' . $primary . ' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;';
			}
		}

		foreach ($keys as $key)
		{
			if (is_array($key))		// compound key
			{
				$arr_sql[] = 'ALTER TABLE ' . $name . ' ADD INDEX(' . implode(', ', $key) . ');';
			}
			else
			{
				$arr_sql[] = 'ALTER TABLE ' . $name . ' ADD INDEX(' . $key . ');';
			}
		}

		return $arr_sql;
	}
}
