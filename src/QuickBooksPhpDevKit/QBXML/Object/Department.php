<?php declare(strict_types=1);

/**
 * QuickBooks Department object container
 *
 * @author Thomas Rientjes
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\Deposit\DepositLine;

/**
 * Department is part of QBPOS only
 */
class Department extends AbstractQbxmlObject
{
	/**
	 * Create a new Department object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID of the department
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of the department
	 */
	public function getListID(): ?string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the parent ListID
	 */
	public function setParentListID(string $ListID): bool
	{
		return $this->set('ParentRef ListID', $ListID);
	}

	/**
	 *
     */
	public function getParentListID(): ?string
	{
		return $this->get('ParentRef ListID');
	}

	/**
	 * Set the name of the department
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name of the department
	 */
	public function getName(): ?string
	{
		return $this->get('Name');
	}

	/**
	 * Get the full name of the department
	 */
	public function getFullName(): ?string
	{
		return $this->get('FullName');
	}

	/**
	 * Set the full name of the department
	 */
	public function setFullName(string $name): bool
	{
		return $this->set('FullName', $name);
	}

	/**
	 * Set this department active or not
	 */
	public function setIsActive(bool $value): bool
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Tell whether or not this department object is active
	 */
	public function getIsActive(): ?bool
	{
		return $this->get('IsActive');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_DEPARTMENT'];
	}
}
