<?php declare(strict_types=1);

/**
 * Schema object for: ItemInventoryModRq
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
class ItemInventoryModRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'ItemInventoryMod';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'ListID' => 'IDTYPE',
			'EditSequence' => 'STRTYPE',
			'Name' => 'STRTYPE',
			'BarCode BarCodeValue' => 'STRTYPE',
			'BarCode AssignEvenIfUsed' => 'BOOLTYPE',
			'BarCode AllowOverride' => 'BOOLTYPE',
			'IsActive' => 'BOOLTYPE',
			'ClassRef ListID' => 'IDTYPE',
			'ClassRef FullName' => 'STRTYPE',
			'ParentRef ListID' => 'IDTYPE',
			'ParentRef FullName' => 'STRTYPE',
			'ManufacturerPartNumber' => 'STRTYPE',
			'UnitOfMeasureSetRef ListID' => 'IDTYPE',
			'UnitOfMeasureSetRef FullName' => 'STRTYPE',
			'ForceUOMChange' => 'BOOLTYPE',
			'IsTaxIncluded' => 'BOOLTYPE',
			'SalesTaxCodeRef ListID' => 'IDTYPE',
			'SalesTaxCodeRef FullName' => 'STRTYPE',
			'SalesDesc' => 'STRTYPE',
			'SalesPrice' => 'PRICETYPE',
			'IncomeAccountRef ListID' => 'IDTYPE',
			'IncomeAccountRef FullName' => 'STRTYPE',
			'ApplyIncomeAccountRefToExistingTxns' => 'BOOLTYPE',
			'PurchaseDesc' => 'STRTYPE',
			'PurchaseCost' => 'PRICETYPE',
			'PurchaseTaxCodeRef ListID' => 'IDTYPE',
			'PurchaseTaxCodeRef FullName' => 'STRTYPE',
			'COGSAccountRef ListID' => 'IDTYPE',
			'COGSAccountRef FullName' => 'STRTYPE',
			'ApplyCOGSAccountRefToExistingTxns' => 'BOOLTYPE',
			'PrefVendorRef ListID' => 'IDTYPE',
			'PrefVendorRef FullName' => 'STRTYPE',
			'AssetAccountRef ListID' => 'IDTYPE',
			'AssetAccountRef FullName' => 'STRTYPE',
			'ReorderPoint' => 'QUANTYPE',
			'Max' => 'QUANTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'ListID' => 0,
			'EditSequence' => 16,
			'Name' => 31,
			'BarCode BarCodeValue' => 50,
			'BarCode AssignEvenIfUsed' => 0,
			'BarCode AllowOverride' => 0,
			'IsActive' => 0,
			'ClassRef ListID' => 0,
			'ClassRef FullName' => 159,
			'ParentRef ListID' => 0,
			'ParentRef FullName' => 159,
			'ManufacturerPartNumber' => 31,
			'UnitOfMeasureSetRef ListID' => 0,
			'UnitOfMeasureSetRef FullName' => 159,
			'ForceUOMChange' => 0,
			'IsTaxIncluded' => 0,
			'SalesTaxCodeRef ListID' => 0,
			'SalesTaxCodeRef FullName' => 159,
			'SalesDesc' => 4095,
			'SalesPrice' => 0,
			'IncomeAccountRef ListID' => 0,
			'IncomeAccountRef FullName' => 159,
			'ApplyIncomeAccountRefToExistingTxns' => 0,
			'PurchaseDesc' => 4095,
			'PurchaseCost' => 0,
			'PurchaseTaxCodeRef ListID' => 0,
			'PurchaseTaxCodeRef FullName' => 159,
			'COGSAccountRef ListID' => 0,
			'COGSAccountRef FullName' => 159,
			'ApplyCOGSAccountRefToExistingTxns' => 0,
			'PrefVendorRef ListID' => 0,
			'PrefVendorRef FullName' => 159,
			'AssetAccountRef ListID' => 0,
			'AssetAccountRef FullName' => 159,
			'ReorderPoint' => 0,
			'Max' => 0,
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
			'BarCode BarCodeValue' => 999.99,
			'BarCode AssignEvenIfUsed' => 999.99,
			'BarCode AllowOverride' => 999.99,
			'IsActive' => 999.99,
			'ClassRef ListID' => 999.99,
			'ClassRef FullName' => 999.99,
			'ParentRef ListID' => 999.99,
			'ParentRef FullName' => 999.99,
			'ManufacturerPartNumber' => 7.0,
			'UnitOfMeasureSetRef ListID' => 999.99,
			'UnitOfMeasureSetRef FullName' => 999.99,
			'ForceUOMChange' => 7.0,
			'IsTaxIncluded' => 6.0,
			'SalesTaxCodeRef ListID' => 999.99,
			'SalesTaxCodeRef FullName' => 999.99,
			'SalesDesc' => 999.99,
			'SalesPrice' => 999.99,
			'IncomeAccountRef ListID' => 999.99,
			'IncomeAccountRef FullName' => 999.99,
			'ApplyIncomeAccountRefToExistingTxns' => 7.0,
			'PurchaseDesc' => 999.99,
			'PurchaseCost' => 999.99,
			'PurchaseTaxCodeRef ListID' => 999.99,
			'PurchaseTaxCodeRef FullName' => 999.99,
			'COGSAccountRef ListID' => 999.99,
			'COGSAccountRef FullName' => 999.99,
			'ApplyCOGSAccountRefToExistingTxns' => 8.0,
			'PrefVendorRef ListID' => 999.99,
			'PrefVendorRef FullName' => 999.99,
			'AssetAccountRef ListID' => 999.99,
			'AssetAccountRef FullName' => 999.99,
			'ReorderPoint' => 999.99,
			'Max' => 13.0,
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
			'BarCode BarCodeValue' => false,
			'BarCode AssignEvenIfUsed' => false,
			'BarCode AllowOverride' => false,
			'IsActive' => false,
			'ClassRef ListID' => false,
			'ClassRef FullName' => false,
			'ParentRef ListID' => false,
			'ParentRef FullName' => false,
			'ManufacturerPartNumber' => false,
			'UnitOfMeasureSetRef ListID' => false,
			'UnitOfMeasureSetRef FullName' => false,
			'ForceUOMChange' => false,
			'IsTaxIncluded' => false,
			'SalesTaxCodeRef ListID' => false,
			'SalesTaxCodeRef FullName' => false,
			'SalesDesc' => false,
			'SalesPrice' => false,
			'IncomeAccountRef ListID' => false,
			'IncomeAccountRef FullName' => false,
			'ApplyIncomeAccountRefToExistingTxns' => false,
			'PurchaseDesc' => false,
			'PurchaseCost' => false,
			'PurchaseTaxCodeRef ListID' => false,
			'PurchaseTaxCodeRef FullName' => false,
			'COGSAccountRef ListID' => false,
			'COGSAccountRef FullName' => false,
			'ApplyCOGSAccountRefToExistingTxns' => false,
			'PrefVendorRef ListID' => false,
			'PrefVendorRef FullName' => false,
			'AssetAccountRef ListID' => false,
			'AssetAccountRef FullName' => false,
			'ReorderPoint' => false,
			'Max' => false,
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
			'BarCode',
			'BarCode BarCodeValue',
			'BarCode AssignEvenIfUsed',
			'BarCode AllowOverride',
			'IsActive',
			'ClassRef',
			'ClassRef ListID',
			'ClassRef FullName',
			'ParentRef',
			'ParentRef ListID',
			'ParentRef FullName',
			'ManufacturerPartNumber',
			'UnitOfMeasureSetRef',
			'UnitOfMeasureSetRef ListID',
			'UnitOfMeasureSetRef FullName',
			'ForceUOMChange',
			'IsTaxIncluded',
			'SalesTaxCodeRef',
			'SalesTaxCodeRef ListID',
			'SalesTaxCodeRef FullName',
			'SalesDesc',
			'SalesPrice',
			'IncomeAccountRef',
			'IncomeAccountRef ListID',
			'IncomeAccountRef FullName',
			'ApplyIncomeAccountRefToExistingTxns',
			'PurchaseDesc',
			'PurchaseCost',
			'PurchaseTaxCodeRef',
			'PurchaseTaxCodeRef ListID',
			'PurchaseTaxCodeRef FullName',
			'COGSAccountRef',
			'COGSAccountRef ListID',
			'COGSAccountRef FullName',
			'ApplyCOGSAccountRefToExistingTxns',
			'PrefVendorRef',
			'PrefVendorRef ListID',
			'PrefVendorRef FullName',
			'AssetAccountRef',
			'AssetAccountRef ListID',
			'AssetAccountRef FullName',
			'ReorderPoint',
			'Max',
			'IncludeRetElement',
		];

		return $paths;
	}
}
