<?php declare(strict_types=1);

/**
 * QuickBooks Invoice object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\{
	PackageInfo,
	QBXML\AbstractQbxmlObject,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\Object\Invoice\{
	DiscountLine,
	InvoiceLine,
	ShippingLine,
	SalesTaxLine,
};

/**
 * QBXML\Object\Invoice class definition
 */
class Invoice extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks Invoice object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Alias of {@link Invoice::setTxnID()}
	 */
	public function setTransactionID(string $TxnID): bool
	{
		return $this->setTxnID($TxnID);
	}

	/**
	 * Set the transaction ID of the Invoice object
	 */
	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}

	/**
	 * Alias of {@link Invoice::getTxnID()}
	 */
	public function getTransactionID(): ?string
	{
		return $this->getTxnID();
	}

	/**
	 * Get the transaction ID for this invoice
	 */
	public function getTxnID(): ?string
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
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function setCustomerApplicationID($value): bool
	{
		return $this->set('CustomerRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CUSTOMER'], PackageInfo::QbId['LISTID'], $value));
	}

	/**
	 * Set the customer name
	 */
	public function setCustomerFullName(string $name): bool
	{
		return $this->set('CustomerRef FullName', $name);
	}

	/**
	 * @deprecated
	 */
	public function setCustomerName(string $name): bool
	{
		return $this->setCustomerFullName($name);
	}

	/**
	 * Get the customer ListID
	 */
	public function getCustomerListID(): ?string
	{
		return $this->get('CustomerRef ListID');
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

	/**
	 * Get the customer name
	 */
	public function getCustomerFullName(): ?string
	{
		return $this->get('CustomerRef FullName');
	}

	/**
	 * @deprecated
	 */
	public function getCustomerName(): ?string
	{
		return $this->getCustomerFullName();
	}


	/**
	 * Set the class application ID
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function setClassApplicationID($value): bool
	{
		return $this->set('ClassRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CLASS'], PackageInfo::QbId['LISTID'], $value));
	}

	public function setClassName(string $name): bool
	{
		return $this->set('ClassRef FullName', $name);
	}
	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	/**
	 * Set the class ListID for this invoice
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}
	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	/*
	public function getClassApplicationID()
	{
		return $this->extractApplicationID($this->get('ClassRef ' . PackageInfo::$API_APPLICATIONID));
	}

	public function setARAccountApplicationID($value)
	{
		return $this->set('ARAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}*/

	public function setARAccountListID(string $ListID): bool
	{
		return $this->set('ARAccountRef ListID', $ListID);
	}

	public function setARAccountName(string $name): bool
	{
		return $this->set('ARAccountRef FullName', $name);
	}

	public function getARAccountListID(): ?string
	{
		return $this->get('ARAccountRef ListID');
	}

	public function getARAccountName(): ?string
	{
		return $this->get('ARAccountRef FullName');
	}

	/**
	 * Get the ARAccount application ID
	 *
	 * @return value
	 */
	public function getARAccountApplicationID()
	{
		return $this->extractApplicationID($this->get('ARAccountRef ' . PackageInfo::$API_APPLICATIONID));
	}

	public function setTemplateApplicationID($value)
	{
		return $this->set('TemplateRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_TEMPLATE'], PackageInfo::QbId['LISTID'], $value));
	}

	public function setTemplateName(string $name): bool
	{
		return $this->set('TemplateRef FullName', $name);
	}

	public function setTemplateListID(string $ListID): bool
	{
		return $this->set('TemplateRef ListID', $ListID);
	}

	public function getTemplateName(): ?string
	{
		return $this->get('TemplateRef FullName');
	}

	public function getTemplateListID(): ?string
	{
		return $this->get('TemplateRef ListID');
	}

	/**
	 * Get the template application ID
	 *
	 * @return value
	 */
	public function getTemplateApplicationID()
	{
		return $this->extractApplicationID($this->get('TemplateRef ' . PackageInfo::$API_APPLICATIONID));
	}

	/**
	 * Set the transaction date
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Alias of {@link Invoice::setTxnDate()}
	 */
	public function setTransactionDate($date): bool
	{
		return $this->setTxnDate($date);
	}

	/**
	 * Get the transaction date
	 */
	public function getTxnDate(string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * Alias of {@link Invoice::getTxnDate()}
	 */
	public function getTransactionDate(): ?string
	{
		return $this->getTxnDate($format);
	}

	/**
	 * Set the reference number
	 */
	public function setRefNumber($str): bool
	{
		return $this->set('RefNumber', (string) $str);
	}

	/**
	 * Alias of {@link Invoice::setRefNumber()}
	 */
	public function setReferenceNumber($str): bool
	{
		return $this->setRefNumber($str);
	}

	/**
	 * Get the reference number
	 */
	public function getRefNumber(): ?string
	{
		return $this->get('RefNumber');
	}

	/**
	 * Alias of {@link Invoice::getRefNumber()}
	 */
	public function getReferenceNumber(): ?string
	{
		return $this->getRefNumber();
	}

	/**
	 * Get the shipping address as an array (or a specific portion of the address as a string)
	 *
	 * @param string $part			A specific portion of the address to get (i.e. "Addr1" or "State")
	 * @param array $defaults		Default values if a value isn't filled in
	 * @return array				The address
	 */
	public function getShipAddress(?string $part = null, array $defaults = [])
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
		$this->set('ShipAddress Addr1', $addr1);
		$this->set('ShipAddress Addr2', $addr2);
		$this->set('ShipAddress Addr3', $addr3);
		$this->set('ShipAddress Addr4', $addr4);
		$this->set('ShipAddress Addr5', $addr5);

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
	public function getBillAddress(?string $part = null, array $defaults = [])
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
		$this->set('BillAddress Addr1', $addr1);
		$this->set('BillAddress Addr2', $addr2);
		$this->set('BillAddress Addr3', $addr3);
		$this->set('BillAddress Addr4', $addr4);
		$this->set('BillAddress Addr5', $addr5);

		$this->set('BillAddress City', $city);
		$this->set('BillAddress State', $state);
		$this->set('BillAddress Province', $province);
		$this->set('BillAddress PostalCode', $postalcode);
		$this->set('BillAddress Country', $country);
		$this->set('BillAddress Note', $note);

		return true;
	}

	/**
	 * Set an invoice as pending (e.g. back-orders, unpaid orders, estimates)
	 */
	public function setIsPending(bool $pending): bool
	{
		return $this->setBooleanType('IsPending', $pending);
	}

	public function getIsPending(): bool
	{
		return $this->getBooleanType('IsPending');
	}

	public function setPONumber($num): bool
	{
		return $this->set('PONumber', (string) $num);
	}

	public function getPONumber(): ?string
	{
		return $this->get('PONumber');
	}

	public function setTermsListID(string $ListID): bool
	{
		return $this->set('TermsRef ListID', $ListID);
	}

	public function setTermsApplicationID($value): bool
	{
		return $this->set('TermsRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_TERMS'], PackageInfo::QbId['LISTID'], $value));
	}

	public function setTermsFullName(string $name): bool
	{
		return $this->set('TermsRef FullName', $name);
	}
	/**
	 * @deprecated
	 */
	public function setTermsName(string $name): bool
	{
		return $this->setTermsFullName($name);
	}

	public function getTermsFullName(): ?string
	{
		return $this->get('TermsRef FullName');
	}
	/**
	 * @deprecated
	 */
	public function getTermsName(): ?string
	{
		return $this->getTermsFullName();
	}


	public function getTermsListID(): ?string
	{
		return $this->get('TermsRef ListID');
	}

	/**
	 * Get the terms application ID for this invoice
	 *
	 * @return value
	 */
	public function getTermsApplicationID()
	{
		return $this->extractApplicationID($this->get('TermsRef ' . PackageInfo::$API_APPLICATIONID));
	}

	/**
	 * Set the due date for the invoice
	 */
	public function setDueDate($date): bool
	{
		return $this->setDateType('DueDate', $date);
	}

	/**
	 * Get the due date for the invoice
	 */
	public function getDueDate(string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('DueDate', $format);
	}

	/**
	 * Set Sales Rep Full Name
	 *
	 * NOTE: It can only be 5 characters, so it's really the "Sales Rep Initials" field in QuickBooks
	 */
	public function setSalesRepName(string $name): bool
	{
		return $this->set('SalesRepRef FullName', $name);
	}

	public function setSalesRepListID(string $ListID): bool
	{
		return $this->set('SalesRepRef ListID', $ListID);
	}

	public function setSalesRepApplicationID($value): bool
	{
		return $this->set('SalesRepRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_EMPLOYEE'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getSalesRepName(): ?string
	{
		return $this->get('SalesRepRef FullName');
	}

	public function getSalesRepListID(): ?string
	{
		return $this->get('SalesRepRef ListID');
	}

	public function getSalesRepApplicationID()
	{
		return $this->extractApplicationID($this->get('SalesRepRef ' . PackageInfo::$API_APPLICATIONID));
	}

	public function getFOB(): ?string
	{
		return $this->get('FOB');
	}

	public function setFOB($fob): bool
	{
		return $this->set('FOB', $fob);
	}

	public function setShipDate($date): bool
	{
		return $this->setDateType('ShipDate', $date);
	}

	public function getShipDate(string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('ShipDate', $format);
	}

	/**
	 * Set the application ID for the shipping method
	 *
	 * @param mixed $value		The shipping method primary key from your application
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

	public function getShipMethodName(): ?string
	{
		return $this->get('ShipMethodRef FullName');
	}

	public function getShipMethodListID(): ?string
	{
		return $this->get('ShipMethodRef ListID');
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

	/**
	 * Set the application ID for the payment method
	 *
	 * @param mixed $value		The payment method primary key from your application
	 */
	public function setPaymentMethodApplicationID($value): bool
	{
		return $this->set('PaymentMethodRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_PAYMENTMETHOD'], PackageInfo::QbId['LISTID'], $value));
	}

	public function setPaymentMethodName(string $name): bool
	{
		return $this->set('PaymentMethodRef FullName', $name);
	}

	public function setPaymentMethodListID(string $ListID): bool
	{
		return $this->set('PaymentMethodRef ListID', $ListID);
	}

	public function getPaymentMethodName(): ?string
	{
		return $this->get('PaymentMethodRef FullName');
	}

	public function getPaymentMethodListID(): ?string
	{
		return $this->get('PaymentMethodRef ListID');
	}

	/**
	 * Get the payment method application ID
	 *
	 * @return value
	 */
	public function getPaymentMethodApplicationID()
	{
		return $this->extractApplicationID($this->get('PaymentMethodRef ' . PackageInfo::$API_APPLICATIONID));
	}

	public function setSalesTaxItemListID(string $ListID): bool
	{
		return $this->set('ItemSalesTaxRef ListID', $ListID);
	}

	public function setSalesTaxItemApplicationID($value): bool
	{
		return $this->set('ItemSalesTaxRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_SALESTAXITEM'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getSalesTaxItemApplicationID()
	{
		return $this->get('ItemSalesTaxRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setSalesTaxItemFullName(string $name): bool
	{
		return $this->set('ItemSalesTaxRef FullName', $name);
	}
	/**
	 * @deprecated
	 */
	public function setSalesTaxItemName(string $name): bool
	{
		return $this->setSalesTaxItemFullName($name);
	}

	public function getSalesTaxItemFullName(): ?string
	{
		return $this->get('ItemSalesTaxRef FullName');
	}
	/**
	 * @deprecated
	 */
	public function getSalesTaxItemName(): ?string
	{
		return $this->getSalesTaxItemFullName();
	}

	public function getSalesTaxItemListID(): ?string
	{
		return $this->get('ItemSalesTaxRef ListID');
	}

	public function setMemo(string $memo): bool
	{
		return $this->set('Memo', $memo);
	}
	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	public function setIsToBePrinted(bool $printed): bool
	{
		return $this->setBooleanType('IsToBePrinted', $printed);
	}
	public function getIsToBePrinted(): ?bool
	{
		return $this->getBooleanType('IsToBePrinted');
	}

	public function setIsToBeEmailed(bool $emailed): bool
	{
		return $this->setBooleanType('IsToBeEmailed', $emailed);
	}
	public function getIsToBeEmailed(): ?bool
	{
		return $this->getBooleanType('IsToBeEmailed');
	}

	/**
	 * Set the tax code ListID for the customer
	 */
	public function setCustomerSalesTaxCodeListID(string $name): bool
	{
		return $this->set('CustomerSalesTaxCodeRef ListID', $name);
	}
	/**
	 * Get the tax code ListID for the customer
	 */
	public function getCustomerSalesTaxCodeListID(): ?string
	{
		return $this->get('CustomerSalesTaxCodeRef ListID');
	}

	/**
	 * Set the tax code FullName for the customer
	 */
	public function setCustomerSalesTaxCodeFullName(string $name): bool
	{
		return $this->set('CustomerSalesTaxCodeRef FullName', $name);
	}
	/**
	 * Get the tax code FullName for the customer
	 */
	public function getCustomerSalesTaxCodeFullName(): ?string
	{
		return $this->get('CustomerSalesTaxCodeRef FullName');
	}


	public function setLinkToTxnID(string $TxnID): bool
	{
		return $this->set('LinkToTxnID', $TxnID);
	}
	public function getLinkToTxnID(): ?string
	{
		return $this->get('LinkToTxnID');
	}

	public function setIncludeLinkedTxns(bool $includeLinkedTxns): bool
	{
		return $this->setBooleanType('IncludeLinkedTxns', $includeLinkedTxns);
	}
	public function getIncludeLinkedTxns(): ?bool
	{
		return $this->getBooleanType('IncludeLinkedTxns');
	}
	/*
	public function getInvoiceLines()
	{
		return $this->getList('InvoiceLine');
	}

	public function getInvoiceLine($which)
	{
		$list = $this->getInvoiceLines();

		if (isset($list[$which]))
		{
			return $list[$which];
		}

		return null;
	}
	*/

	/**
	 *
	 */
	public function addInvoiceLine(InvoiceLine $obj): bool
	{
		return $this->addListItem('InvoiceLine', $obj);
	}

	/*
	public function setInvoiceLineData($i, $key, $value)
	{
		$lines = $this->getInvoiceLines();
		if (isset($lines[$i]))
		{

		}

		return $this->set('InvoiceLine', $lines);
	}
	*/

	public function getInvoiceLine(int $i)
	{
		return $this->getListItem('InvoiceLine', $i);
	}

	public function listInvoiceLines(): array
	{
		return $this->getList('InvoiceLine');
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
	 */
	public function addShippingLine(ShippingLine $obj): bool
	{
		return $this->addListItem('ShippingLine', $obj);
	}

	/**
	 *
	 */
	public function setOther(string $other): bool
	{
		return $this->set('Other', $other);
	}

	public function getOther(): ?string
	{
		return $this->get('Other');
	}

	public function getBalanceRemaining(): ?string
	{
		return $this->getAmountType('BalanceRemaining');
	}

	public function setBalanceRemaining(float $amount): bool
	{
		return $this->setAmountType('BalanceRemaining', $amount);
	}

	public function getAppliedAmount(): ?string
	{
		return $this->getAmountType('AppliedAmount');
	}

	public function asList(string $request)
	{
		switch ($request)
		{
			case 'InvoiceAddRq':
				if (isset($this->_object['InvoiceLine']))
				{
					$this->_object['InvoiceLineAdd'] = $this->_object['InvoiceLine'];
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

			case 'InvoiceModRq':
				if (isset($this->_object['InvoiceLine']))
				{
					$this->_object['InvoiceLineMod'] = $this->_object['InvoiceLine'];
				}
				break;
		}

		return parent::asList($request);
	}

	public function asXML(?string $root = null, ?string $parent = null, ?array $object = null): Node
	{
		//print('INVOICE got called asXML: ' . $root . ', ' . $parent . "\n");
		//exit;

		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($root)
		{
			case PackageInfo::Actions['ADD_INVOICE']:

				//if (isset($this->_object['InvoiceLine']))
				//{
				//	$this->_object['InvoiceLineAdd'] = $this->_object['InvoiceLine'];
				//}

				if (!empty($object['InvoiceLineAdd']))
				{
					foreach ($object['InvoiceLineAdd'] as $key => $obj)
					{
						$obj->setOverride('InvoiceLineAdd');
					}
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

			case PackageInfo::Actions['MOD_INVOICE']:

				/*
				if (isset($object['InvoiceLine']))
				{
					$object['InvoiceLineMod'] = $object['InvoiceLine'];
				}
				*/

				if (!empty($object['InvoiceLineMod']))
				{
					foreach ($object['InvoiceLineMod'] as $key => $obj)
					{
						$obj->setOverride('InvoiceLineMod');
					}
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
		return PackageInfo::Actions['OBJECT_INVOICE'];
	}
}
