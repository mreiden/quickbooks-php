<?php declare(strict_types=1);

/**
 * QuickBooks Vendor object container
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

/**
 *
 */
class Vendor extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks Vendor object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID of the Vendor
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of the Vendor
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name of the Vendor
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name of the Vendor
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	/**
	 *
	 * /
	public function getFullName(): string
	{
		return $this->get('FullName');
	}

	public function setFullName(string $name): bool
	{
		return $this->set('FullName', $name);
	}
	*/

	/**
	 * Set this Vendor active or not
	 */
	public function setIsActive(bool $value): bool
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Tell whether or not this Vendor object is active
	 */
	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive');
	}


	public function setCompanyName(string $name): bool
	{
		return $this->set('CompanyName', $name);
	}

	public function getCompanyName(): string
	{
		return $this->get('CompanyName');
	}

	/**
	 *
	 */
	public function setFirstName(string $fname): bool
	{
		return $this->set('FirstName', $fname);
	}

	/**
	 *
	 */
	public function getFirstName(): string
	{
		return $this->get('FirstName');
	}

	/**
	 *
	 */
	public function setLastName(string $lname): bool
	{
		return $this->set('LastName', $lname);
	}

	public function getLastName(): string
	{
		return $this->get('LastName');
	}

	public function setMiddleName(string $mname): bool
	{
		return $this->set('MiddleName', $mname);
	}

	public function getMiddleName(): string
	{
		return $this->get('MiddleName');
	}

	public function getVendorAddress(string $part = null, array $defaults = [])
	{
		return $this->_getXYZAddress('Vendor', '', $part, $defaults);
	}

	public function setVendorAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $postalcode = '', string $country = '', string $note = ''): bool
	{
		return $this->_setXYZAddress('Vendor', '', $addr1, $addr2, $addr3, $addr4, $addr5, $city, $state, $postalcode, $country, $note);
	}

	protected function _setXYZAddress(string $pre, string $post, string $addr1, string $addr2, string $addr3, string $addr4, string $addr5, string $city, string $state, string $postalcode, string $country, string $note): bool
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set($pre . 'Address' . $post . ' Addr' . $i, ${'addr' . $i});
		}

		$this->set($pre . 'Address' . $post . ' City', $city);
		$this->set($pre . 'Address' . $post . ' State', $state);
		$this->set($pre . 'Address' . $post . ' PostalCode', $postalcode);
		$this->set($pre . 'Address' . $post . ' Country', $country);
		$this->set($pre . 'Address' . $post . ' Note', $note);

		return true;
	}

	protected function _getXYZAddress(string $pre, string $post, ?string $part = null, array $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get($pre . 'Address' . $post . ' ' . $part);
		}

		return $this->getArray($pre . 'Address' . $post . ' *', $defaults);
	}

	public function setPhone(string $phone): bool
	{
		return $this->set('Phone', $phone);
	}

	public function getPhone(): string
	{
		return $this->get('Phone');
	}

	/**
	 * Set the alternate phone number for this customer
	 */
	public function setAltPhone(string $phone): bool
	{
		return $this->set('AltPhone', $phone);
	}

	public function getAltPhone(): string
	{
		return $this->get('AltPhone');
	}

	/**
	 * Set the fax number for this customer
	 */
	public function setFax(string $fax): bool
	{
		return $this->set('Fax', $fax);
	}

	public function getFax(): string
	{
		return $this->get('Fax');
	}

	/**
	 * Set the e-mail address for this customer
	 */
	public function setEmail(string $email): bool
	{
		return $this->set('Email', $email);
	}

	public function getEmail(): string
	{
		return $this->get('Email');
	}

	/**
	 * Set the contact person for this customer
	 */
	public function setContact(string $contact): bool
	{
		return $this->set('Contact', $contact);
	}

	public function getContact(): string
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

	public function getAltContact(): string
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
	public function getSalutation(): string
	{
		return $this->get('Salutation');
	}

	/**
	 *
	 */
	public function getNameOnCheck(): string
	{
		return $this->get('NameOnCheck');
	}

	/**
	 * Set the payee name for this vendor
	 */
	public function setNameOnCheck(string $name): bool
	{
		return $this->set('NameOnCheck', $name);
	}

	/**
	 * Set the VendorTypeRef FullName for the vendor
	 */
	public function setVendorTypeRef(string $type): bool
	{
		return $this->set('VendorTypeRef FullName', $type);
	}

	/**
	 * Get the VendorTypeRef FullName for the vendor
	 */
	public function getVendorTypeRef(): string
	{
		return $this->get('VendorTypeRef FullName');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_VENDOR'];
	}
}
