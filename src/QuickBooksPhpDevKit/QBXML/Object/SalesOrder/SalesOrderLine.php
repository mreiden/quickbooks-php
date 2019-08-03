<?php declare(strict_types=1);

/**
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\SalesOrder;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\SalesOrder;

/**
 *
 *
 */
class SalesOrderLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks SalesOrder SalesOrderLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setItemListID(string $ListID): bool
	{
		return $this->set('ItemRef ListID', $ListID);
	}

	public function setItemApplicationID($value): bool
	{
		return $this->set('ItemRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ITEM'], PackageInfo::QbId['LISTID'], $value));
	}

	public function setItemName(string $name): bool
	{
		return $this->set('ItemRef FullName', $name);
	}

	public function getItemListID(): string
	{
		return $this->get('ItemRef ListID');
	}

	public function getItemName(): string
	{
		return $this->get('ItemRef FullName');
	}

	public function setDescription(string $descrip): bool
	{
		return $this->set('Desc', $descrip);
	}

	public function getDescription(): string
	{
		return $this->get('Desc');
	}

	public function setQuantity($quantity): bool
	{
		return $this->set('Quantity', (float) $quantity);
	}

	public function getQuantity()
	{
		return $this->get('Quantity');
	}

	public function setUnitOfMeasure(string $unit): bool
	{
		return $this->set('UnitOfMeasure', $unit);
	}

	public function getUnitOfMeasure(): string
	{
		return $this->get('UnitOfMeasure');
	}

	public function setRate($rate): bool
	{
		return $this->set('Rate', (float) $rate);
	}

	public function getRate()
	{
		return $this->get('Rate');
	}

	public function setRatePercent($percent): bool
	{
		return $this->set('RatePercent', (float) $percent);
	}

	public function getRatePercent()
	{
		return $this->get('RatePercent');
	}
	/*
	public function setPriceLevelApplicationID($value): bool
	{

	}
	*/

	public function setPriceLevelName(string $name): bool
	{
		return $this->set('PriceLevelRef FullName', $name);
	}

	public function setPriceLevelListID(string $ListID): bool
	{
		return $this->set('PriceLevelRef ListID', $ListID);
	}

	public function getPriceLevelName(): string
	{
		return $this->get('PriceLevelRef FullName');
	}

	public function getPriceLevelListID(): string
	{
		return $this->get('PriceLevelRef ListID');
	}

	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	/*
	public function setClassApplicationID($value): bool
	{

	}
	*/

	public function setClassName(string $name): bool
	{
		return $this->set('ClassRef Name', $name);
	}

	public function getClassListID(): string
	{
		return $this->get('ClassRef ListID');
	}

	public function getClassName(): string
	{
		return $this->get('ClassRef FullName');
	}

	public function setAmount($amount): bool
	{
		return $this->setAmountType('Amount', $amount);
	}

	public function setServiceDate($date): bool
	{
		return $this->setDateType('ServiceDate', $date);
	}

	public function getServiceDate(string $format = 'Y-m-d'): string
	{
		return $this->getDateType('ServiceDate', $format);
	}

	public function setSalesTaxCodeName(string $name): bool
	{
		return $this->set('SalesTaxCodeRef FullName', $name);
	}

	public function setSalesTaxCodeListID(string $ListID): bool
	{
		return $this->set('SalesTaxCodeRef ListID', $ListID);
	}

	public function getSalesTaxCodeName(): string
	{
		return $this->get('SalesTaxCodeRef FullName');
	}

	public function getSalesTaxCodeListID(): string
	{
		return $this->get('SalesTaxCodeRef ListID');
	}

	public function setTaxable(): bool
	{
		return $this->set('SalesTaxCodeRef FullName', PackageInfo::TaxCode['TAXABLE']);
	}

	public function setNonTaxable(): bool
	{
		return $this->set('SalesTaxCodeRef FullName', PackageInfo::TaxCode['NONTAXABLE']);
	}

	public function getTaxable(): bool
	{
		return $this->get('SalesTaxCodeRef FullName') === PackageInfo::TaxCode['TAXABLE'];
	}

	public function setOverrideItemAccountName(string $name): bool
	{
		return $this->set('OverrideItemAccountRef FullName', $name);
	}

	public function setOverrideItemAccountListID(string $ListID): bool
	{
		return $this->set('OverrideItemAccountRef ListID', $ListID);
	}
/*
	public function setOverrideItemAccountApplicationID($value)
	{

	}
*/
	public function getOverrideItemAccountListID(): string
	{
		return $this->get('OverrideItemAccountRef ListID');
	}

	public function getOverrideItemAccountName(): string
	{
		return $this->get('OverrideItemAccountRef FullName');
	}

	public function setOther1(string $value): bool
	{
		return $this->set('Other1', $value);
	}

	public function getOther1(): string
	{
		return $this->get('Other1');
	}

	public function setOther2(string $value): bool
	{
		return $this->set('Other2', $value);
	}

	public function getOther2(): string
	{
		return $this->get('Other2');
	}

	/**
	 *
	 */
	protected function _cleanup(): bool
	{
		if ($this->exists('Amount'))
		{
			$this->set('Amount', sprintf('%01.2f', $this->get('Amount')));
		}

		return true;
	}

	public function asXML(string $root = null, string $parent = null, $object = null)
	{
		$this->_cleanup();

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_SALESORDER']:
				$root = 'SalesOrderLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_SALESORDER']:
				$root = 'SalesOrderLineMod';
				$parent = null;
				break;
		}

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell the type of object this is
	 */
	public function object(): string
	{
		return 'SalesOrderLine';
	}
}
