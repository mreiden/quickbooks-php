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
use QuickBooksPhpDevKit\XML\Node;

/**
 *
 *
 */
class ShippingLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks Invoice ShippingLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setAmount($amount): bool
	{
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
				$root = 'ShippingLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_INVOICE']:
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
