<?php declare(strict_types=1);

/**
 *
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\SalesReceipt;

use QuickBooksPhpDevKit\{
	PackageInfo,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\SalesReceipt,
};

/**
 *
 *
 */
class DiscountLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks SalesReceipt DiscountLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setAmount($amount): bool
	{
		$amount = (float) $amount;

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
			case PackageInfo::Actions['ADD_SALESRECEIPT']:
				$root = 'DiscountLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_SALESRECEIPT']:
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
