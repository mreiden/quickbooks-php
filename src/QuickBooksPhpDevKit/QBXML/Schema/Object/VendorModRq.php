<?php declare(strict_types=1);

/**
 * Schema object for: VendorModRq
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
class VendorModRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'VendorMod';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'ListID' => 'IDTYPE',
			'EditSequence' => 'STRTYPE',
			'Name' => 'STRTYPE',
			'IsActive' => 'BOOLTYPE',
			'ClassRef ListID' => 'IDTYPE',
			'ClassRef FullName' => 'STRTYPE',
			'CompanyName' => 'STRTYPE',
			'Salutation' => 'STRTYPE',
			'FirstName' => 'STRTYPE',
			'MiddleName' => 'STRTYPE',
			'LastName' => 'STRTYPE',
			'Suffix' => 'STRTYPE',
			'JobTitle' => 'STRTYPE',
			'VendorAddress Addr1' => 'STRTYPE',
			'VendorAddress Addr2' => 'STRTYPE',
			'VendorAddress Addr3' => 'STRTYPE',
			'VendorAddress Addr4' => 'STRTYPE',
			'VendorAddress Addr5' => 'STRTYPE',
			'VendorAddress City' => 'STRTYPE',
			'VendorAddress State' => 'STRTYPE',
			'VendorAddress PostalCode' => 'STRTYPE',
			'VendorAddress Country' => 'STRTYPE',
			'VendorAddress Note' => 'STRTYPE',
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
			'Phone' => 'STRTYPE',
			'Mobile' => 'STRTYPE',
			'Pager' => 'STRTYPE',
			'AltPhone' => 'STRTYPE',
			'Fax' => 'STRTYPE',
			'Email' => 'STRTYPE',
			'Cc' => 'STRTYPE',
			'Contact' => 'STRTYPE',
			'AltContact' => 'STRTYPE',
			'AdditionalContactRef ContactName' => 'STRTYPE',
			'AdditionalContactRef ContactValue' => 'STRTYPE',
			'ContactsMod ListID' => 'IDTYPE',
			'ContactsMod EditSequence' => 'STRTYPE',
			'ContactsMod Salutation' => 'STRTYPE',
			'ContactsMod FirstName' => 'STRTYPE',
			'ContactsMod MiddleName' => 'STRTYPE',
			'ContactsMod LastName' => 'STRTYPE',
			'ContactsMod JobTitle' => 'STRTYPE',
			'ContactsMod AdditionalContactRef ContactName' => 'STRTYPE',
			'ContactsMod AdditionalContactRef ContactValue' => 'STRTYPE',
			'NameOnCheck' => 'STRTYPE',
			'AccountNumber' => 'STRTYPE',
			'Notes' => 'STRTYPE',
			'AdditionalNotesMod NoteID' => 'INTTYPE',
			'AdditionalNotesMod Note' => 'STRTYPE',
			'VendorTypeRef ListID' => 'IDTYPE',
			'VendorTypeRef FullName' => 'STRTYPE',
			'TermsRef ListID' => 'IDTYPE',
			'TermsRef FullName' => 'STRTYPE',
			'CreditLimit' => 'AMTTYPE',
			'VendorTaxIdent' => 'STRTYPE',
			'IsVendorEligibleFor1099' => 'BOOLTYPE',
			'BillingRateRef ListID' => 'IDTYPE',
			'BillingRateRef FullName' => 'STRTYPE',
			'SalesTaxCodeRef ListID' => 'IDTYPE',
			'SalesTaxCodeRef FullName' => 'STRTYPE',
			'SalesTaxCountry' => 'ENUMTYPE',
			'IsSalesTaxAgency' => 'BOOLTYPE',
			'SalesTaxReturnRef ListID' => 'IDTYPE',
			'SalesTaxReturnRef FullName' => 'STRTYPE',
			'TaxRegistrationNumber' => 'STRTYPE',
			'ReportingPeriod' => 'ENUMTYPE',
			'IsTaxTrackedOnPurchases' => 'BOOLTYPE',
			'TaxOnPurchasesAccountRef ListID' => 'IDTYPE',
			'TaxOnPurchasesAccountRef FullName' => 'STRTYPE',
			'IsTaxTrackedOnSales' => 'BOOLTYPE',
			'TaxOnSalesAccountRef ListID' => 'IDTYPE',
			'TaxOnSalesAccountRef FullName' => 'STRTYPE',
			'IsTaxOnTax' => 'BOOLTYPE',
			'PrefillAccountRef ListID' => 'IDTYPE',
			'PrefillAccountRef FullName' => 'STRTYPE',
			'CurrencyRef ListID' => 'IDTYPE',
			'CurrencyRef FullName' => 'STRTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'ListID' => 0,
			'EditSequence' => 16,
			'Name' => 41,
			'IsActive' => 0,
			'ClassRef ListID' => 0,
			'ClassRef FullName' => 159,
			'CompanyName' => 41,
			'Salutation' => 15,
			'FirstName' => 25,
			'MiddleName' => 5,
			'LastName' => 25,
			'Suffix' => 10,
			'JobTitle' => 41,
			'VendorAddress Addr1' => 41,
			'VendorAddress Addr2' => 41,
			'VendorAddress Addr3' => 41,
			'VendorAddress Addr4' => 41,
			'VendorAddress Addr5' => 41,
			'VendorAddress City' => 31,
			'VendorAddress State' => 21,
			'VendorAddress PostalCode' => 13,
			'VendorAddress Country' => 31,
			'VendorAddress Note' => 41,
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
			'Phone' => 21,
			'Mobile' => 21,
			'Pager' => 21,
			'AltPhone' => 21,
			'Fax' => 21,
			'Email' => 1023,
			'Cc' => 1023,
			'Contact' => 41,
			'AltContact' => 41,
			'AdditionalContactRef ContactName' => 40,
			'AdditionalContactRef ContactValue' => 255,
			'ContactsMod ListID' => 0,
			'ContactsMod EditSequence' => 16,
			'ContactsMod Salutation' => 15,
			'ContactsMod FirstName' => 25,
			'ContactsMod MiddleName' => 5,
			'ContactsMod LastName' => 25,
			'ContactsMod JobTitle' => 41,
			'ContactsMod AdditionalContactRef ContactName' => 40,
			'ContactsMod AdditionalContactRef ContactValue' => 255,
			'NameOnCheck' => 41,
			'AccountNumber' => 99,
			'Notes' => 4095,
			'AdditionalNotesMod NoteID' => 0,
			'AdditionalNotesMod Note' => 41,
			'VendorTypeRef ListID' => 0,
			'VendorTypeRef FullName' => 159,
			'TermsRef ListID' => 0,
			'TermsRef FullName' => 159,
			'CreditLimit' => 0,
			'VendorTaxIdent' => 15,
			'IsVendorEligibleFor1099' => 0,
			'BillingRateRef ListID' => 0,
			'BillingRateRef FullName' => 159,
			'SalesTaxCodeRef ListID' => 0,
			'SalesTaxCodeRef FullName' => 159,
			'SalesTaxCountry' => 0,
			'IsSalesTaxAgency' => 0,
			'SalesTaxReturnRef ListID' => 0,
			'SalesTaxReturnRef FullName' => 159,
			'TaxRegistrationNumber' => 30,
			'ReportingPeriod' => 0,
			'IsTaxTrackedOnPurchases' => 0,
			'TaxOnPurchasesAccountRef ListID' => 0,
			'TaxOnPurchasesAccountRef FullName' => 159,
			'IsTaxTrackedOnSales' => 0,
			'TaxOnSalesAccountRef ListID' => 0,
			'TaxOnSalesAccountRef FullName' => 159,
			'IsTaxOnTax' => 0,
			'PrefillAccountRef ListID' => 0,
			'PrefillAccountRef FullName' => 159,
			'CurrencyRef ListID' => 0,
			'CurrencyRef FullName' => 159,
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
			'ListID' => 999.99,
			'EditSequence' => 999.99,
			'Name' => 999.99,
			'IsActive' => 3.0,
			'ClassRef ListID' => 999.99,
			'ClassRef FullName' => 999.99,
			'CompanyName' => 999.99,
			'Salutation' => 999.99,
			'FirstName' => 999.99,
			'MiddleName' => 999.99,
			'LastName' => 999.99,
			'Suffix' => 999.99,
			'JobTitle' => 12.0,
			'VendorAddress Addr1' => 999.99,
			'VendorAddress Addr2' => 999.99,
			'VendorAddress Addr3' => 999.99,
			'VendorAddress Addr4' => 2.0,
			'VendorAddress Addr5' => 6.0,
			'VendorAddress City' => 999.99,
			'VendorAddress State' => 999.99,
			'VendorAddress PostalCode' => 999.99,
			'VendorAddress Country' => 999.99,
			'VendorAddress Note' => 6.0,
			'ShipAddress Addr1' => 999.99,
			'ShipAddress Addr2' => 999.99,
			'ShipAddress Addr3' => 999.99,
			'ShipAddress Addr4' => 2.0,
			'ShipAddress Addr5' => 6.0,
			'ShipAddress City' => 999.99,
			'ShipAddress State' => 999.99,
			'ShipAddress PostalCode' => 999.99,
			'ShipAddress Country' => 999.99,
			'ShipAddress Note' => 6.0,
			'Phone' => 999.99,
			'Mobile' => 999.99,
			'Pager' => 999.99,
			'AltPhone' => 999.99,
			'Fax' => 999.99,
			'Email' => 999.99,
			'Cc' => 12.0,
			'Contact' => 999.99,
			'AltContact' => 999.99,
			'AdditionalContactRef ContactName' => 999.99,
			'AdditionalContactRef ContactValue' => 999.99,
			'ContactsMod ListID' => 999.99,
			'ContactsMod EditSequence' => 999.99,
			'ContactsMod Salutation' => 999.99,
			'ContactsMod FirstName' => 999.99,
			'ContactsMod MiddleName' => 999.99,
			'ContactsMod LastName' => 999.99,
			'ContactsMod JobTitle' => 12.0,
			'ContactsMod AdditionalContactRef ContactName' => 999.99,
			'ContactsMod AdditionalContactRef ContactValue' => 999.99,
			'NameOnCheck' => 999.99,
			'AccountNumber' => 999.99,
			'Notes' => 3.0,
			'AdditionalNotesMod NoteID' => 999.99,
			'AdditionalNotesMod Note' => 6.0,
			'VendorTypeRef ListID' => 999.99,
			'VendorTypeRef FullName' => 999.99,
			'TermsRef ListID' => 999.99,
			'TermsRef FullName' => 999.99,
			'CreditLimit' => 3.0,
			'VendorTaxIdent' => 3.0,
			'IsVendorEligibleFor1099' => 3.0,
			'BillingRateRef ListID' => 999.99,
			'BillingRateRef FullName' => 999.99,
			'SalesTaxCodeRef ListID' => 999.99,
			'SalesTaxCodeRef FullName' => 999.99,
			'SalesTaxCountry' => 8.0,
			'IsSalesTaxAgency' => 8.0,
			'SalesTaxReturnRef ListID' => 999.99,
			'SalesTaxReturnRef FullName' => 999.99,
			'TaxRegistrationNumber' => 8.0,
			'ReportingPeriod' => 8.0,
			'IsTaxTrackedOnPurchases' => 8.0,
			'TaxOnPurchasesAccountRef ListID' => 999.99,
			'TaxOnPurchasesAccountRef FullName' => 999.99,
			'IsTaxTrackedOnSales' => 8.0,
			'TaxOnSalesAccountRef ListID' => 999.99,
			'TaxOnSalesAccountRef FullName' => 999.99,
			'IsTaxOnTax' => 8.0,
			'PrefillAccountRef ListID' => 999.99,
			'PrefillAccountRef FullName' => 999.99,
			'CurrencyRef ListID' => 999.99,
			'CurrencyRef FullName' => 999.99,
			'IncludeRetElement' => 4.0,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'ListID' => false,
			'EditSequence' => false,
			'Name' => false,
			'IsActive' => false,
			'ClassRef ListID' => false,
			'ClassRef FullName' => false,
			'CompanyName' => false,
			'Salutation' => false,
			'FirstName' => false,
			'MiddleName' => false,
			'LastName' => false,
			'Suffix' => false,
			'JobTitle' => false,
			'VendorAddress Addr1' => false,
			'VendorAddress Addr2' => false,
			'VendorAddress Addr3' => false,
			'VendorAddress Addr4' => false,
			'VendorAddress Addr5' => false,
			'VendorAddress City' => false,
			'VendorAddress State' => false,
			'VendorAddress PostalCode' => false,
			'VendorAddress Country' => false,
			'VendorAddress Note' => false,
			'ShipAddress Addr1' => false,
			'ShipAddress Addr2' => false,
			'ShipAddress Addr3' => false,
			'ShipAddress Addr4' => false,
			'ShipAddress Addr5' => false,
			'ShipAddress City' => false,
			'ShipAddress State' => false,
			'ShipAddress PostalCode' => false,
			'ShipAddress Country' => false,
			'ShipAddress Note' => false,
			'Phone' => false,
			'Mobile' => false,
			'Pager' => false,
			'AltPhone' => false,
			'Fax' => false,
			'Email' => false,
			'Cc' => false,
			'Contact' => false,
			'AltContact' => false,
			'AdditionalContactRef ContactName' => false,
			'AdditionalContactRef ContactValue' => false,
			'ContactsMod ListID' => false,
			'ContactsMod EditSequence' => false,
			'ContactsMod Salutation' => false,
			'ContactsMod FirstName' => false,
			'ContactsMod MiddleName' => false,
			'ContactsMod LastName' => false,
			'ContactsMod JobTitle' => false,
			'ContactsMod AdditionalContactRef ContactName' => false,
			'ContactsMod AdditionalContactRef ContactValue' => false,
			'NameOnCheck' => false,
			'AccountNumber' => false,
			'Notes' => false,
			'AdditionalNotesMod NoteID' => false,
			'AdditionalNotesMod Note' => false,
			'VendorTypeRef ListID' => false,
			'VendorTypeRef FullName' => false,
			'TermsRef ListID' => false,
			'TermsRef FullName' => false,
			'CreditLimit' => false,
			'VendorTaxIdent' => false,
			'IsVendorEligibleFor1099' => false,
			'BillingRateRef ListID' => false,
			'BillingRateRef FullName' => false,
			'SalesTaxCodeRef ListID' => false,
			'SalesTaxCodeRef FullName' => false,
			'SalesTaxCountry' => false,
			'IsSalesTaxAgency' => false,
			'SalesTaxReturnRef ListID' => false,
			'SalesTaxReturnRef FullName' => false,
			'TaxRegistrationNumber' => false,
			'ReportingPeriod' => false,
			'IsTaxTrackedOnPurchases' => false,
			'TaxOnPurchasesAccountRef ListID' => false,
			'TaxOnPurchasesAccountRef FullName' => false,
			'IsTaxTrackedOnSales' => false,
			'TaxOnSalesAccountRef ListID' => false,
			'TaxOnSalesAccountRef FullName' => false,
			'IsTaxOnTax' => false,
			'PrefillAccountRef ListID' => false,
			'PrefillAccountRef FullName' => false,
			'CurrencyRef ListID' => false,
			'CurrencyRef FullName' => false,
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
			'ListID',
			'EditSequence',
			'Name',
			'IsActive',
			'ClassRef',
			'ClassRef ListID',
			'ClassRef FullName',
			'CompanyName',
			'Salutation',
			'FirstName',
			'MiddleName',
			'LastName',
			'Suffix',
			'JobTitle',
			'VendorAddress',
			'VendorAddress Addr1',
			'VendorAddress Addr2',
			'VendorAddress Addr3',
			'VendorAddress Addr4',
			'VendorAddress Addr5',
			'VendorAddress City',
			'VendorAddress State',
			'VendorAddress PostalCode',
			'VendorAddress Country',
			'VendorAddress Note',
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
			'Phone',
			'Mobile',
			'Pager',
			'AltPhone',
			'Fax',
			'Email',
			'Cc',
			'Contact',
			'AltContact',
			'AdditionalContactRef',
			'AdditionalContactRef ContactName',
			'AdditionalContactRef ContactValue',
			'ContactsMod',
			'ContactsMod ListID',
			'ContactsMod EditSequence',
			'ContactsMod Salutation',
			'ContactsMod FirstName',
			'ContactsMod MiddleName',
			'ContactsMod LastName',
			'ContactsMod JobTitle',
			'ContactsMod AdditionalContactRef ContactName',
			'ContactsMod AdditionalContactRef ContactValue',
			'NameOnCheck',
			'AccountNumber',
			'Notes',
			'AdditionalNotesMod',
			'AdditionalNotesMod NoteID',
			'AdditionalNotesMod Note',
			'VendorTypeRef',
			'VendorTypeRef ListID',
			'VendorTypeRef FullName',
			'TermsRef',
			'TermsRef ListID',
			'TermsRef FullName',
			'CreditLimit',
			'VendorTaxIdent',
			'IsVendorEligibleFor1099',
			'BillingRateRef',
			'BillingRateRef ListID',
			'BillingRateRef FullName',
			'SalesTaxCodeRef',
			'SalesTaxCodeRef ListID',
			'SalesTaxCodeRef FullName',
			'SalesTaxCountry',
			'IsSalesTaxAgency',
			'SalesTaxReturnRef',
			'SalesTaxReturnRef ListID',
			'SalesTaxReturnRef FullName',
			'TaxRegistrationNumber',
			'ReportingPeriod',
			'IsTaxTrackedOnPurchases',
			'TaxOnPurchasesAccountRef',
			'TaxOnPurchasesAccountRef ListID',
			'TaxOnPurchasesAccountRef FullName',
			'IsTaxTrackedOnSales',
			'TaxOnSalesAccountRef',
			'TaxOnSalesAccountRef ListID',
			'TaxOnSalesAccountRef FullName',
			'IsTaxOnTax',
			'PrefillAccountRef',
			'PrefillAccountRef ListID',
			'PrefillAccountRef FullName',
			'CurrencyRef',
			'CurrencyRef ListID',
			'CurrencyRef FullName',
			'IncludeRetElement',
		];

		return $paths;
	}
}
