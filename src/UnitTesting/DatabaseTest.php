<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit_UnitTesting;

use PHPUnit\Framework\TestCase;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use \DomDocument;

use \SQLite3 as PhpSQLite3;
use \SQLite3Result;
use \SQLite3Stmt;

use QuickBooksPhpDevKit\Driver\Sql;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;


abstract class DatabaseTest extends XmlBaseTest
{
	protected $__db;

	protected $__webconnectUser = 'webconnect';
	protected $__webconnectPass = 'password';

	protected $__driver_options = [
		//'new_link' => true,
	];
	protected $__init_options = [
		'quickbooks_sql_enabled' => false,
	];


	/*
	 * Sets up the database connection
	 */
	public function setup(): void
	{
		throw new \Exception('You must implement the setup function in the database subclass.');
	}

	/*
	 * Closes and cleans up the database connection
	 */
	public function tearDown(): void
	{
		throw new \Exception('You must implement the setup function in the database subclass.');
	}

	/*
	 * Runs a query using PHP (not our package) to run a query and fetch all the rows as an associative array
	 */
	abstract protected function &fetchAll(string $sql): ?array;
}
