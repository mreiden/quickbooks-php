<?php declare(strict_types=1);

/**
 *
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\SalesReceipt;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\SalesReceipt;

/**
 *
 *
 */
class ShippingLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks SalesReceipt ShippingLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setAmount($amount): bool
	{
		return $this->setAmountType('Amount', $amount);
	}

	public function setAccountListID(string $ListID):bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	public function setAccountName(string $name): bool
	{
		return $this->set('AccountRef FullName', $name);
	}

	public function asXML(?string $root = null, ?string $parent = null, $object = null)
	{
		switch ($parent)
		{
			case PackageInfo::Actions['ADD_SALESRECEIPT']:
				$root = 'ShippingLineAdd';
				$parent = null;
				break;
			case PackageInfo::Actions['MOD_SALESRECEIPT']:
				$root = 'ShippingLineMod';
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
		return 'ShippingLine';
	}
}
