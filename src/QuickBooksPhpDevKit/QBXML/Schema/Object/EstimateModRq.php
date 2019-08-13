<?php declare(strict_types=1);

/**
 * Schema object for: EstimateModRq
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
final class EstimateModRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'EstimateMod';

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
		'IsActive' => 'BOOLTYPE',
		'CreateChangeOrder' => 'BOOLTYPE',
		'PONumber' => 'STRTYPE',
		'TermsRef ListID' => 'IDTYPE',
		'TermsRef FullName' => 'STRTYPE',
		'DueDate' => 'DATETYPE',
		'SalesRepRef ListID' => 'IDTYPE',
		'SalesRepRef FullName' => 'STRTYPE',
		'FOB' => 'STRTYPE',
		'ItemSalesTaxRef ListID' => 'IDTYPE',
		'ItemSalesTaxRef FullName' => 'STRTYPE',
		'Memo' => 'STRTYPE',
		'CustomerMsgRef ListID' => 'IDTYPE',
		'CustomerMsgRef FullName' => 'STRTYPE',
		'IsToBeEmailed' => 'BOOLTYPE',
		'IsTaxIncluded' => 'BOOLTYPE',
		'CustomerSalesTaxCodeRef ListID' => 'IDTYPE',
		'CustomerSalesTaxCodeRef FullName' => 'STRTYPE',
		'Other' => 'STRTYPE',
		'ExchangeRate' => 'FLOATTYPE',
		'EstimateLineMod TxnLineID' => 'IDTYPE',
		'EstimateLineMod ItemRef ListID' => 'IDTYPE',
		'EstimateLineMod ItemRef FullName' => 'STRTYPE',
		'EstimateLineMod Desc' => 'STRTYPE',
		'EstimateLineMod Quantity' => 'QUANTYPE',
		'EstimateLineMod UnitOfMeasure' => 'STRTYPE',
		'EstimateLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
		'EstimateLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
		'EstimateLineMod Rate' => 'PRICETYPE',
		'EstimateLineMod RatePercent' => 'PERCENTTYPE',
		'EstimateLineMod ClassRef ListID' => 'IDTYPE',
		'EstimateLineMod ClassRef FullName' => 'STRTYPE',
		'EstimateLineMod Amount' => 'AMTTYPE',
		'EstimateLineMod TaxAmount' => 'AMTTYPE',
		'EstimateLineMod OptionForPriceRuleConflict' => 'ENUMTYPE',
		'EstimateLineMod InventorySiteRef ListID' => 'IDTYPE',
		'EstimateLineMod InventorySiteRef FullName' => 'STRTYPE',
		'EstimateLineMod InventorySiteLocationRef ListID' => 'IDTYPE',
		'EstimateLineMod InventorySiteLocationRef FullName' => 'STRTYPE',
		'EstimateLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
		'EstimateLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
		'EstimateLineMod MarkupRate' => 'PRICETYPE',
		'EstimateLineMod MarkupRatePercent' => 'PERCENTTYPE',
		'EstimateLineMod PriceLevelRef ListID' => 'IDTYPE',
		'EstimateLineMod PriceLevelRef FullName' => 'STRTYPE',
		'EstimateLineMod Other1' => 'STRTYPE',
		'EstimateLineMod Other2' => 'STRTYPE',
		'EstimateLineGroupMod TxnLineID' => 'IDTYPE',
		'EstimateLineGroupMod ItemGroupRef ListID' => 'IDTYPE',
		'EstimateLineGroupMod ItemGroupRef FullName' => 'STRTYPE',
		'EstimateLineGroupMod Quantity' => 'QUANTYPE',
		'EstimateLineGroupMod UnitOfMeasure' => 'STRTYPE',
		'EstimateLineGroupMod OverrideUOMSetRef ListID' => 'IDTYPE',
		'EstimateLineGroupMod OverrideUOMSetRef FullName' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod TxnLineID' => 'IDTYPE',
		'EstimateLineGroupMod EstimateLineMod ItemRef ListID' => 'IDTYPE',
		'EstimateLineGroupMod EstimateLineMod ItemRef FullName' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod Desc' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod Quantity' => 'QUANTYPE',
		'EstimateLineGroupMod EstimateLineMod UnitOfMeasure' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod OverrideUOMSetRef ListID' => 'IDTYPE',
		'EstimateLineGroupMod EstimateLineMod OverrideUOMSetRef FullName' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod Rate' => 'PRICETYPE',
		'EstimateLineGroupMod EstimateLineMod RatePercent' => 'PERCENTTYPE',
		'EstimateLineGroupMod EstimateLineMod ClassRef ListID' => 'IDTYPE',
		'EstimateLineGroupMod EstimateLineMod ClassRef FullName' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod Amount' => 'AMTTYPE',
		'EstimateLineGroupMod EstimateLineMod TaxAmount' => 'AMTTYPE',
		'EstimateLineGroupMod EstimateLineMod OptionForPriceRuleConflict' => 'ENUMTYPE',
		'EstimateLineGroupMod EstimateLineMod InventorySiteRef ListID' => 'IDTYPE',
		'EstimateLineGroupMod EstimateLineMod InventorySiteRef FullName' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod InventorySiteLocationRef ListID' => 'IDTYPE',
		'EstimateLineGroupMod EstimateLineMod InventorySiteLocationRef FullName' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod SalesTaxCodeRef ListID' => 'IDTYPE',
		'EstimateLineGroupMod EstimateLineMod SalesTaxCodeRef FullName' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod MarkupRate' => 'PRICETYPE',
		'EstimateLineGroupMod EstimateLineMod MarkupRatePercent' => 'PERCENTTYPE',
		'EstimateLineGroupMod EstimateLineMod PriceLevelRef ListID' => 'IDTYPE',
		'EstimateLineGroupMod EstimateLineMod PriceLevelRef FullName' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod Other1' => 'STRTYPE',
		'EstimateLineGroupMod EstimateLineMod Other2' => 'STRTYPE',
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
		'ItemSalesTaxRef FullName' => 31,
		'Memo' => 4095,
		'CustomerMsgRef FullName' => 101,
		'CustomerSalesTaxCodeRef FullName' => 3,
		'Other' => 29,
		'EstimateLineMod Desc' => 4095,
		'EstimateLineMod UnitOfMeasure' => 31,
		'EstimateLineMod OverrideUOMSetRef FullName' => 31,
		'EstimateLineMod ClassRef FullName' => 159,
		'EstimateLineMod InventorySiteRef FullName' => 31,
		'EstimateLineMod InventorySiteLocationRef FullName' => 31,
		'EstimateLineMod SalesTaxCodeRef FullName' => 3,
		'EstimateLineMod PriceLevelRef FullName' => 31,
		'EstimateLineMod Other1' => 29,
		'EstimateLineMod Other2' => 29,
		'EstimateLineGroupMod ItemGroupRef FullName' => 31,
		'EstimateLineGroupMod UnitOfMeasure' => 31,
		'EstimateLineGroupMod OverrideUOMSetRef FullName' => 31,
		'EstimateLineGroupMod EstimateLineMod Desc' => 4095,
		'EstimateLineGroupMod EstimateLineMod UnitOfMeasure' => 31,
		'EstimateLineGroupMod EstimateLineMod OverrideUOMSetRef FullName' => 31,
		'EstimateLineGroupMod EstimateLineMod ClassRef FullName' => 159,
		'EstimateLineGroupMod EstimateLineMod InventorySiteRef FullName' => 31,
		'EstimateLineGroupMod EstimateLineMod InventorySiteLocationRef FullName' => 31,
		'EstimateLineGroupMod EstimateLineMod SalesTaxCodeRef FullName' => 3,
		'EstimateLineGroupMod EstimateLineMod PriceLevelRef FullName' => 31,
		'EstimateLineGroupMod EstimateLineMod Other1' => 29,
		'EstimateLineGroupMod EstimateLineMod Other2' => 29,
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
		'ShipAddress Addr1' => 7.0,
		'ShipAddress Addr2' => 7.0,
		'ShipAddress Addr3' => 7.0,
		'ShipAddress Addr4' => 7.0,
		'ShipAddress Addr5' => 7.0,
		'ShipAddress City' => 7.0,
		'ShipAddress State' => 7.0,
		'ShipAddress PostalCode' => 7.0,
		'ShipAddress Country' => 7.0,
		'ShipAddress Note' => 7.0,
		'IsToBeEmailed' => 6.0,
		'IsTaxIncluded' => 6.0,
		'Other' => 6.0,
		'ExchangeRate' => 8.0,
		'EstimateLineMod UnitOfMeasure' => 7.0,
		'EstimateLineMod OverrideUOMSetRef ListID' => 7.0,
		'EstimateLineMod OverrideUOMSetRef FullName' => 7.0,
		'EstimateLineMod TaxAmount' => 6.1,
		'EstimateLineMod OptionForPriceRuleConflict' => 13.0,
		'EstimateLineMod InventorySiteRef ListID' => 10.0,
		'EstimateLineMod InventorySiteRef FullName' => 10.0,
		'EstimateLineMod InventorySiteLocationRef ListID' => 12.0,
		'EstimateLineMod InventorySiteLocationRef FullName' => 12.0,
		'EstimateLineMod PriceLevelRef ListID' => 7.0,
		'EstimateLineMod PriceLevelRef FullName' => 7.0,
		'EstimateLineMod Other1' => 6.0,
		'EstimateLineMod Other2' => 6.0,
		'EstimateLineGroupMod UnitOfMeasure' => 7.0,
		'EstimateLineGroupMod OverrideUOMSetRef ListID' => 7.0,
		'EstimateLineGroupMod OverrideUOMSetRef FullName' => 7.0,
		'EstimateLineGroupMod EstimateLineMod UnitOfMeasure' => 7.0,
		'EstimateLineGroupMod EstimateLineMod OverrideUOMSetRef ListID' => 7.0,
		'EstimateLineGroupMod EstimateLineMod OverrideUOMSetRef FullName' => 7.0,
		'EstimateLineGroupMod EstimateLineMod TaxAmount' => 6.1,
		'EstimateLineGroupMod EstimateLineMod OptionForPriceRuleConflict' => 13.0,
		'EstimateLineGroupMod EstimateLineMod InventorySiteRef ListID' => 10.0,
		'EstimateLineGroupMod EstimateLineMod InventorySiteRef FullName' => 10.0,
		'EstimateLineGroupMod EstimateLineMod InventorySiteLocationRef ListID' => 12.0,
		'EstimateLineGroupMod EstimateLineMod InventorySiteLocationRef FullName' => 12.0,
		'EstimateLineGroupMod EstimateLineMod PriceLevelRef ListID' => 7.0,
		'EstimateLineGroupMod EstimateLineMod PriceLevelRef FullName' => 7.0,
		'EstimateLineGroupMod EstimateLineMod Other1' => 6.0,
		'EstimateLineGroupMod EstimateLineMod Other2' => 6.0,
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
		'EstimateLineMod TaxAmount' => ['US','CA','UK'],
		'EstimateLineMod InventorySiteRef ListID' => ['AU'],
		'EstimateLineMod InventorySiteRef FullName' => ['AU'],
		'EstimateLineMod InventorySiteLocationRef ListID' => ['AU'],
		'EstimateLineMod InventorySiteLocationRef FullName' => ['AU'],
		'EstimateLineGroupMod EstimateLineMod TaxAmount' => ['US','CA','UK'],
		'EstimateLineGroupMod EstimateLineMod InventorySiteRef ListID' => ['AU'],
		'EstimateLineGroupMod EstimateLineMod InventorySiteRef FullName' => ['AU'],
		'EstimateLineGroupMod EstimateLineMod InventorySiteLocationRef ListID' => ['AU'],
		'EstimateLineGroupMod EstimateLineMod InventorySiteLocationRef FullName' => ['AU'],
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
		'IsActive',
		'CreateChangeOrder',
		'PONumber',
		'TermsRef',
		'TermsRef ListID',
		'TermsRef FullName',
		'DueDate',
		'SalesRepRef',
		'SalesRepRef ListID',
		'SalesRepRef FullName',
		'FOB',
		'ItemSalesTaxRef',
		'ItemSalesTaxRef ListID',
		'ItemSalesTaxRef FullName',
		'Memo',
		'CustomerMsgRef',
		'CustomerMsgRef ListID',
		'CustomerMsgRef FullName',
		'IsToBeEmailed',
		'IsTaxIncluded',
		'CustomerSalesTaxCodeRef',
		'CustomerSalesTaxCodeRef ListID',
		'CustomerSalesTaxCodeRef FullName',
		'Other',
		'ExchangeRate',
		'EstimateLineMod',
		'EstimateLineMod TxnLineID',
		'EstimateLineMod ItemRef ListID',
		'EstimateLineMod ItemRef FullName',
		'EstimateLineMod Desc',
		'EstimateLineMod Quantity',
		'EstimateLineMod UnitOfMeasure',
		'EstimateLineMod OverrideUOMSetRef ListID',
		'EstimateLineMod OverrideUOMSetRef FullName',
		'EstimateLineMod Rate',
		'EstimateLineMod RatePercent',
		'EstimateLineMod ClassRef ListID',
		'EstimateLineMod ClassRef FullName',
		'EstimateLineMod Amount',
		'EstimateLineMod TaxAmount',
		'EstimateLineMod OptionForPriceRuleConflict',
		'EstimateLineMod InventorySiteRef ListID',
		'EstimateLineMod InventorySiteRef FullName',
		'EstimateLineMod InventorySiteLocationRef ListID',
		'EstimateLineMod InventorySiteLocationRef FullName',
		'EstimateLineMod SalesTaxCodeRef ListID',
		'EstimateLineMod SalesTaxCodeRef FullName',
		'EstimateLineMod MarkupRate',
		'EstimateLineMod MarkupRatePercent',
		'EstimateLineMod PriceLevelRef ListID',
		'EstimateLineMod PriceLevelRef FullName',
		'EstimateLineMod Other1',
		'EstimateLineMod Other2',
		'EstimateLineGroupMod',
		'EstimateLineGroupMod TxnLineID',
		'EstimateLineGroupMod ItemGroupRef ListID',
		'EstimateLineGroupMod ItemGroupRef FullName',
		'EstimateLineGroupMod Quantity',
		'EstimateLineGroupMod UnitOfMeasure',
		'EstimateLineGroupMod OverrideUOMSetRef ListID',
		'EstimateLineGroupMod OverrideUOMSetRef FullName',
		'EstimateLineGroupMod EstimateLineMod TxnLineID',
		'EstimateLineGroupMod EstimateLineMod ItemRef ListID',
		'EstimateLineGroupMod EstimateLineMod ItemRef FullName',
		'EstimateLineGroupMod EstimateLineMod Desc',
		'EstimateLineGroupMod EstimateLineMod Quantity',
		'EstimateLineGroupMod EstimateLineMod UnitOfMeasure',
		'EstimateLineGroupMod EstimateLineMod OverrideUOMSetRef ListID',
		'EstimateLineGroupMod EstimateLineMod OverrideUOMSetRef FullName',
		'EstimateLineGroupMod EstimateLineMod Rate',
		'EstimateLineGroupMod EstimateLineMod RatePercent',
		'EstimateLineGroupMod EstimateLineMod ClassRef ListID',
		'EstimateLineGroupMod EstimateLineMod ClassRef FullName',
		'EstimateLineGroupMod EstimateLineMod Amount',
		'EstimateLineGroupMod EstimateLineMod TaxAmount',
		'EstimateLineGroupMod EstimateLineMod OptionForPriceRuleConflict',
		'EstimateLineGroupMod EstimateLineMod InventorySiteRef ListID',
		'EstimateLineGroupMod EstimateLineMod InventorySiteRef FullName',
		'EstimateLineGroupMod EstimateLineMod InventorySiteLocationRef ListID',
		'EstimateLineGroupMod EstimateLineMod InventorySiteLocationRef FullName',
		'EstimateLineGroupMod EstimateLineMod SalesTaxCodeRef ListID',
		'EstimateLineGroupMod EstimateLineMod SalesTaxCodeRef FullName',
		'EstimateLineGroupMod EstimateLineMod MarkupRate',
		'EstimateLineGroupMod EstimateLineMod MarkupRatePercent',
		'EstimateLineGroupMod EstimateLineMod PriceLevelRef ListID',
		'EstimateLineGroupMod EstimateLineMod PriceLevelRef FullName',
		'EstimateLineGroupMod EstimateLineMod Other1',
		'EstimateLineGroupMod EstimateLineMod Other2',
		'IncludeRetElement',
	];
}
