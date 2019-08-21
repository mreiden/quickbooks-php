<?php declare(strict_types=1);

/**
 * QuickBooks ItemDiscount object container
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
class ItemDiscount extends AbstractQbxmlObject
{
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID for this DiscountItem
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID for this DiscountItem
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name for this DiscountItem
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name for this DiscountItem
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	public function setParentApplicationID($value)
	{
		return $this->set('ParentRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_DISCOUNTITEM'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getParentApplicationID()
	{
		return $this->get('ParentRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setParentListID(string $ListID): bool
	{
		return $this->set('ParentRef ListID', $ListID);
	}

	public function getParentListID(): string
	{
		return $this->get('ParentRef ListID');
	}

	public function setParentName(string $name): bool
	{
		return $this->set('ParentRef FullName', $name);
	}

	public function getParentName(): string
	{
		return $this->get('ParentRef FullName');
	}

	public function setDescription(string $desc): bool
	{
		return $this->set('ItemDesc', $desc);
	}

	public function getDescription(): string
	{
		return $this->get('ItemDesc');
	}

	public function setSalesTaxCodeName(string $name): bool
	{
		return $this->set('SalesTaxCodeRef FullName', $name);
	}

	public function getSalesTaxCodeName(): string
	{
		return $this->get('SalesTaxCodeRef FullName');
	}

	/**
	 * Discount rate amount (i.e.: $20 off purchase price)
	 *
	 * @param float $rate
	 * @return boolean
	 */
	public function setDiscountRate($rate): bool
	{
		return $this->set('DiscountRate', floatval($rate));
	}

	/**
	 *
	 */
	public function getDiscountRate()
	{
		return $this->get('DiscountRate');
	}

	/**
	 * Discount rate percentage (i.e.: 15% discount)
	 *
	 * @param float $percent
	 * @return boolean
	 */
	public function setDiscountRatePercent($percent): bool
	{
		return $this->set('DiscountRatePercent', floatval($percent));
	}

	public function getDiscountRatePercent()
	{
		return $this->get('DiscountRatePercent');
	}

	public function setAccountApplicationID($value): bool
	{
		return $this->set('AccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_DISCOUNTITEM'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getAccountApplicationID()
	{
		return $this->get('AccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setAccountListID(string $ListID): bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	public function getAccountListID(): string
	{
		return $this->get('AccountRef ListID');
	}

	public function setAccountName(string $name): bool
	{
		return $this->set('AccountRef FullName', $name);
	}

	public function getAccountName(): string
	{
		return $this->get('AccountRef FullName');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_DISCOUNTITEM'];
	}
}
