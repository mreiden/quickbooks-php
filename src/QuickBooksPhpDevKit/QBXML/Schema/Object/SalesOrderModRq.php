<?php declare(strict_types=1);

/**
 * Schema object for: SalesOrderModRq
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
final class SalesOrderModRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'SalesOrderMod';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'TxnID' => 'IDTYPE',
		'EditSequence' => 'STRTYPE',
		'CustomerRef ListID' => 'IDTYPE',
		'CustomerRef FullName' => 'STRTYPE',
		'ClassRef ListID' => 'IDTYPE',
		'ClassRef FullName' => 'STRTYPE',
		'TemplateRef ListID' => 'IDTYPE',
		'TemplateRef FullName' => 'STRTYPE',
		'TxnDate' => 'DATETYPE',
		'RefNumber' => 'STRTYPE',
		'BillAddress Addr1' => 'STRTYPE',
		'BillAddress Addr2' => 'STRTYPE',
		'BillAddress Addr3' => 'STRTYPE',
		'BillAddress Addr4' => 'STRTYPE',
		'BillAddress Addr5' => 'STRTYPE',
		'BillAddress City' => 'STRTYPE',
		'BillAddress State' => 'STRTYPE',
		'BillAddress PostalCode' => 'STRTYPE',
		'BillAddress Country' => 'STRTYPE',
		'BillAddress Note' => 'STRTYPE',
		'ShipAddress Addr1' => 'STRTYPE',
		'ShipAddress Addr2' => 'STRTYPE',
		'ShipAddress Addr3' => 'STRTYPE',
		'ShipAddress Addr4' => 'STRTYPE',
		'ShipAddress Addr5' => 'STRTYPE',
		'ShipAddress City' => 'STRTYPE',
		'ShipAddress State' => 'STRTYPE',
		'ShipAddress PostalCode' => 'STRTYPE',
		'ShipAddress Country' => 'STRTYPE',
		'ShipAddress Note' => 'STRTYPE',
		'PONumber' => 'STRTYPE',
		'TermsRef ListID' => 'IDTYPE',
		'TermsRef FullName' => 'STRTYPE',
		'DueDate' => 'DATETYPE',
		'SalesRepRef ListID' => 'IDTYPE',
		'SalesRepRef FullName' => 'STRTYPE',
		'FOB' => 'STRTYPE',
		'ShipDate' => 'DATETYPE',
		'ShipMethodRef ListID' => 'IDTYPE',
		'ShipMethodRef FullName' => 'STRTYPE',
		'ItemSalesTaxRef ListID' => 'IDTYPE',
		'ItemSalesTaxRef FullName' => 'STRTYPE',
		'IsManuallyClosed' => 'BOOLTYPE',
		'Memo' => 'STRTYPE',
		'CustomerMsgRef ListID' => 'IDTYPE',
		'CustomerMsgRef FullName' => 'STRTYPE',
		'IsToBePrinted' => 'BOOLTYPE',
		'IsToBeEmailed' => 'BOOLTYPE',
		'IsTaxIncluded' => 'BOOLTYPE',
		'CustomerSalesTaxCodeRef ListID' => 'IDTYPE',
		'CustomerSalesTaxCodeRef FullName' => 'STRTYPE',
		'Other' => 'STRTYPE',
		'ExchangeRate' => 'FLOATTYPE',
		'SalesOrderLineMod TxnLineID' => 'IDTYPE',
		'SalesOrderLineMod ItemRef ListID' => 'IDTYPE',
		'SalesOrderLineMod ItemRef FullName' => 'STRTYPE',
		'SalesOrderLineMod Desc' => 'STRTYPE',
		'SalesOrderLineMod Quantity' => 'QUANTYPE',
		'SalesOrderLineMod UnitOfMeasure' => 'STRTYPE',
		'SalesOrderLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
		'SalesOrderLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
		'SalesOrderLineMod Rate' => 'PRICETYPE',
		'SalesOrderLineMod RatePercent' => 'PERCENTTYPE',
		'SalesOrderLineMod PriceLevelRef ListID' => 'IDTYPE',
		'SalesOrderLineMod PriceLevelRef FullName' => 'STRTYPE',
		'SalesOrderLineMod ClassRef ListID' => 'IDTYPE',
		'SalesOrderLineMod ClassRef FullName' => 'STRTYPE',
		'SalesOrderLineMod Amount' => 'AMTTYPE',
		'SalesOrderLineMod TaxAmount' => 'AMTTYPE',
		'SalesOrderLineMod OptionForPriceRuleConflict' => 'ENUMTYPE',
		'SalesOrderLineMod InventorySiteRef ListID' => 'IDTYPE',
		'SalesOrderLineMod InventorySiteRef FullName' => 'STRTYPE',
		'SalesOrderLineMod InventorySiteLocationRef ListID' => 'IDTYPE',
		'SalesOrderLineMod InventorySiteLocationRef FullName' => 'STRTYPE',
		'SalesOrderLineMod SerialNumber' => 'STRTYPE',
		'SalesOrderLineMod LotNumber' => 'STRTYPE',
		'SalesOrderLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
		'SalesOrderLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
		'SalesOrderLineMod IsManuallyClosed' => 'BOOLTYPE',
		'SalesOrderLineMod Other1' => 'STRTYPE',
		'SalesOrderLineMod Other2' => 'STRTYPE',
		'SalesOrderLineGroupMod TxnLineID' => 'IDTYPE',
		'SalesOrderLineGroupMod ItemGroupRef ListID' => 'IDTYPE',
		'SalesOrderLineGroupMod ItemGroupRef FullName' => 'STRTYPE',
		'SalesOrderLineGroupMod Quantity' => 'QUANTYPE',
		'SalesOrderLineGroupMod UnitOfMeasure' => 'STRTYPE',
		'SalesOrderLineGroupMod OverrideUOMSetRef ListID' => 'IDTYPE',
		'SalesOrderLineGroupMod OverrideUOMSetRef FullName' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod TxnLineID' => 'IDTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod ItemRef ListID' => 'IDTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod ItemRef FullName' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod Desc' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod Quantity' => 'QUANTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod UnitOfMeasure' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod Rate' => 'PRICETYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod RatePercent' => 'PERCENTTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod PriceLevelRef ListID' => 'IDTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod PriceLevelRef FullName' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod ClassRef ListID' => 'IDTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod ClassRef FullName' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod Amount' => 'AMTTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod TaxAmount' => 'AMTTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod OptionForPriceRuleConflict' => 'ENUMTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteRef ListID' => 'IDTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteRef FullName' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteLocationRef ListID' => 'IDTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteLocationRef FullName' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod SerialNumber' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod LotNumber' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod IsManuallyClosed' => 'BOOLTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod Other1' => 'STRTYPE',
		'SalesOrderLineGroupMod SalesOrderLineMod Other2' => 'STRTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'EditSequence' => 16,
		'CustomerRef FullName' => 209,
		'ClassRef FullName' => 159,
		'TemplateRef FullName' => 31,
		'RefNumber' => 11,
		'BillAddress Addr1' => 41,
		'BillAddress Addr2' => 41,
		'BillAddress Addr3' => 41,
		'BillAddress Addr4' => 41,
		'BillAddress Addr5' => 41,
		'BillAddress City' => 31,
		'BillAddress State' => 21,
		'BillAddress PostalCode' => 13,
		'BillAddress Country' => 31,
		'BillAddress Note' => 41,
		'ShipAddress Addr1' => 41,
		'ShipAddress Addr2' => 41,
		'ShipAddress Addr3' => 41,
		'ShipAddress Addr4' => 41,
		'ShipAddress Addr5' => 41,
		'ShipAddress City' => 31,
		'ShipAddress State' => 21,
		'ShipAddress PostalCode' => 13,
		'ShipAddress Country' => 31,
		'ShipAddress Note' => 41,
		'PONumber' => 25,
		'TermsRef FullName' => 31,
		'SalesRepRef FullName' => 5,
		'FOB' => 13,
		'ShipMethodRef FullName' => 15,
		'ItemSalesTaxRef FullName' => 31,
		'Memo' => 4095,
		'CustomerMsgRef FullName' => 101,
		'CustomerSalesTaxCodeRef FullName' => 3,
		'Other' => 29,
		'SalesOrderLineMod Desc' => 4095,
		'SalesOrderLineMod UnitOfMeasure' => 31,
		'SalesOrderLineMod OverrideUOMSetRef FullName' => 31,
		'SalesOrderLineMod PriceLevelRef FullName' => 31,
		'SalesOrderLineMod ClassRef FullName' => 159,
		'SalesOrderLineMod InventorySiteRef FullName' => 31,
		'SalesOrderLineMod InventorySiteLocationRef FullName' => 31,
		'SalesOrderLineMod SerialNumber' => 4095,
		'SalesOrderLineMod LotNumber' => 40,
		'SalesOrderLineMod SalesTaxCodeRef FullName' => 3,
		'SalesOrderLineMod Other1' => 29,
		'SalesOrderLineMod Other2' => 29,
		'SalesOrderLineGroupMod ItemGroupRef FullName' => 31,
		'SalesOrderLineGroupMod UnitOfMeasure' => 31,
		'SalesOrderLineGroupMod OverrideUOMSetRef FullName' => 31,
		'SalesOrderLineGroupMod SalesOrderLineMod Desc' => 4095,
		'SalesOrderLineGroupMod SalesOrderLineMod UnitOfMeasure' => 31,
		'SalesOrderLineGroupMod SalesOrderLineMod OverrideUOMSetRef FullName' => 31,
		'SalesOrderLineGroupMod SalesOrderLineMod PriceLevelRef FullName' => 31,
		'SalesOrderLineGroupMod SalesOrderLineMod ClassRef FullName' => 159,
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteRef FullName' => 31,
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteLocationRef FullName' => 31,
		'SalesOrderLineGroupMod SalesOrderLineMod SerialNumber' => 4095,
		'SalesOrderLineGroupMod SalesOrderLineMod LotNumber' => 40,
		'SalesOrderLineGroupMod SalesOrderLineMod SalesTaxCodeRef FullName' => 3,
		'SalesOrderLineGroupMod SalesOrderLineMod Other1' => 29,
		'SalesOrderLineGroupMod SalesOrderLineMod Other2' => 29,
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
		'BillAddress Addr4' => 2.0,
		'BillAddress Addr5' => 6.0,
		'BillAddress Note' => 6.0,
		'ShipAddress Addr4' => 2.0,
		'ShipAddress Addr5' => 6.0,
		'ShipAddress Note' => 6.0,
		'IsToBeEmailed' => 6.0,
		'IsTaxIncluded' => 6.0,
		'Other' => 6.0,
		'ExchangeRate' => 8.0,
		'SalesOrderLineMod UnitOfMeasure' => 7.0,
		'SalesOrderLineMod OverrideUOMSetRef ListID' => 7.0,
		'SalesOrderLineMod OverrideUOMSetRef FullName' => 7.0,
		'SalesOrderLineMod PriceLevelRef ListID' => 4.0,
		'SalesOrderLineMod PriceLevelRef FullName' => 4.0,
		'SalesOrderLineMod TaxAmount' => 6.1,
		'SalesOrderLineMod OptionForPriceRuleConflict' => 13.0,
		'SalesOrderLineMod InventorySiteRef ListID' => 10.0,
		'SalesOrderLineMod InventorySiteRef FullName' => 10.0,
		'SalesOrderLineMod InventorySiteLocationRef ListID' => 12.0,
		'SalesOrderLineMod InventorySiteLocationRef FullName' => 12.0,
		'SalesOrderLineMod Other1' => 6.0,
		'SalesOrderLineMod Other2' => 6.0,
		'SalesOrderLineGroupMod UnitOfMeasure' => 7.0,
		'SalesOrderLineGroupMod OverrideUOMSetRef ListID' => 7.0,
		'SalesOrderLineGroupMod OverrideUOMSetRef FullName' => 7.0,
		'SalesOrderLineGroupMod SalesOrderLineMod UnitOfMeasure' => 7.0,
		'SalesOrderLineGroupMod SalesOrderLineMod OverrideUOMSetRef ListID' => 7.0,
		'SalesOrderLineGroupMod SalesOrderLineMod OverrideUOMSetRef FullName' => 7.0,
		'SalesOrderLineGroupMod SalesOrderLineMod PriceLevelRef ListID' => 4.0,
		'SalesOrderLineGroupMod SalesOrderLineMod PriceLevelRef FullName' => 4.0,
		'SalesOrderLineGroupMod SalesOrderLineMod TaxAmount' => 6.1,
		'SalesOrderLineGroupMod SalesOrderLineMod OptionForPriceRuleConflict' => 13.0,
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteRef ListID' => 10.0,
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteRef FullName' => 10.0,
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteLocationRef ListID' => 12.0,
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteLocationRef FullName' => 12.0,
		'SalesOrderLineGroupMod SalesOrderLineMod Other1' => 6.0,
		'SalesOrderLineGroupMod SalesOrderLineMod Other2' => 6.0,
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
		'SalesOrderLineMod TaxAmount' => ['US','CA','UK'],
		'SalesOrderLineMod InventorySiteRef ListID' => ['AU'],
		'SalesOrderLineMod InventorySiteRef FullName' => ['AU'],
		'SalesOrderLineMod InventorySiteLocationRef ListID' => ['AU'],
		'SalesOrderLineMod InventorySiteLocationRef FullName' => ['AU'],
		'SalesOrderLineMod SerialNumber' => ['AU'],
		'SalesOrderLineMod LotNumber' => ['AU'],
		'SalesOrderLineGroupMod SalesOrderLineMod TaxAmount' => ['US','CA','UK'],
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteRef ListID' => ['AU'],
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteRef FullName' => ['AU'],
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteLocationRef ListID' => ['AU'],
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteLocationRef FullName' => ['AU'],
		'SalesOrderLineGroupMod SalesOrderLineMod SerialNumber' => ['AU'],
		'SalesOrderLineGroupMod SalesOrderLineMod LotNumber' => ['AU'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'TxnID',
		'EditSequence',
		'CustomerRef',
		'CustomerRef ListID',
		'CustomerRef FullName',
		'ClassRef',
		'ClassRef ListID',
		'ClassRef FullName',
		'TemplateRef',
		'TemplateRef ListID',
		'TemplateRef FullName',
		'TxnDate',
		'RefNumber',
		'BillAddress',
		'BillAddress Addr1',
		'BillAddress Addr2',
		'BillAddress Addr3',
		'BillAddress Addr4',
		'BillAddress Addr5',
		'BillAddress City',
		'BillAddress State',
		'BillAddress PostalCode',
		'BillAddress Country',
		'BillAddress Note',
		'ShipAddress',
		'ShipAddress Addr1',
		'ShipAddress Addr2',
		'ShipAddress Addr3',
		'ShipAddress Addr4',
		'ShipAddress Addr5',
		'ShipAddress City',
		'ShipAddress State',
		'ShipAddress PostalCode',
		'ShipAddress Country',
		'ShipAddress Note',
		'PONumber',
		'TermsRef',
		'TermsRef ListID',
		'TermsRef FullName',
		'DueDate',
		'SalesRepRef',
		'SalesRepRef ListID',
		'SalesRepRef FullName',
		'FOB',
		'ShipDate',
		'ShipMethodRef',
		'ShipMethodRef ListID',
		'ShipMethodRef FullName',
		'ItemSalesTaxRef',
		'ItemSalesTaxRef ListID',
		'ItemSalesTaxRef FullName',
		'IsManuallyClosed',
		'Memo',
		'CustomerMsgRef',
		'CustomerMsgRef ListID',
		'CustomerMsgRef FullName',
		'IsToBePrinted',
		'IsToBeEmailed',
		'IsTaxIncluded',
		'CustomerSalesTaxCodeRef',
		'CustomerSalesTaxCodeRef ListID',
		'CustomerSalesTaxCodeRef FullName',
		'Other',
		'ExchangeRate',
		'SalesOrderLineMod',
		'SalesOrderLineMod TxnLineID',
		'SalesOrderLineMod ItemRef ListID',
		'SalesOrderLineMod ItemRef FullName',
		'SalesOrderLineMod Desc',
		'SalesOrderLineMod Quantity',
		'SalesOrderLineMod UnitOfMeasure',
		'SalesOrderLineMod OverrideUOMSetRef ListID',
		'SalesOrderLineMod OverrideUOMSetRef FullName',
		'SalesOrderLineMod Rate',
		'SalesOrderLineMod RatePercent',
		'SalesOrderLineMod PriceLevelRef ListID',
		'SalesOrderLineMod PriceLevelRef FullName',
		'SalesOrderLineMod ClassRef ListID',
		'SalesOrderLineMod ClassRef FullName',
		'SalesOrderLineMod Amount',
		'SalesOrderLineMod TaxAmount',
		'SalesOrderLineMod OptionForPriceRuleConflict',
		'SalesOrderLineMod InventorySiteRef ListID',
		'SalesOrderLineMod InventorySiteRef FullName',
		'SalesOrderLineMod InventorySiteLocationRef ListID',
		'SalesOrderLineMod InventorySiteLocationRef FullName',
		'SalesOrderLineMod SerialNumber',
		'SalesOrderLineMod LotNumber',
		'SalesOrderLineMod SalesTaxCodeRef ListID',
		'SalesOrderLineMod SalesTaxCodeRef FullName',
		'SalesOrderLineMod IsManuallyClosed',
		'SalesOrderLineMod Other1',
		'SalesOrderLineMod Other2',
		'SalesOrderLineGroupMod',
		'SalesOrderLineGroupMod TxnLineID',
		'SalesOrderLineGroupMod ItemGroupRef ListID',
		'SalesOrderLineGroupMod ItemGroupRef FullName',
		'SalesOrderLineGroupMod Quantity',
		'SalesOrderLineGroupMod UnitOfMeasure',
		'SalesOrderLineGroupMod OverrideUOMSetRef ListID',
		'SalesOrderLineGroupMod OverrideUOMSetRef FullName',
		'SalesOrderLineGroupMod SalesOrderLineMod TxnLineID',
		'SalesOrderLineGroupMod SalesOrderLineMod ItemRef ListID',
		'SalesOrderLineGroupMod SalesOrderLineMod ItemRef FullName',
		'SalesOrderLineGroupMod SalesOrderLineMod Desc',
		'SalesOrderLineGroupMod SalesOrderLineMod Quantity',
		'SalesOrderLineGroupMod SalesOrderLineMod UnitOfMeasure',
		'SalesOrderLineGroupMod SalesOrderLineMod OverrideUOMSetRef ListID',
		'SalesOrderLineGroupMod SalesOrderLineMod OverrideUOMSetRef FullName',
		'SalesOrderLineGroupMod SalesOrderLineMod Rate',
		'SalesOrderLineGroupMod SalesOrderLineMod RatePercent',
		'SalesOrderLineGroupMod SalesOrderLineMod PriceLevelRef ListID',
		'SalesOrderLineGroupMod SalesOrderLineMod PriceLevelRef FullName',
		'SalesOrderLineGroupMod SalesOrderLineMod ClassRef ListID',
		'SalesOrderLineGroupMod SalesOrderLineMod ClassRef FullName',
		'SalesOrderLineGroupMod SalesOrderLineMod Amount',
		'SalesOrderLineGroupMod SalesOrderLineMod TaxAmount',
		'SalesOrderLineGroupMod SalesOrderLineMod OptionForPriceRuleConflict',
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteRef ListID',
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteRef FullName',
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteLocationRef ListID',
		'SalesOrderLineGroupMod SalesOrderLineMod InventorySiteLocationRef FullName',
		'SalesOrderLineGroupMod SalesOrderLineMod SerialNumber',
		'SalesOrderLineGroupMod SalesOrderLineMod LotNumber',
		'SalesOrderLineGroupMod SalesOrderLineMod SalesTaxCodeRef ListID',
		'SalesOrderLineGroupMod SalesOrderLineMod SalesTaxCodeRef FullName',
		'SalesOrderLineGroupMod SalesOrderLineMod IsManuallyClosed',
		'SalesOrderLineGroupMod SalesOrderLineMod Other1',
		'SalesOrderLineGroupMod SalesOrderLineMod Other2',
		'IncludeRetElement',
	];
}
