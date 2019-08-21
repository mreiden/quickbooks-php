<?php declare(strict_types=1);

/**
 * QuickBooks Employee object container
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
 * QBXML\Object\Employee class
 */
class Employee extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\Employee object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID of this Employee record
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of this Employee record
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	* Get the name of this Employee
	*/
	public function getName(): string
	{
		if (!$this->exists('Name'))
		{
			if (!is_null($this->getFirstName()) || !is_null($this->getLastName()))
			{
				$this->setNameAsFirstLast();
			}
		}

		return $this->get('Name');
	}

	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Set the full name of this Employee (full name)
	 *
	 * NOTE: This will be auto-set to ->getName() if you don't set it
	 * explicitly.
	 */
	public function setFullName(string $name): bool
	{
		if (is_null($name))
		{
			$name = $this->getName();
		}

		return $this->set('FullName', $name);
	}

	/**
	 * Get the name of this Employee (full name)
	 */
	public function getFullName(): string
	{
		if (!$this->exists('FullName'))
		{
			$this->setFullName($this->get('Name'));
		}

		return $this->get('FullName');
	}


	/**
	 * Sets the name as first and last.
	 */
	public function setNameAsFirstLast(): bool
	{
		$first = $this->getFirstName();
		$last = $this->getLastName();
		if (is_null($first))
		{
			$first = '';
		}
		if (is_null($last))
		{
			$last = '';
		}

		return $this->set('Name', $first .' '. $last);
	}

	/**
	 * Set the first name of this Employee
	 */
	public function setFirstName(string $fname): bool
	{
		return $this->set('FirstName', $fname);
	}

	/**
	 * Get the first name of this Employee
	 */
	public function getFirstName(): string
	{
		return $this->get('FirstName');
	}

	/**
	 * Set the last name of this Employee
	 */
	public function setLastName(string $lname): bool
	{
		return $this->set('LastName', $lname);
	}

	/**
	 * Get the last name of this Employee
	 */
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

	public function getEmployeeAddress(?string $part = null, array $defaults = [])
	{
		return $this->_getXYZAddress('Employee', '', $part, $defaults);
	}

	public function setEmployeeAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $province = '', string $postalcode = '', string $country = '', string $note = '')
	{
		return $this->_setXYZAddress('Employee', '', $addr1, $addr2, $addr3, $addr4, $addr5, $city, $state, $province, $postalcode, $country, $note);
	}

	protected function _setXYZAddress(string $pre, string $post, string $addr1, string $addr2, string $addr3, string $addr4, string $addr5, string $city, string $state, string $province, string $postalcode, string $country, string $note)
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set($pre . 'Address' . $post . ' Addr' . $i, ${'addr' . $i});
		}

		$this->set($pre . 'Address' . $post . ' City', $city);
		$this->set($pre . 'Address' . $post . ' State', $state);
		$this->set($pre . 'Address' . $post . ' Province', $province);
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

	/**
	 *
	 */
	public function setPhone(string $phone): bool
	{
		return $this->set('Phone', $phone);
	}

	public function getPhone(): string
	{
		return $this->get('Phone');
	}

	/**
	 * Set the alternate phone number for this Employee
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
	 * Set the fax number for this Employee
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
	 * Set the e-mail address for this Employee
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
	 * Set the salutation for this Employee
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

	public function setNotes(string $notes): bool
	{
		return $this->set('Notes', $notes);
	}

	public function getNotes(): string
	{
		return $this->get('Notes');
	}

	public function setMobile(string $mobile): bool
	{
		return $this->set('Mobile', $mobile);
	}

	public function getMobile(): string
	{
		return $this->get('Mobile');
	}

	public function setPager(string $pager): bool
	{
		return $this->set('Pager', $pager);
	}

	public function getPager(): string
	{
		return $this->get('Pager');
	}

	public function setGender(string $gender): bool
	{
		return $this->set('Gender', $gender);
	}

	public function getGender(): string
	{
		return $this->get('Gender');
	}

	public function setBirthDate($date): bool
	{
		return $this->setDateType('BirthDate', $date);
	}

	public function getBirthDate(string $format = 'Y-m-d'): string
	{
		return $this->getDateType('BirthDate', $format);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_EMPLOYEE'];
	}
}
