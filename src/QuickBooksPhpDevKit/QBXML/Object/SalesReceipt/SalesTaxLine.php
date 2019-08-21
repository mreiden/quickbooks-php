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
class SalesTaxLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks SalesReceipt SalesTaxLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setAmount($amount): bool
	{
		return $this->setAmountType('Amount', $amount);
	}

	public function setRate(float $rate): bool
	{
		return $this->setAmountType('Rate', $rate);
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
				$root = 'SalesTaxLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_SALESRECEIPT']:
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
