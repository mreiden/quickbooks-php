<?php declare(strict_types=1);

/**
 * Schema object for: ItemNonInventoryAddRq
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
final class ItemNonInventoryAddRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'ItemNonInventoryAdd';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'Name' => 'STRTYPE',
		'BarCode BarCodeValue' => 'STRTYPE',
		'BarCode AssignEvenIfUsed' => 'BOOLTYPE',
		'BarCode AllowOverride' => 'BOOLTYPE',
		'IsActive' => 'BOOLTYPE',
		'ParentRef ListID' => 'IDTYPE',
		'ParentRef FullName' => 'STRTYPE',
		'ClassRef ListID' => 'IDTYPE',
		'ClassRef FullName' => 'STRTYPE',
		'ManufacturerPartNumber' => 'STRTYPE',
		'UnitOfMeasureSetRef ListID' => 'IDTYPE',
		'UnitOfMeasureSetRef FullName' => 'STRTYPE',
		'IsTaxIncluded' => 'BOOLTYPE',
		'SalesTaxCodeRef ListID' => 'IDTYPE',
		'SalesTaxCodeRef FullName' => 'STRTYPE',
		'SalesOrPurchase Desc' => 'STRTYPE',
		'SalesOrPurchase Price' => 'PRICETYPE',
		'SalesOrPurchase PricePercent' => 'PERCENTTYPE',
		'SalesOrPurchase AccountRef ListID' => 'IDTYPE',
		'SalesOrPurchase AccountRef FullName' => 'STRTYPE',
		'SalesAndPurchase SalesDesc' => 'STRTYPE',
		'SalesAndPurchase SalesPrice' => 'PRICETYPE',
		'SalesAndPurchase IncomeAccountRef ListID' => 'IDTYPE',
		'SalesAndPurchase IncomeAccountRef FullName' => 'STRTYPE',
		'SalesAndPurchase PurchaseDesc' => 'STRTYPE',
		'SalesAndPurchase PurchaseCost' => 'PRICETYPE',
		'SalesAndPurchase PurchaseTaxCodeRef ListID' => 'IDTYPE',
		'SalesAndPurchase PurchaseTaxCodeRef FullName' => 'STRTYPE',
		'SalesAndPurchase ExpenseAccountRef ListID' => 'IDTYPE',
		'SalesAndPurchase ExpenseAccountRef FullName' => 'STRTYPE',
		'SalesAndPurchase PrefVendorRef ListID' => 'IDTYPE',
		'SalesAndPurchase PrefVendorRef FullName' => 'STRTYPE',
		'ExternalGUID' => 'GUIDTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'Name' => 31,
		'BarCode BarCodeValue' => 50,
		'ClassRef FullName' => 159,
		'ManufacturerPartNumber' => 31,
		'UnitOfMeasureSetRef FullName' => 31,
		'SalesTaxCodeRef FullName' => 3,
		'SalesOrPurchase Desc' => 4095,
		'SalesOrPurchase AccountRef FullName' => 159,
		'SalesAndPurchase SalesDesc' => 4095,
		'SalesAndPurchase IncomeAccountRef FullName' => 159,
		'SalesAndPurchase PurchaseDesc' => 4095,
		'SalesAndPurchase PurchaseTaxCodeRef FullName' => 3,
		'SalesAndPurchase ExpenseAccountRef FullName' => 159,
		'SalesAndPurchase PrefVendorRef FullName' => 41,
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
		'BarCode BarCodeValue' => 12.0,
		'BarCode AssignEvenIfUsed' => 12.0,
		'BarCode AllowOverride' => 12.0,
		'ClassRef ListID' => 12.0,
		'ClassRef FullName' => 12.0,
		'ManufacturerPartNumber' => 7.0,
		'UnitOfMeasureSetRef ListID' => 7.0,
		'UnitOfMeasureSetRef FullName' => 7.0,
		'IsTaxIncluded' => 6.0,
		'SalesAndPurchase PurchaseTaxCodeRef ListID' => 6.0,
		'SalesAndPurchase PurchaseTaxCodeRef FullName' => 6.0,
		'ExternalGUID' => 8.0,
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
		'BarCode BarCodeValue' => ['AU'],
		'BarCode AssignEvenIfUsed' => ['AU'],
		'BarCode AllowOverride' => ['AU'],
		'IsTaxIncluded' => ['US'],
		'SalesAndPurchase PurchaseTaxCodeRef ListID' => ['US'],
		'SalesAndPurchase PurchaseTaxCodeRef FullName' => ['US'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'Name',
		'BarCode',
		'BarCode BarCodeValue',
		'BarCode AssignEvenIfUsed',
		'BarCode AllowOverride',
		'IsActive',
		'ParentRef',
		'ParentRef ListID',
		'ParentRef FullName',
		'ClassRef',
		'ClassRef ListID',
		'ClassRef FullName',
		'ManufacturerPartNumber',
		'UnitOfMeasureSetRef',
		'UnitOfMeasureSetRef ListID',
		'UnitOfMeasureSetRef FullName',
		'IsTaxIncluded',
		'SalesTaxCodeRef',
		'SalesTaxCodeRef ListID',
		'SalesTaxCodeRef FullName',
		'SalesOrPurchase',
		'SalesOrPurchase Desc',
		'SalesOrPurchase Price',
		'SalesOrPurchase PricePercent',
		'SalesOrPurchase AccountRef ListID',
		'SalesOrPurchase AccountRef FullName',
		'SalesAndPurchase',
		'SalesAndPurchase SalesDesc',
		'SalesAndPurchase SalesPrice',
		'SalesAndPurchase IncomeAccountRef ListID',
		'SalesAndPurchase IncomeAccountRef FullName',
		'SalesAndPurchase PurchaseDesc',
		'SalesAndPurchase PurchaseCost',
		'SalesAndPurchase PurchaseTaxCodeRef ListID',
		'SalesAndPurchase PurchaseTaxCodeRef FullName',
		'SalesAndPurchase ExpenseAccountRef ListID',
		'SalesAndPurchase ExpenseAccountRef FullName',
		'SalesAndPurchase PrefVendorRef ListID',
		'SalesAndPurchase PrefVendorRef FullName',
		'ExternalGUID',
		'IncludeRetElement',
	];
}
