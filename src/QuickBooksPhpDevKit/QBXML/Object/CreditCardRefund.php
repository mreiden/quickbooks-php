<?php declare(strict_types=1);

/**
 * QuickBooks CreditCardRefund object
 *
 * @author Jayson Lindsley <jay.lindsley@gmail.com>
 * @author Keith Palmer <keith@consolibyte.com>
 *
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\XML\Node;

/**
* QuickBooks object class
 */
 class CreditCardRefund extends AbstractQbxmlObject
 {
 	/**
	 * Create a new QBXML\Object\CreditCardRefund object
	 */

	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID', $ListID);
	}

	public function getCustomerListID(): ?string
	{
		return $this->get('CustomerRef ListID');
	}

	public function setCustomerFullName(string $name): bool
	{
		return $this->set('CustomerRef FullName', $name);
	}

	public function getCustomerFullName(): ?string
	{
		return $this->get('CustomerRef FullName');
	}

	public function setAccountRefFullName(string $account): bool
	{
		return $this->set('RefundFromAccountRef FullName', $account);
	}

	public function getAccountRefFullName(): ?string
	{
		return $this->get('RefundFromAccountRef FullName');
	}

	public function setARAccountListID(string $ListID): bool
	{
		return $this->set('ARAccountRef ListID', $ListID);
	}

	public function setARAccountName(string $name): bool
	{
		return $this->set('ARAccountRef FullName', $name);
	}

	public function getARAccountListID(): ?string
	{
		return $this->get('ARAccountRef ListID');
	}

	public function getARAccountName(): ?string
	{
		return $this->get('ARAccountRef FullName');
	}

	public function setPaymentMethodName(string $name): bool
	{
		return $this->set('PaymentMethodRef FullName', $name);
	}

	public function getPaymentMethodName(): ?string
	{
		return $this->get('PaymentMethodRef FullName');
	}

	public function setPaymentMethodListID(string $ListID): bool
	{
		return $this->set('PaymentMethodRef ListID', $ListID);
	}

	public function getPaymentMethodListID(): ?string
	{
		return $this->get('PaymentMethodRef ListID');
	}

	public function setExchangeRate($rate): bool
	{
		return $this->set('ExchangeRate', floatval($rate));
	}

	public function getExchangeRate(): ?float
	{
		return $this->get('ExchangeRate');
	}

	public function setExternalGUID(string $guid): bool
	{
		return $this->set('ExternalGUID', $guid);
	}

	public function getExternalGUID(): ?string
	{
		return $this->get('ExternalGUID');
	}

	public function setMemo(string $memo): bool
	{
		return $this->set('Memo', $memo);
	}

	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	public function setRefundAppliedToTxnID(string $ID): bool
	{
		return $this->set('RefundAppliedToTxnAdd TxnID', $ID);
	}

	public function getRefundAppliedToTxnID(): ?string
	{
		return $this->get('RefundAppliedToTxnAdd TxnID');
	}

	public function setRefundAmount($amount): bool
	{
		return $this->set('RefundAppliedToTxnAdd RefundAmount', $amount);
	}

	public function setRefNumber(string $ref): bool
	{
		return $this->set('RefNumber', $ref);
	}

	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}

	public function getTransactionID(): ?string
	{
		return $this->get('TxnID');
	}

	/* The properties below are used when querying only */
	/* They are processed by QB as or statements */

	public function setFromModifiedDate($date): bool
	{
		return $this->setDateType('ModifiedDateRangeFilter FromModifiedDate', $date);
	}

	public function getFromModifiedDate(): ?string
	{
		return $this->getDateType('ModifiedDateRangeFilter FromModifiedDate');
	}

	public function setToModifiedDate($date): bool
	{
		return $this->setDateType('ModifiedDateRangeFilter ToModifiedDate', $date);
	}

	public function getToModifiedDate(): ?string
	{
		return $this->getDateType('ModifiedDateRangeFilter ToModifiedDate');
	}

	public function setFromTxnDate($date): bool
	{
		return $this->setDateType('TxnDateRangeFilter FromTxnDate', $date);
	}

	public function getFromTxnDate(): ?string
	{
		return $this->getDateType('TxnDateRangeFilter FromTxnDate');
	}

	public function setToTxnDate($date): bool
	{
		return $this->setDateType('TxnDateRangeFilter ToTxnDate', $date);
	}

	public function getToTxnDate(): ?string
	{
		return $this->getDateType('TxnDateRangeFilter ToTxnDate');
	}

	public function setDateMacro($date): bool
	{
		return $this->set('TxnDateRangeFilter DateMacro', $date);
	}

	public function getDateMacro(): ?string
	{
		return $this->get('TxnDateRangeFilter DateMacro');
	}

	public function setEntityFilterListID(string $ID): bool
	{
		return $this->set('EntityFilter ListID', $ID);
	}

	public function getEntityFilterListID(): ?string
	{
		return $this->set('EntityFilter ListID');
	}

	public function setAccountFilterListID(string $ID): bool
	{
		return $this->set('AccountFilter ListID', $ID);
	}

	public function getAccountFilterListID(): ?string
	{
		return $this->get('AccountFilter ListID');
	}

	public function setAccountFilterFullName(string $fullname): bool
	{
		return $this->set('AccountFilter FullName', $fullname);
	}

	public function getAccountFilterFullName(): ?string
	{
		return $this->get('AccountFilter FullName');
	}

	/**
	 * Set the credit card information for this refund
	 *
	 * @param string $cardno		The credit card number
	 * @param integer $expmonth		The expiration month (1 is January, 2 is February, etc.)
	 * @param integer $expyear		The expiration year
	 * @param string $name			The name on the credit card
	 * @param string $address		The billing address for the credit card
	 * @param string $postalcode	The postal code for the credit card
	 * @return boolean
	 */
	public function setCreditCardInfo(string $cardno, int $expmonth, int $expyear, string $name, string $address, string $postalcode): bool
	{
		// should probably do better checking here for failed sets.
		$b = $this->set('CreditCardInfo CreditCardNumber', $cardno);
		$b = $this->set('CreditCardInfo ExpirationMonth', $expmonth);
		$b = $this->set('CreditCardInfo ExpirationYear', $expyear);
		$b = $this->set('CreditCardInfo NameOnCard', $name);
		$b = $this->set('CreditCardInfo CreditCardAddress', $address);
		$b = $this->set('CreditCardInfo CreditCardPostalCode', $postalcode);

		return $b;
	}

	/**
	 * Get credit card information from the refund
	 *
	 * @param string $part		If you just want a specific part of the card info, specify it here
	 * @param array $defaults	Defaults for the card data if you want the entire array
	 * @return mixed			If you specify a part, a string part is returned, otherwise an array of card data
	 */
	public function getCreditCardInfo(string $part = null, array $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get('CreditCardInfo ' . $part);
		}

		return $this->getArray('CreditCardInfo *', $defaults);
	}

	/**
	 * Set the address for the refund (optional)
	 *
	 * @param string $addr1			Address line 1
	 * @param string $addr2			Address line 2
	 * @param string $addr3			Address line 3
	 * @param string $addr4			Address line 4
	 * @param string $addr5			Address line 5
	 * @param string $city			City
	 * @param string $state			State
	 * @param string $province		Province (Canadian editions of QuickBooks only!)
	 * @param string $postalcode	Postal code
	 * @param string $country		Country
	 * @param string $note			Notes
	 * @return void
	 */
	public function setAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $province = '', string $postalcode = '', string $country = '', string $note = ''): bool
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set('Address Addr' . $i, ${'addr' . $i});
		}

		$this->set('Address City', $city);
		$this->set('Address State', $state);
		$this->set('Address Province', $province);
		$this->set('Address PostalCode', $postalcode);
		$this->set('Address Country', $country);
		$this->set('Address Note', $note);

		return true;
	}

	/**
	 * Get the address
	 *
	 * @param string $part			A specific portion of the address to get (i.e. "Addr1" or "State")
	 * @param array $defaults		Default values if a value isn't filled in
	 * @return array				The address
	 */
	public function getAddress(?string $part = null, array $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get('Address ' . $part);
		}

		return $this->getArray('Address *', $defaults);
	}

	/**
	 * Set the transaction date
	 *
	 * @param string $date
	 * @return boolean
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Get the transaction date
	 */
	public function getTxnDate(string $format = 'Y-m-d'): ?string
	{
		//return date($format, strtotime($this->get('TxnDate')));
		return $this->getDateType('TxnDate', $format);
	}

	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_CREDITCARDREFUND'];
	}
}
