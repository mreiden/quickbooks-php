<?php declare(strict_types=1);

/**
 * QuickBooks CreditMemoLine object class
 *
 * @author Jayson Lindsley <jay.lindsley@gmail.com>
 * @author Keith Palmer <keith@consolibyte.com>
 *
 * TODO: Add support for items as per the QBXML spec
 *
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\CreditMemo;

use QuickBooksPhpDevKit\{
	PackageInfo,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\CreditMemo,
};

class CreditMemoLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\CreditMemo\CreditMemoLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the item name for this credit memo line
	 */
	public function setItemName(string $name): bool
	{
		return $this->setItemFullName($name);
	}

	//Use this one!
	public function setItemFullName(string $FullName): bool
	{
		return $this->setFullNameType('ItemRef FullName', null, null, $FullName);
	}

	/**
	 * Get the name of the item for this invoice line item
	 */
	public function getItemName(): ?string
	{
		return $this->getItemFullName();
	}

	public function getItemFullName(): ?string
	{
		return $this->get('ItemRef FullName');
	}

	public function setDescription(string $descrip): bool
	{
		return $this->setDesc($descrip);
	}

	public function getDescription(): ?string
	{
		return $this->getDesc();
	}

	public function setDesc(string $value): bool
	{
		return $this->set('Desc', $value);
	}
	public function getDesc(): ?string
	{
		return $this->get('Desc');
	}

	public function setQuantity(float $quantity): bool
	{
		return $this->set('Quantity', $quantity);
	}

	public function getQuantity(): ?float
	{
		return $this->get('Quantity');
	}

	public function setRate($value): bool
	{
		return $this->set('Rate', (float) $value);
	}

	public function getRate(): ?float
	{
		return $this->get('Rate');
	}

	public function setClassFullName(string $value): bool
	{
		return $this->set('ClassRef FullName', $value);
	}

	public function setAmount($amount): bool
	{
		return $this->setAmountType('Amount', $amount);
	}

	public function getAmount()
	{
		$amount = $this->getAmountType('Amount');
		if ($amount)
		{
			return $amount;
		}

		return $this->get('Rate') * $this->get('Quantity');
	}

	/**
	 * Set the Item TxnLineID for this CreditMemoLine
	 */
	public function setTxnLineID(int $TxnLineID): bool
	{
		return $this->set('TxnLineID', $TxnLineID);
	}
	public function getTxnLineID(): ?int
	{
		return $this->get('TxnLineID');
	}

	public function asXML(?string $root = null, ?string $parent = null, ?array $object = null): Node
	{
		$this->_cleanup();

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_CREDITMEMO']:
				$root = 'CreditMemoLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_CREDITMEMO']:
				$root = 'CreditMemoLineMod';
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
		return 'CreditMemoLine';
	}
}
