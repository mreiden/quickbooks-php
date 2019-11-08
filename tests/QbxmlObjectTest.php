<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;

final class QbxmlObjectTest extends XmlBaseTest
{
	private $classMap = [];

	public function setUp(): void
	{
		$this->classMap = $this->classFinder(dirname(__FILE__, 2) . '/src/QuickBooksPhpDevKit/QBXML/Object');
	}
	public function tearDown(): void
	{
	}


	/**
	 * Create an object of the same name as the QBXML Object files.
	 * Catches parse errors and naming mismatches.
	 */
	public function testCreateQbxmlObjects(): void
	{
		// Store the initial error reporting level
		$default_error_reporting = error_reporting();

		foreach ($this->classMap as $className)
		{
			error_reporting($default_error_reporting);
			if (strlen($className) > 4 && substr(strtolower($className), -4) == 'item')
			{
				// This is a deprecated *Item class (e.g. ServiceItem instead of ItemService)
				error_reporting($default_error_reporting & ~E_USER_DEPRECATED);
			}

			$fullClassName = "{$this->namespace}$className";
			//fwrite(STDOUT, "\t$fullClassName\n");
			$obj = new $fullClassName([]);

			$this->assertInstanceOf($fullClassName, $obj);

			// Unset $obj after testing it to clear it out for the next loop
			unset($obj);
		}
	}

	/**
	 * 1. Creates QBXML Object
	 * 2. Calls "setName" with the class name if the method exists.
	 * 3. Compares the generated QBXML for Add and Mod requests to what is expected.
	 */
	public function testQbxmlIsXml(): void
	{
		// Store the initial error reporting level
		$default_error_reporting = error_reporting();

		foreach ($this->classMap as $className)
		{
			error_reporting($default_error_reporting);
			if (strlen($className) > 4 && substr(strtolower($className), -4) == 'item')
			{
				// This is a deprecated *Item class (e.g. ServiceItem instead of ItemService)
				error_reporting($default_error_reporting & ~E_USER_DEPRECATED);
			}

			$fullClassName = "{$this->namespace}$className";
			$obj = new $fullClassName([]);

			if (method_exists($obj, 'setName'))
			{
				switch ($className) {
					case 'Employee':
						$obj->setFirstName('John');
						$node = '<FirstName>John</FirstName>';
						break;
					case'SalesTaxCode':
						// The SalesTaxCode name is limited to 3 characters, so we can not test with the full className
						$className = 'STC';
					default:
						$obj->setName($className);
						$node = '<Name>' . $className . '</Name>';
				}

				//$actionTypes = ['Add', 'Mod', 'Query'];
				$actionTypes = ['Add', 'Mod'];
				foreach ($actionTypes as $actionType)
				{
					$request = $obj->object() . $actionType;
					$schema = "{$this->namespace_schema}{$request}Rq";
					if (class_exists($schema))
					{
						//fwrite(STDOUT, "\t$request:\n");
						$xmlExpected = implode("\n", [
							"<{$request}Rq>",
							"  <{$request}>",
							'    ' . $node,
							"  </{$request}>",
							"</{$request}Rq>",
						]);
						//fwrite(STDOUT, "\t\tExpected  : $expected\n");

						$qbXML = $obj->asQBXML($request, null, PackageInfo::Locale['US']);
						//fwrite(STDOUT, "$xml\n");

						$compareAsString = false;
						$this->commonTests($xmlExpected, $qbXML, $compareAsString, "Failed testing $fullClassName");
					}
				}
			}

			// Unset $obj after testing it to clear it out for the next loop
			unset($obj);
		}
	}
}
