<?php declare(strict_types=1);

/**
 * QuickBooks ItemFixedAsset object container
 *
 * @todo Verify the get/set methods on this one... it was copied from NonInventoryItem
 * @todo Add isActive(), getIsActive(), etc. methods
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
class ItemFixedAsset extends AbstractQbxmlObject
{
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID for this FixedAssetItem
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID for this FixedAssetItem
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name for this FixedAssetItem
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name for this FixedAssetItem
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	/**
	 * Set the description of this FixedAssetItem
	 */
	public function setSalesDescription(string $descrip): bool
	{
		return $this->set('SalesDesc', $descrip);
	}

	public function getSalesDescription(): string
	{
		return $this->get('SalesDesc');
	}

	/**
	 * Set the price for this FixedAssetItem
	 */
	public function setSalesPrice($price): bool
	{
		return $this->setAmountType('SalesPrice', $price);
	}

	/**
	 * Get the price for this FixedAssetItem
	 */
	public function getSalesPrice()
	{
		return $this->getAmountType('SalesPrice');
	}

	/**
	 * Set the account ListID for this FixedAssetItem
	 */
	public function setIncomeAccountListID(string $ListID): bool
	{
		return $this->set('IncomeAccountRef ListID', $ListID);
	}

	/**
	 * (Sales OR Purchase)
	 */
	public function setIncomeAccountName(string $name): bool
	{
		return $this->set('IncomeAccountRef FullName', $name);
	}

	/**
	 * (Sales OR Purchase)
	 */
	public function setIncomeAccountApplicationID($value): bool
	{
		return $this->set('IncomeAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getIncomeAccountApplicationID()
	{
		return $this->get('IncomeAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	/**
	 *
	 */
	public function getIncomeAccountListID(): string
	{
		return $this->get('IncomeAccountRef ListID');
	}

	/**
	 *
	 */
	public function getIncomeAccountName(): string
	{
		return $this->get('IncomeAccountRef FullName');
	}

	public function setAssetAccountName(string $name): bool
	{
		return $this->set('AssetAccountRef FullName', $name);
	}

	public function getAssetAccountName(): string
	{
		return $this->get('AssetAccountRef FullName');
	}

	public function setAssetAccountListID(string $ListID): bool
	{
		return $this->set('AssetAccountRef ListID', $ListID);
	}

	public function getAssetAccountListID(): string
	{
		return $this->get('AssetAccountRef ListID');
	}

	public function setAssetAccountApplicationID($value): bool
	{
		return $this->set('AssetAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getAssetAccountApplicationID()
	{
		return $this->get('AssetAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setPurchaseDescription(string $desc): bool
	{
		return $this->set('PurchaseDesc', $desc);
	}

	public function getPurchaseDescription(): string
	{
		return $this->get('PurchaseDesc');
	}

	public function setPurchaseCost($cost): bool
	{
		return $this->set('PurchaseCost', sprintf('%01.2f', floatval($cost)));
	}

	public function getPurchaseCost(): ?float
	{
		return $this->get('PurchaseCost');
	}

	public function setCOGSAccountListID(string $ListID): bool
	{
		return $this->set('COGSAccountRef ListID', $ListID);
	}

	public function setCOGSAccountName(string $name): bool
	{
		return $this->set('COGSAccountRef FullName', $name);
	}

	public function setCOGSAccountApplicationID($value): bool
	{
		return $this->set('COGSAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getCOGSAccountApplicationID()
	{
		return $this->get('COGSAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getCOGSAccountListID(): string
	{
		return $this->get('COGSAccountRef ListID');
	}

	public function getCOGSAccountName(): string
	{
		return $this->get('COGSAccountRef FullName');
	}

	public function setPreferredVendorListID(string $ListID): bool
	{
		return $this->set('PrefVendorRef ListID', $ListID);
	}

	public function setPreferredVendorName(string $name): bool
	{
		return $this->set('PrefVendorRef FullName', $name);
	}

	public function setPreferredVendorApplicationID($value): bool
	{
		return $this->set('PrefVendorRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_VENDOR'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getPreferredVendorApplicationID()
	{
		return $this->get('PrefVendorRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getPreferredVendorListID(): string
	{
		return $this->get('PrefVendorRef ListID');
	}

	public function getPreferredVendorName(): string
	{
		return $this->get('PrefVendorRef FullName');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_FIXEDASSETITEM'];
	}
}
