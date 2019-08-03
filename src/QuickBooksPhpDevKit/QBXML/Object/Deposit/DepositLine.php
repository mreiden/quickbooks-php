<?php declare(strict_types=1);

/**
 * DepositLine class for QuickBooks
 *
 * @author Keith Palmer Jr. <keith@ConsoliBYTE.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\Deposit;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;

/**
 *
 */
class DepositLine extends AbstractQbxmlObject
{
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setPaymentTxnID(string $TxnID): bool
	{
		return $this->set('PaymentTxnID', $TxnID);
	}

	public function getPaymentTxnID(): string
	{
		return $this->get('PaymentTxnID');
	}

	public function setPaymentTxnLineID(string $TxnLineID): bool
	{
		return $this->set('PaymentTxnLineID', $TxnLineID);
	}

	public function getPaymentTxnLineID(): string
	{
		return $this->get('PaymentTxnLineID');
	}

	public function setOverrideMemo(string $value): bool
	{
		return $this->set('OverrideMemo', $value);
	}

	/**
	 * Get the Override Memo for the DepositLine
	 */
	public function getOverrideMemo(): string
	{
		return $this->get('OverrideMemo');
	}

	public function setOverrideCheckNumber($value): bool
	{
		return $this->set('OverrideCheckNumber', $value);
	}

	/**
	 * Get the Override Check Number for the DepositLine
	 */
	public function getOverrideCheckNumber(): string
	{
		return $this->get('OverrideCheckNumber');
	}


	/**
	 * Set the Amount for the DepositLine
	 *
	 * @param string $value
	 * @return boolean
	 */
	public function setAmount($value): bool
	{
		return $this->set('Amount', $value);
	}

	/**
	 * Get the Amount for the DepositLine
	 *
	 * @return string
	 */
	public function getAmount()
	{
		return $this->get('Amount');
	}

	public function setMemo(string $value): bool
	{
		return $this->set('Memo', $value);
	}

	/**
	 * Get the Memo for the DepositLine
	 */
	public function getMemo(): string
	{
		return $this->get('Memo');
	}


	// Path: ClassRef ListID, datatype:

	/**
	 * Set the ClassRef ListID for the DepositLine
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	/**
	 * Get the ClassRef ListID for the DepositLine
	 */
	public function getClassListID(): string
	{
		return $this->get('ClassRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the DepositLine
	 *
	 * @param mixed $value			The primary key within your own application
	 */
	public function setClassApplicationID($value): bool
	{
		return $this->set('ClassRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CLASS'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: ClassRef FullName, datatype:

	/**
	 * Set the ClassRef FullName for the DepositLine
	 */
	public function setClassFullName(string $FullName): bool
	{
		return $this->set('ClassRef FullName', $FullName);
	}

	/**
	 * Get the ClassRef FullName for the DepositLine
	 */
	public function getClassFullName(): string
	{
		return $this->get('ClassRef FullName');
	}

	public function asXML(string $root = null, string $parent = null, $object = null)
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_DEPOSIT']:
				$root = 'DepositLineAdd';
				$parent = null;
				break;
			case PackageInfo::Actions['MOD_DEPOSIT']:
				$root = 'DepositLineMod';
				$parent = null;
				break;
		}

		return parent::asXML($root, $parent, $object);
	}

	public function object(): string
	{
		return 'DepositLine';
	}
}
