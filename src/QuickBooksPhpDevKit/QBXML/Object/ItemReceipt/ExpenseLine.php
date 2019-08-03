<?php declare(strict_types=1);

/**
 * QuickBooks ExpenseLine object container
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
 * Quickbooks ItemReceipt ExpenseLine definition
 */
class ExpenseLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks ReceiptItem ExpenseLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function getAccountListID(): ?string
	{
		return $this->get('AccountRef ListID');
	}

	public function setAccountListID(string $ListID): bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	public function getAccountName(): ?string
	{
		return $this->get('AccountRef FullName');
	}

	public function setAccountName(string $name): bool
	{
		return $this->set('AccountRef FullName', $name);
	}

	public function getAmount()
	{
		return $this->get('Amount');
	}

	public function setAmount($amount): bool
	{
		return $this->set('Amount', $amount);
	}

	public function getMemo()
	{
		return $this->get('Memo');
	}

	public function setMemo(string $memo): bool
	{
		return $this->set('Memo', $memo);
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

	public function setCustomerName(string $name): ?string
	{
		return $this->set('CustomerRef FullName', $name);
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

	public function setClassName(string $name): bool
	{
		return $this->set('ClassRef FullName', $name);
	}

	public function getBillableStatus(): ?string
	{
		return $this->get('BillableStatus');
	}

	/*
	 * @param billable must be one of: Billable, NotBillable, HasBeenBilled
	 */
	public function setBillableStatus(string $billable): bool
	{
		return $this->set('BillableStatus', $billable);
	}

	public function asXML(?string $root = null, ?string $parent = null, $object = null): Node
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_ITEMRECEIPT']:
				$root = 'ExpenseLineAdd';
				$parent = null;
				break;
// Currently unimplemented
/*
			case PackageInfo::Actions['MOD_ITEMRECEIPT']:
				$root = 'ExpenseLineMod';
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
		return "ExpenseLine";
	}
}
