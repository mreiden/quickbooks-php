<?php declare(strict_types=1);

/**
 * Casts to cast data types to QuickBooks qbXML values and fit values in qbXML fields
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @package QuickBooks
 * @subpackage Cast
 */

namespace QuickBooksPhpDevKit;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\XML;

/**
 * QuickBooks casts and data types
 */
final class Cast
{
	private const ENCODING = 'UTF-8';

	/**
	 * Convert certain strings to their abbreviations
	 *
	 * QuickBooks often uses unusually short field lengths. This function will
	 * convert common long words to shorter abbreviations in an attempt to make
	 * a string fit cleanly into the very short fields.
	 */
	static protected function _castAbbreviations(string $value): string
	{
		$abbrevs = [
			'Administration' => 'Admin.',
			'Academic' => 'Acad.',
			'Academy' => 'Acad.',
			'Association' => 'Assn.',
			'Boulevard' => 'Blvd.',
			'Building' => 'Bldg.',
			'College' => 'Coll.',
			'Company' => 'Co.',
			'Consolidated' => 'Consol.',
			'Corporation' => 'Corp.',
			'Incorporated' => 'Inc.',
			'Department' => 'Dept.',
			'Division' => 'Div.',
			'District' => 'Dist.',
			'Eastern' => 'E.',
			'Government' => 'Govt.',
			'International' => 'Intl.',
			'Institute' => 'Inst.',
			'Institution' => 'Inst.',
			'Laboratory' => 'Lab.',
			'Liberty' => 'Lib.',
			'Library' => 'Lib.',
			'Limited' => 'Ltd.',
			'Manufacturing' => 'Mfg.',
			'Manufacturer' => 'Mfr.',
			'Miscellaneous' => 'Misc.',
			'Museum' => 'Mus.',
			'Northern' => 'N.',
			'Northeastern' => 'NE', // This is *before* Northeast so we don't get "NEern"
			'Northeast' => 'NE',
			'Regional' => 'Reg.', // This is *before* Region so we don't get "Reg.al"
			'Region' => 'Reg.',
			'School' => 'Sch.',
			'Services' => 'Svcs.', // This is *before* Service so that we don't get "Svc.s"
			'Service' => 'Svc.',
			'Southern' => 'S.',
			'Southeastern' => 'SE',
			'Southeast' => 'SE',
			'University' => 'Univ.',
			'Western' => 'W.',
		];

		return str_ireplace(array_keys($abbrevs), array_values($abbrevs), $value);
	}

	/**
	 * Shorten a string to a specific length by truncating or abbreviating the string
	 *
	 * QuickBooks often uses unusually short field lengths. This function can
	 * be used to try to make long strings fit cleanly into the QuickBooks
	 * fields. It tries to do a few things:
	 * 	- Convert long words to shorter abbreviations
	 * 	- Remove non-ASCII characters
	 * 	- Truncate the string if it's still too long
	 *
	 * @param string $value				The string to shorten
	 * @param integer $length			The max. length the string should be
	 * @param boolean $with_abbrevs		Whether or not to abbreviate some long words to shorten the string
	 * @return string					The shortened string
	 */
	static protected function _castTruncate(string $value, int $length, bool $with_abbrevs = true): string
	{
		if (mb_strlen($value, self::ENCODING) > $length)
		{
			if ($with_abbrevs)
			{
				$value = static::_castAbbreviations($value);
			}

			if (mb_strlen($value, self::ENCODING) > $length)
			{
				$value = mb_substr($value, 0, $length, self::ENCODING);
			}
		}

		// Return the value
		return $value;
	}

	/**
	 * Cast a value to ensure that it will fit in a particular field within QuickBooks
	 *
	 * QuickBooks has some strange length limits on some fields (the max.
	 * length of the CompanyName field for Customers is only 41 characters,
	 * etc.) so this method provides an easy way to cast the data type and data
	 * length of a value to the correct type and length for a specific field.
	 *
	 * @param string $object_type	The QuickBooks object type (Customer, Invoice, etc.)
	 * @param string $field_name	The QuickBooks field name (these correspond to the qbXML field names: Addr1, Name, CompanyName, etc.)
	 * @param mixed $value			The value you want to cast
	 * @param boolean $use_abbrevs	There are a lot of strings which can be abbreviated to shorten lengths, this is whether or not you want to use those abbrevaitions ("University" to "Univ.", "Incorporated" to "Inc.", etc.)
	 * @param boolean $htmlspecialchars
	 * @return string
	 */
	static public function cast(string $type_or_action, string $field, $value, bool $use_abbrevs = true, bool $htmlspecialchars = true): ?string
	{
		static $files = [];

		if ([] === $files)
		{
			$list = scandir(__DIR__ . '/QBXML/Schema/Object', SCANDIR_SORT_ASCENDING);
			foreach ($list as $file)
			{
				if (($file{0} !== '.') && ('.php' === strtolower(substr($file, -4))))
				{
					$classname = substr($file, 0, -4);
					$files[strtolower($classname)] = $classname;
				}
			}
		}

		// Remove the QBXML\Object\ namespace if $type_or_action is a namespaced class name
		if (0 === strpos($type_or_action, PackageInfo::NAMESPACE_QBXML_OBJECT."\\"))
		{
			$type_or_action = substr($type_or_action, strlen(PackageInfo::NAMESPACE_QBXML_OBJECT."\\"));
		}


		$class = null;
		$schema = null;
		if ($type_or_action !== '')
		{
			// Remove the ending "Rq" if it is present.
			// It is added back when searching for the schema class, so this prevents a double "RqRq" ending.
			$type_or_action = Utilities::requestToAction($type_or_action);

			// Special handling of Class object due to "class" being a reserved word in PHP
			if (strtolower($type_or_action) === 'qbclass')
			{
				$type_or_action = 'Class';
			}

			// Replace underscore separator with namespace separator
			$type_or_action = str_replace('_', "\\", $type_or_action);

			// Find the schema name for the request
			$suffix_order = [
				'',       // $type_or_action by itself (Handles Actions like CustomerAddRq, CustomerModRq, and CustomerQueryRq)
				//'Mod',  // "Mod" breaks Objects having line items which work with Add actions and must modify those paths to work for Mod actions
				'Add',    // Try the "Add" suffix (Handles Objects like Customer which finds CustomerAddRq)
				'Query',  // Try with the Query suffix (Handles Objects like BarCodeQuery that have a Query action but not an Add action)
			];
			foreach ($suffix_order as $action) {
				$request_name = $type_or_action . $action . 'Rq';
				$try_action = $files[strtolower($request_name)] ?? $request_name;
				try {
					$class = PackageInfo::NAMESPACE_QBXML_SCHEMA_OBJECT . "\\" . $try_action;
					$schema = new $class();
					break;
				}
				catch (\Throwable $e)
				{
					// Do Nothing
					// Exception is expected for Objects (e.g. Customer instead of CustomerAdd)
					// Exception is expected for Query only actions (BarCodeQuery exists but Add and Mod do not)
				}
			}
			if (null === $schema)
			{
				// Do not throw an exception for these $type_or_action
				$ignored = [
					'credittoapply',
				];
				if (!in_array(strtolower($type_or_action), $ignored))
				{
					// Do not throw an exception for subobjects (indicated by the \ namespace separator)
					if (false === strpos($type_or_action, "\\"))
					{
						throw new \Exception('Could not find QBXML\Schema\Object class for "' . $type_or_action .'"');
					}
				}
			}
		}

		//print("\tCasting $type_or_action using schema: " . get_class($schema) . "\n");
		if ($class && $schema)
		{
			if (!$schema->exists($field) && false !== strpos($field, '_'))
			{
				$field = str_replace('_', ' ', $field);
			}

			if ($schema->exists($field))
			{
				switch ($schema->dataType($field))
				{
					case PackageInfo::DataType['ENUM']:
					case PackageInfo::DataType['ID']:
						// do nothing
						break;

					case PackageInfo::DataType['STRING']:
						$maxlength = $schema->maxLength($field);

						// Use only ASCII characters
						//$value = static::_castCharset($value);

						// Make sure it'll fit in the allocated field length
						if ($maxlength < PHP_INT_MAX)
						{
							$value = static::_castTruncate((string) $value, $maxlength, $use_abbrevs);
						}
						break;

					case PackageInfo::DataType['DATE']:
						if ($value)
						{
							$value = date('Y-m-d', strtotime($value));
						}
						break;

					case PackageInfo::DataType['DATETIME']:
						if ($value)
						{
							$value = date('Y-m-d\TH:i:s', strtotime($value));
						}
						break;

					case PackageInfo::DataType['FLOAT']:
						$value = (float) $value;
						break;

					case PackageInfo::DataType['BOOLEAN']:
						$value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
						break;

					case PackageInfo::DataType['INTEGER']:
						$value = (int) $value;
						break;
				}
			}
		}


		if (is_bool($value))
		{
			// No sense in replacing UTF8 characters for boolean values

			return $value ? 'true' : 'false';
		}
		else if (is_int($value) || is_float($value))
		{
			// No sense in replacing UTF8 characters for int and float values

			return (string) $value;
		}
		else if ($htmlspecialchars)
		{
			// Replace multi-byte characters with numeric entities so they work with Web Connector

			//print('DECODING: Field=' . $field .', value=' . var_export($value,true) ."\n");
			$value = XML::encode($value, true, false);
		}

		return $value;
	}
}
