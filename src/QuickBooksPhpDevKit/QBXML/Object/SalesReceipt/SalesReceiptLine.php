<?php declare(strict_types=1);

/**
 *
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\SalesReceipt;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\SalesReceipt;

/**
 *
 *
 */
class SalesReceiptLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks SalesReceipt SalesReceiptLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the Item ListID for this SalesReceiptLine
	 */
	public function setItemListID(string $ListID): bool
	{
		return $this->set('ItemRef ListID', $ListID);
	}

	/**
	 * Set the item application ID for this SalesReceiptLine
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function setItemApplicationID($value): bool
	{
		return $this->set('ItemRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ITEM'], PackageInfo::QbId['LISTID'], $value));
	}

	/**
	 * Set the item name for this SalesReceiptLine
	 * @deprecated
	 * @param string $name
	 * @return boolean
	 */
	public function setItemName(string $name): bool
	{
		return $this->set('ItemRef FullName', $name);
	}

	public function setItemFullName(string $FullName): bool
	{
		return $this->setFullNameType('ItemRef FullName', null, null, $FullName);
	}

	/**
	 * Get the ListID for this item
	 */
	public function getItemListID(): string
	{
		return $this->get('ItemRef ListID');
	}

	/**
	 * Get the item application ID
	 *
	 * @return mixed
	 */
	public function getItemApplicationID()
	{
		//print($this->get('ItemRef ' . PackageInfo::$API_APPLICATIONID) . '<br />');

		return $this->extractApplicationID($this->get('ItemRef ' . PackageInfo::$API_APPLICATIONID));
	}

	/**
	 * Get the name of the item for this SalesReceiptLine item
	 * @deprecated
	 */
	public function getItemName(): string
	{
		return $this->getItemFullName();
	}

	public function getItemFullName(): string
	{
		return $this->get('ItemRef FullName');
	}

	public function setDesc(string $descrip): bool
	{
		return $this->set('Desc', $descrip);
	}

	public function setDescription(string $descrip): bool
	{
		return $this->setDesc($descrip);
	}

	public function setQuantity($quantity): bool
	{
		return $this->set('Quantity', (float) $quantity);
	}

	public function setRate($rate): bool
	{
		return $this->set('Rate', sprintf('%01.2f', (float) $rate));
	}

	public function setAmount($amount): bool
	{
		return $this->setAmountType('Amount', $amount);
	}

	public function setUnitOfMeasureSet($uom): bool
	{
		return $this->set('UnitOfMeasureSet', $uom);
	}

	public function getUnitOfMeasureSet(): string
	{
		return $this->get('UnitOfMeasureSet');
	}

	public function setTaxable(): bool
	{
		return $this->setSalesTaxCodeName(PackageInfo::TaxCode['TAXABLE']);
	}

	public function setNonTaxable(): bool
	{
		return $this->setSalesTaxCodeName(PackageInfo::TaxCode['NONTAXABLE']);
	}

	public function setSalesTaxCodeName(string $name): bool
	{
		return $this->setSalesTaxCodeFullName($name);
	}

	public function setSalesTaxCodeFullName(string $FullName): bool
	{
		return $this->setFullNameType('SalesTaxCodeRef FullName', null, null, $FullName);
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

	public function asXML(?string $root = null, ?string $parent = null, $object = null)
	{
		switch ($parent)
		{
			case PackageInfo::Actions['ADD_SALESRECEIPT']:
				$root = 'SalesReceiptLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_SALESRECEIPT']:
				$root = 'SalesReceiptLineMod';
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
		return 'SalesReceiptLine';
	}
}
