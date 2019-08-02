<?php declare(strict_types=1);

/**
 * Schema object for: ItemSubtotalAddRq
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
class ItemSubtotalAddRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'ItemSubtotalAdd';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'Name' => 'STRTYPE',
			'BarCode BarCodeValue' => 'STRTYPE',
			'BarCode AssignEvenIfUsed' => 'BOOLTYPE',
			'BarCode AllowOverride' => 'BOOLTYPE',
			'IsActive' => 'BOOLTYPE',
			'ItemDesc' => 'STRTYPE',
			'ExternalGUID' => 'GUIDTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'Name' => 31,
			'BarCode BarCodeValue' => 50,
			'BarCode AssignEvenIfUsed' => 0,
			'BarCode AllowOverride' => 0,
			'IsActive' => 0,
			'ItemDesc' => 4095,
			'ExternalGUID' => 0,
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
			'Name' => 999.99,
			'BarCode BarCodeValue' => 999.99,
			'BarCode AssignEvenIfUsed' => 999.99,
			'BarCode AllowOverride' => 999.99,
			'IsActive' => 999.99,
			'ItemDesc' => 999.99,
			'ExternalGUID' => 8.0,
			'IncludeRetElement' => 4.0,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'Name' => false,
			'BarCode BarCodeValue' => false,
			'BarCode AssignEvenIfUsed' => false,
			'BarCode AllowOverride' => false,
			'IsActive' => false,
			'ItemDesc' => false,
			'ExternalGUID' => false,
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
			'Name',
			'BarCode',
			'BarCode BarCodeValue',
			'BarCode AssignEvenIfUsed',
			'BarCode AllowOverride',
			'IsActive',
			'ItemDesc',
			'ExternalGUID',
			'IncludeRetElement',
		];

		return $paths;
	}
}
