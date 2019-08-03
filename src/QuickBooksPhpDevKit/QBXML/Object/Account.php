<?php declare(strict_types=1);

/**
 * QuickBooks Account object container
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

/**
 *
 */
class Account extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks_Object_Account object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}


	/**
	 * Set the ListID of the Class
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of the Class
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}


	/**
	 * Set the full name of the account.
	 * Query uses FullName while Add and Mod use Name and ParentRef FullName.
	 */
	public function setFullName(string $name): bool
	{
		return $this->setFullNameType('FullName', 'Name', 'ParentRef FullName', $name);
	}
	/**
	 *
	 */
	public function getFullName(): ?string
	{
		return $this->getFullNameType('FullName', 'Name', 'ParentRef FullName');
	}

	/**
	 * Set the name of the Account
	 */
	public function setName(string $name): bool
	{
		return $this->setFullName($name);
	}
	/**
	 * Get the name of the Account
	 */
	public function getName(): ?string
	{
		return $this->get('Name');
	}




	/**
	 *
	 */
	public function setParentListID(string $ListID): bool
	{
		return $this->set('ParentRef ListID', $ListID);
	}

	public function getParentListID(): ?string
	{
		return $this->get('ParentRef ListID');
	}

	public function setParentFullName(?string $name): bool
	{
		return $this->set('ParentRef FullName', $name);
	}
	public function getParentFullName(): ?string
	{
		return $this->get('ParentRef FullName');
	}

	public function setParentName(?string $name): bool
	{
		return $this->setParentFullName($name);
	}
	public function getParentName(): ?string
	{
		return $this->getParentFullName();
	}


	public function setParentApplicationID($value): bool
	{
		return $this->set('ParentRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getParentApplicationID()
	{
		return $this->get('ParentRef ' . PackageInfo::$API_APPLICATIONID);
	}


	/**
	 * Set this Class active or not
	 */
	public function setIsActive(bool $value): bool
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Tell whether or not this class object is active
	 */
	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive');
	}

	public function setAccountType(string $type): bool
	{
		$valid = [
			'accountspayable' => 'AccountsPayable',
			'accountsreceivable' => 'AccountsReceivable',
			'bank' => 'Bank',
			'costofgoodssold' => 'CostOfGoodsSold',
			'creditcard' => 'CreditCard',
			'equity' => 'Equity',
			'expense' => 'Expense',
			'fixedasset' => 'FixedAsset',
			'income' => 'Income',
			'longtermliability' => 'LongTermLiability',
			'nonposting' => 'NonPosting',
			'otherasset' => 'OtherAsset',
			'othercurrentasset' => 'OtherCurrentAsset',
			'othercurrentliability' => 'OtherCurrentLiability',
			'otherexpense' => 'OtherExpense',
			'otherincome' => 'OtherIncome',
		];

		$type = strtolower(trim($type));
		if (!isset($valid[$type]))
		{
			throw new \Exception('Account Type "'. $type . '"" is invalid.  See valid options in ' . __METHOD__);
		}

		return $this->set('AccountType', $valid[$type]);
	}

	public function getAccountType(): string
	{
		return $this->get('AccountType');
	}

	/**
	 * Set the QuickBooks Account Number (Not a bank account number)
	 */
	public function setAccountNumber($number): bool
	{
		return $this->set('AccountNumber', $number);
	}

	/**
	 * Get the QuickBooks Account Number (Not a bank account number)
	 */
	public function getAccountNumber(): string
	{
		return $this->get('AccountNumber');
	}

	public function setBankNumber($number): bool
	{
		return $this->set('BankNumber', $number);
	}

	public function getBankNumber(): string
	{
		return $this->get('BankNumber');
	}

	public function setDescription(string $descrip): bool
	{
		return $this->set('Desc', $descrip);
	}

	public function getDescription(): string
	{
		return $this->get('Desc');
	}

	public function setOpenBalance($balance): bool
	{
		return $this->setAmountType('OpenBalance', (float) $balance);
	}

	public function getOpenBalance()
	{
		return $this->getAmountType('OpenBalance');
	}

	public function setOpenBalanceDate(string $date): bool
	{
		return $this->setDateType('OpenBalanceDate', $date);
	}

	public function getOpenBalanceDate(): string
	{
		return $this->getDateType('OpenBalanceDate');
	}

	public function setTaxLineID(string $value): bool
	{
		return $this->set('TxLineID', $value);
	}

	public function getTaxLineID(): string
	{
		return $this->get('TxLineID');
	}

/*
	// These are in the response object, not the request object.
	public function getBalance()
	{
		return $this->getAmountType('Balance');
	}

	public function setBalance($value): bool
	{
		return $this->setAmountType('Balance', $value);
	}

	public function getTotalBalance()
	{
		return $this->getAmountType('TotalBalance');
	}

	public function setTotalBalance($value): bool
	{
		return $this->setAmountType('TotalBalance', $value);
	}

	public function getSpecialAccountType(): string
	{
		return $this->get('SpecialAccountType');
	}

	public function setSpecialAccountType(string $type): bool
	{
		return $this->set('SpecialAccountType', $type);
	}

	public function getCashFlowClassification(): string
	{
		return $this->get('CashFlowClassification');
	}

	public function setCashFlowClassification(string $type): bool
	{
		return $this->set('CashFlowClassification', $type);
	}
*/
	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_ACCOUNT'];
	}
}
