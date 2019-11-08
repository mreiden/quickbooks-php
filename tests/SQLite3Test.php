<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\DatabaseTest;

use \SQLite3 as PhpSQLite3;

use QuickBooksPhpDevKit\{
	Callbacks,
	Driver,
	PackageInfo,
	Utilities,
	WebConnector\Queue,
	WebConnector\QWC,
};
use QuickBooksPhpDevKit\Driver\{
	Factory,
	Singleton,
	Sql,
	Sql\Sqlite3,
};

class SQLite3Test extends DatabaseTest
{
	/**
	 * Whether or not these tests are enabled
	 * @var bool
	 */
	static protected $_enabled;

	/**
	 * The database configuration from ini file
	 * @var array
	 */
	static protected $_ini_config;

	/**
	 * Default database connection settings
	 * @var array
	 */
	static protected $_dsn_defaults = [
		'backend' => 'sqlite3',
		'database' => ':memory:',
	];

	/**
	 * Either ':memory:' or the path to the SQLite3 file to use (or create)
	 * @var string
	 */
	protected $__db_file;


	public static function setUpBeforeClass(?string $ini_section_name = null): void
	{
		$ini_section_name = $ini_section_name ?? 'sqlite3';
		$ini_section_name = strtolower($ini_section_name);

		static::$_enabled = ($ini_section_name === 'sqlite3');
		if (!extension_loaded($ini_section_name))
		{
			return;
		}

		$ini = parse_ini_file(__DIR__.'/database-configuration.ini', true, INI_SCANNER_TYPED);
		if (false !== $ini)
		{
			$ini = array_change_key_case($ini, CASE_LOWER);

			if (!empty($ini[$ini_section_name]))
			{
				$ini = $ini[$ini_section_name];

				if (!empty($ini['enabled']) && !empty($ini['database']))
				{
					static::$_enabled = true;
					static::$_ini_config = array_merge(static::$_dsn_defaults, $ini);
				}
			}
		}
	}
	public static function tearDownAfterClass(): void
	{
	}



	public function tearDown(): void
	{
		//fwrite(STDERR, "\t" . __METHOD__ .': ');
		$this->__db->close();
		$this->__db = null;

		if (!empty($this->__db_file) && ($this->__db_file != ':memory:'))
		{
			$file = realpath($this->__db_file);
			if ($file)
			{
				fwrite(STDERR, "Deleting $file\n");
				unlink($file);
			}
			fwrite(STDERR, var_export($this->__db, true) ."\n");
		}
	}
	public function setUp(): void
	{
		//fwrite(STDERR, "\t" . __METHOD__ .': ' . var_export($this->__db, true) . " | ");

		$this->__db_file = ':memory:';
		//$this->__db_file = tempnam(sys_get_temp_dir(), 'sqlite3.');
		$this->__db = new PhpSQLite3($this->__db_file);
	}


	/**
	 * Fetch all rows into an array using the PHP database connection (not our Driver class)
	 */
	protected function &fetchAll(string $sql): ?array
	{
		$rows = [];

		$res = $this->__db->query($sql);
		if (false === $res)
		{
			// Query Error
			return null;
		}

		while (($row = $res->fetchArray(SQLITE3_ASSOC)) !== false)
		{
			$rows[] = $row;
		}

		return $rows;
	}



	/**
	 * Test Database Connection
	 */
	public function testConnection(): void
	{
		$this->assertInstanceOf(PhpSQLite3::class, $this->__db);
	}

	/**
	 * Test using SQLite instead of SQLite3 (both should use SQLite3 since SQLite was removed in PHP 7.0.0)
	 */
	public function testBackendAlias(): void
	{
		$dsn = [
			'backend' => 'SQLite',
			'database' => ':memory:',
		];

		$Driver = Factory::create($dsn, ['new_link' => true]);
		$this->assertInstanceOf(Sqlite3::class, $Driver);
	}

	/**
	 * Test DSN Connection String
	 */
	public function testDSNConnectionString(): void
	{
		$dsn = 'sqlite3://UnusedHost/:memory:';

		$Driver = Factory::create($dsn, ['new_link' => true]);
		$this->assertInstanceOf(Sqlite3::class, $Driver);
	}

	/**
	 * Test Driver Singleton
	 *
	 * @runInSeparateProcess  Required to have a clean unitialized Singleton
	 */
	public function testSingleton(): void
	{
		$dsn = [
			'backend' => 'SQLite',
			'database' => ':memory:',
		];

		$Driver = Singleton::initialize($dsn);
		$this->assertInstanceOf(Sqlite3::class, $Driver);

		$Driver2 = Singleton::getInstance();
		$this->assertInstanceOf(Sqlite3::class, $Driver);
	}

	/**
	 * Test Connection Failure
	 */
	public function testConnectionFailure(): void
	{
		$this->expectException('\Exception');

		$dsn = [
			'backend' => 'SQLite3',
			'database' => __DIR__.'/corrupt-sqlite3-db.txt',
		];

		$Driver = Factory::create($dsn, ['new_link' => true]);
	}

	/**
	 * Test setting an invalid log level.  This is to test the abstract Driver class.
	 */
	public function testDriverInvalidLogLevelException(): void
	{
		$this->expectException('\Exception');
		$Driver = Factory::create($this->__db, ['new_link' => true]);
		$Driver->setLogLevel(1 + max(PackageInfo::LogLevel));
	}

	/**
	 * Test log level from driver_options.  This is to test the abstract Driver class.
	 */
	public function testLogLevelFromDriverOptions(): void
	{
		$Driver = Factory::create($this->__db, ['new_link' => true, 'log_level' => PackageInfo::LogLevel['DEBUG']]);
		$this->assertEquals(PackageInfo::LogLevel['DEBUG'], $Driver->getLogLevel());
	}

	/**
	 * Test resolving an empty ticket ('').  This is to test the abstract Driver class.
	 */
	public function testDriverTicketResolveEmpty(): void
	{
		$Driver = Factory::create($this->__db, ['new_link' => true]);
		$this->assertEquals(null, $Driver->authResolve(''));
	}


	/**
	 * Test Database Uninitialized / Initialized (Non-SQL Mirroring)
	 */
	public function testInitialization(): void
	{
		//$this->assertEquals($this->__driver_options, []);

		// Confirm the database is not initialized
		$this->assertEquals(false, Utilities::initialized($this->__db, ['new_link' => true]));

		// Initialize the database (Non-SQL Mirroring)
		$this->assertEquals(true, Utilities::initialize($this->__db));

		// Check if it initialized
		$this->assertEquals(true, Utilities::initialized($this->__db));
	}

	/**
	 * Test Database Uninitialized / Initialized (SQL Mirroring)
	 */
	public function testInitializationSqlMirroring(): void
	{
		//$this->assertEquals($this->__driver_options, []);

		// Confirm the database is not initialized
		$this->assertEquals(false, Utilities::initialized($this->__db, ['new_link' => true]));

		// Set the SQL Mirroring initialization flag and initialize the database
		$this->__init_options['quickbooks_sql_enabled'] = true;
		$this->assertEquals(true, Utilities::initialize($this->__db, ['new_link' => true], $this->__init_options));

		// Check if it initialized
		$this->assertEquals(true, Utilities::initialized($this->__db));
	}



   /**
	 * Test resolving a non-existing ticket.  This is to test the abstract Driver class.
	 */
	public function testDriverTicketResolveInvalid(): void
	{
		$Driver = Factory::create($this->__db, ['new_link' => true]);
		$this->assertEquals(true, Utilities::initialize($Driver));

		$this->assertNull($Driver->authResolve('InvalidTicket-fahktjrehabjkhbvkft'));
	}


   /**
	 * Test a non-existing ticket.  This is to test the abstract Driver class.
	 */
	public function testDriverInvalidTicketTests(): void
	{
		$Driver = Factory::create($this->__db, ['new_link' => true]);
		$this->assertEquals(true, Utilities::initialize($Driver));

		// Test Queue status on an non-existent ticket
		$this->assertSame(false, $Driver->queueStatus('InvalidTicket-fahktjrehabjkhbvkft', 1111111, PackageInfo::Status['QUEUED']));

		// Test number of Queue items processed on an non-existent ticket
		$this->assertNull($Driver->queueProcessed('InvalidTicket-fahktjrehabjkhbvkft'));
	}

	/**
	 * Test Database User Functions
	 */
	public function testUser(): void
	{
		// Initialize the Database
		Utilities::initialize($this->__db, ['new_link' => true], $this->__init_options);
		// Get the Driver
		$Driver = Factory::create($this->__db, [], $this->__init_options);


		// Create a user for testing Driver->authDefault()
		$this->assertEquals(true, Utilities::createUser($this->__db, 'NewUser', $this->__webconnectPass));
		sleep(1);
		// createUser()
		$this->assertEquals(true, Utilities::createUser($this->__db, $this->__webconnectUser, $this->__webconnectPass));

		$rows = $this->fetchAll('SELECT * FROM '. Sql::$TablePrefix['BASE'] . Sql::$Table['USER']);
		$this->assertEquals(2, count($rows));

		$row = array_pop($rows);
		$this->assertEquals($row['qb_username'], $this->__webconnectUser);
		$this->assertEquals(password_verify($this->__webconnectPass, $row['qb_password']), true);
		$this->assertEquals(Sql::USER_ENABLED, $row['status']);

		// Test resolving a valid ticket.
		$company_file = null;
		$wait_before_next_update = null;
		$min_run_every_n_seconds = null;
		$ticket = $Driver->authLogin($this->__webconnectUser, $this->__webconnectPass, $company_file, $wait_before_next_update, $min_run_every_n_seconds);
		$this->assertNotNull($ticket);


		// disableUser()
		$this->assertEquals(true, Utilities::disableUser($this->__db, $this->__webconnectUser));

		$rows = $this->fetchAll('SELECT * FROM '. Sql::$TablePrefix['BASE'] . Sql::$Table['USER'] . " WHERE qb_username = '". $Driver->escape($this->__webconnectUser) ."' ");
		$this->assertEquals(1, count($rows));

		$row = $rows[0];
		$this->assertEquals($row['qb_username'], $this->__webconnectUser);
		$this->assertEquals(Sql::USER_DISABLED, $row['status']);

		// Test resolving an existing, but invalid ticket (disabled user).
		$this->assertNull($Driver->authResolve($ticket));


		// enableUser()
		$this->assertEquals(true, Utilities::enableUser($this->__db, $this->__webconnectUser));

		$rows = $this->fetchAll('SELECT * FROM '. Sql::$TablePrefix['BASE'] . Sql::$Table['USER'] . " WHERE qb_username = '". $Driver->escape($this->__webconnectUser) ."' ");
		$this->assertEquals(1, count($rows));

		$row = $rows[0];
		$this->assertEquals($row['qb_username'], $this->__webconnectUser);
		$this->assertEquals(Sql::USER_ENABLED, $row['status']);

		// createUser() on an existing user
		$this->assertEquals(false, Utilities::createUser($this->__db, $this->__webconnectUser, $this->__webconnectPass));


		// authExists() when user does not exist
		$this->assertEquals(false, $Driver->authExists('BadUser'));

		// authExists() when user does exist
		$test = $Driver->authExists($this->__webconnectUser);
		$this->assertEquals($this->__webconnectUser, $test['qb_username']);

		// authExists() with restrictions (enabled status in this test)
		$test = $Driver->authExists($this->__webconnectUser, ['status' => Sql::USER_ENABLED]);
		$this->assertEquals($this->__webconnectUser, $test['qb_username']);

		// authExists() with restrictions (disabled status in this test)
		$this->assertEquals(false, $Driver->authExists($this->__webconnectUser, ['status' => Sql::USER_DISABLED]));


		// Test no Queue user set
		$q = new Queue($this->__db, null);

		// Test Driver->authDefault(): Should be first user created
		$this->assertEquals($this->__webconnectUser, $q->user());

		// Test changing the Queue user
		$q->user('NewUser');
		$this->assertEquals('NewUser', $q->user());
		$this->assertEquals('NewUser', $q->user(null));
	}


	/**
	 * Test Queue Functions
	 */
	public function testQueue(): void
	{
		Utilities::initialize($this->__db, ['new_link' => true], $this->__init_options);

		// Create the Queue for $this->__webconnectUser
		$q = new Queue($this->__db, $this->__webconnectUser, ['new_link' => true]);

		$dbId = 100;
		$priority = 1000;
		$extra = [];

		// Customer with website ID $dbId should not exist
		$this->assertSame([], $q->exists(PackageInfo::Actions['ADD_CUSTOMER'], $dbId, $this->__webconnectUser));
		// Add Customer to queue
		$this->assertSame(true, $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], $dbId, $priority, $extra, $this->__webconnectUser));
		// Customer should exist now
		$test = $q->exists(PackageInfo::Actions['ADD_CUSTOMER'], $dbId, $this->__webconnectUser);
		//fwrite(STDERR, "\nCustomer:\n".print_r($test,true)."\n");
		$this->assertEquals($dbId, $test['ident']);

		// Queue should only have 1 item for the Queue user ($this->__webconnectUser)
		$this->assertSame(1, $q->size($this->__webconnectUser));
		$this->assertSame(1, $q->size());

		// Try a customer with a alphanumeric website id
		$dbId = 'CUSTOMER-12345';
		$this->assertSame([], $q->exists(PackageInfo::Actions['ADD_CUSTOMER'], $dbId, $this->__webconnectUser));
		$this->assertSame(true, $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], $dbId, $priority, $extra, $this->__webconnectUser));

		// Remove the numeric id record
		$dbId = 100;
		$this->assertSame(true, $q->remove(PackageInfo::Actions['ADD_CUSTOMER'], $dbId, $this->__webconnectUser));
		// Customer with website ID $dbId should not exist
		$this->assertEquals([], $q->exists(PackageInfo::Actions['ADD_CUSTOMER'], $dbId, $this->__webconnectUser));


		// Test having an ident automatically generated
		$this->assertSame(true, $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], '', $priority, $extra, $this->__webconnectUser));

		// Test a numeric ident
		$this->assertSame(true, $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], 54321, $priority, $extra, $this->__webconnectUser));

		// Remove the numeric ident record using the configured Queue user
		$this->assertSame(true, $q->remove(PackageInfo::Actions['ADD_CUSTOMER'], 54321));


		// Test setting custom user
		$dbId = 500;
		$this->assertSame(true, $q->enqueue(PackageInfo::Actions['MOD_CUSTOMER'], $dbId, $priority, $extra, 'SomeoneElse'));
		$test = $q->exists(PackageInfo::Actions['MOD_CUSTOMER'], $dbId, 'SomeoneElse');
		$this->assertEquals('SomeoneElse', $test['qb_username']);

		// Test using user set in Queue constructor or via Queue->user('User')
		$dbId = 600;
		$this->assertSame(true, $q->enqueue(PackageInfo::Actions['MOD_CUSTOMER'], $dbId, $priority, $extra,));
		$test = $q->exists(PackageInfo::Actions['MOD_CUSTOMER'], $dbId);
		$this->assertEquals($this->__webconnectUser, $test['qb_username']);


		// Extra should be an empty string for null value
		// Get the Driver
		$Driver = Factory::create($this->__db, [], $this->__init_options);
		// Add a queue item
		$extraId = 50005;
		$this->assertSame(true, $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], $extraId, $extraId+1, null, $this->__webconnectUser));
		$rows = $this->fetchAll('SELECT * FROM '. Sql::$TablePrefix['BASE'] . Sql::$Table['QUEUE'] . " WHERE ident = '$extraId'");
		$this->assertEquals(1, count($rows));
		$row = array_pop($rows);
		$this->assertSame('', $row['extra']);
		// "extra" should be an empty array for queueDequeue, queueExists, and queueGet
		$test = $Driver->queueDequeue($this->__webconnectUser, true);
		$this->assertEquals($extraId, $test['ident']);
		$this->assertSame([], $test['extra']);
		$test = $Driver->queueExists($this->__webconnectUser, PackageInfo::Actions['ADD_CUSTOMER'], $extraId);
		$this->assertEquals($extraId, $test['ident']);
		$this->assertSame([], $test['extra']);
		$test = $Driver->queueGet($this->__webconnectUser, $test['quickbooks_queue_id'], null);
		$this->assertEquals($extraId, $test['ident']);
		$this->assertSame([], $test['extra']);

		$extraId = 50010;
		$extraData = ['test' => true];
		$this->assertSame(true, $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], $extraId, $extraId+1, $extraData, $this->__webconnectUser));
		$rows = $this->fetchAll('SELECT * FROM '. Sql::$TablePrefix['BASE'] . Sql::$Table['QUEUE'] . " WHERE ident = '$extraId'");
		$this->assertEquals(1, count($rows));
		$row = array_pop($rows);
		$this->assertSame(json_encode($extraData), $row['extra']);
		// Should get the original array for queueDequeue, queueExists, and queueGet
		$test = $Driver->queueDequeue($this->__webconnectUser, true);
		$this->assertEquals($extraId, $test['ident']);
		$this->assertSame(json_encode($extraData), $row['extra']);
		$test = $Driver->queueExists($this->__webconnectUser, PackageInfo::Actions['ADD_CUSTOMER'], $extraId);
		$this->assertEquals($extraId, $test['ident']);
		$this->assertSame(json_encode($extraData), $row['extra']);
		$test = $Driver->queueGet($this->__webconnectUser, $test['quickbooks_queue_id'], null);
		$this->assertEquals($extraId, $test['ident']);
		$this->assertSame(json_encode($extraData), $row['extra']);


		// Test Recurring Queue
		$priority = null;
		$dbId = null;
		$extra = [];
		$this->assertSame(true, $q->recurring('5 minutes 30 seconds 1 hour', PackageInfo::Actions['QUERY_CUSTOMER'], $dbId, $priority, $extra, $this->__webconnectUser));

		// Test a nonsense (unparsable by strtotime) Time Interval
		$this->expectException('\Exception');
		$test = $q->recurring('fdaffdasfdaf', PackageInfo::Actions['QUERY_CUSTOMER'], $dbId+5, $priority, $extra);
	}

	/**
	 * Test unimplemented Queue->interactive()
	 */
	public function testQueueInteractiveException(): void
	{
		$this->expectException('\Exception');

		Utilities::initialize($this->__db, ['new_link' => true], $this->__init_options);
		// Create the Queue for $this->__webconnectUser
		$q = new Queue($this->__db, $this->__webconnectUser, ['new_link' => true]);

		$test = $q->interactive(100, $this->__webconnectUser);
	}

	/**
	 * Test an invalid ident (must be string, int, or float)
	 */
	public function testQueueInvalidIdentException(): void
	{
		$this->expectException('\Exception');

		Utilities::initialize($this->__db, ['new_link' => true], $this->__init_options);
		// Create the Queue for $this->__webconnectUser
		$q = new Queue($this->__db, $this->__webconnectUser, ['new_link' => true]);

		$priority = 100;
		$extra = [];
		$test = $q->enqueue(PackageInfo::Actions['QUERY_CUSTOMER'], new stdClass(), $priority, $extra);
	}

	/**
	 * Test Queue->debug()
	 */
	public function testQueueDebug(): void
	{
		Utilities::initialize($this->__db, ['new_link' => true], $this->__init_options);
		// Create the Queue for $this->__webconnectUser
		$q = new Queue($this->__db, $this->__webconnectUser, ['new_link' => true]);

		ob_start();
		$q->debug();
		$test = ob_get_clean();
		$this->assertStringStartsWith(Sql::class."\\", $test);
	}

	/**
	 * Test using a hook that sets an error
	 */
	public function hookErrorTest($requestID, $user, $hook, &$err, $hook_data, $callback_config)
	{
		//fwrite(STDERR, "\nrequestID: $requestID\nUser: $user\nHook Name: $hook\nErrorStr: \"$err\"\nHook Data:\n". print_r($hook_data,true)."\nCallback Config:\n".print_r($callback_config,true)."\n");

		$err = 'Something Bad Happened Test';

		return false;
	}
	public function testDriverMiscellaneous(): void
	{
		PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['VERBOSE'];

		// Initialize the Database
		Utilities::initialize($this->__db, ['new_link' => true], $this->__init_options);
		// Get the Driver
		$Driver = Factory::create($this->__db, [], $this->__init_options);

		$success = Utilities::createUser($Driver, $this->__webconnectUser, $this->__webconnectPass);
		$company_file = null;
		$wait_before_next_update = null;
		$min_run_every_n_seconds = null;
		$ticket = $Driver->authLogin($this->__webconnectUser, $this->__webconnectPass, $company_file, $wait_before_next_update, $min_run_every_n_seconds);
		$this->assertNotNull($ticket);



		// Test for hook error
		$hooks = [
			Driver::HOOK_QUEUEPROCESSED => [$this, 'hookErrorTest'],
		];
		$Driver->registerHooks($hooks);
		$test = $Driver->queueProcessed($ticket);

		//fwrite(STDERR, "\nErr: $err\n\nRet Val: ". print_r($test, true)."\n");
		$this->assertSame(0, $test);


		// Test the last error
		$test = $Driver->errorLast($ticket);
		// Error is not passed between functions, but it should show up in the LOG table
		$this->assertEquals(PackageInfo::Error['HOOK'] . ': Something Bad Happened Test', $test);



		$dbId = 100;
		$priority = 1000;
		$extra = [];
		// Add Customer to queue
		$test = $Driver->queueEnqueue($this->__webconnectUser, PackageInfo::Actions['ADD_CUSTOMER'], (string) $dbId, true, $priority, $extra);
		//$this->assertEquals(true, $test);

		// Test Current Item Processing Queue
		$test = $Driver->queueProcessing($this->__webconnectUser);
		$this->assertNull($test);

		// Add Customer to queue with higher priority
		$test = $Driver->queueEnqueue($this->__webconnectUser, PackageInfo::Actions['ADD_CUSTOMER'], (string) $dbId+50, true, 2*$priority, $extra);

		// Test dequeue priority
		// Should get customer #150
		$test = $Driver->queueDequeue($this->__webconnectUser, true);
		//fwrite(STDERR, "\nDequeue Item #150:\n".print_r($test,true)."\n");
		$this->assertEquals(150, $test['ident']);
		$queueId = $test['quickbooks_queue_id'];

		// Test getting an item by request_id (quickbooks_queue.quickbooks_queue_id)
		$test = $Driver->queueGet($this->__webconnectUser, $queueId, PackageInfo::Status['QUEUED']);
		//fwrite(STDERR, "Queue item id #$queueId:\n".print_r($test,true)."\n");
		$this->assertEquals($queueId, $test['quickbooks_queue_id']);

		// Should get customer #100
		$test = $Driver->queueDequeue($this->__webconnectUser);
		//fwrite(STDERR, "\nDequeue Item #100:\n".print_r($test,true)."\n");
		$this->assertEquals(100, $test['ident']);


		// Set status to processing
		$test = $Driver->queueStatus($ticket, $queueId, PackageInfo::Status['PROCESSING']);
		// Test Current Item Processing Queue
		$test = $Driver->queueProcessing($this->__webconnectUser);
		//fwrite(STDERR, "Currently Processing:\n".print_r($test,true)."\n");
		$this->assertEquals($queueId, $test['quickbooks_queue_id']);
	}
}
