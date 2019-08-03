<?php declare(strict_types=1);

/**
 * QuickBooks CustomerType object container
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
class CustomerType extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\CustomerType object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID of the Class
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of the Class
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
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

	/**
	 * @deprecated
	 */
	public function setParentName(string $name): bool
	{
		return $this->setParentFullName($name);
	}

	/**
	 * @deprecated
	 */
	public function getParentName(): string
	{
		return $this->getParentFullName();
	}

	public function setParentFullName(string $name): bool
	{
		return $this->set('ParentRef FullName', $name);
	}

	public function getParentFullName(): string
	{
		return $this->get('ParentRef FullName');
	}

	public function setParentApplicationID($value): bool
	{
		return $this->set('ParentRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID($this->object(), PackageInfo::QbId['LISTID'], $value));
	}

	public function getParentApplicationID(): string
	{
		return $this->get('ParentRef ' . PackageInfo::$API_APPLICATIONID);
	}

	/**
	 * Set the name of the CustomerType
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name of the CustomerType
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	/**
	 *
	 */
	public function getFullName(): string
	{
		return $this->get('FullName');
	}

	public function setFullName(string $name): bool
	{
		return $this->set('FullName', $name);
	}

	/**
	 * Set this CustomerType active or not
	 */
	public function setIsActive(bool $value): bool
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Tell whether or not this CustomerType is active
	 *
	 * @return boolean
	 */
	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_CUSTOMERTYPE'];
	}
}
