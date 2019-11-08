<?php declare(strict_types=1);

use QuickBooksPhpDevKit\Driver\{
    Factory,
    Singleton,
    Sql,
    Sql\Pgsql,
};
use QuickBooksPhpDevKit\Utilities;

// The PostgreSQL tests extend the SQLite3 tests
require_once(__DIR__ . '/SQLite3Test.php');



final class PgSqlTest extends SQLite3Test
{
    /**
     * Default database connection settings
     * @var array
     */
    static protected $_dsn_defaults = [
        'backend' => 'pgsql',
        'host' => 'localhost',
        'port' => 5432,
        'database' => 'quickbooks_tests',
        'schema' => 'public',
        'username' => 'postgres',
        'password' => '',
    ];

    /**
     * The database connection settings
     * @var array
     */
    protected $_dsn;

    /**
     * The PostgreSQL schema to use for our tables
     * @var string
     */
    private $_schema = 'public';

    /**
     * The PostgreSQL connection string
     * @var string
     */
    private $_conn_str;


    /**
     * Fetch all rows into an array using the PHP database connection (not our Driver class)
     */
    protected function &fetchAll(string $sql, array $params = []): ?array
    {
        $res = pg_query_params($this->__db, $sql, $params);
        $rows = pg_fetch_all($res, PGSQL_ASSOC);
        if ($rows === false)
        {
            if ('' === pg_result_error($res))
            {
                // pg_fetch_all returns false if there are no rows returned, so check if there was an error ('' means no error)
                $rows = [];
                return $rows;
            }

            // There was an error
            $rows = null;
            return $rows;
        }

        return $rows;
    }


    public static function setUpBeforeClass(?string $ini_section_name = null): void
    {
        parent::setUpBeforeClass('pgsql');
    }
    public static function tearDownAfterClass(): void
    {
    }

    public function tearDown(): void
    {
        if (is_resource($this->__db))
        {
            $closed = pg_close($this->__db);
        }
    }
    public function setUp(): void
    {
        // Skip these tests if the extension is not loaded or 'enabled' is not true in the database-configuration.ini
        if (static::$_enabled !== true)
        {
            $this->markTestSkipped();
        }
        //fwrite(STDERR, "\t" . __METHOD__ .': ' . var_export($this->__db, true) . " | ");

        $conn_str = '';
        $this->_dsn = static::$_dsn_defaults;

        $ini = static::$_ini_config;
        if (!empty($ini['host']))
        {
            $this->_dsn['host'] = $ini['host'];
            $conn_str .= "host='{$ini['host']}' ";
        }
        if (!empty($ini['port']))
        {
            $this->_dsn['port'] = $ini['port'];
            $conn_str .= 'port=' . $ini['port'] . ' ';
        }
        if (!empty($ini['database']))
        {
            $tmp = explode('.', $ini['database'], 2);

            $database = array_shift($tmp);
            $this->_dsn['database'] = $database;
            $conn_str .= "dbname='{$database}' ";

            $schema = array_shift($tmp);
            if (null !== $schema)
            {
                $this->_dsn['schema'] = $schema;
                $this->_schema = $schema;
            }
        }
        if (!empty($ini['username']))
        {
            $this->_dsn['username'] = $ini['username'];
            $conn_str .= "user='{$ini['username']}' ";
        }
        if (!empty($ini['password']))
        {
            $this->_dsn['password'] = $ini['password'];
            $conn_str .= "password='{$ini['password']}' ";
        }

        try
        {
            $connection = pg_connect($conn_str, PGSQL_CONNECT_FORCE_NEW);
        }
        catch (\Exception $e)
        {
            fwrite(STDERR, "Error connecting to PostgreSQL database using: \"$conn_str\"");
            throw $e;
        }

        // Set the schema search_path
        if (!empty($this->_schema) && ('public' !== $this->_schema))
        {
            $this->setSearchPath($this->_schema);
        }

        $this->__db = &$connection;
        if (false === $this->dropQuickbooksTables())
        {
            throw new \Exception("Failed to clean up quickbooks tables: \"$conn\"\n");
        }
    }

    private function setSearchPath(?string $schema = null)
    {
        $schema = $schema ?? $this->_schema;

        $res = pg_query_params($this->__db, 'SET search_path TO $1, public', [$this->_schema]);
        if ($res === false)
        {
            throw new \Exception("Failed to set search_path to {$this->_schema}, public\n");
        }
    }

    private function getTables(?string $schema = null): ?array
    {
        $schema = $schema ?? $this->_schema;

        $params = [
            $schema,
            Sql::$TablePrefix['BASE'].'%',
            Sql::$TablePrefix['SQL_MIRROR'].'%',
            Sql::$TablePrefix['INTEGRATOR'].'%',
        ];

        $SQL = '
            SELECT
                table_name
            FROM
                information_schema.tables
            WHERE
                table_schema = $1
                AND table_type = \'BASE TABLE\'
                AND (
                    table_name like $2
                    OR table_name like $3
                    OR table_name like $4
                ) ';
        $rows = $this->fetchAll($SQL, $params);

        return $rows;
    }

    private function dropQuickbooksTables(?string $schema = null): bool
    {
        $schema = trim($schema ?? $this->_schema);

        $tables = $this->getTables($schema);
        foreach ($tables as $row)
        {
            $res = pg_query($this->__db, 'DROP TABLE IF EXISTS ' . pg_escape_identifier($this->__db, $schema) .'.' . pg_escape_identifier($this->__db, $row['table_name']));
            if ($res === false)
            {
                return false;
            }
        }

        // Drop the schema too if it is not "public"
        if ($schema !== 'public')
        {
            $res = pg_query($this->__db, 'DROP SCHEMA IF EXISTS ' . pg_escape_identifier($this->__db, $schema));
            if ($res === false)
            {
                return false;
            }
        }

        return true;
    }




    /**
     * Test Database Connection
     */
    public function testConnection(): void
    {
        $this->assertEquals('pgsql link', strtolower(get_resource_type($this->__db)));
    }

    /**
     * Test Backend Alias
     */
    public function testBackendAlias(): void
    {
        // PostgreSQL does not have an alias (e.g. SQLite is now an alias for SQLite3 since it was removed in PHP 7.0.0)
        $this->assertEquals(true, true);
    }

    /**
     * Test DSN Connection String
     */
    public function testDSNConnectionString(): void
    {
        $dsn = 'pgsql://';
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
        $this->assertInstanceOf(Pgsql::class, $Driver);
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
        $this->assertInstanceOf(Pgsql::class, $Driver);

        $Driver2 = Singleton::getInstance();
        $this->assertInstanceOf(Pgsql::class, $Driver);
    }

    /**
     * Test using a custom PostgreSQL schema
     */
    public function testCustomSchema(): void
    {
        $dsn = $this->_dsn;
        // Remove any existing schema from the database string
        $dsn['database'] = explode('.', $dsn['database'])[0];

        // Pick a random schema name
        $schema = 'custom' . mt_rand(1000, 9999);
        $dsn['database'] .= ".$schema";


        // Drop the tables (just to make sure)
        $this->dropQuickbooksTables($schema);

        // Initialize the database
        Utilities::initialize($dsn, ['new_link' => true]);

        // Count the created tables
        $tables = $this->getTables($schema);
        $this->assertGreaterThan(0, count($tables));

        // Drop the tables
        $this->dropQuickbooksTables($schema);

        // Should have 0 tables after dropping the quickbooks tables
        $tables = $this->getTables($schema);
        $this->assertSame(0, count($tables));
    }

    /**
     * Test Connection Failure
     */
    public function testConnectionFailure(): void
    {
        $this->expectException('\Exception');

        $dsn = [
            'backend' => 'pgsql',
            'host' => '300.300.300.300',    // Invalid host
            'port' => -1,                   // Invalid port
            'database' => 'DoesNotMatter',
            'username' => 'uR1VaP211xHRh8mA',
            'password' => 'm3c7jUiOCZWeGG7B',
        ];

        $Driver = Factory::create($dsn, ['new_link' => true]);
    }
}
