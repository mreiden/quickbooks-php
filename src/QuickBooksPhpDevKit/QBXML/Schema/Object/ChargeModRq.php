<?php declare(strict_types=1);

/**
 * Schema object for: ChargeModRq
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
class ChargeModRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'ChargeMod';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'TxnID' => 'IDTYPE',
			'EditSequence' => 'STRTYPE',
			'CustomerRef ListID' => 'IDTYPE',
			'CustomerRef FullName' => 'STRTYPE',
			'TxnDate' => 'DATETYPE',
			'RefNumber' => 'STRTYPE',
			'ItemRef ListID' => 'IDTYPE',
			'ItemRef FullName' => 'STRTYPE',
			'InventorySiteRef ListID' => 'IDTYPE',
			'InventorySiteRef FullName' => 'STRTYPE',
			'InventorySiteLocationRef ListID' => 'IDTYPE',
			'InventorySiteLocationRef FullName' => 'STRTYPE',
			'Quantity' => 'QUANTYPE',
			'UnitOfMeasure' => 'STRTYPE',
			'OverrideUOMSetRef ListID' => 'IDTYPE',
			'OverrideUOMSetRef FullName' => 'STRTYPE',
			'Rate' => 'PRICETYPE',
			'OptionForPriceRuleConflict' => 'ENUMTYPE',
			'Amount' => 'AMTTYPE',
			'Desc' => 'STRTYPE',
			'ARAccountRef ListID' => 'IDTYPE',
			'ARAccountRef FullName' => 'STRTYPE',
			'ClassRef ListID' => 'IDTYPE',
			'ClassRef FullName' => 'STRTYPE',
			'BilledDate' => 'DATETYPE',
			'DueDate' => 'DATETYPE',
			'OverrideItemAccountRef ListID' => 'IDTYPE',
			'OverrideItemAccountRef FullName' => 'STRTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'TxnID' => 0,
			'EditSequence' => 16,
			'CustomerRef ListID' => 0,
			'CustomerRef FullName' => 209,
			'TxnDate' => 0,
			'RefNumber' => 11,
			'ItemRef ListID' => 0,
			'ItemRef FullName' => 209,
			'InventorySiteRef ListID' => 0,
			'InventorySiteRef FullName' => 209,
			'InventorySiteLocationRef ListID' => 0,
			'InventorySiteLocationRef FullName' => 209,
			'Quantity' => 0,
			'UnitOfMeasure' => 31,
			'OverrideUOMSetRef ListID' => 0,
			'OverrideUOMSetRef FullName' => 209,
			'Rate' => 0,
			'OptionForPriceRuleConflict' => 0,
			'Amount' => 0,
			'Desc' => 4095,
			'ARAccountRef ListID' => 0,
			'ARAccountRef FullName' => 209,
			'ClassRef ListID' => 0,
			'ClassRef FullName' => 209,
			'BilledDate' => 0,
			'DueDate' => 0,
			'OverrideItemAccountRef ListID' => 0,
			'OverrideItemAccountRef FullName' => 209,
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
			'TxnID' => 999.99,
			'EditSequence' => 999.99,
			'CustomerRef ListID' => 999.99,
			'CustomerRef FullName' => 999.99,
			'TxnDate' => 999.99,
			'RefNumber' => 999.99,
			'ItemRef ListID' => 999.99,
			'ItemRef FullName' => 999.99,
			'InventorySiteRef ListID' => 999.99,
			'InventorySiteRef FullName' => 999.99,
			'InventorySiteLocationRef ListID' => 999.99,
			'InventorySiteLocationRef FullName' => 999.99,
			'Quantity' => 999.99,
			'UnitOfMeasure' => 7.0,
			'OverrideUOMSetRef ListID' => 999.99,
			'OverrideUOMSetRef FullName' => 999.99,
			'Rate' => 999.99,
			'OptionForPriceRuleConflict' => 13.0,
			'Amount' => 999.99,
			'Desc' => 999.99,
			'ARAccountRef ListID' => 999.99,
			'ARAccountRef FullName' => 999.99,
			'ClassRef ListID' => 999.99,
			'ClassRef FullName' => 999.99,
			'BilledDate' => 999.99,
			'DueDate' => 999.99,
			'OverrideItemAccountRef ListID' => 999.99,
			'OverrideItemAccountRef FullName' => 999.99,
			'IncludeRetElement' => 4.0,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'TxnID' => false,
			'EditSequence' => false,
			'CustomerRef ListID' => false,
			'CustomerRef FullName' => false,
			'TxnDate' => false,
			'RefNumber' => false,
			'ItemRef ListID' => false,
			'ItemRef FullName' => false,
			'InventorySiteRef ListID' => false,
			'InventorySiteRef FullName' => false,
			'InventorySiteLocationRef ListID' => false,
			'InventorySiteLocationRef FullName' => false,
			'Quantity' => false,
			'UnitOfMeasure' => false,
			'OverrideUOMSetRef ListID' => false,
			'OverrideUOMSetRef FullName' => false,
			'Rate' => false,
			'OptionForPriceRuleConflict' => false,
			'Amount' => false,
			'Desc' => false,
			'ARAccountRef ListID' => false,
			'ARAccountRef FullName' => false,
			'ClassRef ListID' => false,
			'ClassRef FullName' => false,
			'BilledDate' => false,
			'DueDate' => false,
			'OverrideItemAccountRef ListID' => false,
			'OverrideItemAccountRef FullName' => false,
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
			'TxnID',
			'EditSequence',
			'CustomerRef',
			'CustomerRef ListID',
			'CustomerRef FullName',
			'TxnDate',
			'RefNumber',
			'ItemRef',
			'ItemRef ListID',
			'ItemRef FullName',
			'InventorySiteRef',
			'InventorySiteRef ListID',
			'InventorySiteRef FullName',
			'InventorySiteLocationRef',
			'InventorySiteLocationRef ListID',
			'InventorySiteLocationRef FullName',
			'Quantity',
			'UnitOfMeasure',
			'OverrideUOMSetRef',
			'OverrideUOMSetRef ListID',
			'OverrideUOMSetRef FullName',
			'Rate',
			'OptionForPriceRuleConflict',
			'Amount',
			'Desc',
			'ARAccountRef',
			'ARAccountRef ListID',
			'ARAccountRef FullName',
			'ClassRef',
			'ClassRef ListID',
			'ClassRef FullName',
			'BilledDate',
			'DueDate',
			'OverrideItemAccountRef',
			'OverrideItemAccountRef ListID',
			'OverrideItemAccountRef FullName',
			'IncludeRetElement',
		];

		return $paths;
	}
}