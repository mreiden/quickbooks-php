<?php declare(strict_types=1);
/**
 * JournalEntry class for QuickBooks
 *
 * @author Keith Palmer Jr. <keith@ConsoliBYTE.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\JournalEntry\JournalCreditLine;
use QuickBooksPhpDevKit\QBXML\Object\JournalEntry\JournalDebitLine;

/**
 *
 */
class JournalEntry extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\JournalEntry object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	// Path: TxnDate, datatype: DATETYPE

	/**
	 * Set the TxnDate for the JournalEntry
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Get the TxnDate for the JournalEntry
	 */
	public function getTxnDate(?string $format = null): string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * @see QBXML\Object\JournalEntry::setTxnDate()
	 */
	public function setTransactionDate($date): bool
	{
		return $this->setTxnDate($date);
	}

	/**
	 * @see QBXML\Object\JournalEntry::getTxnDate()
	 */
	public function getTransactionDate(?string $format = null): string
	{
		$this->getTxnDate($format = null);
	}
	// Path: RefNumber, datatype: STRTYPE

	/**
	 * Set the RefNumber for the JournalEntry
	 */
	public function setRefNumber(string $value): bool
	{
		return $this->set('RefNumber', $value);
	}

	/**
	 * Get the RefNumber for the JournalEntry
	 */
	public function getRefNumber(): string
	{
		return $this->get('RefNumber');
	}

	// Path: Memo, datatype: STRTYPE

	/**
	 * Set the Memo for the JournalEntry
	 */
	public function setMemo(string $value): bool
	{
		return $this->set('Memo', $value);
	}

	/**
	 * Get the Memo for the JournalEntry
	 */
	public function getMemo(): string
	{
		return $this->get('Memo');
	}

	// Path: IsAdjustment, datatype: BOOLTYPE

	/**
	 * Set the IsAdjustment for the JournalEntry
	 */
	public function setIsAdjustment(bool $bool): bool
	{
		return $this->setBooleanType('IsAdjustment', $bool);
	}

	/**
	 * Get the IsAdjustment for the JournalEntry
	 */
	public function getIsAdjustment(): bool
	{
		return $this->getBooleanType('IsAdjustment');
	}

	public function addDebitLine(JournalDebitLine $obj): bool
	{
		return $this->addListItem('JournalDebitLine', $obj);
	}

	/**
	 * @see QBXML\Object\JournalEntry::addDebitLine()
	 */
	public function addJournalDebitLine(JournalDebitLine $obj): bool
	{
		return $this->addDebitLine($obj);
	}

	public function addCreditLine(JournalCreditLine $obj): bool
	{
		return $this->addListItem('JournalCreditLine', $obj);
	}

	/**
	 * @see QBXML\Object\JournalEntry::addCreditLine()
	 */
	public function addJournalCreditLine(JournalCreditLine $obj): bool
	{
		return $this->addCreditLine($obj);
	}

	/**
	 * Tell the type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_JOURNALENTRY'];
	}
}
