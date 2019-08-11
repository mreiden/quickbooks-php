<?php declare(strict_types=1);

/**
 * QuickBooks ItemService object container
 *
 * NOTE: By default, ServiceItems are created as SalesOrPurchase items, and are
 * thus *NOT* created as SalesAndPurchase items. If you want to create an item
 * that is sold *and* purchased, you'll need to set the type with the method:
 * 	-> {@link QBXML\Object\ItemService::isSalesAndPurchase()}
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
 * QuickBooks ItemService Object
 */
class ItemService extends AbstractQbxmlObject
{
	/**
	 * Flag indicating whether or not this for sales *AND* purchase, or just sales *OR* purchase
	 */
	protected $_is_sales_and_purchase;

	/**
	 * Create a new QuickBooks_Object_ServiceItem object (ServiceItem)
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
	 */
	public function setName(string $name): bool
	{
		return $this->setNameType('Name', $name);
	}

	/**
	 * Get the name for this item
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	public function setFullName(string $fullname): bool
	{
		return $this->setFullNameType('FullName', 'Name', 'ParentRef FullName', $fullname);
	}

	public function getFullName(): string
	{
		return $this->getFullNameType('FullName', 'Name', 'ParentRef FullName');
	}

	public function setIsActive(bool $active)
	{
		return $this->setBooleanType('IsActive', $active);
	}

	public function getIsActive()
	{
		return $this->getBooleanType('IsActive');
	}

	public function setParentListID(string $ListID): bool
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

	public function getUnitOfMeasureSetListID(): string
	{
		return $this->get('UnitOfMeasureSetRef ListID');
	}

	public function getUnitOfMeasureSetFullName(): string
	{
		return $this->get('UnitOfMeasureSetRef FullName');
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
	 * Tell (and optionall set) whether or not this item is currently for Sale *or* Purchase
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
		return $this->get('SalesOrPurchase Price');
	}

	/**
	 * Set the price percent for this item (Sales OR Purchase)
	 */
	public function setPricePercent($percent): bool
	{
		$this->remove('SalesOrPurchase Price');

		return $this->set('SalesOrPurchase PricePercent', $percent);
	}

	/**
	 * Get the price percent for this item (Sales OR Purchase)
	 *
	 * @return float
	 */
	public function getPricePercent()
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
	 * Set the account name for this item
	 */
	public function setAccountFullName(string $name): bool
	{
		return $this->set('SalesOrPurchase AccountRef FullName', $name);
	}
	/**
	 * Get the account name for this item (Sales OR Purchase)
	 */
	public function getAccountFullName(): string
	{
		return $this->get('SalesOrPurchase AccountRef FullName');
	}

	/**
	 * Set the account name for this item (Sales OR Purchase)
	 * @deprecated
	 */
	public function setAccountName(string $name): bool
	{
		return $this->setAccountFullName($name);
	}
	/**
	 * @deprecated
	 */
	public function getAccountName(): string
	{
		return $this->getAccountFullName();
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

	/**
	 * @deprecated
	 */
	public function setIncomeAccountName(string $name): bool
	{
		return $this->set('SalesAndPurchase IncomeAccountRef FullName', $name);
	}

	public function getIncomeAccountFullName(): string
	{
		return $this->get('SalesAndPurchase IncomeAccountRef FullName');
	}

	/**
	 * @deprecated
	 */
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

			// Add "Mod" to SalesAndPurchase nodes (SalesAndPurchaseMod)
			foreach ($this->getArray('SalesAndPurchase*') as $key => $value)
			{
				$this->set(preg_replace('/^SalesAndPurchase /', 'SalesAndPurchaseMod ', $key), $value);
			}
		}
		else
		{
			foreach ($this->getArray('SalesAndPurchase*') as $key => $value)
			{
				$this->remove($key);
			}

			// Add "Mod" to SalesOrPurchase nodes (SalesOrPurchaseMod)
			foreach ($this->getArray('SalesOrPurchase*') as $key => $value)
			{
				$this->set(preg_replace('/^SalesOrPurchase /', 'SalesOrPurchaseMod ', $key), $value);
			}
		}

		// Add "Mod" to SalesOrPurchase and SalesAndPurchase nodes (SalesOrPurchaseMod and SalesAndPurchaseMod)
		foreach ($this->getArray('SalesOrPurchase*') as $key => $value)
		{
			$this->set(preg_replace('/^SalesOrPurchase /', 'SalesOrPurchaseMod ', $key), $value);
		}
		foreach ($this->getArray('SalesAndPurchase*') as $key => $value)
		{
			$this->set(preg_replace('/^SalesAndPurchase /', 'SalesAndPurchaseMod ', $key), $value);
		}

		return true;
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_SERVICEITEM'];
	}
}
