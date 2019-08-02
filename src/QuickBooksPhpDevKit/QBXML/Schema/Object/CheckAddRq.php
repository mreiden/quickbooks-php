<?php declare(strict_types=1);

/**
 * Schema object for: CheckAddRq
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
class CheckAddRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'CheckAdd';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'AccountRef ListID' => 'IDTYPE',
			'AccountRef FullName' => 'STRTYPE',
			'PayeeEntityRef ListID' => 'IDTYPE',
			'PayeeEntityRef FullName' => 'STRTYPE',
			'RefNumber' => 'STRTYPE',
			'TxnDate' => 'DATETYPE',
			'Memo' => 'STRTYPE',
			'Address Addr1' => 'STRTYPE',
			'Address Addr2' => 'STRTYPE',
			'Address Addr3' => 'STRTYPE',
			'Address Addr4' => 'STRTYPE',
			'Address Addr5' => 'STRTYPE',
			'Address City' => 'STRTYPE',
			'Address State' => 'STRTYPE',
			'Address PostalCode' => 'STRTYPE',
			'Address Country' => 'STRTYPE',
			'Address Note' => 'STRTYPE',
			'IsToBePrinted' => 'BOOLTYPE',
			'IsTaxIncluded' => 'BOOLTYPE',
			'SalesTaxCodeRef ListID' => 'IDTYPE',
			'SalesTaxCodeRef FullName' => 'STRTYPE',
			'ExchangeRate' => 'FLOATTYPE',
			'ExternalGUID' => 'GUIDTYPE',
			'ApplyCheckToTxnAdd TxnID' => 'IDTYPE',
			'ApplyCheckToTxnAdd Amount' => 'AMTTYPE',
			'ExpenseLineAdd AccountRef ListID' => 'IDTYPE',
			'ExpenseLineAdd AccountRef FullName' => 'STRTYPE',
			'ExpenseLineAdd Amount' => 'AMTTYPE',
			'ExpenseLineAdd TaxAmount' => 'AMTTYPE',
			'ExpenseLineAdd Memo' => 'STRTYPE',
			'ExpenseLineAdd CustomerRef ListID' => 'IDTYPE',
			'ExpenseLineAdd CustomerRef FullName' => 'STRTYPE',
			'ExpenseLineAdd ClassRef ListID' => 'IDTYPE',
			'ExpenseLineAdd ClassRef FullName' => 'STRTYPE',
			'ExpenseLineAdd SalesTaxCodeRef ListID' => 'IDTYPE',
			'ExpenseLineAdd SalesTaxCodeRef FullName' => 'STRTYPE',
			'ExpenseLineAdd BillableStatus' => 'ENUMTYPE',
			'ExpenseLineAdd SalesRepRef ListID' => 'IDTYPE',
			'ExpenseLineAdd SalesRepRef FullName' => 'STRTYPE',
			'ExpenseLineAdd DataExt OwnerID' => 'GUIDTYPE',
			'ExpenseLineAdd DataExt DataExtName' => 'STRTYPE',
			'ExpenseLineAdd DataExt DataExtValue' => 'STRTYPE',
			'ItemLineAdd ItemRef ListID' => 'IDTYPE',
			'ItemLineAdd ItemRef FullName' => 'STRTYPE',
			'ItemLineAdd InventorySiteRef ListID' => 'IDTYPE',
			'ItemLineAdd InventorySiteRef FullName' => 'STRTYPE',
			'ItemLineAdd InventorySiteLocationRef ListID' => 'IDTYPE',
			'ItemLineAdd InventorySiteLocationRef FullName' => 'STRTYPE',
			'ItemLineAdd SerialNumber' => 'STRTYPE',
			'ItemLineAdd LotNumber' => 'STRTYPE',
			'ItemLineAdd Desc' => 'STRTYPE',
			'ItemLineAdd Quantity' => 'QUANTYPE',
			'ItemLineAdd UnitOfMeasure' => 'STRTYPE',
			'ItemLineAdd Cost' => 'PRICETYPE',
			'ItemLineAdd Amount' => 'AMTTYPE',
			'ItemLineAdd TaxAmount' => 'AMTTYPE',
			'ItemLineAdd CustomerRef ListID' => 'IDTYPE',
			'ItemLineAdd CustomerRef FullName' => 'STRTYPE',
			'ItemLineAdd ClassRef ListID' => 'IDTYPE',
			'ItemLineAdd ClassRef FullName' => 'STRTYPE',
			'ItemLineAdd SalesTaxCodeRef ListID' => 'IDTYPE',
			'ItemLineAdd SalesTaxCodeRef FullName' => 'STRTYPE',
			'ItemLineAdd BillableStatus' => 'ENUMTYPE',
			'ItemLineAdd OverrideItemAccountRef ListID' => 'IDTYPE',
			'ItemLineAdd OverrideItemAccountRef FullName' => 'STRTYPE',
			'ItemLineAdd LinkToTxn TxnID' => 'IDTYPE',
			'ItemLineAdd LinkToTxn TxnLineID' => 'IDTYPE',
			'ItemLineAdd SalesRepRef ListID' => 'IDTYPE',
			'ItemLineAdd SalesRepRef FullName' => 'STRTYPE',
			'ItemLineAdd DataExt OwnerID' => 'GUIDTYPE',
			'ItemLineAdd DataExt DataExtName' => 'STRTYPE',
			'ItemLineAdd DataExt DataExtValue' => 'STRTYPE',
			'ItemGroupLineAdd ItemGroupRef ListID' => 'IDTYPE',
			'ItemGroupLineAdd ItemGroupRef FullName' => 'STRTYPE',
			'ItemGroupLineAdd Desc' => 'STRTYPE',
			'ItemGroupLineAdd Quantity' => 'QUANTYPE',
			'ItemGroupLineAdd UnitOfMeasure' => 'STRTYPE',
			'ItemGroupLineAdd InventorySiteRef ListID' => 'IDTYPE',
			'ItemGroupLineAdd InventorySiteRef FullName' => 'STRTYPE',
			'ItemGroupLineAdd InventorySiteLocationRef ListID' => 'IDTYPE',
			'ItemGroupLineAdd InventorySiteLocationRef FullName' => 'STRTYPE',
			'ItemGroupLineAdd DataExt OwnerID' => 'GUIDTYPE',
			'ItemGroupLineAdd DataExt DataExtName' => 'STRTYPE',
			'ItemGroupLineAdd DataExt DataExtValue' => 'STRTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'AccountRef ListID' => 0,
			'AccountRef FullName' => 159,
			'PayeeEntityRef ListID' => 0,
			'PayeeEntityRef FullName' => 159,
			'RefNumber' => 11,
			'TxnDate' => 0,
			'Memo' => 4095,
			'Address Addr1' => 41,
			'Address Addr2' => 41,
			'Address Addr3' => 41,
			'Address Addr4' => 41,
			'Address Addr5' => 41,
			'Address City' => 31,
			'Address State' => 21,
			'Address PostalCode' => 13,
			'Address Country' => 31,
			'Address Note' => 41,
			'IsToBePrinted' => 0,
			'IsTaxIncluded' => 0,
			'SalesTaxCodeRef ListID' => 0,
			'SalesTaxCodeRef FullName' => 159,
			'ExchangeRate' => 0,
			'ExternalGUID' => 0,
			'ApplyCheckToTxnAdd TxnID' => 0,
			'ApplyCheckToTxnAdd Amount' => 0,
			'ExpenseLineAdd AccountRef ListID' => 0,
			'ExpenseLineAdd AccountRef FullName' => 159,
			'ExpenseLineAdd Amount' => 0,
			'ExpenseLineAdd TaxAmount' => 0,
			'ExpenseLineAdd Memo' => 4095,
			'ExpenseLineAdd CustomerRef ListID' => 0,
			'ExpenseLineAdd CustomerRef FullName' => 159,
			'ExpenseLineAdd ClassRef ListID' => 0,
			'ExpenseLineAdd ClassRef FullName' => 159,
			'ExpenseLineAdd SalesTaxCodeRef ListID' => 0,
			'ExpenseLineAdd SalesTaxCodeRef FullName' => 159,
			'ExpenseLineAdd BillableStatus' => 0,
			'ExpenseLineAdd SalesRepRef ListID' => 0,
			'ExpenseLineAdd SalesRepRef FullName' => 159,
			'ExpenseLineAdd DataExt OwnerID' => 0,
			'ExpenseLineAdd DataExt DataExtName' => 31,
			'ExpenseLineAdd DataExt DataExtValue' => 0,
			'ItemLineAdd ItemRef ListID' => 0,
			'ItemLineAdd ItemRef FullName' => 159,
			'ItemLineAdd InventorySiteRef ListID' => 0,
			'ItemLineAdd InventorySiteRef FullName' => 159,
			'ItemLineAdd InventorySiteLocationRef ListID' => 0,
			'ItemLineAdd InventorySiteLocationRef FullName' => 159,
			'ItemLineAdd SerialNumber' => 4095,
			'ItemLineAdd LotNumber' => 40,
			'ItemLineAdd Desc' => 4095,
			'ItemLineAdd Quantity' => 0,
			'ItemLineAdd UnitOfMeasure' => 31,
			'ItemLineAdd Cost' => 0,
			'ItemLineAdd Amount' => 0,
			'ItemLineAdd TaxAmount' => 0,
			'ItemLineAdd CustomerRef ListID' => 0,
			'ItemLineAdd CustomerRef FullName' => 159,
			'ItemLineAdd ClassRef ListID' => 0,
			'ItemLineAdd ClassRef FullName' => 159,
			'ItemLineAdd SalesTaxCodeRef ListID' => 0,
			'ItemLineAdd SalesTaxCodeRef FullName' => 159,
			'ItemLineAdd BillableStatus' => 0,
			'ItemLineAdd OverrideItemAccountRef ListID' => 0,
			'ItemLineAdd OverrideItemAccountRef FullName' => 159,
			'ItemLineAdd LinkToTxn TxnID' => 0,
			'ItemLineAdd LinkToTxn TxnLineID' => 0,
			'ItemLineAdd SalesRepRef ListID' => 0,
			'ItemLineAdd SalesRepRef FullName' => 159,
			'ItemLineAdd DataExt OwnerID' => 0,
			'ItemLineAdd DataExt DataExtName' => 31,
			'ItemLineAdd DataExt DataExtValue' => 0,
			'ItemGroupLineAdd ItemGroupRef ListID' => 0,
			'ItemGroupLineAdd ItemGroupRef FullName' => 159,
			'ItemGroupLineAdd Desc' => 4095,
			'ItemGroupLineAdd Quantity' => 0,
			'ItemGroupLineAdd UnitOfMeasure' => 31,
			'ItemGroupLineAdd InventorySiteRef ListID' => 0,
			'ItemGroupLineAdd InventorySiteRef FullName' => 159,
			'ItemGroupLineAdd InventorySiteLocationRef ListID' => 0,
			'ItemGroupLineAdd InventorySiteLocationRef FullName' => 159,
			'ItemGroupLineAdd DataExt OwnerID' => 0,
			'ItemGroupLineAdd DataExt DataExtName' => 31,
			'ItemGroupLineAdd DataExt DataExtValue' => 0,
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
			'AccountRef ListID' => 999.99,
			'AccountRef FullName' => 999.99,
			'PayeeEntityRef ListID' => 999.99,
			'PayeeEntityRef FullName' => 999.99,
			'RefNumber' => 999.99,
			'TxnDate' => 999.99,
			'Memo' => 999.99,
			'Address Addr1' => 999.99,
			'Address Addr2' => 999.99,
			'Address Addr3' => 999.99,
			'Address Addr4' => 2.0,
			'Address Addr5' => 6.0,
			'Address City' => 999.99,
			'Address State' => 999.99,
			'Address PostalCode' => 999.99,
			'Address Country' => 999.99,
			'Address Note' => 6.0,
			'IsToBePrinted' => 999.99,
			'IsTaxIncluded' => 6.0,
			'SalesTaxCodeRef ListID' => 999.99,
			'SalesTaxCodeRef FullName' => 999.99,
			'ExchangeRate' => 8.0,
			'ExternalGUID' => 9.0,
			'ApplyCheckToTxnAdd TxnID' => 999.99,
			'ApplyCheckToTxnAdd Amount' => 999.99,
			'ExpenseLineAdd AccountRef ListID' => 999.99,
			'ExpenseLineAdd AccountRef FullName' => 999.99,
			'ExpenseLineAdd Amount' => 999.99,
			'ExpenseLineAdd TaxAmount' => 6.1,
			'ExpenseLineAdd Memo' => 999.99,
			'ExpenseLineAdd CustomerRef ListID' => 999.99,
			'ExpenseLineAdd CustomerRef FullName' => 999.99,
			'ExpenseLineAdd ClassRef ListID' => 999.99,
			'ExpenseLineAdd ClassRef FullName' => 999.99,
			'ExpenseLineAdd SalesTaxCodeRef ListID' => 999.99,
			'ExpenseLineAdd SalesTaxCodeRef FullName' => 999.99,
			'ExpenseLineAdd BillableStatus' => 2.0,
			'ExpenseLineAdd SalesRepRef ListID' => 999.99,
			'ExpenseLineAdd SalesRepRef FullName' => 999.99,
			'ExpenseLineAdd DataExt OwnerID' => 999.99,
			'ExpenseLineAdd DataExt DataExtName' => 999.99,
			'ExpenseLineAdd DataExt DataExtValue' => 999.99,
			'ItemLineAdd ItemRef ListID' => 999.99,
			'ItemLineAdd ItemRef FullName' => 999.99,
			'ItemLineAdd InventorySiteRef ListID' => 999.99,
			'ItemLineAdd InventorySiteRef FullName' => 999.99,
			'ItemLineAdd InventorySiteLocationRef ListID' => 999.99,
			'ItemLineAdd InventorySiteLocationRef FullName' => 999.99,
			'ItemLineAdd SerialNumber' => 999.99,
			'ItemLineAdd LotNumber' => 999.99,
			'ItemLineAdd Desc' => 999.99,
			'ItemLineAdd Quantity' => 999.99,
			'ItemLineAdd UnitOfMeasure' => 7.0,
			'ItemLineAdd Cost' => 999.99,
			'ItemLineAdd Amount' => 999.99,
			'ItemLineAdd TaxAmount' => 6.1,
			'ItemLineAdd CustomerRef ListID' => 999.99,
			'ItemLineAdd CustomerRef FullName' => 999.99,
			'ItemLineAdd ClassRef ListID' => 999.99,
			'ItemLineAdd ClassRef FullName' => 999.99,
			'ItemLineAdd SalesTaxCodeRef ListID' => 999.99,
			'ItemLineAdd SalesTaxCodeRef FullName' => 999.99,
			'ItemLineAdd BillableStatus' => 2.0,
			'ItemLineAdd OverrideItemAccountRef ListID' => 999.99,
			'ItemLineAdd OverrideItemAccountRef FullName' => 999.99,
			'ItemLineAdd LinkToTxn TxnID' => 999.99,
			'ItemLineAdd LinkToTxn TxnLineID' => 999.99,
			'ItemLineAdd SalesRepRef ListID' => 999.99,
			'ItemLineAdd SalesRepRef FullName' => 999.99,
			'ItemLineAdd DataExt OwnerID' => 999.99,
			'ItemLineAdd DataExt DataExtName' => 999.99,
			'ItemLineAdd DataExt DataExtValue' => 999.99,
			'ItemGroupLineAdd ItemGroupRef ListID' => 999.99,
			'ItemGroupLineAdd ItemGroupRef FullName' => 999.99,
			'ItemGroupLineAdd Desc' => 999.99,
			'ItemGroupLineAdd Quantity' => 999.99,
			'ItemGroupLineAdd UnitOfMeasure' => 7.0,
			'ItemGroupLineAdd InventorySiteRef ListID' => 999.99,
			'ItemGroupLineAdd InventorySiteRef FullName' => 999.99,
			'ItemGroupLineAdd InventorySiteLocationRef ListID' => 999.99,
			'ItemGroupLineAdd InventorySiteLocationRef FullName' => 999.99,
			'ItemGroupLineAdd DataExt OwnerID' => 999.99,
			'ItemGroupLineAdd DataExt DataExtName' => 999.99,
			'ItemGroupLineAdd DataExt DataExtValue' => 999.99,
			'IncludeRetElement' => 4.0,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'AccountRef ListID' => false,
			'AccountRef FullName' => false,
			'PayeeEntityRef ListID' => false,
			'PayeeEntityRef FullName' => false,
			'RefNumber' => false,
			'TxnDate' => false,
			'Memo' => false,
			'Address Addr1' => false,
			'Address Addr2' => false,
			'Address Addr3' => false,
			'Address Addr4' => false,
			'Address Addr5' => false,
			'Address City' => false,
			'Address State' => false,
			'Address PostalCode' => false,
			'Address Country' => false,
			'Address Note' => false,
			'IsToBePrinted' => false,
			'IsTaxIncluded' => false,
			'SalesTaxCodeRef ListID' => false,
			'SalesTaxCodeRef FullName' => false,
			'ExchangeRate' => false,
			'ExternalGUID' => false,
			'ApplyCheckToTxnAdd TxnID' => false,
			'ApplyCheckToTxnAdd Amount' => false,
			'ExpenseLineAdd AccountRef ListID' => false,
			'ExpenseLineAdd AccountRef FullName' => false,
			'ExpenseLineAdd Amount' => false,
			'ExpenseLineAdd TaxAmount' => false,
			'ExpenseLineAdd Memo' => false,
			'ExpenseLineAdd CustomerRef ListID' => false,
			'ExpenseLineAdd CustomerRef FullName' => false,
			'ExpenseLineAdd ClassRef ListID' => false,
			'ExpenseLineAdd ClassRef FullName' => false,
			'ExpenseLineAdd SalesTaxCodeRef ListID' => false,
			'ExpenseLineAdd SalesTaxCodeRef FullName' => false,
			'ExpenseLineAdd BillableStatus' => false,
			'ExpenseLineAdd SalesRepRef ListID' => false,
			'ExpenseLineAdd SalesRepRef FullName' => false,
			'ExpenseLineAdd DataExt OwnerID' => false,
			'ExpenseLineAdd DataExt DataExtName' => false,
			'ExpenseLineAdd DataExt DataExtValue' => false,
			'ItemLineAdd ItemRef ListID' => false,
			'ItemLineAdd ItemRef FullName' => false,
			'ItemLineAdd InventorySiteRef ListID' => false,
			'ItemLineAdd InventorySiteRef FullName' => false,
			'ItemLineAdd InventorySiteLocationRef ListID' => false,
			'ItemLineAdd InventorySiteLocationRef FullName' => false,
			'ItemLineAdd SerialNumber' => false,
			'ItemLineAdd LotNumber' => false,
			'ItemLineAdd Desc' => false,
			'ItemLineAdd Quantity' => false,
			'ItemLineAdd UnitOfMeasure' => false,
			'ItemLineAdd Cost' => false,
			'ItemLineAdd Amount' => false,
			'ItemLineAdd TaxAmount' => false,
			'ItemLineAdd CustomerRef ListID' => false,
			'ItemLineAdd CustomerRef FullName' => false,
			'ItemLineAdd ClassRef ListID' => false,
			'ItemLineAdd ClassRef FullName' => false,
			'ItemLineAdd SalesTaxCodeRef ListID' => false,
			'ItemLineAdd SalesTaxCodeRef FullName' => false,
			'ItemLineAdd BillableStatus' => false,
			'ItemLineAdd OverrideItemAccountRef ListID' => false,
			'ItemLineAdd OverrideItemAccountRef FullName' => false,
			'ItemLineAdd LinkToTxn TxnID' => false,
			'ItemLineAdd LinkToTxn TxnLineID' => false,
			'ItemLineAdd SalesRepRef ListID' => false,
			'ItemLineAdd SalesRepRef FullName' => false,
			'ItemLineAdd DataExt OwnerID' => false,
			'ItemLineAdd DataExt DataExtName' => false,
			'ItemLineAdd DataExt DataExtValue' => false,
			'ItemGroupLineAdd ItemGroupRef ListID' => false,
			'ItemGroupLineAdd ItemGroupRef FullName' => false,
			'ItemGroupLineAdd Desc' => false,
			'ItemGroupLineAdd Quantity' => false,
			'ItemGroupLineAdd UnitOfMeasure' => false,
			'ItemGroupLineAdd InventorySiteRef ListID' => false,
			'ItemGroupLineAdd InventorySiteRef FullName' => false,
			'ItemGroupLineAdd InventorySiteLocationRef ListID' => false,
			'ItemGroupLineAdd InventorySiteLocationRef FullName' => false,
			'ItemGroupLineAdd DataExt OwnerID' => false,
			'ItemGroupLineAdd DataExt DataExtName' => false,
			'ItemGroupLineAdd DataExt DataExtValue' => false,
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
			'AccountRef',
			'AccountRef ListID',
			'AccountRef FullName',
			'PayeeEntityRef',
			'PayeeEntityRef ListID',
			'PayeeEntityRef FullName',
			'RefNumber',
			'TxnDate',
			'Memo',
			'Address',
			'Address Addr1',
			'Address Addr2',
			'Address Addr3',
			'Address Addr4',
			'Address Addr5',
			'Address City',
			'Address State',
			'Address PostalCode',
			'Address Country',
			'Address Note',
			'IsToBePrinted',
			'IsTaxIncluded',
			'SalesTaxCodeRef',
			'SalesTaxCodeRef ListID',
			'SalesTaxCodeRef FullName',
			'ExchangeRate',
			'ExternalGUID',
			'ApplyCheckToTxnAdd',
			'ApplyCheckToTxnAdd TxnID',
			'ApplyCheckToTxnAdd Amount',
			'ExpenseLineAdd',
			'ExpenseLineAdd AccountRef ListID',
			'ExpenseLineAdd AccountRef FullName',
			'ExpenseLineAdd Amount',
			'ExpenseLineAdd TaxAmount',
			'ExpenseLineAdd Memo',
			'ExpenseLineAdd CustomerRef ListID',
			'ExpenseLineAdd CustomerRef FullName',
			'ExpenseLineAdd ClassRef ListID',
			'ExpenseLineAdd ClassRef FullName',
			'ExpenseLineAdd SalesTaxCodeRef ListID',
			'ExpenseLineAdd SalesTaxCodeRef FullName',
			'ExpenseLineAdd BillableStatus',
			'ExpenseLineAdd SalesRepRef ListID',
			'ExpenseLineAdd SalesRepRef FullName',
			'ExpenseLineAdd DataExt OwnerID',
			'ExpenseLineAdd DataExt DataExtName',
			'ExpenseLineAdd DataExt DataExtValue',
			'ItemLineAdd',
			'ItemLineAdd ItemRef ListID',
			'ItemLineAdd ItemRef FullName',
			'ItemLineAdd InventorySiteRef ListID',
			'ItemLineAdd InventorySiteRef FullName',
			'ItemLineAdd InventorySiteLocationRef ListID',
			'ItemLineAdd InventorySiteLocationRef FullName',
			'ItemLineAdd SerialNumber',
			'ItemLineAdd LotNumber',
			'ItemLineAdd Desc',
			'ItemLineAdd Quantity',
			'ItemLineAdd UnitOfMeasure',
			'ItemLineAdd Cost',
			'ItemLineAdd Amount',
			'ItemLineAdd TaxAmount',
			'ItemLineAdd CustomerRef ListID',
			'ItemLineAdd CustomerRef FullName',
			'ItemLineAdd ClassRef ListID',
			'ItemLineAdd ClassRef FullName',
			'ItemLineAdd SalesTaxCodeRef ListID',
			'ItemLineAdd SalesTaxCodeRef FullName',
			'ItemLineAdd BillableStatus',
			'ItemLineAdd OverrideItemAccountRef ListID',
			'ItemLineAdd OverrideItemAccountRef FullName',
			'ItemLineAdd LinkToTxn TxnID',
			'ItemLineAdd LinkToTxn TxnLineID',
			'ItemLineAdd SalesRepRef ListID',
			'ItemLineAdd SalesRepRef FullName',
			'ItemLineAdd DataExt OwnerID',
			'ItemLineAdd DataExt DataExtName',
			'ItemLineAdd DataExt DataExtValue',
			'ItemGroupLineAdd',
			'ItemGroupLineAdd ItemGroupRef ListID',
			'ItemGroupLineAdd ItemGroupRef FullName',
			'ItemGroupLineAdd Desc',
			'ItemGroupLineAdd Quantity',
			'ItemGroupLineAdd UnitOfMeasure',
			'ItemGroupLineAdd InventorySiteRef ListID',
			'ItemGroupLineAdd InventorySiteRef FullName',
			'ItemGroupLineAdd InventorySiteLocationRef ListID',
			'ItemGroupLineAdd InventorySiteLocationRef FullName',
			'ItemGroupLineAdd DataExt OwnerID',
			'ItemGroupLineAdd DataExt DataExtName',
			'ItemGroupLineAdd DataExt DataExtValue',
			'IncludeRetElement',
		];

		return $paths;
	}
}
