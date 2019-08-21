<?php declare(strict_types=1);

/**
 * QuickBooks ShipMethod object container
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
 *
 */
class ShipMethod extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks_Object_ShipMethod object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID of the shipping method
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of the shipping method
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name of the shipping method
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name of the shipping method
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	/**
	 * Set this shipping method as active or not
	 */
	public function setIsActive(bool $value): bool
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Tell whether or not this shipping method is active
	 */
	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive');
	}

	/**
	 *
	 */
	public function setParentListID(string $ListID): bool
	{
		return $this->set('ParentRef ListID', $ListID);
	}

	public function getParentListID(): string
	{
		return $this->get('ParentRef ListID');
	}

	public function setParentFullName(string $value): bool
	{
		return $this->set('ParentRef FullName', $value);
	}

	public function getParentFullName(): string
	{
		return $this->get('ParentRef FullName');
	}

	public function setParentApplicationID($value): bool
	{
		return $this->set('ParentRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getParentApplicationID()
	{
		return $this->get('ParentRef ' . PackageInfo::$API_APPLICATIONID);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_SHIPMETHOD'];
	}
}
