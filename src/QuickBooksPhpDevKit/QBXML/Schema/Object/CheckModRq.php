<?php declare(strict_types=1);

/**
 * Schema object for: CheckModRq
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
 * WARNING!!!: This file is generated by QuickBooksPhpDevKit\QBXML\Schema\Generator using the /data/qbxmlops130.xml file in this package.
 */
final class CheckModRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'CheckMod';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'TxnID' => 'IDTYPE',
		'EditSequence' => 'STRTYPE',
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
		'ApplyCheckToTxnMod TxnID' => 'IDTYPE',
		'ApplyCheckToTxnMod Amount' => 'AMTTYPE',
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

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'EditSequence' => 16,
		'AccountRef FullName' => 159,
		'PayeeEntityRef FullName' => 209,
		'RefNumber' => 11,
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
		'SalesTaxCodeRef FullName' => 3,
		'ExpenseLineMod AccountRef FullName' => 159,
		'ExpenseLineMod Memo' => 4095,
		'ExpenseLineMod CustomerRef FullName' => 209,
		'ExpenseLineMod ClassRef FullName' => 159,
		'ExpenseLineMod SalesTaxCodeRef FullName' => 3,
		'ExpenseLineMod SalesRepRef FullName' => 5,
		'ItemLineMod InventorySiteRef FullName' => 31,
		'ItemLineMod InventorySiteLocationRef FullName' => 31,
		'ItemLineMod SerialNumber' => 4095,
		'ItemLineMod LotNumber' => 40,
		'ItemLineMod Desc' => 4095,
		'ItemLineMod UnitOfMeasure' => 31,
		'ItemLineMod OverrideUOMSetRef FullName' => 31,
		'ItemLineMod CustomerRef FullName' => 209,
		'ItemLineMod ClassRef FullName' => 159,
		'ItemLineMod SalesTaxCodeRef FullName' => 3,
		'ItemLineMod OverrideItemAccountRef FullName' => 159,
		'ItemLineMod SalesRepRef FullName' => 5,
		'ItemGroupLineMod ItemGroupRef FullName' => 31,
		'ItemGroupLineMod UnitOfMeasure' => 31,
		'ItemGroupLineMod OverrideUOMSetRef FullName' => 31,
		'ItemGroupLineMod ItemLineMod InventorySiteRef FullName' => 31,
		'ItemGroupLineMod ItemLineMod InventorySiteLocationRef FullName' => 31,
		'ItemGroupLineMod ItemLineMod SerialNumber' => 4095,
		'ItemGroupLineMod ItemLineMod LotNumber' => 40,
		'ItemGroupLineMod ItemLineMod Desc' => 4095,
		'ItemGroupLineMod ItemLineMod UnitOfMeasure' => 31,
		'ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 31,
		'ItemGroupLineMod ItemLineMod CustomerRef FullName' => 209,
		'ItemGroupLineMod ItemLineMod ClassRef FullName' => 159,
		'ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 3,
		'ItemGroupLineMod ItemLineMod OverrideItemAccountRef FullName' => 159,
		'ItemGroupLineMod ItemLineMod SalesRepRef FullName' => 5,
		'IncludeRetElement' => 50,
	];

	/**
	 * Field is optional (may be ommitted)
	 * @var bool[]
	 */
	protected $_isOptionalPaths = []; //'_isOptionalPaths ';

	/**
	 * Field Available Since QBXML Version #
	 * @var float[]
	 */
	protected $_sinceVersionPaths = [
		'Address Addr4' => 2.0,
		'Address Addr5' => 6.0,
		'Address Note' => 6.0,
		'IsTaxIncluded' => 6.0,
		'SalesTaxCodeRef ListID' => 6.0,
		'SalesTaxCodeRef FullName' => 6.0,
		'ExchangeRate' => 8.0,
		'ApplyCheckToTxnMod TxnID' => 7.0,
		'ApplyCheckToTxnMod Amount' => 7.0,
		'ExpenseLineMod TaxAmount' => 6.1,
		'ExpenseLineMod SalesTaxCodeRef ListID' => 6.0,
		'ExpenseLineMod SalesTaxCodeRef FullName' => 6.0,
		'ExpenseLineMod SalesRepRef ListID' => 13.0,
		'ExpenseLineMod SalesRepRef FullName' => 13.0,
		'ItemLineMod InventorySiteRef ListID' => 10.0,
		'ItemLineMod InventorySiteRef FullName' => 10.0,
		'ItemLineMod InventorySiteLocationRef ListID' => 12.0,
		'ItemLineMod InventorySiteLocationRef FullName' => 12.0,
		'ItemLineMod UnitOfMeasure' => 7.0,
		'ItemLineMod OverrideUOMSetRef ListID' => 7.0,
		'ItemLineMod OverrideUOMSetRef FullName' => 7.0,
		'ItemLineMod TaxAmount' => 6.1,
		'ItemLineMod SalesTaxCodeRef ListID' => 6.0,
		'ItemLineMod SalesTaxCodeRef FullName' => 6.0,
		'ItemLineMod SalesRepRef ListID' => 13.0,
		'ItemLineMod SalesRepRef FullName' => 13.0,
		'ItemGroupLineMod UnitOfMeasure' => 7.0,
		'ItemGroupLineMod OverrideUOMSetRef ListID' => 7.0,
		'ItemGroupLineMod OverrideUOMSetRef FullName' => 7.0,
		'ItemGroupLineMod ItemLineMod InventorySiteRef ListID' => 10.0,
		'ItemGroupLineMod ItemLineMod InventorySiteRef FullName' => 10.0,
		'ItemGroupLineMod ItemLineMod InventorySiteLocationRef ListID' => 12.0,
		'ItemGroupLineMod ItemLineMod InventorySiteLocationRef FullName' => 12.0,
		'ItemGroupLineMod ItemLineMod UnitOfMeasure' => 7.0,
		'ItemGroupLineMod ItemLineMod OverrideUOMSetRef ListID' => 7.0,
		'ItemGroupLineMod ItemLineMod OverrideUOMSetRef FullName' => 7.0,
		'ItemGroupLineMod ItemLineMod TaxAmount' => 6.1,
		'ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => 6.0,
		'ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => 6.0,
		'ItemGroupLineMod ItemLineMod SalesRepRef ListID' => 13.0,
		'ItemGroupLineMod ItemLineMod SalesRepRef FullName' => 13.0,
		'IncludeRetElement' => 4.0,
	];

	/**
	 * Field May Be Included Multiple Times
	 * @var bool[]
	 */
	protected $_isRepeatablePaths = [
		'IncludeRetElement' => true,
	];

	/**
	 * Field Is Excluded From These Locales
	 *
	 * QuickBooks labels are QBD, QBCA, QBUK, QBAU, and QBOE but these are mapped to
	 * US, CA, UK, AU, and OE to match what is in the PackageInfo::Locale array.
	 * @var string[][]
	 */
	protected $_localeExcludedPaths = [
		'IsTaxIncluded' => ['US'],
		'SalesTaxCodeRef ListID' => ['US'],
		'SalesTaxCodeRef FullName' => ['US'],
		'ExpenseLineMod TaxAmount' => ['US','CA','UK'],
		'ExpenseLineMod SalesTaxCodeRef ListID' => ['US'],
		'ExpenseLineMod SalesTaxCodeRef FullName' => ['US'],
		'ItemLineMod InventorySiteRef ListID' => ['AU'],
		'ItemLineMod InventorySiteRef FullName' => ['AU'],
		'ItemLineMod InventorySiteLocationRef ListID' => ['AU'],
		'ItemLineMod InventorySiteLocationRef FullName' => ['AU'],
		'ItemLineMod SerialNumber' => ['AU'],
		'ItemLineMod LotNumber' => ['AU'],
		'ItemLineMod TaxAmount' => ['US','CA','UK'],
		'ItemLineMod SalesTaxCodeRef ListID' => ['US'],
		'ItemLineMod SalesTaxCodeRef FullName' => ['US'],
		'ItemGroupLineMod ItemLineMod InventorySiteRef ListID' => ['AU'],
		'ItemGroupLineMod ItemLineMod InventorySiteRef FullName' => ['AU'],
		'ItemGroupLineMod ItemLineMod InventorySiteLocationRef ListID' => ['AU'],
		'ItemGroupLineMod ItemLineMod InventorySiteLocationRef FullName' => ['AU'],
		'ItemGroupLineMod ItemLineMod SerialNumber' => ['AU'],
		'ItemGroupLineMod ItemLineMod LotNumber' => ['AU'],
		'ItemGroupLineMod ItemLineMod TaxAmount' => ['US','CA','UK'],
		'ItemGroupLineMod ItemLineMod SalesTaxCodeRef ListID' => ['US'],
		'ItemGroupLineMod ItemLineMod SalesTaxCodeRef FullName' => ['US'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'TxnID',
		'EditSequence',
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
		'ApplyCheckToTxnMod',
		'ApplyCheckToTxnMod TxnID',
		'ApplyCheckToTxnMod Amount',
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
}
