<?php declare(strict_types=1);

/**
 * SQLite3 backend for the QuickBooks SOAP server
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * You need to use some sort of backend to facilitate communication between the
 * SOAP server and your application. The SOAP server stores queue requests
 * using the backend.
 *
 * This backend driver is for a SQLite3 database. You can use the
 * {@see QuickBooks_Utilities} class to initalize the five tables in the SQLite3
 * database.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @contributor Hoang Nguyen <nkahoang@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Driver
 */

namespace QuickBooksPhpDevKit\Driver\Sql;

use \SQLite3 as PhpSQLite3;
use \SQLite3Result;
use QuickBooksPhpDevKit\Driver\Sql;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;


/**
 * QuickBooks SQLite3 back-end driver
 */
class Sqlite3 extends Sql
{
	/**
	 * SQLite3 connection resource
	 *
	 * @var SQLite3
	 */
	protected $_conn;

	/**
	 *
	 */
	protected $_database;

	/**
	 * Create a new SQLite3 back-end driver
	 *
	 * @param connection|array|string	$dsn	A database connection resource, configuration array, or a DSN-style connection string (i.e.: "sqlite3://your-sqlite3-username:your-sqlite3-password@your-sqlite3-host:port/your-sqlite3-database")
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

		if ($dsn_or_conn instanceof PhpSQLite3)
		{
			$this->_conn = $dsn_or_conn;
		}
		else
		{
			$defaults = [
				'backend' => 'sqlite3',
				'host' => 'localhost',
				'port' => '',
				'username' => '',
				'password' => '',
				'database' => '',
			];

			$parse = Utilities::parseDSN($dsn_or_conn, $defaults);
			//print_r($parse);
			//exit;

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
		$res = $this->_query("SELECT * FROM sqlite_master WHERE type = 'table' ", $errnum, $errmsg);
		while ($arr = $this->_fetch($res))
		{
			$table = $arr['tbl_name'];
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
	 * @param string $db	The absolute path to the SQLite3 database file
	 */
	protected function _connect(string $host, $port, string $user, string $pass, string $db, bool $new_link, ?int $client_flags = null): bool
	{
		$this->_conn = new PhpSQLite3($db);
		$this->_conn->enableExceptions(true);

		try
		{
			// SQLite3 is stupid and does not give an error if you open a non-SQLite3 database (such as a text file), so try querying the database information
			$err = 0;
			$errmsg = '';
			$this->_query('PRAGMA database_list', $err, $errmsg);
		}
		catch(\Exception $e)
		{
			throw new \Exception($this->_conn->lastErrorMsg());
		}

		return true;
	}

	/**
	 * Fetch an array from a database result set
	 */
	protected function _fetch($res): array
	{
		if (null === $res)
		{
			return [];
		}

		// returns false if no rows are fetched
		$arr = $res->fetchArray(SQLITE3_ASSOC);

		return false === $arr ? [] : $arr;
	}

	/**
	 * Query the database
	 */
	protected function _query(string $sql, ?int &$errnum, ?string &$errmsg, ?int $offset = 0, ?int $limit = null): ?SQLite3Result
	{
		$isSelectQuery = strtoupper(substr(trim($sql), 0, 6)) === 'SELECT';
		if ($isSelectQuery && null !== $limit)
		{
			$sql .= ' LIMIT ' . $limit;
			if (null !== $offset)
			{
				$sql .= ' OFFSET ' . $offset;
			}
		}

		$res = $this->_conn->query($sql);
		if (false === $res)
		{
			$errnum = -99;
			$errmsg = 'SQLLite Query Error';

			throw new \Exception('Error Num.: ' . $errnum . "\n" . 'Error Msg.:' . $errmsg . "\n" . 'SQL: ' . $sql);
		}

		if ($isSelectQuery)
		{
			// Really bad hack (fetch all rows and reset to the first) since SQLite3 lacks a num_rows function
			// Only for SELECT queries since it seems to cause an INSERT/UPDATE query to be run a second time.
			$numRows = 0;
			while ($row = $res->fetchArray(SQLITE3_NUM))
			{
				$numRows++;
			}
			$res->quickbooksRowCount = $numRows;
			$res->reset();
		}

		return $res;
	}

	/**
	 *
	 *
	 *
	 */
	protected function _fields(string $table): array
	{
		$sql = "PRAGMA table_info('" . $this->escape($table) ."')";

		$list = [];

		$errnum = 0;
		$errmsg = '';
		$res = $this->_query($sql, $errnum, $errmsg);
		if ($res)
		{
			while ($arr = $this->_fetch($res))
			{
				$list[] = $arr['name'];
			}
		}

		return $list;
	}

	/**
	 * Tell the number of rows the last run query affected
	 */
	public function affected(): int
	{
		return $this->_conn->changes();
	}

	/**
	 * Tell the last inserted AUTO_INCREMENT value
	 */
	public function last(): int
	{
		return $this->_conn->lastInsertRowID();
	}

	/**
	 * Fetch a record from a result set
	 */
	public function fetch($res): array
	{
		return $this->_fetch($res);
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
		return PhpSQLite3::escapeString($str);
	}

	/**
	 * Tell the number of records in a result resource
	 * /
	public function count($res): int
	{
		return $this->_count($res);
	}
	*/

	/**
	 * Count the number of rows returned from the database
	 */
	protected function _count($res): int
	{
		// return sqlite_num_rows($res);
		return $res->quickbooksRowCount ?? 0;
	}

	/**
	 * Rewind the result set
	 */
	public function rewind($res): bool
	{
		return $res->reset();
	}

	/**
	 * Override for the default SQL generation functions, SQLite3-specific field generation function
	 */
	protected function _generateFieldSchema(string $name, array $def): string
	{
		switch ($def[0])
		{
			case static::DataType['SERIAL']:
				$sql = $name . ' INTEGER PRIMARY KEY '; // AUTO_INCREMENT
				return $sql;

			case static::DataType['TIMESTAMP']:
			case static::DataType['TIMESTAMP_ON_INSERT_OR_UPDATE']:
			case static::DataType['TIMESTAMP_ON_UPDATE']:
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
				$sql = $name . ' int(10) ';

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

	protected function _generateCreateTable(string $name, array $arr, $primary = [], array $keys = [], array $uniques = [], bool $if_not_exists = true): array
	{
		$arr_sql = parent::_generateCreateTable($name, $arr, $primary, $keys, $uniques, $if_not_exists);

		if (is_array($primary) && count($primary) == 1)
		{
			$primary = current($primary);
		}

		if (is_array($primary))
		{
			$arr_sql[] = 'CREATE UNIQUE INDEX IF NOT EXISTS ' . $name . '_pkey ON ' . $name . ' ( ' . implode(', ', $primary) . ' ) ';
		}
		else if ($primary)
		{
			if ($arr[$primary][0] != static::DataType['SERIAL'])
			{
				$arr_sql[] = 'CREATE UNIQUE INDEX IF NOT EXISTS ' . $name . '_pkey ON ' . $name . ' ( ' . $primary . ' ) ';
			}
		}

		// Create indexes
		foreach ($keys as $key)
		{
			if (is_array($key))		// compound key
			{
				$arr_sql[] = 'CREATE INDEX ' . implode('_', $key) . '_' . $name . '_index ON ' . $name . ' (' . implode(', ', $key) . ')';
			}
			else
			{
				$arr_sql[] = 'CREATE INDEX ' . $key . '_' . $name . '_index ON ' . $name . ' (' . $key . ')';
			}
		}

		return $arr_sql;
	}
}
