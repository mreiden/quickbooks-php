<?php declare(strict_types=1);

/**
 * QuickBooks Class object container
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
 * Using Qbclass because class is a PHP reserved word.
 */
class Qbclass extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks_Object_Class object
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

	public function setParentFullName(string $name): bool
	{
		return $this->set('ParentRef FullName', $name);
	}

	public function getParentFullName(): string
	{
		return $this->get('ParentRef FullName');
	}

	/**
	 * @deprecated
	 */
	public function getParentName(): string
	{
		return $this->getParentFullName();
	}

	/**
	 * Set the name of the QB Class
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name of the QB Class
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
	 * Set this Class active or not
	 */
	public function setIsActive(bool $value): bool
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Tell whether or not this class object is active
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
		return PackageInfo::Actions['OBJECT_CLASS'];
	}
}
