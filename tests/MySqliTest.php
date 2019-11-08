<?php declare(strict_types=1);

use QuickBooksPhpDevKit\Driver\{
	Factory,
	Singleton,
	Sql,
	Sql\Mysqli
};

// The MySQLi tests extend the SQLite3 tests
require_once(__DIR__ . '/SQLite3Test.php');


use \mysqli as PhpMysqli;


final class MySqliTest extends SQLite3Test
{
	/**
	 * Default database connection settings
	 * @var array
	 */
	static protected $_dsn_defaults = [
		'backend' => 'mysqli',
		'host' => 'localhost',
		'port' => 3306,
		'database' => 'quickbooks_tests',
		'username' => 'mysql',
		'password' => '',
	];

	/**
	 * The database connection settings
	 * @var array
	 */
	protected $_dsn;

	/**
	 * Fetch all rows into an array using the PHP database connection (not our Driver class)
	 */
	protected function &fetchAll(string $sql, $mode = MYSQLI_ASSOC): ?array
	{

		$res = $this->__db->query($sql);
		if ($res)
		{
			$rows = $res->fetch_all($mode);
			if ($this->__db->errno)
			{
				$rows = null;
			}

			return $rows;

			//print($sql);
		}

		$rows = null;

		return $rows;
	}

	public static function setUpBeforeClass(?string $ini_section_name = null): void
	{
		parent::setUpBeforeClass('mysqli');
	}
	public static function tearDownAfterClass(): void
	{
	}


	public function tearDown(): void
	{
		$closed = $this->__db->close();
	}
	public function setUp(): void
	{
		// Skip these tests if the extension is not loaded or 'enabled' is not true in the database-configuration.ini
		if (static::$_enabled !== true)
		{
			$this->markTestSkipped('MySQLi tests are being skipped.');
		}

		//fwrite(STDERR, "\t" . __METHOD__ .': ' . var_export($this->__db, true) . " | ");
		$this->_dsn = static::$_dsn_defaults;
		$ini = static::$_ini_config;
		if (!empty($ini['host']))
		{
			$this->_dsn['host'] = $ini['host'];
		}
		if (!empty($ini['port']))
		{
			$this->_dsn['port'] = $ini['port'];
		}
		if (!empty($ini['database']))
		{
			$this->_dsn['database'] = $ini['database'];
		}
		if (!empty($ini['username']))
		{
			$this->_dsn['username'] = $ini['username'];
		}
		if (!empty($ini['password']))
		{
			$this->_dsn['password'] = $ini['password'];
		}

		$connection = new PhpMysqli($this->_dsn['host'], $this->_dsn['username'], $this->_dsn['password'], $this->_dsn['database'], $this->_dsn['port']);
		if (false === $connection)
		{
			throw new \Exception("Cannot connect to MySQL server: \"$conn\"");
		}

		$this->__db = &$connection;

		if (false === $this->dropQuickbooksTables())
		{
			throw new \Exception("Failed to clean up quickbooks tables: \"$conn\"");
		}
	}

	private function dropQuickbooksTables(): bool
	{
		$errnum = 0;
		$errmsg = '';

		$target_tables = [
			Sql::$TablePrefix['BASE'].'%',
			Sql::$TablePrefix['SQL_MIRROR'].'%',
			Sql::$TablePrefix['INTEGRATOR'].'%',
		];
		foreach ($target_tables as $like)
		{
			$rows = $this->fetchAll("SHOW TABLES LIKE '" . $like . "'", MYSQLI_NUM);
			if (is_array($rows))
			{
				foreach ($rows as $row)
				{
					$errnum = 0;
					$errmsg = '';

					$res = $this->__db->query('DROP TABLE IF EXISTS '. $row[0]);
					if ($res === false)
					{
						return false;
					}
				}
			}
		}

		return true;
	}




	/**
	 * Test Database Connection
	 */
	public function testConnection(): void
	{
		$this->assertInstanceOf(PhpMysqli::class, $this->__db);
	}

	/**
	 * Test Backend Alias
	 */
	public function testBackendAlias(): void
	{
		$dsn = static::$_ini_config;
		$dsn['backend'] = 'MySQL';

		$Driver = Factory::create($dsn, ['new_link' => true]);
		$this->assertInstanceOf(Mysqli::class, $Driver);
	}

	/**
	 * Test DSN Connection String
	 */
	public function testDSNConnectionString(): void
	{
		$dsn = 'mysqli://';
		if (!empty(static::$_ini_config['username']))
		{
			$dsn .= static::$_ini_config['username'];
		}
		if (!empty(static::$_ini_config['password']))
		{
			$dsn .= ':' . static::$_ini_config['password'];
		}
		$dsn .= '@' . static::$_ini_config['host'];
		if (!empty(static::$_ini_config['port']))
		{
			$dsn .= ':' . static::$_ini_config['port'];
		}
		$dsn .= '/' . static::$_ini_config['database'];

		$Driver = Factory::create($dsn, ['new_link' => true]);
		$this->assertInstanceOf(Mysqli::class, $Driver);
	}

	/**
	 * Test Driver Singleton
	 *
	 * @runInSeparateProcess  Required to have a clean unitialized Singleton
	 */
	public function testSingleton(): void
	{
		$dsn = $this->_dsn;

		$Driver = Singleton::initialize($dsn);
		$this->assertInstanceOf(Mysqli::class, $Driver);

		$Driver2 = Singleton::getInstance();
		$this->assertInstanceOf(Mysqli::class, $Driver);
	}

	/**
	 * Test Connection Failure
	 */
	public function testConnectionFailure(): void
	{
		$this->expectException('\Exception');

		$dsn = [
			'backend' => 'mysqli',
			'host' => '300.300.300.300',	// Invalid host
			'port' => -1,				   // Invalid port
			'database' => 'DoesNotMatter',
			'username' => 'uR1VaP211xHRh8mA',
			'password' => 'm3c7jUiOCZWeGG7B',
		];

		$Driver = Factory::create($dsn, ['new_link' => true]);
	}
}
