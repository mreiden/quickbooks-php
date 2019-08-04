<?php declare(strict_types=1);

/**
 * QuickBooks IPP/IDS constants
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author Keith Palmer <Keith@ConsoliBYTE.com>
 *
 * @package QuickBooks
 * @subpackage IPP
 */

namespace QuickBooksPhpDevKit\IPP;

/**
 *
 *
 *
 */
class IDS
{
	public const FLAVOR_DESKTOP = 'QBD';

	public const FLAVOR_ONLINE = 'QBO';

	public const OPTYPE_SYNCSTATUS = 'SyncStatus';

	public const OPTYPE_ADD = 'Add';

	public const OPTYPE_MOD = 'Mod';

	public const OPTYPE_DELETE = 'Delete';

	public const OPTYPE_VOID = 'Void';

	public const OPTYPE_PDF = 'PDF';

	public const OPTYPE_DOWNLOAD = 'DOWNLOAD';

	public const OPTYPE_QUERY = 'Query';

	public const OPTYPE_CDC = 'ChangeDataCapture';

	public const OPTYPE_ENTITLEMENTS = 'Entitlements';

	public const OPTYPE_SEND = 'Send';

	/**
	 * This is not a real operation type in IDS terms, but is neccessary to distinguish between queries and findById in QuickBooks Online in IDS v2.
	 */
	public const OPTYPE_FINDBYID = '_findById_';

	public const OPTYPE_REPORT = 'Report';

	public const DOMAIN_NG = 'ng';

	public const DOMAIN_QB = 'qb';

	// These are the supported IDS version(s)
	public const VERSION_LATEST = 'v3';
	public const VERSION_3 = 'v3';

	// These IDS versions are unsupported and non-functional
	public const VERSION_1 = 'v1';
	public const VERSION_2 = 'v2';


	public const URL_V3 = 'https://quickbooks.api.intuit.com/v3';

	public const URL_V3_SANDBOX = 'https://sandbox-quickbooks.api.intuit.com/v3';

	/**
	 * Default BASEURL for QuickBooks Desktop (QuickBooks Online requires you to fetch a specific BASEURL)
	 */
	public const BASEURL_DESKTOP = 'https://services.intuit.com/sb';

	public const RESOURCE_REPORT_ACCOUNTBALANCES = 'ReportAccountBalances';

	public const RESOURCE_REPORT_BALANCESHEET = 'ReportBalanceSheet';

	public const RESOURCE_REPORT_BALANCESHEETSTD = 'ReportBalanceSheetStd';

	public const RESOURCE_REPORT_CUSTOMERSWHOOWEME = 'ReportCustomersWhoOweMe';

	public const RESOURCE_REPORT_INCOMEBREAKDOWN = 'ReportIncomeBreakdown';

	public const RESOURCE_REPORT_PROFITANDLOSS = 'ReportProfitAndLoss';

	public const RESOURCE_REPORT_SALESSUMMARY = 'ReportSalesSummary';

	public const RESOURCE_REPORT_TOPCUSTOMERSBYSALES = 'ReportTopCustomersBySales';

	public const RESOURCE_PURCHASE = 'Purchase';

	public const RESOURCE_ACCOUNT = 'Account';

	public const RESOURCE_BILL = 'Bill';

    public const RESOURCE_DEPOSIT = 'Deposit';

	public const RESOURCE_BILLPAYMENT = 'BillPayment';

	public const RESOURCE_BILLPAYMENTCREDITCARD = 'BillPaymentCreditCard';

	public const RESOURCE_CHANGEDATADELETED = 'ChangeDataDeleted';

	public const RESOURCE_CHANGEDATACAPTURE = 'ChangeDataCapture';

	public const RESOURCE_CHECK = 'Check';

	public const RESOURCE_CLASS = 'Class';

	public const RESOURCE_COMPANY = 'Company';

	public const RESOURCE_COMPANYMETADATA = 'CompanyMetaData';

	public const RESOURCE_CREDITMEMO = 'CreditMemo';

	public const RESOURCE_CUSTOMER = 'Customer';

	public const RESOURCE_DEPARTMENT = 'Department';

	public const RESOURCE_DISCOUNT = 'Discount';

	public const RESOURCE_DOWNLOAD = 'Download';

	public const RESOURCE_EMPLOYEE = 'Employee';

	public const RESOURCE_ESTIMATE = 'Estimate';

	public const RESOURCE_INVOICE = 'Invoice';

	public const RESOURCE_INVENTORYADJUSTMENT = 'InventoryAdjustment';

	public const RESOURCE_ITEM = 'Item';

	public const RESOURCE_ITEMCONSOLIDATED = 'ItemConsolidated';

	public const RESOURCE_ITEMRECEIPT = 'ItemReceipt';

	public const RESOURCE_PAYROLLITEM = 'PayrollItem';

	public const RESOURCE_JOB = 'Job';

	public const RESOURCE_JOURNALENTRY = 'JournalEntry';

	public const RESOURCE_PAYMENT = 'Payment';

	public const RESOURCE_PAYMENTMETHOD = 'PaymentMethod';

	public const RESOURCE_PREFERENCES = 'Preferences';

	public const RESOURCE_PURCHASEORDER = 'PurchaseOrder';

	public const RESOURCE_REFUNDRECEIPT = 'RefundReceipt';

	public const RESOURCE_SALESORDER = 'SalesOrder';

	public const RESOURCE_SALESRECEIPT = 'SalesReceipt';

	public const RESOURCE_SALESREP = 'SalesRep';

	public const RESOURCE_SALESTAX = 'SalesTax';

	public const RESOURCE_SALESTAXCODE = 'SalesTaxCode';

	public const RESOURCE_SHIPMETHOD = 'ShipMethod';

	public const RESOURCE_TIMEACTIVITY = 'TimeActivity';

	public const RESOURCE_TAXAGENCY = 'TaxAgency';

	/**
	 * IDS v2 - QuickBooks Desktop
	 */
	public const RESOURCE_TERM = 'Term';

	/**
	 * IDS v2 - QuickBooks Online
	 */
	public const RESOURCE_SALESTERM = 'Sales-Term';

	public const RESOURCE_UOM = 'UOM';
	public const RESOURCE_UNITOFMEASURE = 'UOM';

	public const RESOURCE_VENDOR = 'Vendor';

	public const RESOURCE_VENDORCREDIT = 'VendorCredit';

	/**
	 *
	 *
	 *
	 */
	static public function resourceToKeyType(string $resource): string
	{
		$txns = [
			static::RESOURCE_BILL,
			static::RESOURCE_BILLPAYMENT,
			static::RESOURCE_BILLPAYMENTCREDITCARD,
			static::RESOURCE_CHANGEDATADELETED,
			static::RESOURCE_CHECK,
			static::RESOURCE_CREDITMEMO,
			static::RESOURCE_ESTIMATE,
			static::RESOURCE_INVOICE,
			static::RESOURCE_ITEMRECEIPT,
			static::RESOURCE_JOURNALENTRY,
			static::RESOURCE_PAYMENT,
			static::RESOURCE_PURCHASEORDER,
			static::RESOURCE_SALESORDER,
			static::RESOURCE_SALESRECEIPT,
			static::RESOURCE_TIMEACTIVITY,
			static::RESOURCE_VENDORCREDIT,
		];

		if (in_array($resource, $txns))
		{
			return 'TransactionId';
		}

		return 'ListId';
	}

	static public function parseIDType(string $str): array
	{
		$str = trim($str, '{}');

		// @todo Add validation here so that it always returns the correct types (string/integer)
		$arr = explode('-', $str);

		if (count($arr) == 2)
		{
			$arr['domain'] = $arr[0];
			$arr['ID'] = $arr[1];

			return $arr;
		}

		return [0 => '', 'domain' => '', 1 => $str, 'ID' => $str ];
	}

	static public function buildIDType($domain, string $ID): string
	{
		return '{' . $domain . '-' . $ID . '}';
	}

	static public function usableIDType(string $str): string
	{
		return trim(str_replace('-', ':', $str), '{}:');
	}
}
