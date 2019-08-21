<?php declare(strict_types=1);

/**
 * QuickBooks ItemGroupLine object container
 *
 * @todo Documentation
 *
 * @author Harley Laue <harley.laue@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\ItemReceipt;

use QuickBooksPhpDevKit\{
	PackageInfo,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\ItemReceipt,
};

/**
 * Quickbooks ItemReceipt ItemGroupLine definition
 */
class ItemGroupLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks ReceiptItem ItemGroupLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function getItemGroupListID(): ?string
	{
		return $this->get('ItemGroupRef ListID');
	}

	public function setItemGroupListID(string $ListID): bool
	{
		return $this->set('ItemGroupRef ListID', $ListID);
	}

	public function getItemGroupName(): ?string
	{
		return $this->get('ItemGroupRef FullName');
	}

	public function setItemGroupName(string $Name): bool
	{
		return $this->set('ItemGroupRef FullName', $Name);
	}

	public function getQuantity()
	{
		return $this->get('Quantity');
	}

	public function setQuantity($Quantity)
	{
		return $this->set('Quantity', (float) $Quantity);
	}

	public function getUnitOfMeasure(): ?string
	{
		return $this->get('UnitOfMeasure');
	}

	public function setUnitOfMeasure(string $UnitOfMeasure): bool
	{
		return $this->set('UnitOfMeasure', $UnitOfMeasure);
	}

	public function asXML(string $root = null, string $parent = null, ?array $object = null): Node
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_ITEMRECEIPT']:
				$root = 'ItemGroupLineAdd';
				$parent = null;
				break;
// Currently unimplemented
/*
			case PackageInfo::Actions['MOD_ITEMRECEIPT']:
				$root = 'ItemGroupLineMod';
				break;
*/
		}

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return 'ItemGroupLine';
	}
}
