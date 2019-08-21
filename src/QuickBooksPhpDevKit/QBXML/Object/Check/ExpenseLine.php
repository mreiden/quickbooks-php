<?php declare(strict_types=1);

/**
 * Check ExpenseLine class for QuickBooks
 *
 * @author Keith Palmer Jr. <keith@ConsoliBYTE.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\Check;

use QuickBooksPhpDevKit\{
	PackageInfo,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\Check,
};

/**
 *
 */
class ExpenseLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\Check\ExpenseLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	// Path: AccountRef ListID, datatype:

	/**
	 * Set the AccountRef ListID for the Check
	 */
	public function setAccountListID(string $ListID): bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	/**
	 * Get the AccountRef ListID for the Check
	 */
	public function getAccountListID(): ?string
	{
		return $this->get('AccountRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 */
	public function setAccountApplicationID($value): bool
	{
		return $this->set('AccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: AccountRef FullName, datatype:

	/**
	 * Set the AccountRef FullName for the Check
	 */
	public function setAccountName(string $FullName): bool
	{
		return $this->set('AccountRef FullName', $FullName);
	}

	/**
	 * Get the AccountRef FullName for the Check
	 */
	public function getAccountName(): ?string
	{
		return $this->get('AccountRef FullName');
	}

	// Path: Amount, datatype:

	/**
	 * Set the Amount for the Check
	 */
	public function setAmount($value): bool
	{
		return $this->setAmountType('Amount', $value);
	}

	/**
	 * Get the Amount for the Check
	 */
	public function getAmount(): ?string
	{
		return $this->getAmountType('Amount');
	}

	// Path: TaxAmount, datatype:

	/**
	 * Set the TaxAmount for the Check
	 */
	public function setTaxAmount($value): bool
	{
		return $this->setAmountType('TaxAmount', $value);
	}

	/**
	 * Get the TaxAmount for the Check
	 */
	public function getTaxAmount(): ?string
	{
		return $this->getAmountType('TaxAmount');
	}

	// Path: Memo, datatype: STRTYPE

	/**
	 * Set the Memo for the Check
	 */
	public function setMemo(string $value): bool
	{
		return $this->set('Memo', $value);
	}

	/**
	 * Get the Memo for the Check
	 */
	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	// Path: CustomerRef ListID, datatype:

	/**
	 * Set the CustomerRef ListID for the Check
	 */
	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID', $ListID);
	}

	/**
	 * Get the CustomerRef ListID for the Check
	 */
	public function getCustomerListID(): ?string
	{
		return $this->get('CustomerRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setCustomerApplicationID($value): bool
	{
		return $this->set('CustomerRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CUSTOMER'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: CustomerRef FullName, datatype:

	/**
	 * Set the CustomerRef FullName for the Check
	 */
	public function setCustomerFullName(string $FullName): bool
	{
		return $this->set('CustomerRef FullName', $FullName);
	}

	/**
	 * Get the CustomerRef FullName for the Check
	 */
	public function getCustomerFullName(): ?string
	{
		return $this->get('CustomerRef FullName');
	}

	// Path: ClassRef ListID, datatype:

	/**
	 * Set the ClassRef ListID for the Check
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	/**
	 * Get the ClassRef ListID for the Check
	 *
	 * @return string
	 */
	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setClassApplicationID($value): bool
	{
		return $this->set('ClassRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CLASS'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: ClassRef FullName, datatype:

	/**
	 * Set the ClassRef FullName for the Check
	 */
	public function setClassName(string $FullName): bool
	{
		return $this->set('ClassRef FullName', $FullName);
	}

	/**
	 * Get the ClassRef FullName for the Check
	 */
	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	// Path: SalesTaxCodeRef ListID, datatype:

	/**
	 * Set the SalesTaxCodeRef ListID for the Check
	 */
	public function setSalesTaxCodeListID(string $ListID): bool
	{
		return $this->set('SalesTaxCodeRef ListID', $ListID);
	}

	/**
	 * Get the SalesTaxCodeRef ListID for the Check
	 */
	public function getSalesTaxCodeListID(): ?string
	{
		return $this->get('SalesTaxCodeRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 */
	public function setSalesTaxCodeApplicationID($value): bool
	{
		return $this->set('SalesTaxCodeRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_SALESTAXCODE'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: SalesTaxCodeRef FullName, datatype:

	/**
	 * Set the SalesTaxCodeRef FullName for the Check
	 */
	public function setSalesTaxCodeName(string $FullName): bool
	{
		return $this->set('SalesTaxCodeRef FullName', $FullName);
	}

	/**
	 * Get the SalesTaxCodeRef FullName for the Check
	 */
	public function getSalesTaxCodeName(): ?string
	{
		return $this->get('SalesTaxCodeRef FullName');
	}

	// Path: BillableStatus, datatype:

	/**
	 * Set the BillableStatus for the Check
	 */
	public function setBillableStatus(string $value): bool
	{
		return $this->set('BillableStatus', $value);
	}

	/**
	 * Get the BillableStatus for the Check
	 */
	public function getBillableStatus(): ?string
	{
		return $this->get('BillableStatus');
	}

	public function object(): string
	{
		return 'ExpenseLine';
	}
}
