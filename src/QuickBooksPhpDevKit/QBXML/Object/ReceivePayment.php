<?php declare(strict_types=1);

/**
 * QuickBooks ReceivePayment object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\{
	PackageInfo,
	QBXML\AbstractQbxmlObject,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\Object\ReceivePayment\AppliedToTxn;

/**
 * QuickBooks ReceivePayment object
 */
class ReceivePayment extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\ReceivePayment object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the TxnID of the ReceivePayment
	 */
	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}

	/**
	 * Alias of {@link QBXML\Object\ReceivePayment::setTxnID()}
	 */
	public function setTransactionID(string $TxnID): bool
	{
		return $this->setTxnID($TxnID);
	}

	/**
	 * Get the ListID of the ReceivePayment
	 */
	public function getTxnID(): ?string
	{
		return $this->get('TxnID');
	}

	/**
	 * Alias of {@link QBXML\Object\ReceivePayment::getTxnID()}
	 */
	public function getTransactionID(): ?string
	{
		return $this->getTxnID();
	}

	/**
	 * Set the customer ListID
	 */
	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID' , $ListID);
	}

	/**
	 * Set the customer ApplicationID (auto-replaced by the API with a ListID)
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function setCustomerApplicationID($value): bool
	{
		return $this->set('CustomerRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CUSTOMER'], PackageInfo::QbId['LISTID'], $value));
	}


	public function getCustomerApplicationID()
	{
		return $this->get('CustomerRef ' . PackageInfo::$API_APPLICATIONID);
	}

	/**
	 * @deprecated
	 */
	public function setCustomerName(string $name): bool
	{
		return $this->setCustomerFullName($name);
	}

	public function setCustomerFullName(string $name): bool
	{
		return $this->set('CustomerRef FullName', $name);
	}

	/**
	 * Get the customer ListID
	 */
	public function getCustomerListID(): ?string
	{
		return $this->get('CustomerRef ListID');
	}

	/**
	 * @deprecated
	 */
	public function getCustomerName(): ?string
	{
		return $this->getCustomerFullName();
	}

	public function getCustomerFullName(): ?string
	{
		return $this->get('CustomerRef FullName');
	}

	/**
	 * Set the transaction date
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Alias of {@link QBXML\Object\ReceivePayment::setTxnDate()}
	 */
	public function setTransactionDate($date): bool
	{
		return $this->setTxnDate($date);
	}

	/**
	 * Get the transaction date
	 */
	public function getTxnDate(string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('TxnDate');
	}



	/**
	 * Set the reference number
	 */
	public function setRefNumber($str): bool
	{
		return $this->set('RefNumber', (string) $str);
	}

	/**
	 * Get the reference number
	 */
	public function getRefNumber(): ?string
	{
		return $this->get('RefNumber');
	}

	/**
	 * Alias of {@link QBXML\Object\ReceivePayment::addAppliedToTxn()}
	 */
	public function addAppliedToTransaction(AppliedToTxn $obj): bool
	{
		return $this->addAppliedToTxn($obj);
	}

	/**
	 *
	 *
	 */
	public function addAppliedToTxn(AppliedToTxn $obj): bool
	{
		/*
		$lines = $this->get('AppliedToTxn');

		if (!is_array($lines))
		{
			$lines = [];
		}

		//
		$lines[] = $obj;

		return $this->set('AppliedToTxn', $lines);*/

		return $this->addListItem('AppliedToTxn', $obj);
	}

	public function getAppliedToTxn(int $i): ?AppliedToTxn
	{
		return $this->getListItem('AppliedToTxn', $i);
	}

	public function listAppliedToTxns(): array
	{
		return $this->getList('AppliedToTxn');
	}

	/**
	 * Alias of {@link QBXML\Object\ReceivePayment::getTxnDate()}
	 */
	public function getTransactionDate(string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * Set the total amount of the received payment
	 */
	public function setTotalAmount($amount): bool
	{
		return $this->setAmountType('TotalAmount', $amount);
	}

	/**
	 * Get the total amount of the received payment
	 */
	public function getTotalAmount()
	{
		return $this->getAmountType('TotalAmount');
	}

	public function setARAccountListID(string $ListID): bool
	{
		return $this->set('ARAccountRef ListID', $ListID);
	}

	/**
	 * @deprecated
	 */
	public function setARAccountName(string $name): bool
	{
		return $this->setARAccountFullName($name);
	}

	public function setARAccountFullName(string $name): bool
	{
		return $this->set('ARAccountRef FullName', $name);
	}

	public function setARAccountApplicationID($value): bool
	{
		return $this->set('ARAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['QUICKBOOKS_OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getARAccountApplicationID()
	{
		return $this->get('ARAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getARAccountListID(): ?string
	{
		return $this->get('ARAccountRef ListID');
	}

	/**
	 * @deprecated
	 */
	public function getARAccountName(): ?string
	{
		return $this->getARAccountFullName();
	}

	public function getARAccountFullName(): ?string
	{
		return $this->get('ARAccountRef FullName');
	}

	public function setPaymentMethodListID(string $ListID): bool
	{
		return $this->set('PaymentMethodRef ListID', $ListID);
	}

	/**
	 * @deprecated
	 */
	public function setPaymentMethodName(string $name): bool
	{
		return $this->setPaymentMethodFullName($name);
	}

	public function setPaymentMethodFullName(string $name): bool
	{
		return $this->set('PaymentMethodRef FullName', $name);
	}

	public function setPaymentMethodApplicationID($value): bool
	{
		return $this->set('PaymentMethodRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_PAYMENTMETHOD'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getPaymentMethodApplicationID()
	{
		return $this->get('PaymentMethodRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getPaymentMethodListID(): ?string
	{
		return $this->get('PaymentMethodRef ListID');
	}

	/**
	 * @deprecated
	 */
	public function getPaymentMethodName(): ?string
	{
		return $this->getPaymentMethodFullName();
	}

	public function getPaymentMethodFullName(): ?string
	{
		return $this->get('PaymentMethodRef FullName');
	}

	public function setDepositToAccountListID(string $ListID): bool
	{
		return $this->set('DepositToAccountRef ListID', $ListID);
	}

	/**
	 * @deprecated
	 */
	public function setDepositToAccountName(string $name): bool
	{
		return $this->setDepositToAccountFullName($name);
	}

	public function setDepositToAccountFullName(string $name): bool
	{
		return $this->set('DepositToAccountRef FullName', $name);
	}

	public function setDepositToAccountApplicationID($value): bool
	{
		return $this->set('DepositToAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getDepositToAccountApplicationID()
	{
		return $this->get('DepositToAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getDepositToAccountListID(): ?string
	{
		return $this->get('DepositToAccountRef ListID');
	}

	/**
	 * @deprecated
	 */
	public function getDepositToAccountName(): ?string
	{
		return $this->getDepositToAccountFullName();
	}

	public function getDepositToAccountFullName(): ?string
	{
		return $this->get('DepositToAccountRef FullName');
	}

	public function setMemo(string $memo): bool
	{
		return $this->set('Memo', $memo);
	}

	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	/**
	 * Set whether or not this transaction is an auto-apply transaction
	 */
	public function setIsAutoApply(bool $isautoapply): bool
	{
		return $this->setBooleanType('IsAutoApply', $isautoapply);
	}

	/**
	 * Get whether or not this transaction is an auto-apply transaction
	 */
	public function getIsAutoApply(): ?bool
	{
		return $this->getBooleanType('IsAutoApply');
	}

	public function asList(string $request)
	{
		switch ($request)
		{
			case 'ReceivePaymentAddRq':
				if (isset($this->_object['AppliedToTxn']))
				{
					$this->_object['AppliedToTxnAdd'] = $this->_object['AppliedToTxn'];
				}
				break;

			case 'ReceivePaymentModRq':
				if (isset($this->_object['AppliedToTxn']))
				{
					$this->_object['AppliedToTxnMod'] = $this->_object['AppliedToTxn'];
				}
				break;
		}

		return parent::asList($request);
	}

	public function asXML(?string $root = null, ?string $parent = null, ?array $object = null): Node
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($root)
		{
			case PackageInfo::Actions['ADD_RECEIVEPAYMENT']:
				if ($this->exists('AppliedToTxnAdd'))
				{
					foreach ($object['AppliedToTxnAdd'] as $key => $obj)
					{
						$obj->setOverride('AppliedToTxnAdd');
					}
				}
				break;

			case PackageInfo::Actions['MOD_RECEIVEPAYMENT']:
				if ($this->exists('AppliedToTxnMod'))
				{
					foreach ($object['AppliedToTxnMod'] as $key => $obj)
					{
						$obj->setOverride('AppliedToTxnMod');
					}
				}

				break;
		}

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_RECEIVEPAYMENT'];
	}
}
