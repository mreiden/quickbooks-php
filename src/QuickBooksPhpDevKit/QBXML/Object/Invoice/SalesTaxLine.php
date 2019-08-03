<?php declare(strict_types=1);

/**
 *
 *
 * @license LICENSE.txt
 * @author Keith Palmer <keith@ConsoliBYTE.com>
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\Invoice;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\Invoice;

/**
 *
 *
 */
class SalesTaxLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks SalesReceipt SalesReceiptLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setAmount($amount): bool
	{
		return $this->setAmountType('Amount', $amount);
	}

	public function setRate($rate): bool
	{
		return $this->set('Rate', $rate);
	}

	public function setAccountListID(string $ListID): bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	public function setAccountName(string $name): bool
	{
		return $this->set('AccountRef FullName', $name);
	}

	public function asXML(string $root = null, string $parent = null, $object = null)
	{
		switch ($parent)
		{
			case PackageInfo::Actions['ADD_INVOICE']:
				$root = 'SalesTaxLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_INVOICE']:
				$root = 'SalesTaxLineMod';
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
		return 'SalesTaxLine';
	}
}
