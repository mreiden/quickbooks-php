<?php declare(strict_types=1);
/**
 * JournalCreditLine class for QuickBooks
 *
 * @author Keith Palmer Jr. <keith@ConsoliBYTE.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\JournalEntry;

use QuickBooksPhpDevKit\{
	PackageInfo,
	QBXML\AbstractQbxmlObject,
};

/**
 *
 */
class JournalCreditLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\JournalEntry\JournalCreditLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	// Path: TxnLineID, datatype:

	/**
	 * Set the TxnLineID for the JournalCreditLine
	 */
	public function setTxnLineID(string $value): bool
	{
		return $this->set('TxnLineID', $value);
	}

	/**
	 * Get the TxnLineID for the JournalCreditLine
	 */
	public function getTxnLineID(): ?string
	{
		return $this->get('TxnLineID');
	}

	// Path: AccountRef ListID, datatype:

	/**
	 * Set the AccountRef ListID for the JournalCreditLine
	 */
	public function setAccountListID(string $ListID): bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	/**
	 * Get the AccountRef ListID for the JournalCreditLine
	 */
	public function getAccountListID(): ?string
	{
		return $this->get('AccountRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the JournalCreditLine
	 *
	 * @param mixed $value			The primary key within your own application
	 */
	public function setAccountApplicationID($value): bool
	{
		return $this->set('AccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: AccountRef FullName, datatype:

	/**
	 * Set the AccountRef FullName for the JournalCreditLine
	 */
	public function setAccountName(string $FullName): bool
	{
		return $this->set('AccountRef FullName', $FullName);
	}

	/**
	 * Get the AccountRef FullName for the JournalCreditLine
	 */
	public function getAccountName(): ?string
	{
		return $this->get('AccountRef FullName');
	}

	// Path: Amount, datatype:

	/**
	 * Set the Amount for the JournalCreditLine
	 */
	public function setAmount($value): bool
	{
		return $this->setAmountType('Amount', $value);
	}

	/**
	 * Get the Amount for the JournalCreditLine
	 *
	 * @return float
	 */
	public function getAmount()
	{
		return $this->getAmountType('Amount');
	}

	// Path: Memo, datatype: STRTYPE

	/**
	 * Set the Memo for the JournalCreditLine
	 */
	public function setMemo(string $value): bool
	{
		return $this->set('Memo', $value);
	}

	/**
	 * Get the Memo for the JournalCreditLine
	 */
	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	// Path: EntityRef ListID, datatype: STRTYPE

	/**
	 * Set the EntityRef ListID for the JournalCreditLine
	 */
	public function setEntityListID(string $ListID): bool
	{
		return $this->set('EntityRef ListID', $ListID);
	}

	/**
	 * Get the EntityRef ListID for the JournalCreditLine
	 */
	public function getEntityListID(): ?string
	{
		return $this->get('EntityRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the JournalCreditLine
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setEntityApplicationID($value): bool
	{
		return $this->set('EntityRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ENTITY'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: EntityRef FullName, datatype: STRTYPE

	/**
	 * Set the EntityRef FullName for the JournalCreditLine
	 */
	public function setEntityName(string $FullName): bool
	{
		return $this->set('EntityRef FullName', $FullName);
	}

	/**
	 * Get the EntityRef FullName for the JournalCreditLine
	 */
	public function getEntityName(): ?string
	{
		return $this->get('EntityRef FullName');
	}

	// Path: ClassRef ListID, datatype: STRTYPE

	/**
	 * Set the ClassRef ListID for the JournalCreditLine
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	/**
	 * Get the ClassRef ListID for the JournalCreditLine
	 */
	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the JournalCreditLine
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setClassApplicationID($value): bool
	{
		return $this->set('ClassRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CLASS'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: ClassRef FullName, datatype: STRTYPE

	/**
	 * Set the ClassRef FullName for the JournalCreditLine
	 */
	public function setClassName(string $FullName): bool
	{
		return $this->set('ClassRef FullName', $FullName);
	}

	/**
	 * Get the ClassRef FullName for the JournalCreditLine
	 */
	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	// Path: ItemSalesTaxRef ListID, datatype: STRTYPE

	/**
	 * Set the ItemSalesTaxRef ListID for the JournalCreditLine
	 */
	public function setItemSalesTaxListID(string $ListID): bool
	{
		return $this->set('ItemSalesTaxRef ListID', $ListID);
	}

	/**
	 * Get the ItemSalesTaxRef ListID for the JournalCreditLine
	 */
	public function getItemSalesTaxListID(): ?string
	{
		return $this->get('ItemSalesTaxRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the JournalCreditLine
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setItemSalesTaxApplicationID($value): bool
	{
		return $this->set('ItemSalesTaxRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ITEMSALESTAX'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: ItemSalesTaxRef FullName, datatype: STRTYPE

	/**
	 * Set the ItemSalesTaxRef FullName for the JournalCreditLine
	 */
	public function setItemSalesTaxName(string $FullName): bool
	{
		return $this->set('ItemSalesTaxRef FullName', $FullName);
	}

	/**
	 * Get the ItemSalesTaxRef FullName for the JournalCreditLine
	 */
	public function getItemSalesTaxName(): ?string
	{
		return $this->get('ItemSalesTaxRef FullName');
	}

	// Path: BillableStatus, datatype:

	/**
	 * Set the BillableStatus for the JournalCreditLine
	 */
	public function setBillableStatus(string $value): bool
	{
		return $this->set('BillableStatus', $value);
	}

	/**
	 * Get the BillableStatus for the JournalCreditLine
	 */
	public function getBillableStatus(): ?string
	{
		return $this->get('BillableStatus');
	}

	/**
	 * Tell the type of object this is
	 */
	public function object(): string
	{
		return 'JournalCreditLine';
	}
}
