<?php declare(strict_types=1);

/**
 * QuickBooks SalesReceipt object
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\SalesReceipt\DiscountLine;			// Sales Receipt discount line item
use QuickBooksPhpDevKit\QBXML\Object\SalesReceipt\SalesReceiptLine;	// Sales Receipt line item
use QuickBooksPhpDevKit\QBXML\Object\SalesReceipt\ShippingLine;			// Sales Receipt shipping line item
use QuickBooksPhpDevKit\QBXML\Object\SalesReceipt\SalesTaxLine;			// Sales Receipt sales tax line item

/**
 * QuickBooks Sales Receipts
 *
 * Sales receipts are like invoices and payments combined together, and are
 * usually used in cases where the purchase and payment are made at the same
 * time (store front purchases, shopping cart purchases by credit card, etc.)
 *
 */
class SalesReceipt extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks SalesReceipt object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Alias of {@link SalesReceipt::setTxnID()}
	 */
	public function setTransactionID(string $TxnID): bool
	{
		return $this->setTxnID($TxnID);
	}

	public function getTransactionID(): string
	{
		return $this->getTxnID();
	}

	/**
	 * Set the transaction ID of the SalesReceipt object
	 */
	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}

	public function getTxnID(): string
	{
		return $this->get('TxnID');
	}

	/**
	 * Set the customer ListID
	 */
	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID' , $ListID);
	}

	/**
	 * Set the customer ApplicationID (auto-replaced by the API with a ListID)
	 */
	public function setCustomerApplicationID($value): bool
	{
		return $this->set('CustomerRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CUSTOMER'], PackageInfo::QbId['LISTID'], $value));
	}

	/**
	 * Set the customer name
	 */
	public function setCustomerName(string $name): bool
	{
		return $this->set('CustomerRef FullName', $name);
	}

	public function setCustomerFullName(string $FullName): bool
	{
		return $this->setFullNameType('CustomerRef FullName', null, null, $FullName);
	}

	/**
	 * Get the customer ListID
	 */
	public function getCustomerListID(): string
	{
		return $this->get('CustomerRef ListID');
	}

	/**
	 * Get the customer name
	 */
	public function getCustomerName(): string
	{
		return $this->get('CustomerRef FullName');
	}

	/**
	 * Get the customer application ID
	 *
	 * @return mixed
	 */
	public function getCustomerApplicationID()
	{
		return $this->extractApplicationID($this->get('CustomerRef ' . PackageInfo::$API_APPLICATIONID));
	}

	/*
	public function setDiscountLineAmount($amount)
	{
		return $this->set('DiscountLine
	}

	public function setDiscountLineAccountName($name)
	{

	}

	public function setShippingLineAmount($amount)
	{

	}

	public function setShippingLineAccountName($name)
	{

	}

	public function setSalesTaxLineAmount($amount)
	{

	}

	public function setSalesTaxLineAccountName($name)
	{

	}
	*/

	public function setSalesTaxItemFullName(string $FullName): bool
	{
		return $this->setItemSalesTaxFullName($FullName);
	}

	public function setItemSalesTaxFullName(string $FullName): bool
	{
		return $this->setFullNameType('ItemSalesTaxRef FullName', null, null, $FullName);
	}

	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	public function getClassListID(): string
	{
		return $this->get('ClassRef ListID');
	}

	public function setClassApplicationID($value): bool
	{
		return $this->set('ClassRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CLASS'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getClassApplicationID()
	{
		return $this->get('ClassRef ' . PackageInfo::$API_APPLICATIONID);
	}



	/**
	 * Set the application ID for the shipping method
	 *
	 * @param mixed $value		The shipping method primary key from your application
	 * @return 					boolean
	 */
	public function setShipMethodApplicationID($value): bool
	{
		return $this->set('ShipMethodRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_SHIPMETHOD'], PackageInfo::QbId['LISTID'], $value));
	}

	public function setShipMethodName(string $name): bool
	{
		return $this->set('ShipMethodRef FullName', $name);
	}

	public function setShipMethodListID(string $ListID): bool
	{
		return $this->set('ShipMethodRef ListID', $ListID);
	}

	public function getShipMethodName(): string
	{
		return $this->get('ShipMethodRef FullName');
	}

	public function getShipMethodListID(): string
	{
		return $this->get('ShipMethodRef ListID');
	}

	/**
	 * Set an invoice as pending
	 */
	public function setIsPending(?bool $pending): bool
	{
		return $this->setBooleanType('IsPending', $pending);
	}

	public function getIsPending(): ?bool
	{
		return $this->getBooleanType('IsPending');
	}

	public function setCheckNumber(string $check): bool
	{
		return $this->set('CheckNumber', $check);
	}

	public function getCheckNumber(): string
	{
		return $this->get('CheckNumber');
	}

	public function setPaymentMethodApplicationID($value): bool
	{
		return $this->set('PaymentMethodRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_PAYMENTMETHOD'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getPaymentMethodApplicationID()
	{
		return $this->get('PaymentMethodRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setPaymentMethodListID(string $ListID): bool
	{
		return $this->set('PaymentMethodRef ListID', $ListID);
	}

	public function setPaymentMethodName(string $name): bool
	{
		return $this->set('PaymentMethodRef FullName', $name);
	}

	public function getPaymentMethodListID(): string
	{
		return $this->get('PaymentMethodRef ListID');
	}

	public function getPaymentMethodName(): string
	{
		return $this->get('PaymentMethodRef FullName');
	}

	public function setDueDate($date): bool
	{
		return $this->setDateType('DueDate', $date);
	}

	public function getDueDate(string $format = null): string
	{
		return $this->getDateType('DueDate', $format);
	}

	public function setSalesRepApplicationID($value): bool
	{
		return $this->set('SalesRepRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(QUICKBOOKS_OBJECT_SALESREP, PackageInfo::QbId['LISTID'], $value));
	}

	public function getSalesRepApplicationID()
	{
		return $this->get('SalesRepRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setSalesRepListID(string $ListID): bool
	{
		return $this->set('SalesRepRef ListID', $ListID);
	}

	public function setSalesRepName(string $name): bool
	{
		return $this->set('SalesRepRef FullName', $name);
	}

	public function getSalesRepListID(): string
	{
		return $this->get('SalesRepRef ListID');
	}

	public function getSalesRepName(): string
	{
		return $this->get('SalesRepRef FullName');
	}



	public function setIsToBePrinted(?bool $printed): bool
	{
		return $this->setBooleanType('IsToBePrinted', $printed);
	}

	public function getIsToBePrinted(): ?bool
	{
		return $this->getBooleanType('IsToBePrinted');
	}

	public function setIsToBeEmailed(?bool $emailed): bool
	{
		return $this->setBooleanType('IsToBeEmailed', $emailed);
	}

	public function getIsToBeEmailed(): ?bool
	{
		return $this->getBooleanType('IsToBeEmailed');
	}

	/**
	 * Get the ship method application ID
	 *
	 * @return value
	 */
	public function getShipMethodApplicationID()
	{
		return $this->extractApplicationID($this->get('ShipMethodRef ' . PackageInfo::$API_APPLICATIONID));
	}

	public function setDepositToAccountApplicationID($value): bool
	{
		return $this->set('DepositToAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function setDepositToAccountListID(string $ListID): bool
	{
		return $this->set('DepositToAccountRef ListID', $ListID);
	}

	public function setDepositToAccountName(string $name): bool
	{
		return $this->set('DepositToAccountRef FullName', $name);
	}

	public function getDepositToAccountListID(): string
	{
		return $this->get('DepositToAccountRef ListID');
	}

	public function getDepositToAccountName(): string
	{
		return $this->get('DepositToAccountRef FullName');
	}

	/**
	 * Get the ARAccount application ID
	 *
	 * @return value
	 */
	public function getDepositToAccountApplicationID()
	{
		return $this->extractApplicationID($this->get('DepositToAccountRef ' . PackageInfo::$API_APPLICATIONID));
	}

	/**
	 * Set the transaction date
	 *
	 * @param string $date
	 * @return boolean
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Alias of {@link SalesReceipt::setTxnDate()}
	 */
	public function setTransactionDate($date): bool
	{
		return $this->setTxnDate($date);
	}

	public function getTxnDate(string $format = null): string
	{
		return $this->getDateType('TxnDate', $format);
	}

	public function getTransactionDate(string $format = null): string
	{
		return $this->getTxnDate($format);
	}

	/**
	 * Set the shipping date
	 *
	 * @param string $date
	 * @return boolean
	 */
	public function setShipDate($date): bool
	{
		return $this->setDateType('ShipDate', $date);
	}

	/**
	 * Get the shipping date
	 *
	 * @param string $format	The format you want the date in (as for {@link http://www.php.net/date})
	 * @return string
	 */
	public function getShipDate(string $format = null): string
	{
		return $this->getDateType('ShipDate', $format);
	}

	/**
	 * Set the reference number
	 */
	public function setRefNumber(string $str): bool
	{
		return $this->set('RefNumber', $str);
	}

	/**
	 * Get the reference number
	 */
	public function getRefNumber(): string
	{
		return $this->get('RefNumber');
	}

	public function setMemo(string $memo): bool
	{
		return $this->set('Memo', $memo);
	}

	public function getMemo(): string
	{
		return $this->get('Memo');
	}

	public function getFOB(): string
	{
		return $this->get('FOB');
	}

	public function setFOB(string $fob): bool
	{
		return $this->set('FOB', $fob);
	}

	public function setLinkToTxnID(string $TxnID): bool
	{
		return $this->set('LinkToTxnID', $TxnID);
	}

	public function getLinkToTxnID(): string
	{
		return $this->get('LinkToTxnID');
	}

	/**
	 *
	 */
	public function addSalesReceiptLine(SalesReceiptLine $obj): bool
	{
		$lines = $this->get('SalesReceiptLine');
		$lines[] = $obj;

		return $this->set('SalesReceiptLine', $lines);
	}

	/**
	 *
	 */
	public function getSalesReceiptLines(): array
	{
		return $this->getList('SalesReceiptLine');
	}

	/**
	 *
	 */
	public function getSalesReceiptLine(int $i): SalesReceiptLine
	{
		return $this->getListItem('SalesReceiptLine', $i);
	}

	/**
	 * Add a discount line (only supported by Online Edition as of 8.0)
	 */
	public function addDiscountLine(DiscountLine $obj): bool
	{
		return $this->addListItem('DiscountLine', $obj);
	}

	/**
	 * Add a sales tax line (only supported by Online Edition as of 8.0)
	 */
	public function addSalesTaxLine(SalesTaxLine $obj): bool
	{
		return $this->addListItem('SalesTaxLine', $obj);
	}

	/**
	 * Add a shipping line (only supported by Online Edition as of 8.0)
	 *
	 * @param QuickBooks_Object_SalesReceipt_ShippingLine
	 * @return boolean
	 */
	public function addShippingLine(ShippingLine $obj): bool
	{
		return $this->addListItem('ShippingLine', $obj);
	}

	/**
	 * Get an shipping address as an array (or a specific portion of the address as a string)
	 *
	 * @param string $part			A specific portion of the address to get (i.e. "Addr1" or "State")
	 * @param array $defaults		Default values if a value isn't filled in
	 * @return array				The address
	 */
	public function getShipAddress(string $part = null, array $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get('ShipAddress ' . $part);
		}

		return $this->getArray('ShipAddress *', $defaults);
	}

	/**
	 * Set the shipping address for the invoice
	 *
	 * @param string $addr1			Address line 1
	 * @param string $addr2			Address line 2
	 * @param string $addr3			Address line 3
	 * @param string $addr4			Address line 4
	 * @param string $addr5			Address line 5
	 * @param string $city			City
	 * @param string $state			State
	 * @param string $province		Province (Canadian editions of QuickBooks only!)
	 * @param string $postalcode	Postal code
	 * @param string $country		Country
	 * @param string $note			Notes
	 * @return void
	 */
	public function setShipAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $province = '', string $postalcode = '', string $country = '', string $note = ''): bool
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set('ShipAddress Addr' . $i, ${'addr' . $i});
		}

		$this->set('ShipAddress City', $city);
		$this->set('ShipAddress State', $state);
		$this->set('ShipAddress Province', $province);
		$this->set('ShipAddress PostalCode', $postalcode);
		$this->set('ShipAddress Country', $country);
		$this->set('ShipAddress Note', $note);

		return true;
	}

	/**
	 * Get the billing address
	 *
	 * @param string $part			A specific portion of the address to get (i.e. "Addr1" or "State")
	 * @param array $defaults		Default values if a value isn't filled in
	 * @return array				The address
	 */
	public function getBillAddress(string $part = null, array $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get('BillAddress ' . $part);
		}

		return $this->getArray('BillAddress *', $defaults);
	}

	/**
	 * Set the billing address for the invoice
	 *
	 * @param string $addr1			Address line 1
	 * @param string $addr2			Address line 2
	 * @param string $addr3			Address line 3
	 * @param string $addr4			Address line 4
	 * @param string $addr5			Address line 5
	 * @param string $city			City
	 * @param string $state			State
	 * @param string $province		Province (Canadian editions of QuickBooks only!)
	 * @param string $postalcode	Postal code
	 * @param string $country		Country
	 * @param string $note			Notes
	 * @return void
	 */
	public function setBillAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $province = '', string $postalcode = '', string $country = '', string $note = ''): bool
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set('BillAddress Addr' . $i, ${'addr' . $i});
		}

		$this->set('BillAddress City', $city);
		$this->set('BillAddress State', $state);
		$this->set('BillAddress Province', $province);
		$this->set('BillAddress PostalCode', $postalcode);
		$this->set('BillAddress Country', $country);
		$this->set('BillAddress Note', $note);

		return true;
	}

	public function setOther(string $other): bool
	{
		return $this->set('Other', $other);
	}

	public function getOther(): string
	{
		return $this->get('Other');
	}

	public function asList(string $request)
	{
		switch ($request)
		{
			case 'SalesReceiptAddRq':
				if (isset($this->_object['SalesReceiptLine']))
				{
					$this->_object['SalesReceiptLineAdd'] = $this->_object['SalesReceiptLine'];
				}

				if (isset($this->_object['ShippingLine']))
				{
					$this->_object['ShippingLineAdd'] = $this->_object['ShippingLine'];
				}

				if (isset($this->_object['SalesTaxLine']))
				{
					$this->_object['SalesTaxLineAdd'] = $this->_object['SalesTaxLine'];
				}

				if (isset($this->_object['DiscountLine']))
				{
					$this->_object['DiscountLineAdd'] = $this->_object['DiscountLine'];
				}
				break;

			case 'SalesReceiptModRq':
				if (isset($this->_object['SalesReceiptLine']))
				{
					$this->_object['SalesReceiptLineMod'] = $this->_object['SalesReceiptLine'];
				}
				break;
		}

		return parent::asList($request);
	}

	public function asXML(string $root = null, string $parent = null, $object = null)
	{
		//print('SalesReceipt got called asXML: ' . $root . ', ' . $parent . "\n");
		//print('sales receipt got called as: {' . $root . '}, {' . PackageInfo::Actions['ADD_SALESRECEIPT'] . "}\n");
		//exit;

		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($root)
		{
			case PackageInfo::Actions['ADD_SALESRECEIPT']:

				//if (isset($this->_object['InvoiceLine']))
				//{
				//	$this->_object['InvoiceLineAdd'] = $this->_object['InvoiceLine'];
				//}

				foreach ($object['SalesReceiptLineAdd'] as $key => $obj)
				{
					$obj->setOverride('SalesReceiptLineAdd');
				}

				if (!empty($object['ShippingLineAdd']))
				{
					foreach ($object['ShippingLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ShippingLineAdd');
					}
				}

				if (!empty($object['DiscountLineAdd']))
				{
					foreach ($object['DiscountLineAdd'] as $key => $obj)
					{
						$obj->setOverride('DiscountLineAdd');
					}
				}

				if (!empty($object['SalesTaxLineAdd']))
				{
					foreach ($object['SalesTaxLineAdd'] as $key => $obj)
					{
						$obj->setOverride('SalesTaxLineAdd');
					}
				}
				break;

			case PackageInfo::Actions['MOD_SALESRECEIPT']:
				if (isset($object['SalesReceiptLine']))
				{
					$object['SalesReceiptLineMod'] = $object['SalesReceiptLine'];
				}
				break;
		}

		//print_r($this->_object);

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell the type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_SALESRECEIPT'];
	}
}
