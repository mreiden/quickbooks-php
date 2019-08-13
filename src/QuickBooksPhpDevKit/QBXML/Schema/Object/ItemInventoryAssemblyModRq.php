<?php declare(strict_types=1);

/**
 * Schema object for: ItemInventoryAssemblyModRq
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
final class ItemInventoryAssemblyModRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'ItemInventoryAssemblyMod';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
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
		'PrefVendorRef ListID' => 'IDTYPE',
		'PrefVendorRef FullName' => 'STRTYPE',
		'AssetAccountRef ListID' => 'IDTYPE',
		'AssetAccountRef FullName' => 'STRTYPE',
		'BuildPoint' => 'QUANTYPE',
		'Max' => 'QUANTYPE',
		'ClearItemsInGroup' => 'BOOLTYPE',
		'ItemInventoryAssemblyLine ItemInventoryRef ListID' => 'IDTYPE',
		'ItemInventoryAssemblyLine ItemInventoryRef FullName' => 'STRTYPE',
		'ItemInventoryAssemblyLine Quantity' => 'QUANTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'EditSequence' => 16,
		'Name' => 31,
		'BarCode BarCodeValue' => 50,
		'ClassRef FullName' => 159,
		'ManufacturerPartNumber' => 31,
		'UnitOfMeasureSetRef FullName' => 31,
		'SalesTaxCodeRef FullName' => 3,
		'SalesDesc' => 4095,
		'IncomeAccountRef FullName' => 159,
		'PurchaseDesc' => 4095,
		'PurchaseTaxCodeRef FullName' => 3,
		'COGSAccountRef FullName' => 159,
		'PrefVendorRef FullName' => 41,
		'AssetAccountRef FullName' => 159,
		'ItemInventoryAssemblyLine ItemInventoryRef FullName' => 159,
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
		'ManufacturerPartNumber' => 12.0,
		'UnitOfMeasureSetRef ListID' => 7.0,
		'UnitOfMeasureSetRef FullName' => 7.0,
		'ForceUOMChange' => 7.0,
		'IsTaxIncluded' => 6.0,
		'IncomeAccountRef ListID' => 7.0,
		'IncomeAccountRef FullName' => 7.0,
		'ApplyIncomeAccountRefToExistingTxns' => 7.0,
		'PurchaseTaxCodeRef ListID' => 6.0,
		'PurchaseTaxCodeRef FullName' => 6.0,
		'Max' => 13.0,
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
		'PurchaseTaxCodeRef ListID' => ['US'],
		'PurchaseTaxCodeRef FullName' => ['US'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
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
		'PrefVendorRef',
		'PrefVendorRef ListID',
		'PrefVendorRef FullName',
		'AssetAccountRef',
		'AssetAccountRef ListID',
		'AssetAccountRef FullName',
		'BuildPoint',
		'Max',
		'ClearItemsInGroup',
		'ItemInventoryAssemblyLine',
		'ItemInventoryAssemblyLine ItemInventoryRef',
		'ItemInventoryAssemblyLine ItemInventoryRef ListID',
		'ItemInventoryAssemblyLine ItemInventoryRef FullName',
		'ItemInventoryAssemblyLine Quantity',
		'IncludeRetElement',
	];
}
