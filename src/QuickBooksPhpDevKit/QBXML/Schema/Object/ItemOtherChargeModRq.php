<?php declare(strict_types=1);

/**
 * Schema object for: ItemOtherChargeModRq
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
class ItemOtherChargeModRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'ItemOtherChargeMod';

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
			'IsTaxIncluded' => 'BOOLTYPE',
			'SalesTaxCodeRef ListID' => 'IDTYPE',
			'SalesTaxCodeRef FullName' => 'STRTYPE',
			'SalesOrPurchaseMod Desc' => 'STRTYPE',
			'SalesOrPurchaseMod Price' => 'PRICETYPE',
			'SalesOrPurchaseMod PricePercent' => 'PERCENTTYPE',
			'SalesOrPurchaseMod AccountRef ListID' => 'IDTYPE',
			'SalesOrPurchaseMod AccountRef FullName' => 'STRTYPE',
			'SalesOrPurchaseMod ApplyAccountRefToExistingTxns' => 'BOOLTYPE',
			'SalesAndPurchaseMod SalesDesc' => 'STRTYPE',
			'SalesAndPurchaseMod SalesPrice' => 'PRICETYPE',
			'SalesAndPurchaseMod IncomeAccountRef ListID' => 'IDTYPE',
			'SalesAndPurchaseMod IncomeAccountRef FullName' => 'STRTYPE',
			'SalesAndPurchaseMod ApplyIncomeAccountRefToExistingTxns' => 'BOOLTYPE',
			'SalesAndPurchaseMod PurchaseDesc' => 'STRTYPE',
			'SalesAndPurchaseMod PurchaseCost' => 'PRICETYPE',
			'SalesAndPurchaseMod PurchaseTaxCodeRef ListID' => 'IDTYPE',
			'SalesAndPurchaseMod PurchaseTaxCodeRef FullName' => 'STRTYPE',
			'SalesAndPurchaseMod ExpenseAccountRef ListID' => 'IDTYPE',
			'SalesAndPurchaseMod ExpenseAccountRef FullName' => 'STRTYPE',
			'SalesAndPurchaseMod ApplyExpenseAccountRefToExistingTxns' => 'BOOLTYPE',
			'SalesAndPurchaseMod PrefVendorRef ListID' => 'IDTYPE',
			'SalesAndPurchaseMod PrefVendorRef FullName' => 'STRTYPE',
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
			'IsTaxIncluded' => 0,
			'SalesTaxCodeRef ListID' => 0,
			'SalesTaxCodeRef FullName' => 159,
			'SalesOrPurchaseMod Desc' => 4095,
			'SalesOrPurchaseMod Price' => 0,
			'SalesOrPurchaseMod PricePercent' => 0,
			'SalesOrPurchaseMod AccountRef ListID' => 0,
			'SalesOrPurchaseMod AccountRef FullName' => 159,
			'SalesOrPurchaseMod ApplyAccountRefToExistingTxns' => 0,
			'SalesAndPurchaseMod SalesDesc' => 4095,
			'SalesAndPurchaseMod SalesPrice' => 0,
			'SalesAndPurchaseMod IncomeAccountRef ListID' => 0,
			'SalesAndPurchaseMod IncomeAccountRef FullName' => 159,
			'SalesAndPurchaseMod ApplyIncomeAccountRefToExistingTxns' => 0,
			'SalesAndPurchaseMod PurchaseDesc' => 4095,
			'SalesAndPurchaseMod PurchaseCost' => 0,
			'SalesAndPurchaseMod PurchaseTaxCodeRef ListID' => 0,
			'SalesAndPurchaseMod PurchaseTaxCodeRef FullName' => 159,
			'SalesAndPurchaseMod ExpenseAccountRef ListID' => 0,
			'SalesAndPurchaseMod ExpenseAccountRef FullName' => 159,
			'SalesAndPurchaseMod ApplyExpenseAccountRefToExistingTxns' => 0,
			'SalesAndPurchaseMod PrefVendorRef ListID' => 0,
			'SalesAndPurchaseMod PrefVendorRef FullName' => 159,
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
			'IsTaxIncluded' => 6.0,
			'SalesTaxCodeRef ListID' => 999.99,
			'SalesTaxCodeRef FullName' => 999.99,
			'SalesOrPurchaseMod Desc' => 999.99,
			'SalesOrPurchaseMod Price' => 999.99,
			'SalesOrPurchaseMod PricePercent' => 999.99,
			'SalesOrPurchaseMod AccountRef ListID' => 999.99,
			'SalesOrPurchaseMod AccountRef FullName' => 999.99,
			'SalesOrPurchaseMod ApplyAccountRefToExistingTxns' => 7.0,
			'SalesAndPurchaseMod SalesDesc' => 999.99,
			'SalesAndPurchaseMod SalesPrice' => 999.99,
			'SalesAndPurchaseMod IncomeAccountRef ListID' => 999.99,
			'SalesAndPurchaseMod IncomeAccountRef FullName' => 999.99,
			'SalesAndPurchaseMod ApplyIncomeAccountRefToExistingTxns' => 7.0,
			'SalesAndPurchaseMod PurchaseDesc' => 999.99,
			'SalesAndPurchaseMod PurchaseCost' => 999.99,
			'SalesAndPurchaseMod PurchaseTaxCodeRef ListID' => 999.99,
			'SalesAndPurchaseMod PurchaseTaxCodeRef FullName' => 999.99,
			'SalesAndPurchaseMod ExpenseAccountRef ListID' => 999.99,
			'SalesAndPurchaseMod ExpenseAccountRef FullName' => 999.99,
			'SalesAndPurchaseMod ApplyExpenseAccountRefToExistingTxns' => 7.0,
			'SalesAndPurchaseMod PrefVendorRef ListID' => 999.99,
			'SalesAndPurchaseMod PrefVendorRef FullName' => 999.99,
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
			'IsTaxIncluded' => false,
			'SalesTaxCodeRef ListID' => false,
			'SalesTaxCodeRef FullName' => false,
			'SalesOrPurchaseMod Desc' => false,
			'SalesOrPurchaseMod Price' => false,
			'SalesOrPurchaseMod PricePercent' => false,
			'SalesOrPurchaseMod AccountRef ListID' => false,
			'SalesOrPurchaseMod AccountRef FullName' => false,
			'SalesOrPurchaseMod ApplyAccountRefToExistingTxns' => false,
			'SalesAndPurchaseMod SalesDesc' => false,
			'SalesAndPurchaseMod SalesPrice' => false,
			'SalesAndPurchaseMod IncomeAccountRef ListID' => false,
			'SalesAndPurchaseMod IncomeAccountRef FullName' => false,
			'SalesAndPurchaseMod ApplyIncomeAccountRefToExistingTxns' => false,
			'SalesAndPurchaseMod PurchaseDesc' => false,
			'SalesAndPurchaseMod PurchaseCost' => false,
			'SalesAndPurchaseMod PurchaseTaxCodeRef ListID' => false,
			'SalesAndPurchaseMod PurchaseTaxCodeRef FullName' => false,
			'SalesAndPurchaseMod ExpenseAccountRef ListID' => false,
			'SalesAndPurchaseMod ExpenseAccountRef FullName' => false,
			'SalesAndPurchaseMod ApplyExpenseAccountRefToExistingTxns' => false,
			'SalesAndPurchaseMod PrefVendorRef ListID' => false,
			'SalesAndPurchaseMod PrefVendorRef FullName' => false,
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
			'IsTaxIncluded',
			'SalesTaxCodeRef',
			'SalesTaxCodeRef ListID',
			'SalesTaxCodeRef FullName',
			'SalesOrPurchaseMod',
			'SalesOrPurchaseMod Desc',
			'SalesOrPurchaseMod Price',
			'SalesOrPurchaseMod PricePercent',
			'SalesOrPurchaseMod AccountRef ListID',
			'SalesOrPurchaseMod AccountRef FullName',
			'SalesOrPurchaseMod ApplyAccountRefToExistingTxns',
			'SalesAndPurchaseMod',
			'SalesAndPurchaseMod SalesDesc',
			'SalesAndPurchaseMod SalesPrice',
			'SalesAndPurchaseMod IncomeAccountRef ListID',
			'SalesAndPurchaseMod IncomeAccountRef FullName',
			'SalesAndPurchaseMod ApplyIncomeAccountRefToExistingTxns',
			'SalesAndPurchaseMod PurchaseDesc',
			'SalesAndPurchaseMod PurchaseCost',
			'SalesAndPurchaseMod PurchaseTaxCodeRef ListID',
			'SalesAndPurchaseMod PurchaseTaxCodeRef FullName',
			'SalesAndPurchaseMod ExpenseAccountRef ListID',
			'SalesAndPurchaseMod ExpenseAccountRef FullName',
			'SalesAndPurchaseMod ApplyExpenseAccountRefToExistingTxns',
			'SalesAndPurchaseMod PrefVendorRef ListID',
			'SalesAndPurchaseMod PrefVendorRef FullName',
			'IncludeRetElement',
		];

		return $paths;
	}
}
