<?php declare(strict_types=1);

/**
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\BillPaymentCheck;

use QuickBooksPhpDevKit\{
	PackageInfo,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\BillPaymentCheck,
};

/**
 *
 *
 */
class AppliedToTxn extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks BillPaymentCheck AppliedToTxn object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}

	public function setTransactionID(string $TxnID): bool
	{
		return $this->setTxnID($TxnID);
	}

	public function getTxnID(): ?string
	{
		return $this->get('TxnID');
	}

	public function getTransactionID(): ?string
	{
		return $this->getTxnID();
	}

	public function setTxnApplicationID($value): bool
	{
		return $this->set(PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_BILL'], PackageInfo::QbId['TXNID'], $value));
		//return $this->set('NullRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_INVOICE'], PackageInfo::QbId['TXNID'], $value));
	}

	public function getTxnApplicationID()
	{

	}

	public function getPaymentAmount($amount)
	{
		return $this->getAmountType('PaymentAmount');
	}

	public function setPaymentAmount($amount): bool
	{
		return $this->setAmountType('PaymentAmount', $amount);
	}

	public function setDiscountAmount($amount): bool
	{
		return $this->setAmountType('DiscountAmount', $amount);
	}

	public function getDiscountAmount()
	{
		return $this->getDiscountAmount('DiscountAmount');
	}

	public function asXML(string $root = null, string $parent = null, $object = null): Node
	{
		$this->_cleanup();

		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_BILLPAYMENTCHECK']:
				$root = 'AppliedToTxnAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_BILLPAYMENTCHECK']:
				$root = 'AppliedToTxnMod';
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
		return 'AppliedToTxn';
	}
}
