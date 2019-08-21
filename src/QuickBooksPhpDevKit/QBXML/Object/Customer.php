<?php declare(strict_types=1);

/**
 * QuickBooks Customer object container
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
};

/**
 * QuickBooks Customer object class
 */
class Customer extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks_Object_Customer object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID of this customer record
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of this customer record
	 */
	public function getListID(): ?string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the ListID of the parent client
	 */
	public function setParentListID(string $ListID): bool
	{
		return $this->set('ParentRef ListID', $ListID);
	}

	/**
	 * Set the FullName of the parent client
	 */
	public function setParentFullName(string $FullName): bool
	{
		return $this->set('ParentRef FullName', $FullName);
	}
	public function setParentName(string $name): bool
	{
		return $this->setParentFullName($name);
	}

	/**
	 * Set the application id of the parent client
	 *
	 * @param string $id
	 */
	public function setParentApplicationID($id): bool
	{
		return $this->set('ParentRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CUSTOMER'], PackageInfo::QbId['LISTID'], $id));
	}

	/**
	 * Get the ListID of the parent client (if exists)
	 */
	public function getParentListID(): ?string
	{
		return $this->get('ParentRef ListID');
	}

	/**
	 * Get the FullName of the parent client (if exists)
	 */
	public function getParentFullName(): ?string
	{
		return $this->get('ParentRef FullName');
	}
	public function getParentName(): ?string
	{
		return $this->getParentFullName();
	}

	/**
	 * Get the application id of the parent client
	 */
	public function getParentApplicationID(): ?string
	{
		return $this->get('ParentRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setCurrencyListID(string $lid): bool
	{
		return $this->set('CurrencyRef ListID', $lid);
	}

	public function setCurrencyFullName(string $FullName): bool
	{
		return $this->setFullNameType('CurrencyRef FullName', null, null, $FullName);
	}



	/**
	 * Set the customer type list id
	 */
	public function setCustomerTypeListID(string $lid): bool
	{
		return $this->set('CustomerTypeRef ListID', $lid);
	}

	public function setCustomerTypeFullName(string $FullName): bool
	{
		return $this->setFullNameType('CustomerTypeRef FullName', null, null, $FullName);
	}

	public function setCustomerTypeName(string $name): bool
	{
		return $this->set('CustomerTypeRef FullName', $name);
	}

	public function setCustomerTypeApplicationID($value): bool
	{
		return $this->set('CustomerTypeRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CUSTOMERTYPE'], PackageInfo::QbId['LISTID'], $value));
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

	public function setTermsListID(string $ListID): bool
	{
		return $this->set('TermsRef ListID', $ListID);
	}

	/*
	public function setTermsApplicationID($value)
	{
		return $this->set('TermsRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_TERMS'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getTermsApplicationID()
	{
		return $this->get('TermsRef ' . PackageInfo::$API_APPLICATIONID);
	}*/

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

	public function setSalesRepFullName(string $name): bool
	{
		return $this->set('SalesRepRef FullName', $name);
	}
	/**
	 * @deprecated
	 */
	public function setSalesRepName(string $name): bool
	{
		return $this->setSalesRepFullName($name);
	}


	public function setSalesRepListID(string $ListID): bool
	{
		return $this->set('SalesRepRef ListID', $ListID);
	}

	/*
	public function setSalesRepApplicationID($value)
	{
		return $this->set('SalesRepRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_SALESREP'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getSalesRepApplicationID()
	{
		return $this->get('SalesRepRef ' . PackageInfo::$API_APPLICATIONID);
	}*/

	public function getSalesRepFullName(): ?string
	{
		return $this->get('SalesRepRef FullName');
	}
	/**
	 * @deprecated
	 */
	public function getSalesRepName(): ?string
	{
		return $this->getSalesRepFullName();
	}

	public function getSalesRepListID(): ?string
	{
		return $this->get('SalesRepRef ListID');
	}

	/**
	 * Set the delivery method
	 *
	 * @see QUICKBOOKS_CUSTOMER_DELIVERYMETHOD_PRINT, QUICKBOOKS_CUSTOMER_DELIVERYMETHOD_EMAIL, QUICKBOOKS_CUSTOMER_DELIVERYMETHOD_FAX
	 *
	 * Only supported by QuickBooks Online Edition as of qbXML version 7.0.
	 * QuickBooks Online Edition has a bug where if you do not provide a
	 * DeliveryMethod when issueing CustomerAdd or CustomerMod requests, the
	 * CustomerAdd or CustomerMod request will take a very long time to
	 * process (2+ minutes sometimes). The fix is to simply provide this tag,
	 * after which requests process very quickly (2 seconds or less).
	 */
	public function setDeliveryMethod(string $value): bool
	{
		return $this->set('DeliveryMethod', $value);
	}

	/**
	 * Get the delivery method
	 *
	 * @see QUICKBOOKS_CUSTOMER_DELIVERYMETHOD_PRINT, QUICKBOOKS_CUSTOMER_DELIVERYMETHOD_EMAIL, QUICKBOOKS_CUSTOMER_DELIVERYMETHOD_FAX
	 */
	public function getDeliveryMethod(): ?string
	{
		return $this->get('DeliveryMethod');
	}

	/**
	 * Set the name of this customer
	 *
	 * NOTE: This will be auto-set to ->getFirstName() ->getLastName() if you
	 * don't set it explicitly.
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name of this customer
	 *
	 * @TODO What should the behavior of this be if "Name" is not set...?
	 */
	public function getName(): ?string
	{
		return $this->get('Name');
	}

	/**
	 * Set the full name of this customer (full name)
	 *
	 * NOTE: QuickBooks will auto-set this to ->getName() if you don't set it
	 * explicitly.
	 */
	public function setFullName(string $name): bool
	{
		return $this->setFullNameType('FullName', 'Name', 'ParentRef FullName', $name);
	}

	/**
	 * Get the name of this customer (full name)
	 */
	public function getFullName(): ?string
	{
		return $this->getFullNameType('FullName', 'Name', 'ParentRef FullName');
	}

	/**
	 * Set the company name of this customer
	 */
	public function setCompanyName(string $name): bool
	{
		return $this->set('CompanyName', $name);
	}

	/**
	 * Get the company name of this customer
	 */
	public function getCompanyName(): ?string
	{
		return $this->get('CompanyName');
	}

	/**
	 * Set the first name of this customer
	 */
	public function setFirstName(string $fname): bool
	{
		return $this->set('FirstName', $fname);
	}
	/**
	 * Get the first name of this customer
	 */
	public function getFirstName(): ?string
	{
		return $this->get('FirstName');
	}

	/**
	 * Set the last name of this customer
	 */
	public function setLastName(string $lname): bool
	{
		return $this->set('LastName', $lname);
	}
	/**
	 * Get the last name of this customer
	 */
	public function getLastName(): ?string
	{
		return $this->get('LastName');
	}

	/**
	 * Set the middle name of this customer
	 */
	public function setMiddleName(string $mname): bool
	{
		return $this->set('MiddleName', $mname);
	}
	/**
	 * Get the middle name of this customer
	 */
	public function getMiddleName(): ?string
	{
		return $this->get('MiddleName');
	}

	public function getShipAddress(?string $part = null, array $defaults = []): bool
	{
		return $this->_getXYZAddress('Ship', '', $part, $defaults);
	}

	public function setShipAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $province = '', string $postalcode = '', string $country = '', string $note = ''): bool
	{
		return $this->_setXYZAddress('Ship', '', $addr1, $addr2, $addr3, $addr4, $addr5, $city, $state, $province, $postalcode, $country, $note);
	}

	public function getBillAddress(?string $part = null, array $defaults = [])
	{
		return $this->_getXYZAddress('Bill', '', $part, $defaults);
	}

	public function setBillAddress(
		string $addr1,
		string $addr2 = '',
		string $addr3 = '',
		string $addr4 = '',
		string $addr5 = '',
		string $city = '',
		string $state = '',
		string $province = '',
		string $postalcode = '',
		string $country = '',
		string $note = ''): bool
	{
		return $this->_setXYZAddress('Bill', '', $addr1, $addr2, $addr3, $addr4, $addr5, $city, $state, $province, $postalcode, $country, $note);
	}

	public function getShipAddressBlock(?string $part = null, array $defaults = [])
	{
		return $this->_getXYZAddress('Ship', 'Block', $part, $defaults);
	}

	public function getBillAddressBlock(?string $part = null, array $defaults = [])
	{
		return $this->_getXYZAddress('Bill', 'Block', $part, $defaults);
	}

	protected function _setXYZAddress(string $pre, string $post, string $addr1, string $addr2, string $addr3, string $addr4, string $addr5, string $city, string $state, string $province, string $postalcode, string $country, string $note)
	{
		$b = false;
		for ($i = 1; $i <= 5; $i++)
		{
			$b = $this->set($pre . 'Address' . $post . ' Addr' . $i, ${'addr' . $i});
		}

		$b = $this->set($pre . 'Address' . $post . ' City', $city);
		$b = $this->set($pre . 'Address' . $post . ' State', $state);
		$b = $this->set($pre . 'Address' . $post . ' Province', $province);
		$b = $this->set($pre . 'Address' . $post . ' PostalCode', $postalcode);
		$b = $this->set($pre . 'Address' . $post . ' Country', $country);
		$b = $this->set($pre . 'Address' . $post . ' Note', $note);

		return $b;
	}

	protected function _getXYZAddress(string $pre, string $post, ?string $part = null, array $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get($pre . 'Address' . $post . ' ' . $part);
		}

		return $this->getArray($pre . 'Address' . $post . ' *', $defaults);
	}

	/**
	 * Set the primary phone number for this customer
	 */
	public function setPhone($phone): bool
	{
		return $this->set('Phone', $phone);
	}
	public function getPhone(): ?string
	{
		return $this->get('Phone');
	}

	/**
	 * Set the alternate phone number for this customer
	 */
	public function setAltPhone($phone): bool
	{
		return $this->set('AltPhone', $phone);
	}
	public function getAltPhone(): ?string
	{
		return $this->get('AltPhone');
	}

	/**
	 * Set the fax number for this customer
	 */
	public function setFax($fax): bool
	{
		return $this->set('Fax', $fax);
	}
	public function getFax(): ?string
	{
		return $this->get('Fax');
	}

	/**
	 * Set the e-mail address for this customer
	 */
	public function setEmail($email): bool
	{
		return $this->set('Email', $email);
	}
	public function getEmail(): ?string
	{
		return $this->get('Email');
	}

	public function setAccountNumber($num): bool
	{
		return $this->set('AccountNumber', $num);
	}

	public function getAccountNumber()
	{
		return $this->get('AccountNumber');
	}

	/**
	 * Set the contact person for this customer
	 */
	public function setContact(string $contact): bool
	{
		return $this->set('Contact', $contact);
	}

	/**
	 * Get the name of the contact at this company/customer
	 */
	public function getContact(): ?string
	{
		return $this->get('Contact');
	}

	/**
	 * Set the alternate contact for this customer
	 */
	public function setAltContact(string $contact): bool
	{
		return $this->set('AltContact', $contact);
	}

	/**
	 * Get the name of the alternate contact for this customer/company
	 */
	public function getAltContact(): ?string
	{
		return $this->get('AltContact');
	}

	/**
	 * Set the salutation for this customer
	 */
	public function setSalutation(string $salut): bool
	{
		return $this->set('Salutation', $salut);
	}

	/**
	 *
	 */
	public function getSalutation(): ?string
	{
		return $this->get('Salutation');
	}

	public function getCustomerTypeApplicationID()
	{
		return $this->get('CustomerTypeRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getCustomerTypeListID(): ?string
	{
		return $this->get('CustomerTypeRef ListID');
	}

	public function getCustomerTypeName(): ?string
	{
		return $this->get('CustomerTypeRef FullName');
	}

	public function setOpenBalance($balance): bool
	{
		return $this->setAmountType('OpenBalance', (float) $balance);
	}

	public function getOpenBalance()
	{
		return $this->getAmountType('OpenBalance');
	}

	public function setOpenBalanceDate($date): bool
	{
		return $this->setDateType('OpenBalanceDate', $date);
	}

	public function getOpenBalanceDate(?string $format = null): ?string
	{
		return $this->getDateType('OpenBalanceDate', $format);
	}

	/**
	 * Get the balance for this customer (not including sub-customers or jobs)
	 *
	 * @return float
	 */
	public function getBalance()
	{
		return $this->getAmountType('Balance');
	}

	public function setBalance($value): bool
	{
		return $this->setAmountType('Balance', (float) $value);
	}

	/**
	 * Get the total balance for this customer and all of this customer's sub-customers/jobs
	 *
	 * @return float
	 */
	public function getTotalBalance()
	{
		return $this->getAmountType('TotalBalance');
	}

	public function setTotalBalance($value): bool
	{
		return $this->setAmountType('TotalBalance', (float) $value);
	}

	public function setSalesTaxCodeName(string $name): bool
	{
		return $this->set('SalesTaxCodeRef FullName', $name);
	}

	public function setSalesTaxCodeListID(string $ListID): bool
	{
		return $this->set('SalesTaxCodeRef ListID', $ListID);
	}

	public function setSalesTaxCodeApplicationID($value): bool
	{
		return $this->set('SalesTaxCodeRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_SALESTAXCODE'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getSalesTaxCodeApplicationID()
	{
		return $this->get('SalesTaxCodeRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getSalesTaxCodeName(): ?string
	{
		return $this->get('SalesTaxCodeRef FullName');
	}

	public function getSalesTaxCodeListID(): ?string
	{
		return $this->get('SalesTaxCodeRef ListID');
	}

	/**
	 * Set the credit card information for this customer
	 *
	 * @param string $cardno		The credit card number
	 * @param integer $expmonth		The expiration month (1 is January, 2 is February, etc.)
	 * @param integer $expyear		The expiration year
	 * @param string $name			The name on the credit card
	 * @param string $address		The billing address for the credit card
	 * @param string $postalcode	The postal code for the credit card
	 * @return boolean
	 */
	public function setCreditCardInfo(string $cardno, int $expmonth, int $expyear, string $name, string $address, string $postalcode): bool
	{
		// should probably do better checking here for failed sets.
		$b = false;
		$b = $this->set('CreditCardInfo CreditCardNumber', $cardno);
		$b = $this->set('CreditCardInfo ExpirationMonth', $expmonth);
		$b = $this->set('CreditCardInfo ExpirationYear', $expyear);
		$b = $this->set('CreditCardInfo NameOnCard', $name);
		$b = $this->set('CreditCardInfo CreditCardAddress', $address);
		$b = $this->set('CreditCardInfo CreditCardPostalCode', $postalcode);

		return $b;
	}

	/**
	 * Get credit card information for this customer
	 *
	 * @param string $part		If you just want a specific part of the card info, specify it here
	 * @param array $defaults	Defaults for the card data if you want the entire array
	 * @return mixed			If you specify a part, a string part is returned, otherwise an array of card data
	 */
	public function getCreditCardInfo(?string $part = null, array $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get('CreditCardInfo ' . $part);
		}

		return $this->getArray('CreditCardInfo *', $defaults);
	}

	public function setNotes(string $notes): bool
	{
		return $this->set('Notes', $notes);
	}

	public function getNotes(): ?string
	{
		return $this->get('Notes');
	}

	public function setPriceLevelName(string $name): bool
	{
		return $this->set('PriceLevelRef FullName', $name);
	}

	public function setPriceLevelListID(string $ListID): bool
	{
		return $this->set('PriceLevelRef ListID', $ListID);
	}

	public function setPriceLevelApplicationID($value): bool
	{
		return $this->set('PriceLevelRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_PRICELEVEL'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getPriceLevelApplicationID()
	{
		return $this->get('PriceLevelRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getPriceLevelName(): ?string
	{
		return $this->get('PriceLevelRef FullName');
	}

	public function setPreferredDeliveryMethod($value): bool
	{
		return $this->set('PreferredDeliveryMethod', $value);
	}

	public function getPreferredDeliveryMethod(): ?string
	{
		return $this->get('PreferredDeliveryMethod');
	}

	/**
	 * Get the price level list id.
	 */
	public function getPriceLevelListID(): ?string
	{
		return $this->get('PriceLevelRef ListID');
	}

	/**
	 * Set the Class FullName for this customer.
	 *
	 * NOTE!!!: This is only available in QuickBooks Enterprise (https://quickbooks.intuit.com/learn-support/en-us/reports-and-accounting/setting-up-defaults-for-classes-in-premier-2016/00/241500)
	 */
	public function setClassName(string $name): bool
	{
		return $this->set('ClassRef FullName', $name);
	}
	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	/**
	 * Set the Class ListID for this customer. (In QBXML 12.0+)
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}
	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	/**
	 * Set whether this is an active customer (Not In QBOE)
	 */
	public function setIsActive(bool $isActive): bool
	{
		return $this->setBooleanType('IsActive', $isActive);
	}
	public function getIsActive(): ?bool
	{
		return $this->getBooleanType('IsActive');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_CUSTOMER'];
	}
}
