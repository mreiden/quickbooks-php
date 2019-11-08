<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit_UnitTesting;

use PHPUnit\Framework\TestCase;

use \DomDocument;
use QuickBooksPhpDevKit\PackageInfo;


abstract class XmlBaseTest extends TestCase
{
	protected $namespace = PackageInfo::NAMESPACE_QBXML_OBJECT . "\\";
	protected $namespace_schema = PackageInfo::NAMESPACE_QBXML_SCHEMA_OBJECT . "\\";
	protected $namespace_ipp = PackageInfo::NAMESPACE_IPP_OBJECT . "\\";

	protected function commonTests(string $expected, string $qbXML, bool $compare_as_string = false, ?string $failure_message = null): void
	{
		// Print out what was received
		//fwrite(STDOUT, "RECEIVED:\n$qbXML\n");

		// Convert qbXML into DomDocument
		$qbXML = $this->sanitizeXml($qbXML);

		if ($compare_as_string !== true)
		{
			$expected = $this->sanitizeXml($expected);

			// Make sure neither the expected nor generated qbXML is null (DomDocument->loadXML failed)
			$this->assertNotNull($expected, 'DomDocument failed to load the expected qbXML.');

			// Make sure neither the expected nor generated qbXML is blank (empty string)
			$this->assertNotEmpty($expected, 'Expected qbXML is blank.');
		}
		//fwrite(STDOUT, "Santized:\n$qbXML\n");

		// Make sure neither the expected nor generated qbXML is null (DomDocument->loadXML failed)
		//$this->assertNotNull($expected, 'DomDocument failed to load the expected qbXML.');
		$this->assertNotNull($qbXML, 'DomDocument failed to load the generated qbXML.');

		// Make sure neither the expected nor generated qbXML is blank (empty string)
		//$this->assertNotEmpty($this->domToString($expected), 'Expected qbXML is blank.');
		//$this->assertNotEmpty($this->domToString($qbXML), 'Generated qbXML is blank.');
		$this->assertNotEmpty($expected, 'Expected qbXML is blank.');
		$this->assertNotEmpty($qbXML, 'Generated qbXML is blank.');

		//fwrite(STDOUT, "Expected\n". $expected->saveXML() . "\n");
		//fwrite(STDOUT, "Actual\n". $qbXML->saveXML() . "\n");

		if ($compare_as_string !== true)
		{
			// Make sure the dom documents are equal
			$this->assertEquals($expected, $qbXML, $failure_message ?? '');
		}
		else
		{
			// Compare as strings for testing utf-8 character encodings translated into numeric character entities like QuickBooks demands.
			// PHPUnit's DOM comparison seems to mess this up)
			//
			// To get numeric entity references in decimal format we use the us-ascii encoding
			// From http://www.xmlsoft.org/encoding.html:
			//   A special "ascii" encoding name is used to save documents to a pure ascii form can be used when portability is really crucial
			$qbXML->encoding = 'us-ascii';

			$this->assertEquals($expected, $qbXML->saveXML(), $failure_message ?? '');
		}

	}

	protected function sanitizeXml(string $xml): ?DomDocument
	{
		$load_options = LIBXML_COMPACT | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOXMLDECL;

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = false;

		$loaded = $dom->loadXML(trim($xml), $load_options);
		if (false === $loaded) {
			return null;
		}

		$dom->formatOutput = true;
		$dom->xmlVersion = '1.0';
		$dom->encoding = 'utf-8';

		return $dom;
	}


	protected function classFinder(string $directory_path, string $class_prefix = ''): array
	{
		$classes = [];
		$class_prefix = empty($class_prefix) ? '' : $class_prefix . "\\";

		$files = scandir($directory_path, SCANDIR_SORT_ASCENDING);
		foreach ($files as $file)
		{
			// Skip current directory (.), parent directory (..), and anything else starting with a dot
			if (substr($file, 0, 1) !== '.')
			{
				if (is_dir("$directory_path/$file"))
				{
					$subdir_classes = $this->classFinder("$directory_path/$file", $class_prefix . $file);
					if (count($subdir_classes))
					{
						array_push($classes, ...$subdir_classes);
					}
				}
				else if ('.php' === strtolower(substr($file, -4)))
				{
					$classes[] = $class_prefix . basename($file, '.php');
				}
			}
		}

		return $classes;
	}
}
