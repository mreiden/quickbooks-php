<?php declare(strict_types=1);

/**
 * PgSQL backend for the QuickBooks SOAP server
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
 * This backend driver is for a PostgreSQL database. You can use the
 * {@see Utilities} class to initalize the tables in the
 * PostgreSQL database.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Driver
 */

namespace QuickBooksPhpDevKit\Driver\Sql;

use QuickBooksPhpDevKit\Driver\Sql;
use QuickBooksPhpDevKit\Utilities;

/**
 * QuickBooks PostgreSQL back-end driver
 */
class Pgsql extends Sql
{
	/**
	 * PostgreSQL connection resource
	 *
	 * @var resource
	 */
	protected $_conn;

	/**
	 *
	 */
	protected $_last_result;

	/**
	 *
	 */
	protected $_schema = 'public';

	/**
	 * The table last used by $this->insert()
	 * @var string
	 */
	protected $last_insert_table;

	/**
	 * Create a new PgSQL back-end driver
	 *
	 * @param resource|array|string	$dsn	A database connection resource, configuration array, or a DSN-style connection string (i.e.: "pgsql://your-pgsql-username:your-pgsql-password@your-pgsql-host:port/your-pgsql-database")
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

		if (is_resource($dsn_or_conn))
		{
			$this->_conn = $dsn_or_conn;
		}
		else
		{
			$defaults = [
				'backend' => 'pgsql',
				'host' => 'localhost',
				'port' => 5432,
				'username' => 'pgsql',
				'password' => '',
				'database' => 'quickbooks',
			];

			$parse = Utilities::parseDSN($dsn_or_conn, $defaults);

			$this->_connect($parse['host'], $parse['port'], $parse['username'], $parse['password'], $parse['database'], $config['new_link']);
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
		$res = $this->_query("
			SELECT
				table_name
			FROM
				information_schema.tables
			WHERE
				table_schema = '" . $this->_escape($this->_schema) . "' AND table_type = 'BASE TABLE' ", $errnum, $errmsg);
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
	 * @param integer $client_flags		Database connection flags (see the PHP/PgSQL documentation)
	 * @return boolean
	 */
	protected function _connect(string $host, $port, string $user, string $pass, string $db, bool $new_link, ?int $client_flags = null): bool
	{
		$tmp = [];

		if ($host)
		{
			$tmp[] = "host='{$host}'";
		}

		$port = filter_var($port, FILTER_VALIDATE_INT);
		if (false !== $port)
		{
			$tmp[] = 'port=' . $port;
		}

		if ($user)
		{
			$tmp[] = "user='{$user}'";
		}

		if ($pass)
		{
			$tmp[] = "password='{$pass}'";
		}

		// Default to using the 'public' schema
		$this->_schema = 'public';

		if ($db)
		{
			$db_and_schema = explode('.', $db, 2);
			$database = array_shift($db_and_schema);
			$tmp[] = "dbname='{$database}'";

			$schema = array_shift($db_and_schema);
			if (null !== $schema)
            {
				$this->_schema = $schema;
			}
		}

		$str = implode(' ', $tmp);

		// Connect to PostgreSQL
		try
		{
			$this->_conn = $new_link ? pg_connect($str, PGSQL_CONNECT_FORCE_NEW) : pg_connect($str);
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}

		if (!empty($this->_schema) && ('public' !== $this->_schema))
		{
			//print('SETTING HERE: [' . $this->_schema . ']');

			$errnum = 0;
			$errmsg = null;
			$this->_query("SET search_path TO " . $this->_escape($this->_schema) . ', public', $errnum, $errmsg);
		}

		//$errnum = 0;
		//$errmsg = null;
		//print_r($this->_fetch($this->_query("SHOW search_path", $errnum, $errmsg)));
		//die('SCHEMA IS: ' . $this->_schema);

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
	protected function _fetch($res, bool $print = false): array
	{
		$arr = pg_fetch_assoc($res);
		if (false === $arr)
		{
			// No rows returned
			return [];
		}

		$booleans = [
			static::Field['TO_SYNC'],
			static::Field['TO_VOID'],
			static::Field['TO_DELETE'],
			static::Field['TO_SKIP'],
			static::Field['FLAG_SKIPPED'],
			static::Field['FLAG_DELETED'],
			static::Field['FLAG_VOIDED']
		];

		if ($arr)
		{
			foreach ($arr as $key => $value)
			{
				if (in_array($key, $booleans))
				{
					if ($value == 'f')
					{
						$value = false;
					}
					else if ($value == 't')
					{
						$value = true;
					}
					else
					{
						$value = null;
					}

					$arr[$key] = $value;
				}
			}
		}

		if (is_array($arr))
		{
			reset($arr);
		}

		/*
		if ($print)
		{
			print('{{');
			print_r($arr);
			die('}} OUTPUT STOP');
		}
		*/

		return $arr;
	}

	/**
	 * Query the database
	 *
	 * @param string $sql
	 * @return resource
	 */
	protected function _query(string $sql, ?int &$errnum, ?string &$errmsg, ?int $offset = 0, ?int $limit = null)
	{
		if (strtoupper(substr(trim($sql), 0, 6)) != 'UPDATE')
		{
			// PostgreSQL does not support LIMIT for UPDATE queries

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
		}

		//
		$boolean_fixes = [
			'qbsql_to_skip != 1' =>			' qbsql_to_skip <> TRUE ',
			'qbsql_to_delete != 1' =>		' qbsql_to_delete <> TRUE ',
			'qbsql_to_delete = 1' =>		' qbsql_to_delete = TRUE ',
			'qbsql_flag_deleted != 1' =>	' qbsql_flag_deleted <> TRUE ',
			'qbsql_to_void != 1' =>			' qbsql_to_void <> TRUE ',
			'qbsql_to_void = 1' =>			' qbsql_to_void = TRUE ',
			'qbsql_flag_voided != 1' =>		' qbsql_flag_voided <> TRUE ',
		];

		$sql = str_replace(array_keys($boolean_fixes), array_values($boolean_fixes), $sql);

		// Run the query
		$res = pg_query($this->_conn, $sql);

		$this->_last_result = $res;

		if (!$res)
		{
			$errnum = -1;
			$errmsg = pg_last_error($this->_conn);
			trigger_error('PostgreSQL Error: ' . $errmsg . ', SQL: ' . $sql, E_USER_ERROR);

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
		return pg_affected_rows($this->_last_result);
	}

	/**
	 * Tell the last inserted sequence (AUTO_INCREMENT) value
	 */
	public function last(): int
	{
		$errnum = 0;
		$errmsg = '';

		// get the current table's primary key
		$sql = "SELECT
				pg_attribute.attname,
				format_type(pg_attribute.atttypid, pg_attribute.atttypmod)
			FROM pg_index, pg_class, pg_attribute
			WHERE
				pg_class.oid = '" . $this->last_insert_table . "'::regclass AND
				indrelid = pg_class.oid AND
				pg_attribute.attrelid = pg_class.oid AND
				pg_attribute.attnum = any(pg_index.indkey)
				AND indisprimary";

		$res = $this->query($sql, $errnum, $errmsg);

		$sequence = pg_fetch_result($res, 0, 0);

		// get the last ID
		$sql = "select currval(pg_get_serial_sequence('" . $this->last_insert_table . "', '" . $sequence . "'));";

		$res = $this->query($sql, $errnum, $errmsg);

		$last_insert_id = pg_fetch_result($res, 0, 0);

		return (int) $last_insert_id;
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
		return pg_escape_string($this->_conn, $str);
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
		return pg_num_rows($res);
	}

	/**
	 * Rewind the result set
	 */
	public function rewind($res): bool
	{
		if (pg_num_rows($res) > 0)
		{
			pg_fetch_assoc($res, 0);
		}

		return true;
	}

	/**
	 *
	 */
	protected function _fields(string $table): array
	{
		$list = [];

		$sql = "
			SELECT
				column_name
			FROM
				information_schema.columns
			WHERE
				table_name = '" . $this->_escape($table) . "' ";

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
	 * Override for the default SQL generation functions, PostgreSQL-specific field generation function
	 */
	protected function _generateFieldSchema(string $name, array $def): string
	{
		if ($this->foldsToLower())
		{
			$name = strtolower($name);
		}

		switch ($def[0])
		{
			case static::DataType['INTEGER']:
				$sql = '"' . $name . '" INTEGER ';

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
				break;

			case static::DataType['DECIMAL']:
				$sql = '"' . $name . '" DECIMAL ';

				if (!empty($def[1]))
				{
					$tmp = explode(',', $def[1]);
					if (count($tmp) == 2)
					{
						$sql .= '(' . (int) $tmp[0] . ',' . (int) $tmp[1] . ') ';
					}
				}

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' NULL ';
					}
					else
					{
						if (isset($tmp) && count($tmp) == 2)
						{
							$sql .= ' DEFAULT ' . sprintf('%01.'. (int) $tmp[1] . 'f', (float) $def[2]);
						}
						else
						{
							$sql .= ' DEFAULT ' . sprintf('%01.2f', (float) $def[2]);
						}
					}
				}

				if (isset($tmp))
				{
					unset($tmp);
				}
				break;

			case static::DataType['FLOAT']:
				$sql = '"' . $name . '" FLOAT ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' NULL ';
					}
					else
					{
						$sql .= ' DEFAULT ' . sprintf('%01.2f', (float) $def[2]);
					}
				}
				break;

			case static::DataType['BOOLEAN']:
				$sql = '"' . $name . '" BOOLEAN ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else if ($def[2])
					{
						$sql .= ' DEFAULT TRUE ';
					}
					else
					{
						$sql .= ' DEFAULT FALSE ';
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case static::DataType['SERIAL']:
				$sql = '"' . $name . '" SERIAL NOT NULL '; // AUTO_INCREMENT
				break;

			case static::DataType['DATE']:
				$sql = '"' . $name . '" DATE ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case static::DataType['TIMESTAMP']:
			case static::DataType['TIMESTAMP_ON_INSERT_OR_UPDATE']:
			case static::DataType['TIMESTAMP_ON_UPDATE']:
			case static::DataType['TIMESTAMP_ON_INSERT']:
			case static::DataType['DATETIME']:
				$sql = '"' . $name . '" timestamp without time zone ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else
					{
						$sql .= ' DEFAULT ' . $def[2] . ' NOT NULL ';
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case static::DataType['VARCHAR']:
				$sql = '"' . $name . '" VARCHAR';

				/*if ($name == 'ListID')
				{
					print('LIST ID:');
					print_r($def);
				}*/

				if (!empty($def[1]))
				{
					$sql .= '(' . (int) $def[1] . ') ';
				}

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else if ($def[2] === false)
					{
						$sql .= ' NOT NULL ';
					}
					else
					{
						$sql .= " NOT NULL DEFAULT '" . $def[2] . "' ";
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case static::DataType['CHAR']:
				$sql = '"' . $name . '" CHAR';

				if (!empty($def[1]))
				{
					$sql .= '(' . (int) $def[1] . ') ';
				}

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else
					{
						$sql .= " NOT NULL DEFAULT '" . $def[2] . "' ";
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case static::DataType['TEXT']:
			default:
				$sql = '"' . $name . '" TEXT ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else
					{
						$sql .= " NOT NULL DEFAULT '" . $def[2] . "' ";
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;
		}

		return $sql;
	}

	/**
	 * Override for the default SQL generation functions, PostgreSQL-specific field generation function
	 */
	protected function _generateCreateTable(string $name, array $arr, $primary = [], array $keys = [], array $uniques = [], bool $if_not_exists = true): array
	{
		static $schema_created;

		if ($this->foldsToLower())
		{
			$name = strtolower($name);
		}

		$arr_sql = parent::_generateCreateTable('"' . $name . '"', $arr, $primary, $keys, $uniques, $if_not_exists);

		// Create the schema if it's not "public"
		if (null === $schema_created && $this->_schema !== 'public')
		{
			$schema_created = true;
			array_unshift($arr_sql, 'CREATE SCHEMA IF NOT EXISTS ' . pg_escape_identifier($this->_conn, $this->_schema) . ';');
		}

		// Create primary key
		if (!is_array($primary))
		{
			$primary = [$primary];
		}
		if ($this->foldsToLower())
		{
			$primary = array_map('strtolower', $primary);
		}
		$arr_sql[] = 'ALTER TABLE ONLY "' . $name . '"
			ADD CONSTRAINT "' . $name . '_pkey" PRIMARY KEY ("' . implode('", "', $primary) . '");';

		// Create indexes
		foreach ($keys as $key)
		{
			if (is_array($key))		// compound key
			{
				if ($this->foldsToLower())
				{
					$key = array_map('strtolower', $key);
				}
				$arr_sql[] = 'CREATE INDEX "' . implode('_', $key) . '_' . $name . '_index" ON "' . $name . '" USING btree ("' . implode('", "', $key) . '")';
			}
			else
			{
				if ($this->foldsToLower())
				{
					$key = strtolower($key);
				}
				$arr_sql[] = 'CREATE INDEX "' . $key . '_' . $name . '_index" ON "' . $name . '" USING btree ("' . $key . '")';
			}
		}

		return $arr_sql;
	}

	/**
	 * Table and field names are folded to lowercase
	 */
	public function foldsToLower(): bool
	{
		return true;
	}

	/**
	 * Boolean datatype is a true boolean (true/false) and not 1/0
	 */
	public function hasTrueBoolean(): bool
	{
		return true;
	}

	/**
	 * Insert a new record into an SQL table
	 */
	public function insert(string $table, $object, bool $discov_and_resync = true): bool
	{
		$this->last_insert_table = $table;

		return parent::insert($table, $object, $discov_and_resync);
	}
}
