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

use QuickBooksPhpDevKit\{
	PackageInfo,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\Invoice,
};

/**
 *
 *
 */
class DiscountLine extends AbstractQbxmlObject
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
		$amount = floatval($amount);

		// Discount amounts are always negative in QuickBooks
		if ($amount > 0)
		{
			$amount = $amount * -1.0;
		}

		return $this->setAmountType('Amount', $amount);
	}

	public function setAccountListID(string $ListID): bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	public function setAccountName(string $name): bool
	{
		return $this->set('AccountRef FullName', $name);
	}

	public function asXML(?string $root = null, ?string $parent = null, ?array $object = null): Node
	{
		switch ($parent)
		{
			case PackageInfo::Actions['ADD_INVOICE']:
				$root = 'DiscountLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_INVOICE']:
				$root = 'DiscountLineMod';
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
		return 'DiscountLine';
	}
}
