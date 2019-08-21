<?php declare(strict_types=1);

/**
 * QuickBooks InvoiceLine object class
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\Invoice;

use QuickBooksPhpDevKit\{
	PackageInfo,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\Invoice,
};

/**
 * QuickBooks InvoiceLine class for Invoices
 */
class InvoiceLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks Invoice InvoiceLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the Item ListID for this InvoiceLine
	 */
	public function setItemListID(string $ListID): bool
	{
		return $this->set('ItemRef ListID', $ListID);
	}

	/**
	 * Set the item application ID for this invoice line
	 *
	 * @param mixed $value
	 */
	public function setItemApplicationID($value): bool
	{
		return $this->set('ItemRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ITEM'], PackageInfo::QbId['LISTID'], $value));
	}

	/**
	 * Set the item name for this invoice line
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
	public function getItemListID(): ?string
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
	 * Get the name of the item for this invoice line item
	 */
	public function getItemName(): ?string
	{
		return $this->get('ItemRef FullName');
	}

	public function getItemFullName(): ?string
	{
		return $this->get('ItemRef FullName');
	}

	public function setDesc(string $descrip): bool
	{
		return $this->set('Desc', $descrip);
	}

	public function getDesc(): ?string
	{
		return $this->get('Desc');
	}

	public function setDescription(string $descrip): bool
	{
		return $this->setDesc($descrip);
	}

	public function getDescription(): ?string
	{
		return $this->getDesc();
	}

	public function setQuantity($quantity): bool
	{
		return $this->setAmountType('Quantity', $quantity);
	}

	public function getQuantity()
	{
		return $this->get('Quantity');
	}

	public function setUnitOfMeasure(string $unit): bool
	{
		return $this->set('UnitOfMeasure', $unit);
	}

	public function getUnitOfMeasure(): ?string
	{
		return $this->get('UnitOfMeasure');
	}

	public function setRate($rate): bool
	{
		return $this->setAmountType('Rate', $rate);
	}

	public function getRate()
	{
		return $this->get('Rate');
	}

	public function getAmount()
	{
		if ($amount = $this->get('Amount'))
		{
			return $this->get('Amount');
		}

		return $this->get('Rate') * $this->get('Quantity');
	}

	public function setRatePercent($percent)
	{
		return $this->set('RatePercent', floatval($percent));
	}

	public function getRatePercent()
	{
		return $this->get('RatePercent');
	}

	/*
	public function setPriceLevelApplicationID($value): void
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

	public function getPriceLevelName(): ?string
	{
		return $this->get('PriceLevelRef FullName');
	}

	public function getPriceLevelListID(): ?string
	{
		return $this->get('PriceLevelRef ListID');
	}

	/**
	 * Set the class ListID for this invoice line item
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}
	/*
	public function setClassApplicationID($value): void
	{

	}
	*/

	/**
	 * Set the class name for this invoice line item
	 */
	public function setClassName(string $name): bool
	{
		return $this->set('ClassRef FullName', $name);
	}

	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	public function setAmount($amount): bool
	{
		return $this->setAmountType('Amount', floatval($amount));
	}

	public function setServiceDate(string $date): bool
	{
		return $this->setDateType('ServiceDate', $date);
	}

	public function getServiceDate(string $format = 'Y-m-d'): ?string
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

	public function getSalesTaxCodeName(): ?string
	{
		return $this->get('SalesTaxCodeRef FullName');
	}

	public function getSalesTaxCodeListID(): ?string
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
		return $this->get('SalesTaxCodeRef FullName') == PackageInfo::TaxCode['TAXABLE'];
	}

	/**
	 * Set the account name for this line item
	 */
	public function setOverrideItemAccountName(string $name): bool
	{
		return $this->set('OverrideItemAccountRef FullName', $name);
	}

	/**
	 * Set the account ListID for this line item
	 */
	public function setOverrideItemAccountListID(string $ListID): bool
	{
		return $this->set('OverrideItemAccountRef ListID', $ListID);
	}

	public function setOverrideItemAccountApplicationID($value): void
	{

	}

	public function getOverrideItemAccountListID(): ?string
	{
		return $this->get('OverrideItemAccountRef ListID');
	}

	public function getOverrideItemAccountName(): ?string
	{
		return $this->get('OverrideItemAccountRef FullName');
	}

	/**
	 * Set the Item TxnLineID for this InvoiceLine
	 */
	public function setTxnLineID(int $TxnLineID): bool
	{
		return $this->set('TxnLineID', $TxnLineID);
	}
	public function getTxnLineID(): ?int
	{
		return $this->get('TxnLineID');
	}


	public function setOther1(string $value): bool
	{
		return $this->set('Other1', $value);
	}

	public function getOther1(): ?string
	{
		return $this->get('Other1');
	}

	public function setOther2(string $value): bool
	{
		return $this->set('Other2', $value);
	}

	public function getOther2(): ?string
	{
		return $this->get('Other2');
	}

	/**
	 *
	 *
	 * @return boolean
	 */
	protected function _cleanup(): bool
	{
		if ($this->exists('Amount'))
		{
			$this->setAmountType('Amount', $this->getAmountType('Amount'));
		}

		return true;
	}

	public function asXML(?string $root = null, ?string $parent = null, ?array $object = null): Node
	{
		$this->_cleanup();

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_INVOICE']:
				$root = 'InvoiceLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_INVOICE']:
				$root = 'InvoiceLineMod';
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
		return 'InvoiceLine';
	}
}
