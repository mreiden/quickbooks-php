<?php declare(strict_types=1);
/**
 * QuickBooks SalesRep object container
 *
 * @author Adam Heinz <amh@metricwise.net>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;

/**
 * QuickBooks Customer object class
 */
class SalesRep extends AbstractQbxmlObject
{
	/**
	 * Create a new SalesRep object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the initials of this sales rep
	 */
	public function setInitial(string $value): bool
	{
		return $this->set('Initial', $value);
	}

	/**
	 * Get the initials of this sales rep
	 */
	public function getInitial(): string
	{
		return $this->get('Initial');
	}

	/**
	 * Set this sales rep active or not
	 */
	public function setIsActive(?bool $value): bool
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Get whether or not this sales rep is active
	 */
	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive');
	}

	/**
	 *
	 */
	public function setSalesRepEntityListID(string $ListID): bool
	{
		return $this->set('SalesRepEntityRef ListID', $lid);
	}

	/**
	 *
	 */
	public function getSalesRepEntityListID(): string
	{
		return $this->get('SalesRepEntityRef ListID');
	}

	/**
	 *
	 */
	public function setSalesRepEntityName(string $name): bool
	{
		return $this->set('SalesRepEntityRef FullName', $name);
	}

	/**
	 *
	 */
	public function getSalesRepEntityName(): string
	{
		return $this->get('SalesRepEntityRef FullName');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_SALESREP'];
	}
}
