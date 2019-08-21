<?php declare(strict_types=1);

/**
 * QuickBooks OtherChargeItem object container
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
 * QuickBooks ItemOtherCharge Object
 */
class ItemOtherCharge extends AbstractQbxmlObject
{
	/**
	 * Flag indicating whether or not this for sales *AND* purchase, or just sales *OR* purchase
	 */
	protected $_is_sales_and_purchase;

	/**
	 * Create a new QuickBooks_Object_OtherChargeItem object (OtherChargeItem)
	 */
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
	 *
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name for this item
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	public function setIsActive(bool $active): bool
	{
		return $this->setBooleanType('IsActive', $active);
	}

	public function getIsActive(): ?bool
	{
		return $this->getBooleanType('IsActive');
	}

	public function setParentListID(string $ListID):bool
	{
		return $this->set('ParentRef ListID', $ListID);
	}

	public function setParentName(string $name): bool
	{
		return $this->set('ParentRef FullName', $name);
	}

	public function setParentApplicationID($value): bool
	{
		return $this->set('ParentRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ITEM'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getParentListID(): string
	{
		return $this->get('ParentRef ListID');
	}

	public function getParentName(): string
	{
		return $this->get('ParentRef FullName');
	}

	public function getParentApplicationID()
	{
		return $this->extractApplicationID($this->get('ParentRef ' . PackageInfo::$API_APPLICATIONID));
	}

	public function setSalesTaxCodeListID(string $ListID): bool
	{
		return $this->set('SalesTaxCodeRef ListID', $ListID);
	}

	public function setSalesTaxCodeName(string $name): bool
	{
		return $this->set('SalesTaxCodeRef FullName', $name);
	}

	public function setSalesTaxCodeApplicationID($value): bool
	{
		return $this->set('SalesTaxCodeRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_SALESTAXCODE'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getSalesTaxCodeListID(): string
	{
		return $this->get('SalesTaxCodeRef ListID');
	}

	public function getSalesTaxCodeName(): string
	{
		return $this->get('SalesTaxCodeRef FullName');
	}

	public function getSalesTaxCodeApplicationID()
	{
		return $this->extractApplicationID($this->get('SalesTaxCodeRef ' . PackageInfo::$API_APPLICATIONID));
	}

	/**
	 * Tell (and optionally set) whether or not this item is currently for Sale *and* Purchase
	 */
	public function isSalesAndPurchase(?bool $enable = null): bool
	{
		$current = $this->_is_sales_and_purchase;

		if (!is_null($enable))
		{
			$this->_is_sales_and_purchase = $enable;
		}

		return $current;
	}

	/**
	 * Tell (and optionally set) whether or not this item is currently for Sale *or* Purchase
	 */
	public function isSalesOrPurchase(?bool $enable = null): bool
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
	 * Set the description of this item (Sales OR Purchase)
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
	 * Set the price for this item (Sales OR Purchase)
	 */
	public function setPrice($price): bool
	{
		$this->remove('SalesOrPurchase PricePercent');

		return $this->setAmountType('SalesOrPurchase Price', $price);
	}

	/**
	 * Get the price for this item (Sales OR Purchase)
	 */
	public function getPrice()
	{
		return $this->getAmountType('SalesOrPurchase Price');
	}

	/**
	 * Set the price percent for this item (Sales OR Purchase)
	 */
	public function setPricePercent($percent): bool
	{
		$this->remove('SalesOrPurchase Price');

		return $this->set('SalesOrPurchase PricePercent', floatval($percent));
	}

	/**
	 * Get the price percent for this item (Sales OR Purchase)
	 */
	public function getPricePercent(): ?float
	{
		return $this->get('SalesOrPurchase PricePercent');
	}

	/**
	 * Set the account ListID for this item (Sales OR Purchase)
	 */
	public function setAccountListID(string $ListID): bool
	{
		return $this->set('SalesOrPurchase AccountRef ListID', $ListID);
	}

	/**
	 * Set the account name for this item (Sales OR Purchase)
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
	 * Get the account ListID for this item (Sales OR Purchase)
	 */
	public function getAccountListID(): string
	{
		return $this->get('SalesOrPurchase AccountRef ListID');
	}

	/**
	 * Get the account name for this item (Sales OR Purchase)
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

	public function getPurchaseDescription(): string
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
		return PackageInfo::Actions['OBJECT_OTHERCHARGEITEM'];
	}
}
