<?php declare(strict_types=1);

/**
 * Schema object for: CreditMemoAddRq
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
final class CreditMemoAddRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'CreditMemoAdd';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'CustomerRef ListID' => 'IDTYPE',
		'CustomerRef FullName' => 'STRTYPE',
		'ClassRef ListID' => 'IDTYPE',
		'ClassRef FullName' => 'STRTYPE',
		'ARAccountRef ListID' => 'IDTYPE',
		'ARAccountRef FullName' => 'STRTYPE',
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
		'IsPending' => 'BOOLTYPE',
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
		'ExternalGUID' => 'GUIDTYPE',
		'CreditMemoLineAdd ItemRef ListID' => 'IDTYPE',
		'CreditMemoLineAdd ItemRef FullName' => 'STRTYPE',
		'CreditMemoLineAdd Desc' => 'STRTYPE',
		'CreditMemoLineAdd Quantity' => 'QUANTYPE',
		'CreditMemoLineAdd UnitOfMeasure' => 'STRTYPE',
		'CreditMemoLineAdd Rate' => 'PRICETYPE',
		'CreditMemoLineAdd RatePercent' => 'PERCENTTYPE',
		'CreditMemoLineAdd PriceLevelRef ListID' => 'IDTYPE',
		'CreditMemoLineAdd PriceLevelRef FullName' => 'STRTYPE',
		'CreditMemoLineAdd ClassRef ListID' => 'IDTYPE',
		'CreditMemoLineAdd ClassRef FullName' => 'STRTYPE',
		'CreditMemoLineAdd Amount' => 'AMTTYPE',
		'CreditMemoLineAdd TaxAmount' => 'AMTTYPE',
		'CreditMemoLineAdd InventorySiteRef ListID' => 'IDTYPE',
		'CreditMemoLineAdd InventorySiteRef FullName' => 'STRTYPE',
		'CreditMemoLineAdd InventorySiteLocationRef ListID' => 'IDTYPE',
		'CreditMemoLineAdd InventorySiteLocationRef FullName' => 'STRTYPE',
		'CreditMemoLineAdd SerialNumber' => 'STRTYPE',
		'CreditMemoLineAdd LotNumber' => 'STRTYPE',
		'CreditMemoLineAdd ServiceDate' => 'DATETYPE',
		'CreditMemoLineAdd SalesTaxCodeRef ListID' => 'IDTYPE',
		'CreditMemoLineAdd SalesTaxCodeRef FullName' => 'STRTYPE',
		'CreditMemoLineAdd IsTaxable' => 'BOOLTYPE',
		'CreditMemoLineAdd OverrideItemAccountRef ListID' => 'IDTYPE',
		'CreditMemoLineAdd OverrideItemAccountRef FullName' => 'STRTYPE',
		'CreditMemoLineAdd Other1' => 'STRTYPE',
		'CreditMemoLineAdd Other2' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardNumber' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo ExpirationMonth' => 'INTTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo ExpirationYear' => 'INTTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo NameOnCard' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardAddress' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardPostalCode' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CommercialCardCode' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo TransactionMode' => 'ENUMTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardTxnType' => 'ENUMTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ResultCode' => 'INTTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ResultMessage' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo CreditCardTransID' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo MerchantAccountNumber' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AuthorizationCode' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AVSStreet' => 'ENUMTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AVSZip' => 'ENUMTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo CardSecurityCodeMatch' => 'ENUMTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ReconBatchID' => 'STRTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo PaymentGroupingCode' => 'INTTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo PaymentStatus' => 'ENUMTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo TxnAuthorizationTime' => 'DATETIMETYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo TxnAuthorizationStamp' => 'INTTYPE',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ClientTransID' => 'STRTYPE',
		'CreditMemoLineAdd DataExt OwnerID' => 'GUIDTYPE',
		'CreditMemoLineAdd DataExt DataExtName' => 'STRTYPE',
		'CreditMemoLineAdd DataExt DataExtValue' => 'STRTYPE',
		'CreditMemoLineGroupAdd ItemGroupRef ListID' => 'IDTYPE',
		'CreditMemoLineGroupAdd ItemGroupRef FullName' => 'STRTYPE',
		'CreditMemoLineGroupAdd Desc' => 'STRTYPE',
		'CreditMemoLineGroupAdd Quantity' => 'QUANTYPE',
		'CreditMemoLineGroupAdd UnitOfMeasure' => 'STRTYPE',
		'CreditMemoLineGroupAdd ServiceDate' => 'DATETYPE',
		'CreditMemoLineGroupAdd InventorySiteRef ListID' => 'IDTYPE',
		'CreditMemoLineGroupAdd InventorySiteRef FullName' => 'STRTYPE',
		'CreditMemoLineGroupAdd InventorySiteLocationRef ListID' => 'IDTYPE',
		'CreditMemoLineGroupAdd InventorySiteLocationRef FullName' => 'STRTYPE',
		'CreditMemoLineGroupAdd DataExt OwnerID' => 'GUIDTYPE',
		'CreditMemoLineGroupAdd DataExt DataExtName' => 'STRTYPE',
		'CreditMemoLineGroupAdd DataExt DataExtValue' => 'STRTYPE',
		'DiscountLineAdd Amount' => 'AMTTYPE',
		'DiscountLineAdd RatePercent' => 'PERCENTTYPE',
		'DiscountLineAdd IsTaxable' => 'BOOLTYPE',
		'DiscountLineAdd AccountRef ListID' => 'IDTYPE',
		'DiscountLineAdd AccountRef FullName' => 'STRTYPE',
		'SalesTaxLineAdd Amount' => 'AMTTYPE',
		'SalesTaxLineAdd RatePercent' => 'PERCENTTYPE',
		'SalesTaxLineAdd AccountRef ListID' => 'IDTYPE',
		'SalesTaxLineAdd AccountRef FullName' => 'STRTYPE',
		'ShippingLineAdd Amount' => 'AMTTYPE',
		'ShippingLineAdd AccountRef ListID' => 'IDTYPE',
		'ShippingLineAdd AccountRef FullName' => 'STRTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'CustomerRef FullName' => 209,
		'ClassRef FullName' => 159,
		'ARAccountRef FullName' => 159,
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
		'CreditMemoLineAdd Desc' => 4095,
		'CreditMemoLineAdd UnitOfMeasure' => 31,
		'CreditMemoLineAdd PriceLevelRef FullName' => 31,
		'CreditMemoLineAdd ClassRef FullName' => 159,
		'CreditMemoLineAdd InventorySiteRef FullName' => 31,
		'CreditMemoLineAdd InventorySiteLocationRef FullName' => 31,
		'CreditMemoLineAdd SerialNumber' => 4095,
		'CreditMemoLineAdd LotNumber' => 40,
		'CreditMemoLineAdd SalesTaxCodeRef FullName' => 3,
		'CreditMemoLineAdd OverrideItemAccountRef FullName' => 159,
		'CreditMemoLineAdd Other1' => 29,
		'CreditMemoLineAdd Other2' => 29,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardNumber' => 25,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo NameOnCard' => 41,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardAddress' => 41,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardPostalCode' => 18,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CommercialCardCode' => 24,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ResultMessage' => 60,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo CreditCardTransID' => 24,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo MerchantAccountNumber' => 32,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AuthorizationCode' => 12,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ReconBatchID' => 84,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ClientTransID' => 16,
		'CreditMemoLineAdd DataExt DataExtName' => 31,
		'CreditMemoLineGroupAdd ItemGroupRef FullName' => 31,
		'CreditMemoLineGroupAdd UnitOfMeasure' => 31,
		'CreditMemoLineGroupAdd InventorySiteRef FullName' => 31,
		'CreditMemoLineGroupAdd InventorySiteLocationRef FullName' => 31,
		'CreditMemoLineGroupAdd DataExt DataExtName' => 31,
		'DiscountLineAdd AccountRef FullName' => 1000,
		'SalesTaxLineAdd AccountRef FullName' => 1000,
		'ShippingLineAdd AccountRef FullName' => 1000,
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
		'TemplateRef ListID' => 3.0,
		'TemplateRef FullName' => 3.0,
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
		'ExternalGUID' => 9.0,
		'CreditMemoLineAdd UnitOfMeasure' => 7.0,
		'CreditMemoLineAdd PriceLevelRef ListID' => 4.0,
		'CreditMemoLineAdd PriceLevelRef FullName' => 4.0,
		'CreditMemoLineAdd TaxAmount' => 6.1,
		'CreditMemoLineAdd InventorySiteRef ListID' => 10.0,
		'CreditMemoLineAdd InventorySiteRef FullName' => 10.0,
		'CreditMemoLineAdd InventorySiteLocationRef ListID' => 12.0,
		'CreditMemoLineAdd InventorySiteLocationRef FullName' => 12.0,
		'CreditMemoLineAdd IsTaxable' => 4.0,
		'CreditMemoLineAdd OverrideItemAccountRef ListID' => 2.0,
		'CreditMemoLineAdd OverrideItemAccountRef FullName' => 2.0,
		'CreditMemoLineAdd Other1' => 6.0,
		'CreditMemoLineAdd Other2' => 6.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardNumber' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo ExpirationMonth' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo ExpirationYear' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo NameOnCard' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardAddress' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardPostalCode' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CommercialCardCode' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo TransactionMode' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardTxnType' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ResultCode' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ResultMessage' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo CreditCardTransID' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo MerchantAccountNumber' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AuthorizationCode' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AVSStreet' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AVSZip' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo CardSecurityCodeMatch' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ReconBatchID' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo PaymentGroupingCode' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo PaymentStatus' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo TxnAuthorizationTime' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo TxnAuthorizationStamp' => 7.0,
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ClientTransID' => 7.0,
		'CreditMemoLineAdd DataExt OwnerID' => 5.0,
		'CreditMemoLineAdd DataExt DataExtName' => 5.0,
		'CreditMemoLineAdd DataExt DataExtValue' => 5.0,
		'CreditMemoLineGroupAdd UnitOfMeasure' => 7.0,
		'CreditMemoLineGroupAdd InventorySiteRef ListID' => 10.0,
		'CreditMemoLineGroupAdd InventorySiteRef FullName' => 10.0,
		'CreditMemoLineGroupAdd InventorySiteLocationRef ListID' => 12.0,
		'CreditMemoLineGroupAdd InventorySiteLocationRef FullName' => 12.0,
		'CreditMemoLineGroupAdd DataExt OwnerID' => 5.0,
		'CreditMemoLineGroupAdd DataExt DataExtName' => 5.0,
		'CreditMemoLineGroupAdd DataExt DataExtValue' => 5.0,
		'DiscountLineAdd Amount' => 4.0,
		'DiscountLineAdd RatePercent' => 4.0,
		'DiscountLineAdd IsTaxable' => 4.0,
		'DiscountLineAdd AccountRef ListID' => 4.0,
		'DiscountLineAdd AccountRef FullName' => 4.0,
		'SalesTaxLineAdd Amount' => 4.0,
		'SalesTaxLineAdd RatePercent' => 4.0,
		'SalesTaxLineAdd AccountRef ListID' => 4.0,
		'SalesTaxLineAdd AccountRef FullName' => 4.0,
		'ShippingLineAdd Amount' => 4.0,
		'ShippingLineAdd AccountRef ListID' => 4.0,
		'ShippingLineAdd AccountRef FullName' => 4.0,
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
		'TemplateRef ListID' => ['OE'],
		'TemplateRef FullName' => ['OE'],
		'BillAddress Addr5' => ['OE'],
		'BillAddress Note' => ['OE'],
		'ShipAddress Addr5' => ['OE'],
		'ShipAddress Note' => ['OE'],
		'IsPending' => ['OE'],
		'PONumber' => ['OE'],
		'TermsRef ListID' => ['OE'],
		'TermsRef FullName' => ['OE'],
		'DueDate' => ['OE'],
		'SalesRepRef ListID' => ['OE'],
		'SalesRepRef FullName' => ['OE'],
		'FOB' => ['OE'],
		'ShipMethodRef ListID' => ['OE'],
		'ShipMethodRef FullName' => ['OE'],
		'ItemSalesTaxRef ListID' => ['OE'],
		'ItemSalesTaxRef FullName' => ['OE'],
		'CustomerMsgRef ListID' => ['OE'],
		'CustomerMsgRef FullName' => ['OE'],
		'IsToBeEmailed' => ['OE'],
		'IsTaxIncluded' => ['US','OE'],
		'CustomerSalesTaxCodeRef ListID' => ['OE'],
		'CustomerSalesTaxCodeRef FullName' => ['OE'],
		'Other' => ['OE'],
		'ExchangeRate' => ['OE'],
		'ExternalGUID' => ['OE'],
		'CreditMemoLineAdd UnitOfMeasure' => ['OE'],
		'CreditMemoLineAdd PriceLevelRef ListID' => ['OE'],
		'CreditMemoLineAdd PriceLevelRef FullName' => ['OE'],
		'CreditMemoLineAdd TaxAmount' => ['US','CA','UK','OE'],
		'CreditMemoLineAdd InventorySiteRef ListID' => ['AU','OE'],
		'CreditMemoLineAdd InventorySiteRef FullName' => ['AU','OE'],
		'CreditMemoLineAdd InventorySiteLocationRef ListID' => ['AU','OE'],
		'CreditMemoLineAdd InventorySiteLocationRef FullName' => ['AU','OE'],
		'CreditMemoLineAdd SerialNumber' => ['AU','OE'],
		'CreditMemoLineAdd LotNumber' => ['AU','OE'],
		'CreditMemoLineAdd SalesTaxCodeRef ListID' => ['OE'],
		'CreditMemoLineAdd SalesTaxCodeRef FullName' => ['OE'],
		'CreditMemoLineAdd IsTaxable' => ['US','CA','UK','AU'],
		'CreditMemoLineAdd Other1' => ['OE'],
		'CreditMemoLineAdd Other2' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardNumber' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo ExpirationMonth' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo ExpirationYear' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo NameOnCard' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardAddress' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardPostalCode' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CommercialCardCode' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo TransactionMode' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardTxnType' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ResultCode' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ResultMessage' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo CreditCardTransID' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo MerchantAccountNumber' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AuthorizationCode' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AVSStreet' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AVSZip' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo CardSecurityCodeMatch' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ReconBatchID' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo PaymentGroupingCode' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo PaymentStatus' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo TxnAuthorizationTime' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo TxnAuthorizationStamp' => ['OE'],
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ClientTransID' => ['OE'],
		'CreditMemoLineAdd DataExt OwnerID' => ['OE'],
		'CreditMemoLineAdd DataExt DataExtName' => ['OE'],
		'CreditMemoLineAdd DataExt DataExtValue' => ['OE'],
		'CreditMemoLineGroupAdd ItemGroupRef ListID' => ['OE'],
		'CreditMemoLineGroupAdd ItemGroupRef FullName' => ['OE'],
		'CreditMemoLineGroupAdd Desc' => ['OE'],
		'CreditMemoLineGroupAdd Quantity' => ['OE'],
		'CreditMemoLineGroupAdd UnitOfMeasure' => ['OE'],
		'CreditMemoLineGroupAdd ServiceDate' => ['OE'],
		'CreditMemoLineGroupAdd InventorySiteRef ListID' => ['AU','OE'],
		'CreditMemoLineGroupAdd InventorySiteRef FullName' => ['AU','OE'],
		'CreditMemoLineGroupAdd InventorySiteLocationRef ListID' => ['AU','OE'],
		'CreditMemoLineGroupAdd InventorySiteLocationRef FullName' => ['AU','OE'],
		'CreditMemoLineGroupAdd DataExt OwnerID' => ['OE'],
		'CreditMemoLineGroupAdd DataExt DataExtName' => ['OE'],
		'CreditMemoLineGroupAdd DataExt DataExtValue' => ['OE'],
		'DiscountLineAdd Amount' => ['US','CA','UK','AU'],
		'DiscountLineAdd RatePercent' => ['US','CA','UK','AU'],
		'DiscountLineAdd IsTaxable' => ['US','CA','UK','AU'],
		'DiscountLineAdd AccountRef ListID' => ['US','CA','UK','AU'],
		'DiscountLineAdd AccountRef FullName' => ['US','CA','UK','AU'],
		'SalesTaxLineAdd Amount' => ['US','CA','UK','AU'],
		'SalesTaxLineAdd RatePercent' => ['US','CA','UK','AU'],
		'SalesTaxLineAdd AccountRef ListID' => ['US','CA','UK','AU'],
		'SalesTaxLineAdd AccountRef FullName' => ['US','CA','UK','AU'],
		'ShippingLineAdd Amount' => ['US','CA','UK','AU'],
		'ShippingLineAdd AccountRef ListID' => ['US','CA','UK','AU'],
		'ShippingLineAdd AccountRef FullName' => ['US','CA','UK','AU'],
		'IncludeRetElement' => ['OE'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'CustomerRef',
		'CustomerRef ListID',
		'CustomerRef FullName',
		'ClassRef',
		'ClassRef ListID',
		'ClassRef FullName',
		'ARAccountRef',
		'ARAccountRef ListID',
		'ARAccountRef FullName',
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
		'IsPending',
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
		'ExternalGUID',
		'CreditMemoLineAdd',
		'CreditMemoLineAdd ItemRef',
		'CreditMemoLineAdd ItemRef ListID',
		'CreditMemoLineAdd ItemRef FullName',
		'CreditMemoLineAdd Desc',
		'CreditMemoLineAdd Quantity',
		'CreditMemoLineAdd UnitOfMeasure',
		'CreditMemoLineAdd Rate',
		'CreditMemoLineAdd RatePercent',
		'CreditMemoLineAdd PriceLevelRef ListID',
		'CreditMemoLineAdd PriceLevelRef FullName',
		'CreditMemoLineAdd ClassRef ListID',
		'CreditMemoLineAdd ClassRef FullName',
		'CreditMemoLineAdd Amount',
		'CreditMemoLineAdd TaxAmount',
		'CreditMemoLineAdd InventorySiteRef ListID',
		'CreditMemoLineAdd InventorySiteRef FullName',
		'CreditMemoLineAdd InventorySiteLocationRef ListID',
		'CreditMemoLineAdd InventorySiteLocationRef FullName',
		'CreditMemoLineAdd SerialNumber',
		'CreditMemoLineAdd LotNumber',
		'CreditMemoLineAdd ServiceDate',
		'CreditMemoLineAdd SalesTaxCodeRef ListID',
		'CreditMemoLineAdd SalesTaxCodeRef FullName',
		'CreditMemoLineAdd IsTaxable',
		'CreditMemoLineAdd OverrideItemAccountRef ListID',
		'CreditMemoLineAdd OverrideItemAccountRef FullName',
		'CreditMemoLineAdd Other1',
		'CreditMemoLineAdd Other2',
		'CreditMemoLineAdd',
		'CreditMemoLineAdd CreditCardTxnInfo',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardNumber',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo ExpirationMonth',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo ExpirationYear',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo NameOnCard',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardAddress',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardPostalCode',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CommercialCardCode',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo TransactionMode',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnInputInfo CreditCardTxnType',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ResultCode',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ResultMessage',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo CreditCardTransID',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo MerchantAccountNumber',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AuthorizationCode',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AVSStreet',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo AVSZip',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo CardSecurityCodeMatch',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ReconBatchID',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo PaymentGroupingCode',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo PaymentStatus',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo TxnAuthorizationTime',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo TxnAuthorizationStamp',
		'CreditMemoLineAdd CreditCardTxnInfo CreditCardTxnResultInfo ClientTransID',
		'CreditMemoLineAdd DataExt OwnerID',
		'CreditMemoLineAdd DataExt DataExtName',
		'CreditMemoLineAdd DataExt DataExtValue',
		'CreditMemoLineGroupAdd',
		'CreditMemoLineGroupAdd ItemGroupRef ListID',
		'CreditMemoLineGroupAdd ItemGroupRef FullName',
		'CreditMemoLineGroupAdd Desc',
		'CreditMemoLineGroupAdd Quantity',
		'CreditMemoLineGroupAdd UnitOfMeasure',
		'CreditMemoLineGroupAdd ServiceDate',
		'CreditMemoLineGroupAdd InventorySiteRef ListID',
		'CreditMemoLineGroupAdd InventorySiteRef FullName',
		'CreditMemoLineGroupAdd InventorySiteLocationRef ListID',
		'CreditMemoLineGroupAdd InventorySiteLocationRef FullName',
		'CreditMemoLineGroupAdd DataExt OwnerID',
		'CreditMemoLineGroupAdd DataExt DataExtName',
		'CreditMemoLineGroupAdd DataExt DataExtValue',
		'DiscountLineAdd',
		'DiscountLineAdd Amount',
		'DiscountLineAdd RatePercent',
		'DiscountLineAdd IsTaxable',
		'DiscountLineAdd AccountRef ListID',
		'DiscountLineAdd AccountRef FullName',
		'SalesTaxLineAdd',
		'SalesTaxLineAdd Amount',
		'SalesTaxLineAdd RatePercent',
		'SalesTaxLineAdd AccountRef ListID',
		'SalesTaxLineAdd AccountRef FullName',
		'ShippingLineAdd',
		'ShippingLineAdd Amount',
		'ShippingLineAdd AccountRef ListID',
		'ShippingLineAdd AccountRef FullName',
		'IncludeRetElement',
	];
}
