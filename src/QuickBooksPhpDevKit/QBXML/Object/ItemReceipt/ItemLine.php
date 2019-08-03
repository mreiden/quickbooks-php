<?php declare(strict_types=1);

/**
 * QuickBooks ItemLine object container
 *
 * @todo Documentation
 *
 * @author Harley Laue <harley.laue@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\ItemReceipt;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\ItemReceipt;
use QuickBooksPhpDevKit\XML\Node;

/**
 * Quickbooks ItemReceipt ItemLine definition
 */
class ItemLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks ReceiptItem ItemLine object
	 *
	 * @param array $arr
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function getItemListID(): ?string
	{
		return $this->get('ItemRef ListID');
	}

	public function setItemListID(string $ListID): bool
	{
		return $this->set('ItemRef ListID', $ListID);
	}

	public function getItemName(): ?string
	{
		return $this->get('ItemRef FullName');
	}

	public function setItemName(string $Name): bool
	{
		return $this->set('ItemRef FullName', $Name);
	}

	public function getDescription(): ?string
	{
		return $this->get('Desc');
	}

	public function setDescription(string $Desc): bool
	{
		return $this->set('Desc', $Desc);
	}

	public function getQuantity()
	{
		return $this->get('Quantity');
	}

	public function setQuantity($Quantity): bool
	{
		return $this->set('Quantity', (float) $Quantity);
	}

	public function getUnitOfMeasure(): ?string
	{
		return $this->get('UnitOfMeasure');
	}

	public function setUnitOfMeasure(string $UnitOfMeasure): bool
	{
		return $this->set('UnitOfMeasure', $UnitOfMeasure);
	}

	public function getCost()
	{
		return $this->getAmountType('Cost');
	}

	public function setCost($Cost): bool
	{
		return $this->setAmountType('Cost', $Cost);
	}

	public function getAmount()
	{
		return $this->getAmountType('Amount');
	}

	public function setAmount($Amount): bool
	{
		return $this->setAmountType('Amount', $Amount);
	}

	public function getTaxAmount()
	{
		return $this->getAmountType('TaxAmount');
	}

	public function setTaxAmount($TaxAmount): bool
	{
		return $this->setAmountType('TaxAmount', $TaxAmount);
	}

	public function getCustomerListID(): ?string
	{
		return $this->get('CustomerRef ListID');
	}

	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID', $ListID);
	}

	public function getCustomerName(): ?string
	{
		return $this->get('CustomerRef FullName');
	}

	public function setCustomerName(string $Name): bool
	{
		return $this->set('CustomerRef FullName', $Name);
	}

	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	public function setClassName(string $Name): bool
	{
		return $this->set('ClassRef FullName', $Name);
	}

	public function getSalesTaxCodeListID(): ?string
	{
		return $this->get('SalesTaxCodeRef ListID');
	}

	public function setSalesTaxCodeListID(string $ListID): bool
	{
		return $this->set('SalesTaxCodeRef ListID', $ListID);
	}

	public function getSalesTaxCodeName(): ?string
	{
		return $this->get('SalesTaxCodeRef FullName');
	}

	public function setSalesTaxCodeName(stromg $Name): bool
	{
		return $this->set('SalesTaxCodeRef FullName', $Name);
	}

	public function getBillableStatus(): ?string
	{
		return $this->get('BillableStatus');
	}

	public function setBillableStatus(string $BillableStatus): bool
	{
		return $this->set('BillableStatus', $BillableStatus);
	}

	public function getOverrideItemAccountListID(): ?string
	{
		return $this->get('OverrideItemAccountRef ListID');
	}

	public function setOverrideItemAccountListID(string $ListID): bool
	{
		return $this->set('OverrideItemAccountRef ListID', $ListID);
	}

	public function getOverrideItemAccountName(): ?string
	{
		return $this->get('OverrideItemAccountRef FullName');
	}

	public function setOverrideItemAccountName(string $Name): bool
	{
		return $this->set('OverrideItemAccountRef FullName', $Name);
	}

	public function getLinkToTxnID(): ?string
	{
		return $this->getLinkToTxn('LinkToTxn TxnID');
	}

	public function setLinkToTxnID(string $TxnID): bool
	{
		return $this->set('LinkToTxn TxnID', $TxnID);
	}

	public function getLinkToTxnLineID()
	{
		return $this->get('LinkToTxn TxnLineID');
	}

	public function setLinkToTxnLineID($TxnLineID)
	{
		return $this->set('LinkToTxn TxnLineID', $TxnLineID);
	}

	public function asXML(string $root = null, string $parent = null, $object = null): Node
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_ITEMRECEIPT']:
				$root = 'ItemLineAdd';
				$parent = null;
				break;
// Currently unimplemented
/*
			case PackageInfo::Actions['ADD_ITEMRECEIPT']:
				$root = 'ItemLineMod';
				break;
*/
		}

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return 'ItemLine';
	}
}
