<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\QBXML;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;

class QbxmlTestdataGenerator
{
	public static function Account(string $accountType, string $accountFullName, string $accountDescription, float $openingBalance = 0): \QuickBooksPhpDevKit\QBXML\Object\Account
	{
		$qbAccount = new \QuickBooksPhpDevKit\QBXML\Object\Account();

		// ListID is Quickbooks Unique Identifier
		$qbAccount->setListId('70000007-1234567890');
		// EditSequence is a Secondary Quickbooks Unique Identifier
		$qbAccount->setEditSequence(9876543210);

		// Set the Account Active/Inactive
		$qbAccount->setIsActive(true);

		// Set the AccountType
		$qbAccount->setAccountType($accountType);

		// Set the Default Customer Terms
		$qbAccount->setFullName($accountFullName);

		// Set the Account Description
		$qbAccount->setDescription($accountDescription);

		if ($accountType == 'Bank')
		{
			// Set the Bank Account Number (bank account, credit card number, etc)
			$qbAccount->setBankNumber('0011 123 9999');

			// Set the Account's Opening Balance
			if ($openingBalance > 0)
			{
				$qbAccount->setOpenBalance($openingBalance);
			}

			// Set the Account's Opening Balance Date
			$qbAccount->setOpenBalanceDate('2019-07-07');
		}


		// Do these even exist in AccountAdd and AccountMod?
		//$qbAccount->setSpecialAccountType('SPECIAL');
		//$qbAccount->setCashFlowClassification('CashFlowPositive');

		return $qbAccount;
	}

	public static function Bill(?string $vendorFullName, ?string $vendorListID = null, ?string $customerFullName = null): \QuickBooksPhpDevKit\QBXML\Object\Bill
	{
		if (null === $vendorFullName && null === $vendorListID)
		{
			throw new \Exception('VendorFullName and/or VendorListID must be included for a Bill in '. __METHOD__);
		}

		$qbBill = new \QuickBooksPhpDevKit\QBXML\Object\Bill();

		// ListID is Quickbooks Unique Identifier
		$qbBill->setTxnId('70000005-1234567890');
		// EditSequence is a Secondary Quickbooks Unique Identifier
		$qbBill->setEditSequence(9876543210);

		// Set the Bill's Vendor
		if (null !== $vendorFullName)
		{
			$qbBill->setVendorFullname($vendorFullName);
		}
		// - AND/OR -
		if (null !== $vendorListID)
		{
			$qbBill->setVendorListID($vendorListID);
		}

		// Set the Date of the Bill
		$qbBill->setTxnDate('2019-07-07');

		// Set the Due Date
		$qbBill->setDueDate('2019-07-27');

		// Set the Bill's Reference Number
		$qbBill->setRefNumber('Invoice 20002');

		// Set a Bill Memo
		$qbBill->setMemo('Vendor performed a service.');


		$expenseLine = new \QuickBooksPhpDevKit\QBXML\Object\Bill\ExpenseLine();
		$expenseLine->setTxnLineID(-1);
		$expenseLine->setAccountFullName('Taxes Owed:Licensing');
		$expenseLine->setAmount(1500.00);
		$expenseLine->setMemo('Licensing Tax and related fees');
		if (null !== $customerFullName)
		{
			$expenseLine->setBillableStatus('Billable');
			$expenseLine->setCustomerFullName($customerFullName);
		}
		$qbBill->addExpenseLine($expenseLine);

		$expenseLine = new \QuickBooksPhpDevKit\QBXML\Object\Bill\ExpenseLine();
		$expenseLine->setTxnLineID(-1);
		$expenseLine->setAccountFullName('Taxes Owed:Business');
		$expenseLine->setAmount(542.24);
		$expenseLine->setMemo('Local Business Tax');
		if (null !== $customerFullName)
		{
			$expenseLine->setBillableStatus('NotBillable');
			$expenseLine->setCustomerFullName($customerFullName);
		}
		$qbBill->addExpenseLine($expenseLine);

		return $qbBill;
	}

	public static function CreditMemo($refNumber, $CreditMemoDate, string $customerFullName, ?string $poNumber = null, ?string $salesRepName = null): \QuickBooksPhpDevKit\QBXML\Object\CreditMemo
	{
		$qbCreditMemo = new \QuickBooksPhpDevKit\QBXML\Object\CreditMemo();

		$qbCreditMemo->setIsToBePrinted(false);
		$qbCreditMemo->setisToBeEmailed(true);

		// Sets the Quickbooks Class (Business Segment)
		$qbCreditMemo->setClassName('QuickBooksClass');
		//$qbCreditMemo->setTermsName('11% 12 Net 31');
		//$qbCreditMemo->setTemplateName('Intuit Service Invoice');

		$qbCreditMemo->setRefNumber($refNumber);
		$qbCreditMemo->setTxnDate($CreditMemoDate);
		$qbCreditMemo->setDueDate('2019-08-03');
		$qbCreditMemo->setPONumber($poNumber);

		$qbCreditMemo->setCustomerFullName($customerFullName);
		if (null !== $salesRepName)
		{
			$qbCreditMemo->setSalesRepName($salesRepName);
		}

		$qbCreditMemo->setCustomerSalesTaxCodeFullName('Non');

		// QuickBooks addresses cannot exceed 5 lines, so you can use:
		//  1) Address lines 1-3 plus city, state, zip, and country
		// *2) Address lines 1-4 plus city, state, and zip
		//  3) Address lines 1-5 without specifying any other fields (No city, state, zip, province, or country)
		$qbCreditMemo->setBillAddress(
			'My Customer',			// Address Line 1
			'attn: John Doe',		// Address Line 2
			'123 Billing Street',	// Address Line 3
			'APT 5555',				// Address Line 4
			'',						// Address Line 5
			'BillCity',				// City
			'NY',					// State
			'',						// Province
			'10019',				// Postal Code
			'',						// Country
			''						// Note for Address
		);
		$qbCreditMemo->setShipAddress(
			'My Customer',			// Address Line 1
			'attn: John Doe',		// Address Line 2
			'123 Ship Street',		// Address Line 3
			'APT 5555',				// Address Line 4
			'',						// Address Line 5
			'ShipCity',				// City
			'NY',					// State
			'',						// Province
			'10019',				// Postal Code
			'',						// Country
			''						// Note for Address
		);

		// Need for CreditMemoMod requests
		$qbCreditMemo->setTxnID('7777-1234567890');
		$qbCreditMemo->setEditSequence(9876543210);


		$line = new \QuickBooksPhpDevKit\QBXML\Object\CreditMemo\CreditMemoLine();
		$line->setItemFullName('Items:ServiceItem1');
		$line->setDesc('First ServiceItem');
		$line->setQuantity(99);
		$line->setRate(1.15);
		// A TnxLineID of -1 means add a new line item.
		$line->setTxnLineID(-1);
		$qbCreditMemo->addCreditMemoLine($line);

		$line = new \QuickBooksPhpDevKit\QBXML\Object\CreditMemo\CreditMemoLine();
		$line->setItemFullName('Items:ServiceItem2');
		$line->setDesc('Second ServiceItem');
		$line->setAmount(15.67);
		// A TnxLineID of -1 means add a new line item.
		$line->setTxnLineID(-1);
		$qbCreditMemo->addCreditMemoLine($line);

		return $qbCreditMemo;
	}

	public static function Customer(string $customerName, ?string $parentFullName = null, ?string $salesRepName = null): \QuickBooksPhpDevKit\QBXML\Object\Customer
	{
		$qbCustomer = new \QuickBooksPhpDevKit\QBXML\Object\Customer();

		// ListID is Quickbooks Unique Identifier
		$qbCustomer->setListId('70000077-1286207897');
		// EditSequence is a Secondary Quickbooks Unique Identifier
		$qbCustomer->setEditSequence(1191523643);

		// Set the Quickbooks Class (Business Segment)
		// NOTE!!!: Only available in QuickBooks Enterprise
		//$qbCustomer->setClassName('QuickBooksClass');

		// Set the Default Customer Terms
		$qbCustomer->setTermsFullName('11% 12 Net 31');

		// Set our Customer ID (Our Website ID)
		$websiteAccountNumber = 12345;
		$qbCustomer->setAccountNumber($websiteAccountNumber);

		// Set Sales Representative Name (or initials)
		if (null !== $salesRepName)
		{
			$qbCustomer->setSalesRepName('SR');
		}

		$qbCustomer->setSalesTaxCodeName('Non');

		// This is the Customer Name that shows in QuickBook's customer list
		// I like to include my web app's id (e.g. "My Customer Name -12345")
		$qbCustomer->setName($customerName);

		// Set the Parent of this Customer.  This makes it a Job of the Customer (e.g. Customer:Job)
		if (null !== $parentFullName)
		{
			$qbCustomer->setParentFullName('Parent');
		}

		// Set the Company name (Not customer name)
		$qbCustomer->setCompanyName('My Company Name');

		// Set the Customer's Name
		$qbCustomer->setFirstName('John');
		$qbCustomer->setMiddleName('Q.');
		$qbCustomer->setLastName('Doe');

		// Set the Customer's Email and Primary, Alternate, and Fax Numbers
		$qbCustomer->setEmail('someone@example.com');
		$qbCustomer->setPhone('(111) 555-5550');
		$qbCustomer->setAltPhone(1115555551);
		$qbCustomer->setFax('(111) 555-5555');

		// QuickBooks addresses cannot exceed 5 lines, so you can use:
		//  1) Address lines 1-3 plus city, state, zip, and country
		// *2) Address lines 1-4 plus city, state, and zip
		//  3) Address lines 1-5 without specifying any other fields (No city, state, zip, province, or country)
		$qbCustomer->setBillAddress(
			'My Customer',			// Address Line 1
			'attn: John Q. Doe',	// Address Line 2
			'123 Billing Street',	// Address Line 3
			'APT 5555',				// Address Line 4
			'',						// Address Line 5
			'BillCity',				// City
			'NY',					// State
			'',						// Province
			'10019',				// Postal Code
			'',						// Country
			''						// Note for Address
		);
		$qbCustomer->setShipAddress(
			'My Customer',			// Address Line 1
			'attn: John Q. Doe',	// Address Line 2
			'123 Ship Street',		// Address Line 3
			'APT 5555',				// Address Line 4
			'',						// Address Line 5
			'ShipCity',				// City
			'NY',					// State
			'',						// Province
			'10019',				// Postal Code
			'',						// Country
			''						// Note for Address
		);

		return $qbCustomer;
	}

	public static function Invoice($refNumber, $invoiceDate, string $customerFullName, ?string $poNumber = null, ?string $salesRepName = null): \QuickBooksPhpDevKit\QBXML\Object\Invoice
	{
		$qbInvoice = new \QuickBooksPhpDevKit\QBXML\Object\Invoice();

		$qbInvoice->setIsToBePrinted(false);
		$qbInvoice->setisToBeEmailed(true);

		// Sets the Quickbooks Class (Business Segment)
		$qbInvoice->setClassName('QuickBooksClass');
		$qbInvoice->setTermsName('11% 12 Net 31');
		$qbInvoice->setTemplateName('Intuit Service Invoice');
		$qbInvoice->setIncludeLinkedTxns(true);

		$qbInvoice->setRefNumber($refNumber);
		$qbInvoice->setTxnDate($invoiceDate);
		$qbInvoice->setDueDate('2019-08-03');
		$qbInvoice->setPONumber($poNumber);

		$qbInvoice->setCustomerFullName($customerFullName);
		if (null !== $salesRepName)
		{
			$qbInvoice->setSalesRepName($salesRepName);
		}

		$qbInvoice->setCustomerSalesTaxCodeFullName('Non');

		// QuickBooks addresses cannot exceed 5 lines, so you can use:
		//  1) Address lines 1-3 plus city, state, zip, and country
		// *2) Address lines 1-4 plus city, state, and zip
		//  3) Address lines 1-5 without specifying any other fields (No city, state, zip, province, or country)
		$qbInvoice->setBillAddress(
			'My Customer',			// Address Line 1
			'attn: John Doe',		// Address Line 2
			'123 Billing Street',	// Address Line 3
			'APT 5555',				// Address Line 4
			'',						// Address Line 5
			'BillCity',				// City
			'NY',					// State
			'',						// Province
			'10019',				// Postal Code
			'',						// Country
			''						// Note for Address
		);
		$qbInvoice->setShipAddress(
			'My Customer',		// Address Line 1
			'attn: John Doe',	// Address Line 2
			'123 Ship Street',	// Address Line 3
			'APT 5555',			// Address Line 4
			'',					// Address Line 5
			'ShipCity',			// City
			'NY',				// State
			'',					// Province
			'10019',			// Postal Code
			'',					// Country
			''					// Note for Address
		);

		// Need for InvoiceMod requests
		$qbInvoice->setTxnID('7623-1234567890');
		$qbInvoice->setEditSequence(9876543210);


		$line = new \QuickBooksPhpDevKit\QBXML\Object\Invoice\InvoiceLine();
		$line->setItemFullName('Items:ServiceItem1');
		$line->setDesc('First ServiceItem');
		$line->setQuantity(111);
		$line->setRate(1.15);
		// A TnxLineID of -1 means add a new line item.
		$line->setTxnLineID(-1);
		$qbInvoice->addInvoiceLine($line);

		$line = new \QuickBooksPhpDevKit\QBXML\Object\Invoice\InvoiceLine();
		$line->setItemFullName('Items:ServiceItem2');
		$line->setDesc('Second ServiceItem');
		$line->setAmount(45.67);
		// A TnxLineID of -1 means add a new line item.
		$line->setTxnLineID(-1);
		$qbInvoice->addInvoiceLine($line);

		return $qbInvoice;
	}

	public static function ItemSites(): \QuickBooksPhpDevKit\QBXML\Object\ItemSites
	{
		$qbObject = new \QuickBooksPhpDevKit\QBXML\Object\ItemSites();

		$qbObject->addListID('70000100-1111111111');
		$qbObject->addListID('70000100-1111111112');
		$qbObject->addListID('70000100-1111111113');

		return $qbObject;
	}

	public static function Qbclass(string $name, bool $isActive = true): \QuickBooksPhpDevKit\QBXML\Object\Qbclass
	{
		$qbObject = new \QuickBooksPhpDevKit\QBXML\Object\Qbclass();

		$qbObject->setName($name);
		$qbObject->setIsActive($isActive);

		return $qbObject;
	}

	public static function ReceivePayment(): \QuickBooksPhpDevKit\QBXML\Object\ReceivePayment
	{
		$qbPayment = new \QuickBooksPhpDevKit\QBXML\Object\ReceivePayment();

		$qbPayment->setIsAutoApply(false);
		$qbPayment->setARAccountFullName('Accounts Receivable');
		$qbPayment->setDepositToAccountFullName('Undeposited Funds');
		$qbPayment->setTxnDate('2019-07-02');
		$qbPayment->setTotalAmount(543.21);
		$qbPayment->setPaymentMethodFullName('Check');
		$qbPayment->setRefNumber(1001);
		$qbPayment->setCustomerListID('80000196-1234567890');
		$qbPayment->setMemo('Invoice ####');

		// Add an invoice to this payment
		$qbAppliedToTxn = new \QuickBooksPhpDevKit\QBXML\Object\ReceivePayment\AppliedToTxn();
		$qbAppliedToTxn->setTxnID('7625-1234567890');
		$qbAppliedToTxn->setPaymentAmount(543.21);

		// Add the Applied-To_Transaction (i.e. Invoice) to the payment
		$qbPayment->addAppliedToTxn($qbAppliedToTxn);

		return $qbPayment;
	}

	public static function ServiceItem(string $itemName, string $accountName): \QuickBooksPhpDevKit\QBXML\Object\ServiceItem
	{
		$qbItem = new \QuickBooksPhpDevKit\QBXML\Object\ServiceItem();

		// ListID is Quickbooks Unique Identifier
		$qbItem->setListId('70000010-1234567890');
		// EditSequence is a Secondary Quickbooks Unique Identifier
		$qbItem->setEditSequence(9876543210);

		$qbItem->setIsActive(true);
		$qbItem->setFullName($itemName);
		$qbItem->setAccountFullName($accountName);

		$qbItem->setSalesTaxCodeName('Non');

		$price = 100 - ord(substr(strtoupper($itemName), -1));
		$price += $price/100;
		$qbItem->setPrice($price);

		$qbItem->setDescription("$itemName for $price");

		return $qbItem;
	}

	public static function StandardTerms(string $name, int $daysDue, ?int $daysDiscount = null, ?float $discountPercent = null): \QuickBooksPhpDevKit\QBXML\Object\StandardTerms
	{
		$qbObject = new \QuickBooksPhpDevKit\QBXML\Object\StandardTerms();

		$qbObject->setName($name);
		$qbObject->setIsActive(true);
		$qbObject->setStdDueDays($daysDue);

		if (null !== $daysDiscount && null !== $discountPercent)
		{
			$qbObject->setStdDiscountDays($daysDiscount);
			$qbObject->setDiscountPct($discountPercent);
		}

		return $qbObject;
	}

	public static function Vendor(): \QuickBooksPhpDevKit\QBXML\Object\Vendor
	{
		$qbVendor = new \QuickBooksPhpDevKit\QBXML\Object\Vendor();

		// ListID is Quickbooks Unique Identifier
		$qbVendor->setListId('70000007-1234567890');
		// EditSequence is a Secondary Quickbooks Unique Identifier
		$qbVendor->setEditSequence(9876543210);

		$qbVendor->setIsActive(true);
		$qbVendor->setVendorTypeRef('1099 Contractor');
		$qbVendor->setName('Vendor1');
		$qbVendor->setCompanyName('Vendor Company Name');
		$qbVendor->setFirstName('John');
		$qbVendor->setLastName('Doe');
		$qbVendor->setVendorAddress(
			'Vendor Company Name',	// Address Line 1
			'attn: John Doe',		// Address Line 2
			'123 Vendor Street',	// Address Line 3
			'STE 5555',				// Address Line 4
			'',						// Address Line 5
			'City',					// City
			'NY',					// State
			'10019',				// Postal Code
			'',						// Country
			''						// Note for Address
		);

		$qbVendor->setPhone('(555) 555-1234');
		$qbVendor->setAltPhone('(555) 555-4321');
		$qbVendor->setFax('(555) 555-2222');
		$qbVendor->setEmail('vendor@example.com');

		return $qbVendor;
	}
}
