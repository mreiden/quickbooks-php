<?php declare(strict_types=1);

/**
 * Bill ExpenseLine class for QuickBooks
 *
 * @author Keith Palmer Jr. <keith@ConsoliBYTE.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\Bill;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\XML\Node;

/**
 *
 */
class ExpenseLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\Bill\ExpenseLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	// Path: AccountRef ListID, datatype:

	/**
	 * Set the AccountRef ListID for the ExpenseLine
	 */
	public function setAccountListID(string $ListID): bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	/**
	 * Get the AccountRef ListID for the ExpenseLine
	 */
	public function getAccountListID(): string
	{
		return $this->get('AccountRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the ExpenseLine
	 *
	 * @param mixed $value			The primary key within your own application
	 */
	public function setAccountApplicationID($value): bool
	{
		return $this->set('AccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: AccountRef FullName, datatype:

	/**
	 * Set the AccountRef FullName for the ExpenseLine
	 *
	 * @param string $FullName		The FullName of the record to reference
	 */
	public function setAccountFullName(string $FullName): bool
	{
		return $this->set('AccountRef FullName', $FullName);
	}

	/**
	 * Get the AccountRef FullName for the ExpenseLine
	 */
	public function getAccountFullName(): string
	{
		return $this->get('AccountRef FullName');
	}

	// Path: Amount, datatype:

	/**
	 * Set the Amount for the ExpenseLine
	 *
	 * @param string $value
	 */
	public function setAmount($value): bool
	{
		return $this->setAmountType('Amount', $value);
	}

	/**
	 * Get the Amount for the ExpenseLine
	 */
	public function getAmount(): string
	{
		return $this->getAmountType('Amount');
	}

	// Path: Memo, datatype: STRTYPE

	/**
	 * Set the Memo for the ExpenseLine
	 */
	public function setMemo(string $value): bool
	{
		return $this->set('Memo', $value);
	}

	/**
	 * Get the Memo for the ExpenseLine
	 */
	public function getMemo(): string
	{
		return $this->get('Memo');
	}

	// Path: CustomerRef ListID, datatype:

	/**
	 * Set the CustomerRef ListID for the ExpenseLine
	 */
	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID', $ListID);
	}

	/**
	 * Get the CustomerRef ListID for the ExpenseLine
	 */
	public function getCustomerListID(): string
	{
		return $this->get('CustomerRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the ExpenseLine
	 *
	 * @param mixed $value			The primary key within your own application
	 */
	public function setCustomerApplicationID($value): bool
	{
		return $this->set('CustomerRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CUSTOMER'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: CustomerRef FullName, datatype:

	/**
	 * Set the CustomerRef FullName for the ExpenseLine
	 */
	public function setCustomerFullName(string $FullName): bool
	{
		return $this->set('CustomerRef FullName', $FullName);
	}

	/**
	 * Get the CustomerRef FullName for the ExpenseLine
	 */
	public function getCustomerFullName(): string
	{
		return $this->get('CustomerRef FullName');
	}

	// Path: ClassRef ListID, datatype:

	/**
	 * Set the ClassRef ListID for the ExpenseLine
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	/**
	 * Get the ClassRef ListID for the ExpenseLine
	 */
	public function getClassListID(): string
	{
		return $this->get('ClassRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the ExpenseLine
	 *
	 * @param mixed $value			The primary key within your own application
	 */
	public function setClassApplicationID($value): bool
	{
		return $this->set('ClassRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CLASS'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: ClassRef FullName, datatype:

	/**
	 * Set the ClassRef FullName for the ExpenseLine
	 */
	public function setClassName(string $FullName): bool
	{
		return $this->set('ClassRef FullName', $FullName);
	}

	/**
	 * Get the ClassRef FullName for the ExpenseLine
	 */
	public function getClassName(): bool
	{
		return $this->get('ClassRef FullName');
	}

	// Path: BillableStatus, datatype:

	/**
	 * Set the BillableStatus for the ExpenseLine
	 *
	 * NOTE!!!  You must set a CustomerFullName or CustomerListID or you will get "QuickBooks error message: Target is not reimbursable."
	 */
	public function setBillableStatus(string $value): bool
	{

		$valid = [
			'billable' => 'Billable',
			'notbillable' => 'NotBillable',
			'hasbeenbilled' => 'HasBeenBilled',
		];

		$value = strtolower(trim($value));
		if (!isset($valid[$value]))
		{
			throw new \Exception('Billable Status is invalid.  Must be Billable, NotBillable, or HasBeenBilled.');
		}

		return $this->set('BillableStatus', $valid[$value]);
	}

	/**
	 * Get the BillableStatus for the ExpenseLine
	 */
	public function getBillableStatus(): string
	{
		return $this->get('BillableStatus');
	}

	/**
	 * Set the Item TxnLineID for this ExpenseLine
	 */
	public function setTxnLineID(int $TxnLineID): bool
	{
		return $this->set('TxnLineID', $TxnLineID);
	}
	public function getTxnLineID(): int
	{
		return $this->get('TxnLineID');
	}


	public function asXML(string $root = null, string $parent = null, $object = null): Node
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_BILL']:
				$root = 'ExpenseLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_BILL']:
				$root = 'ExpenseLineMod';
				$parent = null;
				break;
		}

		return parent::asXML($root, $parent, $object);
	}

	public function object(): string
	{
		return 'ExpenseLine';
	}
}
