<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit;

class PackageInfo {
	// Give credit where credit is due!
	public const Package = [
		'NAME' => 'QuickBooks PHP DevKit',
		'VERSION' => '4.0-dev',
		'AUTHOR' => '"Keith Palmer" <keith@consolibyte.com>',
		'WEBSITE' => 'https://github.com/consolibyte/quickbooks-php',
	];

	// Controls whether old passwords with a static hash are upgraded upon successful login.
	// Disabled as a default since it could be destructive if the password is used elsewhere (users table on their website).
	// All the examples recommend setting this to true.
	public static $PASSWORD_UPGRADE = false;

	// Controls whether the SOAP handlers ->sendRequestXML() and receiveResponseXML->() will use the PHP unserialize function if json_decode fails.
	// Needed if the queue has existing records that have extra data saved using the PHP serialize function.
	public static $ALLOW_PHP_UNSERIALIZE_EXTRA_DATA = false;

	// Passing a log level around between all the different static functions is error-prone and some do not include a log_level argument.
	// Setting this static variable is a convenient way to set the logging level: PackageInfo::LogLevel['NONE|NORMAL|VERBOSE|DEBUG|DEVELOP']
	public static $LOGLEVEL;

	// It is a good idea to set the timezone to the client Web Connector zone (e.g. 'America/Chicago' for Central time)
	public static $TIMEZONE = null;

	public static $CRLF = "\r\n";
	public static $WSDL = __DIR__ . '/QBWebConnectorSvc.wsdl';

	// Used to store whether self::$TIMEZONE was configure by the user (false) or guessed at by the server (true)
	public static $TIMEZONE_AUTOSET = null;

	public const NAMESPACE_QBXML_OBJECT = __NAMESPACE__ . "\\QBXML\\Object";
	public const NAMESPACE_QBXML_SCHEMA_OBJECT = __NAMESPACE__ . "\\QBXML\\Schema\\Object";
	public const NAMESPACE_IPP_OBJECT = __NAMESPACE__ . "\\IPP";


	public const Status = [
		'QUEUED' => 'q',		// QuickBooks status for items that have been queued and are waiting to be processed - QUEUED
		'SUCCESS' => 's',		// QuickBooks status for items that have been dequeued and SUCCESSFULLY PROCESSED
		'ERROR' => 'e',			// QuickBooks status for items that have been dequeued but an ERROR OCCURRED when processing it
		'PROCESSING' => 'i',	// QuickBooks status for items that have been dequeued and are being processed by QuickBooks (we assume) but we havn't received a response back about them yet
		'HANDLED' => 'h',		// QuickBooks status for items that were dequeued, had an error occurred, and then the error was handled by an error handler
		'CANCELLED' => 'c',		// QuickBooks status for items that were cancelled
		'REMOVED' => 'r',		// QuickBooks status for items that were forcibly removed from the queue (see Queue->remove())
		'NOOP' => 'n',			// QuickBooks status for items that were NoOp
	];


	public const LogLevel = [
		'DEVELOP' => 4,		// Use this while you are developing, but should be lowered for production
		'DEBUG' => 3,		// Debug logging (too much data is logged... generally only useful for debugging)
		'VERBOSE' => 2,		// Verbose logging (lots of data is logged)
		'NORMAL' => 1,		// Normal logging (minimal data is logged)
		'NONE' => 0,		// No logging at all (you probably should not use this...)
	];


	public const Error = [
		'OK' => 0,			// Error code for errors that are not really errors...
		'INTERNAL' => -1,	// Error code for SOAP server errors that occur internally (misc. errors)
		'HANDLER' => -2,	// Error code for errors that occur within function handlers
		'DRIVER' => -3,		// Error code for errors that occur within driver classes
		'HOOK' => -4,		// Error code for errors that are reported by hook classes
	];


	// QuickBooks stuff from here down
	public const Locale = [
		'UNITED_STATES' => 'US',
		'US' => 'US',

		'CANADA' => 'CA',
		'CA' => 'CA',

		'UNITED_KINGDOM' => 'UK',
		'UK' => 'UK',

		'AUSTRALIA' => 'AU',
		'AU' => 'AU',

		'ONLINE_EDITION' => 'OE',
		'OE' => 'OE',
	];


	public const QbType = [
		'QBFS' => 'QBFS',
		'QBPOS' => 'QBPOS',
	];

	public const TaxCode = [
		'TAXABLE' => 'TAX',		// An always-present QuickBooks constant for "TAXABLE" items to embed in "SalesTaxCodeRef FullName" qbXML values
		'NONTAXABLE' => 'NON',	// An always-present QuickBooks constant for "NON-TAXABLE" items to embed in "SalesTaxCodeRef FullName" qbXML values
	];

	public const QbId = [
		'LISTID' => 'ListID',
		'TXNID' => 'TxnID',
		'TXNLINEID' => 'TxnLineID',
	];

	public const DataType = [
		'STRING' => 'STRTYPE',
		'ID' => 'IDTYPE',
		'FLOAT' => 'AMTTYPE',
		'BOOLEAN' => 'BOOLTYPE',
		'INTEGER' => 'INTTYPE',
		'DATE' => 'DATETYPE',
		'ENUM' => 'ENUMTYPE',
		'DATETIME' => 'DATETIMETYPE',
	];


	public const Actions = [
		'NOOP' => 'NoOp',
		'SKIP' => 'NoOp',	// This is temporary, eventually we should implement an actual in-handler skip method
		'ADD' => 'Add',
		'MOD' => 'Mod',
		'QUERY' => 'Query',
		'DELETE' => 'Delete',
		'IMPORT' => 'Import',
		'AUDIT' => 'Audit',

		'DERIVE_INVENTORYLEVELS' => 'InventoryLevels',
		'DERIVE_INVENTORYASSEMBLYLEVELS' => 'InventoryAssemblyLevels',

		'OBJECT_HOST' => 'Host',
		'QUERY_HOST' => 'HostQuery',
		'IMPORT_HOST' => 'HostImport',

		'OBJECT_PREFERENCES' => 'Preferences',
		'QUERY_PREFERENCES' => 'PreferencesQuery',
		'IMPORT_PREFERENCES' => 'PreferencesImport',

		'OBJECT_ACCOUNT' => 'Account',
		'ADD_ACCOUNT' => 'AccountAdd',
		'MOD_ACCOUNT' => 'AccountMod',
		'QUERY_ACCOUNT' => 'AccountQuery',
		'IMPORT_ACCOUNT' => 'AccountImport',
		'DERIVE_ACCOUNT' => 'AccountDerive',
		'AUDIT_ACCOUNT' => 'AccountAudit',

		'OBJECT_BILL' => 'Bill',
		'ADD_BILL' => 'BillAdd',
		'MOD_BILL' => 'BillMod',
		'QUERY_BILL' => 'BillQuery',
		'IMPORT_BILL' => 'BillImport',
		'DERIVE_BILL' => 'BillDerive',
		'AUDIT_BILL' => 'BillAudit',

		'OBJECT_BILLINGRATE' => 'BillingRate',
		'ADD_BILLINGRATE' => 'BillingRateAdd',
		'QUERY_BILLINGRATE' => 'BillingRateQuery',
		'IMPORT_BILLINGRATE' => 'BillingRateImport',

		'OBJECT_BILLTOPAY' => 'BillToPay',
		'QUERY_BILLTOPAY' => 'BillToPayQuery',
		'IMPORT_BILLTOPAY' => 'BillToPayImport',

		'OBJECT_BILLPAYMENTCHECK' => 'BillPaymentCheck',
		'ADD_BILLPAYMENTCHECK' => 'BillPaymentCheckAdd',
		'MOD_BILLPAYMENTCHECK' => 'BillPaymentCheckMod',
		'QUERY_BILLPAYMENTCHECK' => 'BillPaymentCheckQuery',
		'IMPORT_BILLPAYMENTCHECK' => 'BillPaymentCheckImport',

		'OBJECT_BILLPAYMENTCREDITCARD' => 'BillPaymentCreditCard',
		'ADD_BILLPAYMENTCREDITCARD' => 'BillPaymentCreditCardAdd',
		'MOD_BILLPAYMENTCREDITCARD' => 'BillPaymentCreditCardMod',		// Not supported by current QuickBooks SDK
		'QUERY_BILLPAYMENTCREDITCARD' => 'BillPaymentCreditCardQuery',
		'IMPORT_BILLPAYMENTCREDITCARD' => 'BillPaymentCreditCardImport',

		'OBJECT_CHARGE' => 'Charge',
		'ADD_CHARGE' => 'ChargeAdd',
		'MOD_CHARGE' => 'ChargeMod',
		'QUERY_CHARGE' => 'ChargeQuery',
		'IMPORT_CHARGE' => 'ChargeImport',
		'DERIVE_CHARGE' => 'ChargeDerive',

		'OBJECT_CHECK' => 'Check',
		'ADD_CHECK' => 'CheckAdd',
		'MOD_CHECK' => 'CheckMod',
		'QUERY_CHECK' => 'CheckQuery',
		'IMPORT_CHECK' => 'CheckImport',

		'OBJECT_CLASS' => 'Qbclass',
		'ADD_CLASS' => 'ClassAdd',
		'MOD_CLASS' => 'ClassMod',
		'QUERY_CLASS' => 'ClassQuery',
		'IMPORT_CLASS' => 'ClassImport',


		'OBJECT_COMPANY' => 'Company',
		// Company meta-data requests
		'QUERY_COMPANY' => 'CompanyQuery',
		'IMPORT_COMPANY' => 'CompanyImport',


		'OBJECT_CREDITCARDCREDIT' => 'CreditCardCredit',
		'ADD_CREDITCARDCREDIT' => 'CreditCardCreditAdd',
		'MOD_CREDITCARDCREDIT' => 'CreditCardCreditMod',
		'QUERY_CREDITCARDCREDIT' => 'CreditCardCreditQuery',
		'IMPORT_CREDITCARDCREDIT' => 'CreditCardCreditImport',

		'OBJECT_CREDITCARDREFUND' => 'ARRefundCreditCard',
		'ADD_CREDITCARDREFUND' => 'ARRefundCreditCardAdd',
		'QUERY_CREDITCARDREFUND' => 'ARRefundCreditCardQuery',

		'OBJECT_CREDITCARDCHARGE' => 'CreditCardCharge',
		'ADD_CREDITCARDCHARGE' => 'CreditCardChargeAdd',
		'MOD_CREDITCARDCHARGE' => 'CreditCardChargeMod',
		'QUERY_CREDITCARDCHARGE' => 'CreditCardChargeQuery',
		'IMPORT_CREDITCARDCHARGE' => 'CreditCardChargeImport',

		'OBJECT_CREDITCARDMEMO' => 'CreditCardMemo',
		'ADD_CREDITCARDMEMO' => 'CreditCardMemoAdd',
		'MOD_CREDITCARDMEMO' => 'CreditCardMemoMod',
		'QUERY_CREDITCARDMEMO' => 'CreditCardMemoQuery',

		'OBJECT_CREDITMEMO' => 'CreditMemo',
		'ADD_CREDITMEMO' => 'CreditMemoAdd',
		'MOD_CREDITMEMO' => 'CreditMemoMod',
		'QUERY_CREDITMEMO' => 'CreditMemoQuery',
		'IMPORT_CREDITMEMO' => 'CreditMemoImport',
		'DERIVE_CREDITMEMO' => 'CreditMemoDerive',

		'OBJECT_CURRENCY' => 'Currency',
		'ADD_CURRENCY' => 'CurrencyAdd',
		'MOD_CURRENCY' => 'CurrencyMod',
		'QUERY_CURRENCY' => 'CurrencyQuery',
		'IMPORT_CURRENCY' => 'CurrencyImport',


		'OBJECT_CUSTOMER' => 'Customer',
		// Customer Requests
		'ADD_CUSTOMER' => 'CustomerAdd',
		'MOD_CUSTOMER' => 'CustomerMod',
		'QUERY_CUSTOMER' => 'CustomerQuery',
		'IMPORT_CUSTOMER' => 'CustomerImport',
		'DERIVE_CUSTOMER' => 'CustomerDerive',

		'OBJECT_CUSTOMERMSG' => 'CustomerMsg',
		'ADD_CUSTOMERMSG' => 'CustomerMsgAdd',
		'QUERY_CUSTOMERMSG' => 'CustomerMsgQuery',
		'IMPORT_CUSTOMERMSG' => 'CustomerMsgImport',

		'OBJECT_CUSTOMERTYPE' => 'CustomerType',
		'ADD_CUSTOMERTYPE' => 'CustomerTypeAdd',
		'QUERY_CUSTOMERTYPE' => 'CustomerTypeQuery',
		'IMPORT_CUSTOMERTYPE' => 'CustomerTypeImport',

		'OBJECT_DATAEXT' => 'DataExt',
		'ADD_DATAEXT' => 'DataExtAdd',
		'MOD_DATAEXT' => 'DataExtMod',
		'DEL_DATAEXT' => 'DataExtDel',
			'DELETE_DATAEXT' => 'DataExtDel',

		'OBJECT_DATAEXTDEF' => 'DataExtDef',
		'ADD_DATAEXTDEF' => 'DataExtDefAdd',
		'MOD_DATAEXTDEF' => 'DataExtDefMod',
		'DEL_DATAEXTDEF' => 'DataExtDefDel',
			'DELETE_DATAEXTDEF' => 'DataExtDefDel',
		'QUERY_DATAEXTDEF' => 'DataExtDefQuery',
		'IMPORT_DATAEXTDEF' => 'DataExtDefImport',

		'OBJECT_DATEDRIVENTERMS' => 'DateDrivenTerms',
		'ADD_DATEDRIVENTERMS' => 'DateDrivenTermsAdd',
		'QUERY_DATEDRIVENTERMS' => 'DateDrivenTermsQuery',
		'IMPORT_DATEDRIVENTERMS' => 'DateDrivenTermsImport',

		/**
		 * Query QuickBooks for lists of deleted list items (customers, items, etc.)
		 */
		'OBJECT_DELETEDLISTS' => 'ListDeleted',
		'QUERY_DELETEDLISTS' => 'ListDeletedQuery',
		'IMPORT_DELETEDLISTS' => 'ListDeletedImport',

		/**
		 * Deleted transactions (payments, invoices, estimates, etc.)
		 */
		'OBJECT_DELETEDTXN' => 'TxnDeleted',
			'OBJECT_TXNDELETED' => 'TxnDeleted',
		'QUERY_DELETEDTXNS' => 'TxnDeletedQuery',
			'QUERY_DELETEDTRANSACTIONS' => 'TxnDeletedQuery',
			'QUERY_TXNDELETED' => 'TxnDeletedQuery',
		'IMPORT_DELETEDTXN' => 'TxnDeletedImport',

		/**
		 * General Detail Report
		 */
		'OBJECT_GENERALDETAILREPORT' => 'GeneralDetailReport',
		'QUERY_GENERALDETAILREPORT' => 'GeneralDetailReportQuery',
		/**
		 * General Summary Report
		 */
		'OBJECT_GENERALSUMMARYREPORT' => 'GeneralSummaryReport',
		'QUERY_GENERALSUMMARYREPORT' => 'GeneralSummaryReportQuery',


		'OBJECT_DEPOSIT' => 'Deposit',
		'ADD_DEPOSIT' => 'DepositAdd',
		'MOD_DEPOSIT' => 'DepositMod',
		'QUERY_DEPOSIT' => 'DepositQuery',
		'IMPORT_DEPOSIT' => 'DepositImport',

		'OBJECT_DEPARTMENT' => 'Department',
		'ADD_DEPARTMENT' => 'DepartmentAdd',
		'MOD_DEPARTMENT' => 'DepartmentMod',
		'QUERY_DEPARTMENT' => 'DepartmentQuery',
		'IMPORT_DEPARTMENT' => 'DepartmentImport',

		'OBJECT_EMPLOYEE' => 'Employee',
		'ADD_EMPLOYEE' => 'EmployeeAdd',
		'MOD_EMPLOYEE' => 'EmployeeMod',
		'QUERY_EMPLOYEE' => 'EmployeeQuery',
		'IMPORT_EMPLOYEE' => 'EmployeeImport',

		'OBJECT_ENTITY' => 'Entity',
		'QUERY_ENTITY' => 'EntityQuery',

		'OBJECT_ESTIMATE' => 'Estimate',
		'ADD_ESTIMATE' => 'EstimateAdd',
		'MOD_ESTIMATE' => 'EstimateMod',
		'QUERY_ESTIMATE' => 'EstimateQuery',
		'IMPORT_ESTIMATE' => 'EstimateImport',
		'AUDIT_ESTIMATE' => 'EstimateAudit',

		'OBJECT_INVENTORYADJUSTMENT' => 'InventoryAdjustment',
		'ADD_INVENTORYADJUSTMENT' => 'InventoryAdjustmentAdd',
		'QUERY_INVENTORYADJUSTMENT' => 'InventoryAdjustmentQuery',
		'IMPORT_INVENTORYADJUSTMENT' => 'InventoryAdjustmentImport',

		/**
		 * Job constant in QuickBooks
		 *
		 * In actuality, there are no such thing as "Jobs" in QuickBooks. Jobs in
		 * QuickBooks are handled as customers with parent customers.
		 */
		'OBJECT_JOB' => 'Job',
		'ADD_JOB' => 'JobAdd',
		'MOD_JOB' => 'JobMod',
		'QUERY_JOB' => 'JobQuery',
		'IMPORT_JOB' => 'JobImport',

		'OBJECT_ITEM' => 'Item',
		'QUERY_ITEM' => 'ItemQuery',
		'IMPORT_ITEM' => 'ItemImport',
		'DERIVE_ITEM' => 'ItemDerive',

		'OBJECT_INVENTORYITEM' => 'ItemInventory',
		'ADD_INVENTORYITEM' => 'ItemInventoryAdd',
		'MOD_INVENTORYITEM' => 'ItemInventoryMod',
		'QUERY_INVENTORYITEM' => 'ItemInventoryQuery',
		'IMPORT_INVENTORYITEM' => 'ItemInventoryImport',
		'DERIVE_INVENTORYITEM' => 'ItemInventoryDerive',

		'OBJECT_INVENTORYASSEMBLYITEM' => 'ItemInventoryAssembly',
		'ADD_INVENTORYASSEMBLYITEM' => 'ItemInventoryAssemblyAdd',
		'MOD_INVENTORYASSEMBLYITEM' => 'ItemInventoryAssemblyMod',
		'QUERY_INVENTORYASSEMBLYITEM' => 'ItemInventoryAssemblyQuery',
		'IMPORT_INVENTORYASSEMBLYITEM' => 'ItemInventoryAssemblyImport',

		'OBJECT_GROUPITEM' => 'ItemGroup',
		'ADD_GROUPITEM' => 'ItemGroupAdd',
		'MOD_GROUPITEM' => 'ItemGroupMod',
		'QUERY_GROUPITEM' => 'ItemGroupQuery',
		'IMPORT_GROUPITEM' => 'ItemGroupImport',

		'OBJECT_NONINVENTORYITEM' => 'ItemNonInventory',
		'ADD_NONINVENTORYITEM' => 'ItemNonInventoryAdd',
		'MOD_NONINVENTORYITEM' => 'ItemNonInventoryMod',
		'QUERY_NONINVENTORYITEM' => 'ItemNonInventoryQuery',
		'IMPORT_NONINVENTORYITEM' => 'ItemNonInventoryImport',

		'OBJECT_DISCOUNTITEM' => 'ItemDiscount',
		'ADD_DISCOUNTITEM' => 'ItemDiscountAdd',
		'MOD_DISCOUNTITEM' => 'ItemDiscountMod',
		'QUERY_DISCOUNTITEM' => 'ItemDiscountQuery',
		'IMPORT_DISCOUNTITEM' => 'ItemDiscountImport',

		'OBJECT_FIXEDASSETITEM' => 'ItemFixedAsset',
		'ADD_FIXEDASSETITEM' => 'ItemFixedAssetAdd',
		'MOD_FIXEDASSETITEM' => 'ItemFixedAssetMod',
		'QUERY_FIXEDASSETITEM' => 'ItemFixedAssetQuery',
		'IMPORT_FIXEDASSETITEM' => 'ItemFixedAssetImport',

		'OBJECT_PAYMENTITEM' => 'ItemPayment',
		'ADD_PAYMENTITEM' => 'ItemPaymentAdd',
		'MOD_PAYMENTITEM' => 'ItemPaymentMod',
		'QUERY_PAYMENTITEM' => 'ItemPaymentQuery',
		'IMPORT_PAYMENTITEM' => 'ItemPaymentImport',

		'OBJECT_SERVICEITEM' => 'ServiceItem',
		'ADD_SERVICEITEM' => 'ItemServiceAdd',
		'MOD_SERVICEITEM' => 'ItemServiceMod',
		'QUERY_SERVICEITEM' => 'ItemServiceQuery',
		'IMPORT_SERVICEITEM' => 'ItemServiceImport',

		'OBJECT_SALESTAXITEM' => 'ItemSalesTax',
		'ADD_SALESTAXITEM' => 'ItemSalesTaxAdd',
		'MOD_SALESTAXITEM' => 'ItemSalesTaxMod',
		'QUERY_SALESTAXITEM' => 'ItemSalesTaxQuery',
		'IMPORT_SALESTAXITEM' => 'ItemSalesTaxImport',

		'OBJECT_SALESTAXGROUPITEM' => 'ItemSalesTaxGroup',
		'ADD_SALESTAXGROUPITEM' => 'ItemSalesTaxGroupAdd',
		'MOD_SALESTAXGROUPITEM' => 'ItemSalesTaxGroupMod',
		'QUERY_SALESTAXGROUPITEM' => 'ItemSalesTaxGroupQuery',
		'IMPORT_SALESTAXGROUPITEM' => 'ItemSalesTaxGroupImport',

		'OBJECT_OTHERCHARGEITEM' => 'ItemOtherCharge',
		'ADD_OTHERCHARGEITEM' => 'ItemOtherChargeAdd',
		'MOD_OTHERCHARGEITEM' => 'ItemOtherChargeMod',
		'QUERY_OTHERCHARGEITEM' => 'ItemOtherChargeQuery',
		'IMPORT_OTHERCHARGEITEM' => 'ItemOtherChargeImport',

		'OBJECT_PAYROLLITEMWAGE' => 'PayrollItemWage',
		'ADD_PAYROLLITEMWAGE' => 'PayrollItemWageAdd',
		'MOD_PAYROLLITEMWAGE' => 'PayrollItemWageMod',
		'QUERY_PAYROLLITEMWAGE' => 'PayrollItemWageQuery',
		'IMPORT_PAYROLLITEMWAGE' => 'PayrollItemWageImport',

		'OBJECT_PAYROLLITEMNONWAGE' => 'PayrollItemNonWage',
		'ADD_PAYROLLITEMNONWAGE' => 'PayrollItemNonWageAdd',
		'MOD_PAYROLLITEMNONWAGE' => 'PayrollItemNonWageMod',
		'QUERY_PAYROLLITEMNONWAGE' => 'PayrollItemNonWageQuery',
		'IMPORT_PAYROLLITEMNONWAGE' => 'PayrollItemNonWageImport',

		'OBJECT_ITEMRECEIPT' => 'ItemReceipt',
		'ADD_ITEMRECEIPT' => 'ItemReceiptAdd',
		'MOD_ITEMRECEIPT' => 'ItemReceiptMod',
		'QUERY_ITEMRECEIPT' => 'ItemReceiptQuery',
		'IMPORT_ITEMRECEIPT' => 'ItemReceiptImport',

		'OBJECT_SUBTOTALITEM' => 'ItemSubtotal',
		'ADD_SUBTOTALITEM' => 'ItemSubtotalAdd',
		'MOD_SUBTOTALITEM' => 'ItemSubtotalMod',
		'QUERY_SUBTOTALITEM' => 'ItemSubtotalQuery',
		'IMPORT_SUBTOTALITEM' => 'ItemSubtotalImport',

		'OBJECT_ITEMSITES' => 'ItemSites',
		'QUERY_ITEMSITES' => 'ItemSitesQuery',

		'OBJECT_INVENTORYSITE' => 'InventorySite',
		'ADD_INVENTORYSITE' => 'InventorySiteAdd',
		'MOD_INVENTORYSITE' => 'InventorySiteMod',
		'QUERY_INVENTORYSITE' => 'InventorySiteQuery',
		'IMPORT_INVENTORYSITE' => 'InventorySiteImport',

		'OBJECT_JOBTYPE' => 'JobType',
		'ADD_JOBTYPE' => 'JobTypeAdd',
		'QUERY_JOBTYPE' => 'JobTypeQuery',
		'IMPORT_JOBTYPE' => 'JobTypeImport',

		'OBJECT_JOURNALENTRY' => 'JournalEntry',
		'ADD_JOURNALENTRY' => 'JournalEntryAdd',
		'MOD_JOURNALENTRY' => 'JournalEntryMod',
		'QUERY_JOURNALENTRY' => 'JournalEntryQuery',
		'IMPORT_JOURNALENTRY' => 'JournalEntryImport',


		'OBJECT_INVOICE' => 'Invoice',
		// Invoice Requests
		'ADD_INVOICE' => 'InvoiceAdd',
		'MOD_INVOICE' => 'InvoiceMod',
		'QUERY_INVOICE' => 'InvoiceQuery',
		'IMPORT_INVOICE' => 'InvoiceImport',
		'DERIVE_INVOICE' => 'InvoiceDerive',
		'AUDIT_INVOICE' => 'InvoiceAudit',

		'OBJECT_RECEIVEPAYMENT' => 'ReceivePayment',
		// Receive Payment Requests
		'ADD_RECEIVEPAYMENT' => 'ReceivePaymentAdd',
			'ADD_RECEIVE_PAYMENT' => 'ReceivePaymentAdd',
		'MOD_RECEIVEPAYMENT' => 'ReceivePaymentMod',
			'MOD_RECEIVE_PAYMENT' => 'ReceivePaymentMod',
		'QUERY_RECEIVEPAYMENT' => 'ReceivePaymentQuery',
			'QUERY_RECEIVE_PAYMENT' => 'ReceivePaymentQuery',
		'IMPORT_RECEIVEPAYMENT' => 'ReceivePaymentImport',
			'IMPORT_RECEIVE_PAYMENT' => 'ReceivePaymentImport',
		'AUDIT_RECEIVEPAYMENT' => 'ReceivePaymentAudit',
			'AUDIT_RECEIVE_PAYMENT' => 'ReceivePaymentAudit',
		'DERIVE_RECEIVEPAYMENT' => 'ReceivePaymentDerive',
			'DERIVE_RECEIVEPAYMENT' => 'ReceivePaymentDerive',

		'OBJECT_OTHERNAME' => 'OtherName',
		'ADD_OTHERNAME' => 'OtherNameAdd',
		'MOD_OTHERNAME' => 'OtherNameMod',
		'QUERY_OTHERNAME' => 'OtherNameQuery',
		'IMPORT_OTHERNAME' => 'OtherNameImport',

		'OBJECT_PAYMENTMETHOD' => 'PaymentMethod',
		'ADD_PAYMENTMETHOD' => 'PaymentMethodAdd',
		'QUERY_PAYMENTMETHOD' => 'PaymentMethodQuery',
		'IMPORT_PAYMENTMETHOD' => 'PaymentMethodImport',

		'OBJECT_PRICELEVEL' => 'PriceLevel',
		'ADD_PRICELEVEL' => 'PriceLevelAdd',
		'MOD_PRICELEVEL' => 'PriceLevelMod',
		'QUERY_PRICELEVEL' => 'PriceLevelQuery',
		'IMPORT_PRICELEVEL' => 'PriceLevelImport',

		'OBJECT_PURCHASEORDER' => 'PurchaseOrder',
		'ADD_PURCHASEORDER' => 'PurchaseOrderAdd',
			'ADD_PURCHASE_ORDER' => 'PurchaseOrderAdd',
		'MOD_PURCHASEORDER' => 'PurchaseOrderMod',
			'MOD_PURCHASE_ORDER' => 'PurchaseOrderMod',
		'QUERY_PURCHASEORDER' => 'PurchaseOrderQuery',
			'QUERY_PURCHASE_ORDER' => 'PurchaseOrderQuery',
		'IMPORT_PURCHASEORDER' => 'PurchaseOrderImport',
			'IMPORT_PURCHASE_ORDER' => 'PurchaseOrderImport',
		'DERIVE_PURCHASEORDER' => 'PurchaseOrderDerive',
		'AUDIT_PURCHASEORDER' => 'PurchaseOrderAudit',

		'OBJECT_SALESORDER' => 'SalesOrder',
		'ADD_SALESORDER' => 'SalesOrderAdd',
		'MOD_SALESORDER' => 'SalesOrderMod',
		'QUERY_SALESORDER' => 'SalesOrderQuery',
		'IMPORT_SALESORDER' => 'SalesOrderImport',
		'DERIVE_SALESORDER' => 'SalesOrderDerive',

		'OBJECT_SALESRECEIPT' => 'SalesReceipt',
		'ADD_SALESRECEIPT' => 'SalesReceiptAdd',
		'MOD_SALESRECEIPT' => 'SalesReceiptMod',
		'QUERY_SALESRECEIPT' => 'SalesReceiptQuery',
		'IMPORT_SALESRECEIPT' => 'SalesReceiptImport',
		'AUDIT_SALESRECEIPT' => 'SalesReceiptAudit',

		'OBJECT_SALESREP' => 'SalesRep',
		'ADD_SALESREP' => 'SalesRepAdd',
		'MOD_SALESREP' => 'SalesRepMod',
		'QUERY_SALESREP' => 'SalesRepQuery',
		'IMPORT_SALESREP' => 'SalesRepImport',

		'OBJECT_SALESTAXCODE' => 'SalesTaxCode',
		'ADD_SALESTAXCODE' => 'SalesTaxCodeAdd',
		'MOD_SALESTAXCODE' => 'SalesTaxCodeMod',
		'QUERY_SALESTAXCODE' => 'SalesTaxCodeQuery',
		'IMPORT_SALESTAXCODE' => 'SalesTaxCodeImport',

		'OBJECT_SHIPMETHOD' => 'ShipMethod',
		'ADD_SHIPMETHOD' => 'ShipMethodAdd',
		'QUERY_SHIPMETHOD' => 'ShipMethodQuery',
		'IMPORT_SHIPMETHOD' => 'ShipMethodImport',

		'OBJECT_SPECIALACCOUNT' => 'SpecialAccount',
		'ADD_SPECIALACCOUNT' => 'SpecialAccountAdd',

		'OBJECT_SPECIALITEM' => 'SpecialItem',
		'ADD_SPECIALITEM' => 'SpecialItemAdd',

		'OBJECT_STANDARDTERMS' => 'StandardTerms',
		'ADD_STANDARDTERMS' => 'StandardTermsAdd',
		'QUERY_STANDARDTERMS' => 'StandardTermsQuery',
		'IMPORT_STANDARDTERMS' => 'StandardTermsImport',

		'OBJECT_TEMPLATE' => 'Template',
		'QUERY_TEMPLATE' => 'TemplateQuery',
		'IMPORT_TEMPLATE' => 'TemplateImport',

		'OBJECT_TERMS' => 'Terms',
		'QUERY_TERMS' => 'TermsQuery',
		'IMPORT_TERMS' => 'TermsImport',

		'OBJECT_LIST' => 'ListDel', // (TODO: Does not exist yet)
		'DEL_LIST' => 'ListDel',
			'DELETE_LIST' => 'ListDel',



		'OBJECT_TIMETRACKING' => 'TimeTracking',
		'ADD_TIMETRACKING' => 'TimeTrackingAdd',
		'MOD_TIMETRACKING' => 'TimeTrackingMod',
		'QUERY_TIMETRACKING' => 'TimeTrackingQuery',
		'IMPORT_TIMETRACKING' => 'TimeTrackingImport',

		'OBJECT_TRANSACTION' => 'Transaction',
			'OBJECT_TXN' => 'Transaction',

		'DELETE_TRANSACTION' => 'TxnDel',
			'DEL_TRANSACTION' => 'TxnDel',
			'DEL_TXN' => 'TxnDel',
			'DELETE_TXN' => 'TxnDel',
		'QUERY_TRANSACTION' => 'TransactionQuery',
		'VOID_TRANSACTION' => 'TxnVoid',
		'IMPORT_TRANSACTION' => 'TransactionImport',

		'OBJECT_VEHICLE' => 'Vehicle',
		'ADD_VEHICLE' => 'VehicleAdd',
		'MOD_VEHICLE' => 'VehicleMod',
		'QUERY_VEHICLE' => 'VehicleQuery',
		'IMPORT_VEHICLE' => 'VehicleImport',

		'OBJECT_VEHICLEMILEAGE' => 'VehicleMileage',
		'ADD_VEHICLEMILEAGE' => 'VehicleMileageAdd',
		'MOD_VEHICLEMILEAGE' => 'VehicleMileageMod',
		'QUERY_VEHICLEMILEAGE' => 'VehicleMileageQuery',
		'IMPORT_VEHICLEMILEAGE' => 'VehicleMileageImport',

		'OBJECT_VENDOR' => 'Vendor',
		'ADD_VENDOR' => 'VendorAdd',
		'MOD_VENDOR' => 'VendorMod',
		'QUERY_VENDOR' => 'VendorQuery',
		'IMPORT_VENDOR' => 'VendorImport',
		'DERIVE_VENDOR' => 'VendorDerive',

		'OBJECT_VENDORCREDIT' => 'VendorCredit',
		'ADD_VENDORCREDIT' => 'VendorCreditAdd',
		'MOD_VENDORCREDIT' => 'VendorCreditMod',
		'QUERY_VENDORCREDIT' => 'VendorCreditQuery',
		'IMPORT_VENDORCREDIT' => 'VendorCreditImport',
		'DERIVE_VENDORCREDIT' => 'VendorCreditDerive',

		'OBJECT_VENDORTYPE' => 'VendorType',
		'ADD_VENDORTYPE' => 'VendorTypeAdd',
		'QUERY_VENDORTYPE' => 'VendorTypeQuery',
		'IMPORT_VENDORTYPE' => 'VendorTypeImport',

		'OBJECT_WORKERSCOMPCODE' => 'WorkersCompCode',
		'ADD_WORKERSCOMPCODE' => 'WorkersCompCodeAdd',
		'MOD_WORKERSCOMPCODE' => 'WorkersCompCodeMod',
		'QUERY_WORKERSCOMPCODE' => 'WorkersCompCodeQuery',
		'IMPORT_WORKERSCOMPCODE' => 'WorkersCompCodeImport',

		'OBJECT_UNITOFMEASURESET' => 'UnitOfMeasureSet',
		'ADD_UNITOFMEASURESET' => 'UnitOfMeasureSetAdd',
		//'MOD_UNITOFMEASURESET' => 'UnitOfMeasureSetMod',
		'QUERY_UNITOFMEASURESET' => 'UnitOfMeasureSetQuery',
		'IMPORT_UNITOFMEASURESET' => 'UnitOfMeasureSetImport',
	];
}
