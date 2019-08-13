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

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\BillPaymentCheck\AppliedToTxn;
use QuickBooksPhpDevKit\XML\Node;

/**
 * QuickBooks ReceivePayment object
 */
class BillPaymentCheck extends AbstractQbxmlObject
{
	/**
	 * Create a new BillPaymentCheck object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the TxnID of the BillPaymentCheck
	 */
	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}

	/**
	 * Alias of {@link BillPaymentCheck::setTxnID()}
	 */
	public function setTransactionID($TxnID): bool
	{
		return $this->setTxnID($TxnID);
	}

	/**
	 * Get the ListID of the BillPaymentCheck
	 */
	public function getTxnID(): ?string
	{
		return $this->get('TxnID');
	}

	/**
	 * Alias of {@link BillPaymentCheck::getTxnID()}
	 */
	public function getTransactionID(): ?string
	{
		return $this->getTxnID();
	}

	/**
	 * Set the PayeeEntity ListID
	 */
	public function setPayeeEntityListID(string $ListID): bool
	{
		return $this->set('PayeeEntityRef ListID' , $ListID);
	}

	/**
	 * Set the PayeeEntity ApplicationID (auto-replaced by the API with a ListID)
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function setPayeeEntityApplicationID($value): bool
	{
		return $this->set('PayeeEntityRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_VENDOR'], PackageInfo::QbId['LISTID'], $value));
	}


	public function getPayeeEntityApplicationID(): ?string
	{
		return $this->get('PayeeEntityRef ' . PackageInfo::$API_APPLICATIONID);
	}

	/**
	 * Set the PayeeEntity full name
	 */
	public function setPayeeEntityFullName(string $name): bool
	{
		return $this->set('PayeeEntityRef FullName', $name);
	}

	/**
	 * Get the PayeeEntity ListID
	 */
	public function getPayeeEntityListID(): ?string
	{
		return $this->get('PayeeEntityRef ListID');
	}

	/**
	 * Get the PayeeEntity name
	 */
	public function getPayeeEntityFullName(): ?string
	{
		return $this->get('PayeeEntityRef FullName');
	}

	/**
	 * Set the transaction date
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Alias of {@link BillPaymentCheck::setTxnDate()}
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

	public function setIsToBePrinted(bool $bool): bool
	{
		return $this->setBooleanType('IsToBePrinted', $bool);
	}

	public function getIsToBePrinted(): ?bool
	{
		return $this->getBooleanType('IsToBePrinted');
	}

	/**
	 * Set the reference number
	 */
	public function setRefNumber(string $str): bool
	{
		return $this->set('RefNumber', $str);
	}

	/**
	 * Get the reference number
	 */
	public function getRefNumber(): ?string
	{
		return $this->get('RefNumber');
	}

	/**
	 * Add a transcation this payment is applied to
	 */
	public function addAppliedToTxn(AppliedToTxn $obj): bool
	{
		return $this->addListItem('AppliedToTxn', $obj);
	}

	/**
	 * Alias of {@link BillPaymentCheck::addAppliedToTxn()}
	 */
	public function addAppliedToTransaction(AppliedToTxn $obj): bool
	{
		return $this->addAppliedToTxn($obj);
	}

	/**
	 * Alias of {@link BillPaymentCheck::getTxnDate()}
	 */
	public function getTransactionDate(string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * Set the total amount of the received payment
	 */
	public function setTotalAmount(float $amount): bool
	{
		return $this->setAmountType('TotalAmount', $amount);
	}

	/**
	 * Get the total amount of the received payment
	 *
	 * @return float
	 */
	public function getTotalAmount()
	{
		return $this->getAmountType('TotalAmount');
	}

	public function setAPAccountListID(string $ListID): bool
	{
		return $this->set('APAccountRef ListID', $ListID);
	}

	public function setAPAccountFullName(string $name): bool
	{
		return $this->set('APAccountRef FullName', $name);
	}

	public function setAPAccountApplicationID(string $value): bool
	{
		return $this->set('APAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getAPAccountApplicationID(): ?string
	{
		return $this->get('APAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getAPAccountListID(): ?string
	{
		return $this->get('APAccountRef ListID');
	}

	public function getAPAccountFullName(): ?string
	{
		return $this->get('APAccountRef FullName');
	}

	public function setBankAccountListID(string $ListID): bool
	{
		return $this->set('BankAccountRef ListID', $ListID);
	}

	public function setBankAccountFullName(string $name): bool
	{
		return $this->set('BankAccountRef FullName', $name);
	}

	public function setBankAccountApplicationID($value): bool
	{
		return $this->set('BankAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(QUICKBOOKS_OBJECT_ACCOUNT, PackageInfo::QbId['LISTID'], $value));
	}

	public function getBankAccountApplicationID(): ?string
	{
		return $this->get('BankAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getBankAccountListID(): ?string
	{
		return $this->get('BankAccountRef ListID');
	}

	public function getBankAccountFullName(): ?string
	{
		return $this->get('BankAccountRef FullName');
	}

	public function setMemo(string $memo): bool
	{
		return $this->set('Memo', $memo);
	}

	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	public function asList(string $request): array
	{
		switch ($request)
		{
			case 'BillPaymentCheckAddRq':

				if (isset($this->_object['AppliedToTxn']))
				{
					$this->_object['AppliedToTxnAdd'] = $this->_object['AppliedToTxn'];
				}
				break;

			case 'BillPaymentCheckModRq':
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
			case PackageInfo::Actions['ADD_BILLPAYMENTCHECK']:

				/*
				if (isset($this->_object['AppliedToTxn']))
				{
					$this->_object['AppliedToTxnAdd'] = $this->_object['AppliedToTxn'];
				}
				*/

				if ($this->exists('AppliedToTxnAdd'))
				{
					foreach ($object['AppliedToTxnAdd'] as $key => $obj)
					{
						$obj->setOverride('AppliedToTxnAdd');
					}
				}
				break;

			case PackageInfo::Actions['MOD_BILLPAYMENTCHECK']:
				/**
				 * @todo finish me!
				 */

				break;
		}

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_BILLPAYMENTCHECK'];
	}
}
