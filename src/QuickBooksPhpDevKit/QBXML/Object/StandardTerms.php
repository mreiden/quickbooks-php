<?php declare(strict_types=1);

/**
 * QuickBooks StandardTerms object container
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
 * QuickBooks StandardTerms container
 */
class StandardTerms extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks_Object_StandardTerms object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID of the termspwd
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of the terms
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name of the terms
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name of these terms
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	/**
	 * Set this StandardTerms as active or not
	 */
	public function setIsActive(bool $IsActive): bool
	{
		return $this->setBooleanType('IsActive', $IsActive);
	}

	/**
	 * Gets whether or not this StandardTerms is active
	 */
	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive');
	}

	/**
	 * Get the number of days until payment is due
	 */
	public function getStdDueDays(): int
	{
		return $this->get('StdDueDays');
	}

	/**
	 * Alias of QBXML\Object\StandardTerms::getStdDueDays()
	 */
	public function getStandardDueDays(): int
	{
		return $this->getStdDueDays();
	}

	/**
	 * Set the number of days until payment is due
	 */
	public function setStdDueDays(int $days): bool
	{
		return $this->set('StdDueDays', $days);
	}

	/**
	 * Alias of QBXML\Object\StandardTerms::setStdDueDays()
	 */
	public function setStandardDueDays(int $days): bool
	{
		return $this->setStdDueDays($days);
	}

	/**
	 * Gets the number of days the discount percentage is applicable
	 */
	public function getStdDiscountDays(): int
	{
		return $this->get('StdDiscountDays');
	}

	/**
	 * Alias of QBXML\Object\StandardTerms::getStdDiscountDays()
	 */
	public function getStandardDiscountDays(): int
	{
		return $this->getStdDiscountDays();
	}

	/**
	 * Set the number of days the discount percentage is applicable
	 */
	public function setStdDiscountDays(int $days): bool
	{
		return $this->set('StdDiscountDays', $days);
	}

	/**
	 * Alias of QBXML\Object\StandardTerms::setStdDiscountDays()
	 */
	public function setStandardDiscountDays(int $days): bool
	{
		return $this->setStdDiscountDays($days);
	}

	/**
	 * Gets the discount percentage
	 */
	public function getDiscountPct(): float
	{
		return $this->get('DiscountPct');
	}

	/**
	 * Alias of QBXML\Object\StandardTerms::getDiscountPct()
	 */
	public function getDiscountPercent(): float
	{
		return $this->getDiscountPct();
	}

	/**
	 * Sets the discount percentage
	 */
	public function setDiscountPct(float $percent): bool
	{
		return $this->set('DiscountPct', $percent);
	}

	/**
	 * Alias of QBXML\Object\StandardTerms::setDiscountPct()
	 */
	public function setDiscountPercent(float $percent): bool
	{
		return $this->setDiscountPct($percent);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_STANDARDTERMS'];
	}
}
