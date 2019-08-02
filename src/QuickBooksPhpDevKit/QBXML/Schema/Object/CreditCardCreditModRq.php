<?php declare(strict_types=1);

/**
 * Schema object for: CreditCardCreditModRq
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
class CreditCardCreditModRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'CreditCardCreditMod';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'TxnID' => 'IDTYPE',
			'EditSequence' => 'STRTYPE',
			'AccountRef ListID' => 'IDTYPE',
			'AccountRef FullName' => 'STRTYPE',
			'PayeeEntityRef ListID' => 'IDTYPE',
			'PayeeEntityRef FullName' => 'STRTYPE',
			'TxnDate' => 'DATETYPE',
			'RefNumber' => 'STRTYPE',
			'Memo' => 'STRTYPE',
			'IsTaxIncluded' => 'BOOLTYPE',
			'SalesTaxCodeRef ListID' => 'IDTYPE',
			'SalesTaxCodeRef FullName' => 'STRTYPE',
			'ExchangeRate' => 'FLOATTYPE',
			'ClearExpenseLines' => 'BOOLTYPE',
			'ExpenseLineMod TxnLineID' => 'IDTYPE',
			'ExpenseLineMod AccountRef ListID' => 'IDTYPE',
			'ExpenseLineMod AccountRef FullName' => 'STRTYPE',
			'ExpenseLineMod Amount' => 'AMTTYPE',
			'ExpenseLineMod TaxAmount' => 'AMTTYPE',
			'ExpenseLineMod Memo' => 'STRTYPE',
			'ExpenseLineMod CustomerRef ListID' => 'IDTYPE',
			'ExpenseLineMod CustomerRef FullName' => 'STRTYPE',
			'ExpenseLineMod ClassRef ListID' => 'IDTYPE',
			'ExpenseLineMod ClassRef FullName' => 'STRTYPE',
			'ExpenseLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
			'ExpenseLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
			'ExpenseLineMod BillableStatus' => 'ENUMTYPE',
			'ExpenseLineMod SalesRepRef ListID' => 'IDTYPE',
			'ExpenseLineMod SalesRepRef FullName' => 'STRTYPE',
			'ClearItemLines' => 'BOOLTYPE',
			'ItemLineMod TxnLineID' => 'IDTYPE',
			'ItemLineMod ItemRef ListID' => 'IDTYPE',
			'ItemLineMod ItemRef FullName' => 'STRTYPE',
			'ItemLineMod InventorySiteRef ListID' => 'IDTYPE',
			'ItemLineMod InventorySiteRef FullName' => 'STRTYPE',
			'ItemLineMod InventorySiteLocationRef ListID' => 'IDTYPE',
			'ItemLineMod InventorySiteLocationRef FullName' => 'STRTYPE',
			'ItemLineMod SerialNumber' => 'STRTYPE',
			'ItemLineMod LotNumber' => 'STRTYPE',
			'ItemLineMod Desc' => 'STRTYPE',
			'ItemLineMod Quantity' => 'QUANTYPE',
			'ItemLineMod UnitOfMeasure' => 'STRTYPE',
			'ItemLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
			'ItemLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
			'ItemLineMod Cost' => 'PRICETYPE',
			'ItemLineMod Amount' => 'AMTTYPE',
			'ItemLineMod TaxAmount' => 'AMTTYPE',
			'ItemLineMod CustomerRef ListID' => 'IDTYPE',
			'ItemLineMod CustomerRef FullName' => 'STRTYPE',
			'ItemLineMod ClassRef ListID' => 'IDTYPE',
			'ItemLineMod ClassRef FullName' => 'STRTYPE',
			'ItemLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
			'ItemLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
			'ItemLineMod BillableStatus' => 'ENUMTYPE',
			'ItemLineMod OverrideItemAccountRef ListID' => 'IDTYPE',
			'ItemLineMod OverrideItemAccountRef FullName' => 'STRTYPE',
			'ItemLineMod SalesRepRef ListID' => 'IDTYPE',
			'ItemLineMod SalesRepRef FullName' => 'STRTYPE',
			'ItemGroupLineMod TxnLineID' => 'IDTYPE',
			'ItemGroupLineMod ItemGroupRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemGroupRef FullName' => 'STRTYPE',
			'ItemGroupLineMod Quantity' => 'QUANTYPE',
			'ItemGroupLineMod UnitOfMeasure' => 'STRTYPE',
			'ItemGroupLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
			'ItemGroupLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod TxnLineID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod ItemRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod ItemRef FullName' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod InventorySiteRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod InventorySiteRef FullName' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef FullName' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod SerialNumber' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod LotNumber' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod Desc' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod Quantity' => 'QUANTYPE',
			'ItemGroupLineMod ItemLineMod UnitOfMeasure' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod Cost' => 'PRICETYPE',
			'ItemGroupLineMod ItemLineMod Amount' => 'AMTTYPE',
			'ItemGroupLineMod ItemLineMod TaxAmount' => 'AMTTYPE',
			'ItemGroupLineMod ItemLineMod CustomerRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod CustomerRef FullName' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod ClassRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod ClassRef FullName' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod BillableStatus' => 'ENUMTYPE',
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 'STRTYPE',
			'ItemGroupLineMod ItemLineMod SalesRepRef ListID' => 'IDTYPE',
			'ItemGroupLineMod ItemLineMod SalesRepRef FullName' => 'STRTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'TxnID' => 0,
			'EditSequence' => 16,
			'AccountRef ListID' => 0,
			'AccountRef FullName' => 159,
			'PayeeEntityRef ListID' => 0,
			'PayeeEntityRef FullName' => 159,
			'TxnDate' => 0,
			'RefNumber' => 11,
			'Memo' => 4095,
			'IsTaxIncluded' => 0,
			'SalesTaxCodeRef ListID' => 0,
			'SalesTaxCodeRef FullName' => 159,
			'ExchangeRate' => 0,
			'ClearExpenseLines' => 0,
			'ExpenseLineMod TxnLineID' => 0,
			'ExpenseLineMod AccountRef ListID' => 0,
			'ExpenseLineMod AccountRef FullName' => 159,
			'ExpenseLineMod Amount' => 0,
			'ExpenseLineMod TaxAmount' => 0,
			'ExpenseLineMod Memo' => 4095,
			'ExpenseLineMod CustomerRef ListID' => 0,
			'ExpenseLineMod CustomerRef FullName' => 159,
			'ExpenseLineMod ClassRef ListID' => 0,
			'ExpenseLineMod ClassRef FullName' => 159,
			'ExpenseLineMod SalesTaxCodeRef ListID' => 0,
			'ExpenseLineMod SalesTaxCodeRef FullName' => 159,
			'ExpenseLineMod BillableStatus' => 0,
			'ExpenseLineMod SalesRepRef ListID' => 0,
			'ExpenseLineMod SalesRepRef FullName' => 159,
			'ClearItemLines' => 0,
			'ItemLineMod TxnLineID' => 0,
			'ItemLineMod ItemRef ListID' => 0,
			'ItemLineMod ItemRef FullName' => 159,
			'ItemLineMod InventorySiteRef ListID' => 0,
			'ItemLineMod InventorySiteRef FullName' => 159,
			'ItemLineMod InventorySiteLocationRef ListID' => 0,
			'ItemLineMod InventorySiteLocationRef FullName' => 159,
			'ItemLineMod SerialNumber' => 4095,
			'ItemLineMod LotNumber' => 40,
			'ItemLineMod Desc' => 4095,
			'ItemLineMod Quantity' => 0,
			'ItemLineMod UnitOfMeasure' => 31,
			'ItemLineMod OverrideUOMSetRef ListID' => 0,
			'ItemLineMod OverrideUOMSetRef FullName' => 159,
			'ItemLineMod Cost' => 0,
			'ItemLineMod Amount' => 0,
			'ItemLineMod TaxAmount' => 0,
			'ItemLineMod CustomerRef ListID' => 0,
			'ItemLineMod CustomerRef FullName' => 159,
			'ItemLineMod ClassRef ListID' => 0,
			'ItemLineMod ClassRef FullName' => 159,
			'ItemLineMod SalesTaxCodeRef ListID' => 0,
			'ItemLineMod SalesTaxCodeRef FullName' => 159,
			'ItemLineMod BillableStatus' => 0,
			'ItemLineMod OverrideItemAccountRef ListID' => 0,
			'ItemLineMod OverrideItemAccountRef FullName' => 159,
			'ItemLineMod SalesRepRef ListID' => 0,
			'ItemLineMod SalesRepRef FullName' => 159,
			'ItemGroupLineMod TxnLineID' => 0,
			'ItemGroupLineMod ItemGroupRef ListID' => 0,
			'ItemGroupLineMod ItemGroupRef FullName' => 159,
			'ItemGroupLineMod Quantity' => 0,
			'ItemGroupLineMod UnitOfMeasure' => 31,
			'ItemGroupLineMod OverrideUOMSetRef ListID' => 0,
			'ItemGroupLineMod OverrideUOMSetRef FullName' => 159,
			'ItemGroupLineMod ItemLineMod TxnLineID' => 0,
			'ItemGroupLineMod ItemLineMod ItemRef ListID' => 0,
			'ItemGroupLineMod ItemLineMod ItemRef FullName' => 159,
			'ItemGroupLineMod ItemLineMod InventorySiteRef ListID' => 0,
			'ItemGroupLineMod ItemLineMod InventorySiteRef FullName' => 159,
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef ListID' => 0,
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef FullName' => 159,
			'ItemGroupLineMod ItemLineMod SerialNumber' => 4095,
			'ItemGroupLineMod ItemLineMod LotNumber' => 40,
			'ItemGroupLineMod ItemLineMod Desc' => 4095,
			'ItemGroupLineMod ItemLineMod Quantity' => 0,
			'ItemGroupLineMod ItemLineMod UnitOfMeasure' => 31,
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 0,
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 159,
			'ItemGroupLineMod ItemLineMod Cost' => 0,
			'ItemGroupLineMod ItemLineMod Amount' => 0,
			'ItemGroupLineMod ItemLineMod TaxAmount' => 0,
			'ItemGroupLineMod ItemLineMod CustomerRef ListID' => 0,
			'ItemGroupLineMod ItemLineMod CustomerRef FullName' => 159,
			'ItemGroupLineMod ItemLineMod ClassRef ListID' => 0,
			'ItemGroupLineMod ItemLineMod ClassRef FullName' => 159,
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 0,
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 159,
			'ItemGroupLineMod ItemLineMod BillableStatus' => 0,
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => 0,
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 159,
			'ItemGroupLineMod ItemLineMod SalesRepRef ListID' => 0,
			'ItemGroupLineMod ItemLineMod SalesRepRef FullName' => 159,
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
			'AccountRef ListID' => 999.99,
			'AccountRef FullName' => 999.99,
			'PayeeEntityRef ListID' => 999.99,
			'PayeeEntityRef FullName' => 999.99,
			'TxnDate' => 999.99,
			'RefNumber' => 999.99,
			'Memo' => 999.99,
			'IsTaxIncluded' => 8.0,
			'SalesTaxCodeRef ListID' => 999.99,
			'SalesTaxCodeRef FullName' => 999.99,
			'ExchangeRate' => 8.0,
			'ClearExpenseLines' => 999.99,
			'ExpenseLineMod TxnLineID' => 999.99,
			'ExpenseLineMod AccountRef ListID' => 999.99,
			'ExpenseLineMod AccountRef FullName' => 999.99,
			'ExpenseLineMod Amount' => 999.99,
			'ExpenseLineMod TaxAmount' => 6.1,
			'ExpenseLineMod Memo' => 999.99,
			'ExpenseLineMod CustomerRef ListID' => 999.99,
			'ExpenseLineMod CustomerRef FullName' => 999.99,
			'ExpenseLineMod ClassRef ListID' => 999.99,
			'ExpenseLineMod ClassRef FullName' => 999.99,
			'ExpenseLineMod SalesTaxCodeRef ListID' => 999.99,
			'ExpenseLineMod SalesTaxCodeRef FullName' => 999.99,
			'ExpenseLineMod BillableStatus' => 999.99,
			'ExpenseLineMod SalesRepRef ListID' => 999.99,
			'ExpenseLineMod SalesRepRef FullName' => 999.99,
			'ClearItemLines' => 999.99,
			'ItemLineMod TxnLineID' => 999.99,
			'ItemLineMod ItemRef ListID' => 999.99,
			'ItemLineMod ItemRef FullName' => 999.99,
			'ItemLineMod InventorySiteRef ListID' => 999.99,
			'ItemLineMod InventorySiteRef FullName' => 999.99,
			'ItemLineMod InventorySiteLocationRef ListID' => 999.99,
			'ItemLineMod InventorySiteLocationRef FullName' => 999.99,
			'ItemLineMod SerialNumber' => 999.99,
			'ItemLineMod LotNumber' => 999.99,
			'ItemLineMod Desc' => 999.99,
			'ItemLineMod Quantity' => 999.99,
			'ItemLineMod UnitOfMeasure' => 7.0,
			'ItemLineMod OverrideUOMSetRef ListID' => 999.99,
			'ItemLineMod OverrideUOMSetRef FullName' => 999.99,
			'ItemLineMod Cost' => 999.99,
			'ItemLineMod Amount' => 999.99,
			'ItemLineMod TaxAmount' => 6.1,
			'ItemLineMod CustomerRef ListID' => 999.99,
			'ItemLineMod CustomerRef FullName' => 999.99,
			'ItemLineMod ClassRef ListID' => 999.99,
			'ItemLineMod ClassRef FullName' => 999.99,
			'ItemLineMod SalesTaxCodeRef ListID' => 999.99,
			'ItemLineMod SalesTaxCodeRef FullName' => 999.99,
			'ItemLineMod BillableStatus' => 999.99,
			'ItemLineMod OverrideItemAccountRef ListID' => 999.99,
			'ItemLineMod OverrideItemAccountRef FullName' => 999.99,
			'ItemLineMod SalesRepRef ListID' => 999.99,
			'ItemLineMod SalesRepRef FullName' => 999.99,
			'ItemGroupLineMod TxnLineID' => 999.99,
			'ItemGroupLineMod ItemGroupRef ListID' => 999.99,
			'ItemGroupLineMod ItemGroupRef FullName' => 999.99,
			'ItemGroupLineMod Quantity' => 999.99,
			'ItemGroupLineMod UnitOfMeasure' => 7.0,
			'ItemGroupLineMod OverrideUOMSetRef ListID' => 999.99,
			'ItemGroupLineMod OverrideUOMSetRef FullName' => 999.99,
			'ItemGroupLineMod ItemLineMod TxnLineID' => 999.99,
			'ItemGroupLineMod ItemLineMod ItemRef ListID' => 999.99,
			'ItemGroupLineMod ItemLineMod ItemRef FullName' => 999.99,
			'ItemGroupLineMod ItemLineMod InventorySiteRef ListID' => 999.99,
			'ItemGroupLineMod ItemLineMod InventorySiteRef FullName' => 999.99,
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef ListID' => 999.99,
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef FullName' => 999.99,
			'ItemGroupLineMod ItemLineMod SerialNumber' => 999.99,
			'ItemGroupLineMod ItemLineMod LotNumber' => 999.99,
			'ItemGroupLineMod ItemLineMod Desc' => 999.99,
			'ItemGroupLineMod ItemLineMod Quantity' => 999.99,
			'ItemGroupLineMod ItemLineMod UnitOfMeasure' => 7.0,
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 999.99,
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 999.99,
			'ItemGroupLineMod ItemLineMod Cost' => 999.99,
			'ItemGroupLineMod ItemLineMod Amount' => 999.99,
			'ItemGroupLineMod ItemLineMod TaxAmount' => 6.1,
			'ItemGroupLineMod ItemLineMod CustomerRef ListID' => 999.99,
			'ItemGroupLineMod ItemLineMod CustomerRef FullName' => 999.99,
			'ItemGroupLineMod ItemLineMod ClassRef ListID' => 999.99,
			'ItemGroupLineMod ItemLineMod ClassRef FullName' => 999.99,
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 999.99,
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 999.99,
			'ItemGroupLineMod ItemLineMod BillableStatus' => 999.99,
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => 999.99,
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 999.99,
			'ItemGroupLineMod ItemLineMod SalesRepRef ListID' => 999.99,
			'ItemGroupLineMod ItemLineMod SalesRepRef FullName' => 999.99,
			'IncludeRetElement' => 4.0,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'TxnID' => false,
			'EditSequence' => false,
			'AccountRef ListID' => false,
			'AccountRef FullName' => false,
			'PayeeEntityRef ListID' => false,
			'PayeeEntityRef FullName' => false,
			'TxnDate' => false,
			'RefNumber' => false,
			'Memo' => false,
			'IsTaxIncluded' => false,
			'SalesTaxCodeRef ListID' => false,
			'SalesTaxCodeRef FullName' => false,
			'ExchangeRate' => false,
			'ClearExpenseLines' => false,
			'ExpenseLineMod TxnLineID' => false,
			'ExpenseLineMod AccountRef ListID' => false,
			'ExpenseLineMod AccountRef FullName' => false,
			'ExpenseLineMod Amount' => false,
			'ExpenseLineMod TaxAmount' => false,
			'ExpenseLineMod Memo' => false,
			'ExpenseLineMod CustomerRef ListID' => false,
			'ExpenseLineMod CustomerRef FullName' => false,
			'ExpenseLineMod ClassRef ListID' => false,
			'ExpenseLineMod ClassRef FullName' => false,
			'ExpenseLineMod SalesTaxCodeRef ListID' => false,
			'ExpenseLineMod SalesTaxCodeRef FullName' => false,
			'ExpenseLineMod BillableStatus' => false,
			'ExpenseLineMod SalesRepRef ListID' => false,
			'ExpenseLineMod SalesRepRef FullName' => false,
			'ClearItemLines' => false,
			'ItemLineMod TxnLineID' => false,
			'ItemLineMod ItemRef ListID' => false,
			'ItemLineMod ItemRef FullName' => false,
			'ItemLineMod InventorySiteRef ListID' => false,
			'ItemLineMod InventorySiteRef FullName' => false,
			'ItemLineMod InventorySiteLocationRef ListID' => false,
			'ItemLineMod InventorySiteLocationRef FullName' => false,
			'ItemLineMod SerialNumber' => false,
			'ItemLineMod LotNumber' => false,
			'ItemLineMod Desc' => false,
			'ItemLineMod Quantity' => false,
			'ItemLineMod UnitOfMeasure' => false,
			'ItemLineMod OverrideUOMSetRef ListID' => false,
			'ItemLineMod OverrideUOMSetRef FullName' => false,
			'ItemLineMod Cost' => false,
			'ItemLineMod Amount' => false,
			'ItemLineMod TaxAmount' => false,
			'ItemLineMod CustomerRef ListID' => false,
			'ItemLineMod CustomerRef FullName' => false,
			'ItemLineMod ClassRef ListID' => false,
			'ItemLineMod ClassRef FullName' => false,
			'ItemLineMod SalesTaxCodeRef ListID' => false,
			'ItemLineMod SalesTaxCodeRef FullName' => false,
			'ItemLineMod BillableStatus' => false,
			'ItemLineMod OverrideItemAccountRef ListID' => false,
			'ItemLineMod OverrideItemAccountRef FullName' => false,
			'ItemLineMod SalesRepRef ListID' => false,
			'ItemLineMod SalesRepRef FullName' => false,
			'ItemGroupLineMod TxnLineID' => false,
			'ItemGroupLineMod ItemGroupRef ListID' => false,
			'ItemGroupLineMod ItemGroupRef FullName' => false,
			'ItemGroupLineMod Quantity' => false,
			'ItemGroupLineMod UnitOfMeasure' => false,
			'ItemGroupLineMod OverrideUOMSetRef ListID' => false,
			'ItemGroupLineMod OverrideUOMSetRef FullName' => false,
			'ItemGroupLineMod ItemLineMod TxnLineID' => false,
			'ItemGroupLineMod ItemLineMod ItemRef ListID' => false,
			'ItemGroupLineMod ItemLineMod ItemRef FullName' => false,
			'ItemGroupLineMod ItemLineMod InventorySiteRef ListID' => false,
			'ItemGroupLineMod ItemLineMod InventorySiteRef FullName' => false,
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef ListID' => false,
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef FullName' => false,
			'ItemGroupLineMod ItemLineMod SerialNumber' => false,
			'ItemGroupLineMod ItemLineMod LotNumber' => false,
			'ItemGroupLineMod ItemLineMod Desc' => false,
			'ItemGroupLineMod ItemLineMod Quantity' => false,
			'ItemGroupLineMod ItemLineMod UnitOfMeasure' => false,
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => false,
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => false,
			'ItemGroupLineMod ItemLineMod Cost' => false,
			'ItemGroupLineMod ItemLineMod Amount' => false,
			'ItemGroupLineMod ItemLineMod TaxAmount' => false,
			'ItemGroupLineMod ItemLineMod CustomerRef ListID' => false,
			'ItemGroupLineMod ItemLineMod CustomerRef FullName' => false,
			'ItemGroupLineMod ItemLineMod ClassRef ListID' => false,
			'ItemGroupLineMod ItemLineMod ClassRef FullName' => false,
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => false,
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => false,
			'ItemGroupLineMod ItemLineMod BillableStatus' => false,
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID' => false,
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => false,
			'ItemGroupLineMod ItemLineMod SalesRepRef ListID' => false,
			'ItemGroupLineMod ItemLineMod SalesRepRef FullName' => false,
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
			'AccountRef',
			'AccountRef ListID',
			'AccountRef FullName',
			'PayeeEntityRef',
			'PayeeEntityRef ListID',
			'PayeeEntityRef FullName',
			'TxnDate',
			'RefNumber',
			'Memo',
			'IsTaxIncluded',
			'SalesTaxCodeRef',
			'SalesTaxCodeRef ListID',
			'SalesTaxCodeRef FullName',
			'ExchangeRate',
			'ClearExpenseLines',
			'ExpenseLineMod',
			'ExpenseLineMod TxnLineID',
			'ExpenseLineMod AccountRef ListID',
			'ExpenseLineMod AccountRef FullName',
			'ExpenseLineMod Amount',
			'ExpenseLineMod TaxAmount',
			'ExpenseLineMod Memo',
			'ExpenseLineMod CustomerRef ListID',
			'ExpenseLineMod CustomerRef FullName',
			'ExpenseLineMod ClassRef ListID',
			'ExpenseLineMod ClassRef FullName',
			'ExpenseLineMod SalesTaxCodeRef ListID',
			'ExpenseLineMod SalesTaxCodeRef FullName',
			'ExpenseLineMod BillableStatus',
			'ExpenseLineMod SalesRepRef ListID',
			'ExpenseLineMod SalesRepRef FullName',
			'ClearItemLines',
			'ItemLineMod',
			'ItemLineMod TxnLineID',
			'ItemLineMod ItemRef ListID',
			'ItemLineMod ItemRef FullName',
			'ItemLineMod InventorySiteRef ListID',
			'ItemLineMod InventorySiteRef FullName',
			'ItemLineMod InventorySiteLocationRef ListID',
			'ItemLineMod InventorySiteLocationRef FullName',
			'ItemLineMod SerialNumber',
			'ItemLineMod LotNumber',
			'ItemLineMod Desc',
			'ItemLineMod Quantity',
			'ItemLineMod UnitOfMeasure',
			'ItemLineMod OverrideUOMSetRef ListID',
			'ItemLineMod OverrideUOMSetRef FullName',
			'ItemLineMod Cost',
			'ItemLineMod Amount',
			'ItemLineMod TaxAmount',
			'ItemLineMod CustomerRef ListID',
			'ItemLineMod CustomerRef FullName',
			'ItemLineMod ClassRef ListID',
			'ItemLineMod ClassRef FullName',
			'ItemLineMod SalesTaxCodeRef ListID',
			'ItemLineMod SalesTaxCodeRef FullName',
			'ItemLineMod BillableStatus',
			'ItemLineMod OverrideItemAccountRef ListID',
			'ItemLineMod OverrideItemAccountRef FullName',
			'ItemLineMod SalesRepRef ListID',
			'ItemLineMod SalesRepRef FullName',
			'ItemGroupLineMod',
			'ItemGroupLineMod TxnLineID',
			'ItemGroupLineMod ItemGroupRef ListID',
			'ItemGroupLineMod ItemGroupRef FullName',
			'ItemGroupLineMod Quantity',
			'ItemGroupLineMod UnitOfMeasure',
			'ItemGroupLineMod OverrideUOMSetRef ListID',
			'ItemGroupLineMod OverrideUOMSetRef FullName',
			'ItemGroupLineMod ItemLineMod TxnLineID',
			'ItemGroupLineMod ItemLineMod ItemRef ListID',
			'ItemGroupLineMod ItemLineMod ItemRef FullName',
			'ItemGroupLineMod ItemLineMod InventorySiteRef ListID',
			'ItemGroupLineMod ItemLineMod InventorySiteRef FullName',
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef ListID',
			'ItemGroupLineMod ItemLineMod InventorySiteLocationRef FullName',
			'ItemGroupLineMod ItemLineMod SerialNumber',
			'ItemGroupLineMod ItemLineMod LotNumber',
			'ItemGroupLineMod ItemLineMod Desc',
			'ItemGroupLineMod ItemLineMod Quantity',
			'ItemGroupLineMod ItemLineMod UnitOfMeasure',
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID',
			'ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName',
			'ItemGroupLineMod ItemLineMod Cost',
			'ItemGroupLineMod ItemLineMod Amount',
			'ItemGroupLineMod ItemLineMod TaxAmount',
			'ItemGroupLineMod ItemLineMod CustomerRef ListID',
			'ItemGroupLineMod ItemLineMod CustomerRef FullName',
			'ItemGroupLineMod ItemLineMod ClassRef ListID',
			'ItemGroupLineMod ItemLineMod ClassRef FullName',
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID',
			'ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName',
			'ItemGroupLineMod ItemLineMod BillableStatus',
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef ListID',
			'ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName',
			'ItemGroupLineMod ItemLineMod SalesRepRef ListID',
			'ItemGroupLineMod ItemLineMod SalesRepRef FullName',
			'IncludeRetElement',
		];

		return $paths;
	}
}
