<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

final class IppObjectTest extends XmlBaseTest
{
	private $classMap = [];

	public function setUp(): void
	{
		$this->classMap = $this->classFinder(dirname(__DIR__) . '/src/QuickBooksPhpDevKit/IPP/Object');
	}

	public function tearDown(): void
	{
	}


	/**
	 * Create an object of the same name as the IPP Object files.
	 * Catches parse errors and naming mismatches.
	 */
	public function testCreateIppObjects(): void
	{
		foreach ($this->classMap as $className) {
			$fullClassName = "{$this->namespace_ipp}Object\\$className";
			//fwrite(STDOUT, "\t$fullClassName\n");
			if ($className == 'Report')
			{
				// Report class takes an argument
				$obj = new $fullClassName('report-title');
			} else {
				$obj = new $fullClassName();
			}

			$this->assertInstanceOf($fullClassName, $obj);

			// Unset $obj after testing it to clear it out for the next loop
			unset($obj);
		}

	}
}
