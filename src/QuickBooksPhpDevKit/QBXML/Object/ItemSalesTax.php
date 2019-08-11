<?php declare(strict_types=1);

/**
 * QuickBooks ItemSalesTax object container
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
 * QuickBooks ItemSalesTax Object
 */
class ItemSalesTax extends AbstractQbxmlObject
{
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID for this item
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID for this item
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name for this item
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive', true);
	}

	public function setIsActive(bool $IsActive): bool
	{
		return $this->setBooleanType('IsActive', $IsActive);
	}

	/**
	 * Get the name for this item
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	public function setTaxRate($rate): bool
	{
		return $this->set('TaxRate', (float) $rate);
	}

	public function getTaxRate()
	{
		return $this->get('TaxRate');
	}

	public function setDescription(string $desc): bool
	{
		return $this->set('ItemDesc', $desc);
	}

	public function getDescription(): string
	{
		return $this->get('ItemDesc');
	}

	public function setTaxVendorListID(string $ListID): bool
	{
		return $this->set('TaxVendorRef ListID', $ListID);
	}

	public function setTaxVendorName(string $name): bool
	{
		return $this->set('TaxVendorRef FullName', $name);
	}

	// @todo Make sure these are ->setFullNameType instead of just ->set
	public function setTaxVendorFullName(string $FullName): bool
	{
		return $this->set('TaxVendorRef FullName', $FullName);
	}

	public function setTaxVendorApplicationID($value): bool
	{
		return $this->set('TaxVendorRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_VENDOR'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getTaxVendorApplicationID()
	{
		return $this->get('TaxVendorRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getTaxVendorListID(): string
	{
		return $this->get('TaxVendorRef ListID');
	}

	public function getTaxVendorName(): string
	{
		return $this->get('TaxVendorRef FullName');
	}

	public function getTaxVendorFullName(): string
	{
		return $this->get('TaxVendorRef FullName');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_SALESTAXITEM'];
	}
}
