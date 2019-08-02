<?php declare(strict_types=1);

/**
 * Schema object for: CurrencyModRq
 *
 * @author "Keith Palmer Jr." <Keith@ConsoliByte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage QBXML
 */

namespace QuickBooksPhpDevKit\QBXML\Schema\Object;

use QuickBooksPhpDevKit\QBXML\Schema\AbstractSchemaObject;

/**
 * WARNING!!!  This file is auto-generated by QBXML\Schema\Generator and the data/qbxmlops130.xml schema
 */
class CurrencyModRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'CurrencyMod';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'ListID' => 'IDTYPE',
			'EditSequence' => 'STRTYPE',
			'Name' => 'STRTYPE',
			'IsActive' => 'BOOLTYPE',
			'CurrencyCode' => 'STRTYPE',
			'CurrencyFormat ThousandSeparator' => 'ENUMTYPE',
			'CurrencyFormat ThousandSeparatorGrouping' => 'ENUMTYPE',
			'CurrencyFormat DecimalPlaces' => 'ENUMTYPE',
			'CurrencyFormat DecimalSeparator' => 'ENUMTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'ListID' => 0,
			'EditSequence' => 16,
			'Name' => 64,
			'IsActive' => 0,
			'CurrencyCode' => 3,
			'CurrencyFormat ThousandSeparator' => 0,
			'CurrencyFormat ThousandSeparatorGrouping' => 0,
			'CurrencyFormat DecimalPlaces' => 0,
			'CurrencyFormat DecimalSeparator' => 0,
			'IncludeRetElement' => 50,
		];

		return $paths;
	}

	protected function &_isOptionalPaths(): array
	{
		// This seems broken when a parent is optional but an element is required if the parent is included (See HostQueryRq IncludeMaxCapacity).
		static $paths = []; //'_isOptionalPaths ';

		return $paths;
	}

	protected function &_sinceVersionPaths(): array
	{
		static $paths = [
			'ListID' => 999.99,
			'EditSequence' => 999.99,
			'Name' => 999.99,
			'IsActive' => 999.99,
			'CurrencyCode' => 999.99,
			'CurrencyFormat ThousandSeparator' => 999.99,
			'CurrencyFormat ThousandSeparatorGrouping' => 999.99,
			'CurrencyFormat DecimalPlaces' => 999.99,
			'CurrencyFormat DecimalSeparator' => 999.99,
			'IncludeRetElement' => 4.0,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'ListID' => false,
			'EditSequence' => false,
			'Name' => false,
			'IsActive' => false,
			'CurrencyCode' => false,
			'CurrencyFormat ThousandSeparator' => false,
			'CurrencyFormat ThousandSeparatorGrouping' => false,
			'CurrencyFormat DecimalPlaces' => false,
			'CurrencyFormat DecimalSeparator' => false,
			'IncludeRetElement' => true,
		];

		return $paths;
	}

	/*
	protected function &_inLocalePaths(): array
	{
		static $paths = [
			'FirstName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
			'LastName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
		];

		return $paths;
	}
	*/

	protected function &_reorderPathsPaths(): array
	{
		static $paths = [
			'ListID',
			'EditSequence',
			'Name',
			'IsActive',
			'CurrencyCode',
			'CurrencyFormat',
			'CurrencyFormat ThousandSeparator',
			'CurrencyFormat ThousandSeparatorGrouping',
			'CurrencyFormat DecimalPlaces',
			'CurrencyFormat DecimalSeparator',
			'IncludeRetElement',
		];

		return $paths;
	}
}
