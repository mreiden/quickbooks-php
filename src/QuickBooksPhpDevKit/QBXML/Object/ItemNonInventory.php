<?php declare(strict_types=1);

/**
 * QuickBooks NonInventoryItem object container
 *
 * NOTE: By default, NonInventoryItems are created as SalesOrPurchase items, and are
 * thus *NOT* created as SalesAndPurchase items. If you want to create an item
 * that is sold *and* purchased, you'll need to set the type with the method:
 * 	-> {@link QuickBooks_Object_NonInventoryItem::isSalesAndPurchase()}
 *
 * @todo Verify the get/set methods on this one... it was copied from ServiceItem
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
class NonInventoryItem extends AbstractQbxmlObject
{
	protected $_is_sales_and_purchase;

	public function __construct(array $arr = [], bool $is_sales_and_purchase = false)
	{
		parent::__construct($arr);

		if (count($this->getArray('SalesAndPurchase')) > 0)
		{
			$is_sales_and_purchase = true;
		}

		$this->_is_sales_and_purchase = $is_sales_and_purchase;
	}

	/**
	 * Set the ListID for this NonInventoryItem
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID for this NonInventoryItem
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name for this NonInventoryItem
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name for this NonInventoryItem
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	public function getUnitOfMeasureSetListID(): string
	{
		return $this->get('UnitOfMeasureSetRef ListID');
	}

	public function getUnitOfMeasureSetFullName(): string
	{
		return $this->get('UnitOfMeasureSetRef FullName');
	}

	/**
	 * Tell (and optionally set) whether or not this NonInventoryItem is currently for Sale *and* Purchase
	 */
	public function isSalesAndPurchase(?bool $enable = null): ?bool
	{
		$current = $this->_is_sales_and_purchase;

		if (!is_null($enable))
		{
			$this->_is_sales_and_purchase = $enable;
		}

		return $current;
	}

	/**
	 * Tell (and optionall set) whether or not this NonInventoryItem is currently for Sale *or* Purchase
	 */
	public function isSalesOrPurchase(?bool $enable = null): ?bool
	{
		$current = !$this->_is_sales_and_purchase;

		if (!is_null($enable))
		{
			$this->_is_sales_and_purchase = !$enable;
		}

		return $current;
	}

	// Sales OR Purchase

	/**
	 * Set the description of this NonInventoryItem (Sales OR Purchase)
	 */
	public function setDescription(string $descrip): bool
	{
		return $this->set('SalesOrPurchase Desc', $descrip);
	}

	public function getDescription(): string
	{
		return $this->get('SalesOrPurchase Desc');
	}

	/**
	 * Set the price for this NonInventoryItem (Sales OR Purchase)
	 *
	 * @param string $price
	 */
	public function setPrice($price): bool
	{
		$this->remove('SalesOrPurchase PricePercent');

		return $this->setAmountType('SalesOrPurchase Price', $price);
	}

	/**
	 * Get the price for this NonInventoryItem (Sales OR Purchase)
	 */
	public function getPrice()
	{
		return $this->get('SalesOrPurchase Price');
	}

	/**
	 * Set the price percent for this NonInventoryItem (Sales OR Purchase)
	 */
	public function setPricePercent($percent): bool
	{
		$this->remove('SalesOrPurchase Price');

		return $this->set('SalesOrPurchase PricePercent', (float) $percent);
	}

	/**
	 * Get the price percent for this NonInventoryItem (Sales OR Purchase)
	 */
	public function getPricePercent()
	{
		return $this->get('SalesOrPurchase PricePercent');
	}

	/**
	 * Set the account ListID for this NonInventoryItem (Sales OR Purchase)
	 */
	public function setAccountListID(string $ListID): bool
	{
		return $this->set('SalesOrPurchase AccountRef ListID', $ListID);
	}

	/**
	 * (Sales OR Purchase)
	 */
	public function setAccountName(string $name): bool
	{
		return $this->set('SalesOrPurchase AccountRef FullName', $name);
	}

	/**
	 * (Sales OR Purchase)
	 */
	public function setAccountApplicationID($value): bool
	{
		return $this->set('SalesOrPurchase AccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getAccountApplicationID()
	{
		return $this->get('SalesOrPurchase AccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	/**
	 * (Sales OR Purchase)
	 */
	public function getAccountListID(): string
	{
		return $this->get('SalesOrPurchase AccountRef ListID');
	}

	/**
	 * (Sales OR Purchase)
	 */
	public function getAccountName(): string
	{
		return $this->get('SalesOrPurchase AccountRef FullName');
	}

	// Sales AND Purchase

	public function setSalesDescription(string $descrip): bool
	{
		return $this->set('SalesAndPurchase SalesDesc', $descrip);
	}

	public function getSalesDescription(): string
	{
		return $this->get('SalesAndPurchase SalesDesc');
	}

	public function setSalesPrice($price): bool
	{
		return $this->setAmountType('SalesAndPurchase SalesPrice', $price);
	}

	public function getSalesPrice()
	{
		return $this->get('SalesAndPurchase SalesPrice');
	}

	public function setIncomeAccountListID(string $ListID): bool
	{
		return $this->set('SalesAndPurchase IncomeAccountRef ListID', $ListID);
	}

	public function getIncomeAccountListID(): string
	{
		return $this->get('SalesAndPurchase IncomeAccountRef ListID');
	}

	public function setIncomeAccountName(string $name): bool
	{
		return $this->set('SalesAndPurchase IncomeAccountRef FullName', $name);
	}

	public function getIncomeAccountName(): string
	{
		return $this->get('SalesAndPurchase IncomeAccountRef FullName');
	}

	public function setIncomeAccountApplicationID($value): bool
	{
		return $this->set('SalesAndPurchase IncomeAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getIncomeAccountApplicationID()
	{
		return $this->get('SalesAndPurchase IncomeAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setPurchaseDescription(string $descrip): bool
	{
		return $this->set('SalesAndPurchase PurchaseDesc', $descrip);
	}

	public function getPurchaseDescription(): bool
	{
		return $this->get('SalesAndPurchase PurchaseDesc');
	}

	public function setPurchaseCost($cost): bool
	{
		return $this->setAmountType('SalesAndPurchase PurchaseCost', $cost);
	}

	public function getPurchaseCost()
	{
		return $this->get('SalesAndPurchase PurchaseCost');
	}

	public function setExpenseAccountListID(string $ListID): bool
	{
		return $this->set('SalesAndPurchase ExpenseAccountRef ListID', $ListID);
	}

	public function setExpenseAccountName(string $name): bool
	{
		return $this->set('SalesAndPurchase ExpenseAccountRef FullName', $name);
	}

	public function setExpenseAccountApplicationID($value): bool
	{
		return $this->set('SalesAndPurchase ExpenseAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getExpenseAccountApplicationID()
	{
		return $this->get('SalesAndPurchase ExpenseAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getExpenseAccountListID(): string
	{
		return $this->get('SalesAndPurchase ExpenseAccountRef ListID');
	}

	public function getExpenseAccountName(): string
	{
		return $this->get('SalesAndPurchase ExpenseAccountRef FullName');
	}

	public function setPreferredVendorListID(string $ListID): bool
	{
		return $this->set('SalesAndPurchase PrefVendorRef ListID', $ListID);
	}

	public function setPreferredVendorName(string $name): bool
	{
		return $this->set('SalesAndPurchase PrefVendorRef FullName', $name);
	}

	public function setPreferredVendorApplicationID($value): bool
	{
		return $this->set('SalesAndPurchase PrefVendorRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_VENDOR'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getPreferredVendorApplicationID()
	{
		return $this->get('SalesAndPurchase PrefVendorRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getPreferredVendorListID(): string
	{
		return $this->get('SalesAndPurchase PrefVendorRef ListID');
	}

	public function getPreferredVendorName(): string
	{
		return $this->get('SalesAndPurchase PrefVendorRef FullName');
	}

	/**
	 *
	 */
	protected function _cleanup(): bool
	{
		if ($this->isSalesAndPurchase())
		{
			// Remove any SalesOrPurchase keys
			foreach ($this->getArray('SalesOrPurchase*') as $key => $value)
			{
				$this->remove($key);
			}
		}
		else
		{
			foreach ($this->getArray('SalesAndPurchase*') as $key => $value)
			{
				$this->remove($key);
			}
		}

		return true;
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_NONINVENTORYITEM'];
	}
}
