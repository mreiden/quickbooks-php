<?php declare(strict_types=1);

/**
 * Check class for QuickBooks
 *
 * @author Keith Palmer Jr. <keith@ConsoliBYTE.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\Check;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Check;
use QuickBooksPhpDevKit\XML\Node;

/**
 *
 */
class ApplyCheckToTxn extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\Check\ApplyCheckToTxnAdd object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	// Path: TxnID, datatype:

	/**
	 * Set the TxnID for the Check
	 */
	public function setTxnID(string $value): bool
	{
		return $this->set('TxnID', $value);
	}

	/**
	 * Get the TxnID for the Check
	 */
	public function getTxnID(): ?string
	{
		return $this->get('TxnID');
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
	public function getAmount()
	{
		return $this->getAmountType('Amount');
	}

	public function object(): string
	{
		return 'ApplyCheckToTxn';
	}
}
