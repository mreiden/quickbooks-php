<?php declare(strict_types=1);

/**
 * QuickBooks InventoryAdjustment object container
 *
 * @todo Verify the get/set methods on this one... it was copied from InventoryItem
 *
 * @author Harley Laue <harley.laue@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\InventoryAdjustment;

use QuickBooksPhpDevKit\{
	PackageInfo,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\InventoryAdjustment,
};

/**
 * Quickbooks InventoryAdjustmentLine definition
 */
class InventoryAdjustmentLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks InventoryAdjustment object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function getLineItemListID(): ?string
	{
		return $this->get('ItemRef ListID');
	}

	public function setLineItemListID(string $ListID): bool
	{
		return $this->set('ItemRef ListID', $ListID);
	}

	public function getLineItemName(): ?string
	{
		return $this->get('ItemRef FullName');
	}

	public function setLineItemName(string $name): bool
	{
		return $this->set('ItemRef FullName', $name);
	}

	public function getLineQuantityNew()
	{
		return $this->get('QuantityAdjustment NewQuantity');
	}

	public function setLineQuantityNew($value): bool
	{
		return $this->set('QuantityAdjustment NewQuantity', $value);
	}

	public function getLineQuantityDifference()
	{
		return $this->get('QuantityAdjustment QuantityDifference');
	}

	public function setLineQuantityDifference($value): bool
	{
		return $this->set('QuantityAdjustment QuantityDifference', $value);
	}

	public function getLineValueQuantity()
	{
		return $this->get('ValueAdjustment NewQuantity');
	}

	public function setLineValueQuantity($value): bool
	{
		return $this->set('ValueAdjustment NewQuantity', $value);
	}

	public function getLineValueNew()
	{
		return $this->get('ValueAdjustment NewValue');
	}

	public function setLineValueNew($value): bool
	{
		return $this->set('ValueAdjustment NewValue', $value);
	}

	public function asXML(?string $root = null, ?string $parent = null, $object = null): Node
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_INVENTORYADJUSTMENT']:
				$root = 'InventoryAdjustmentLineAdd';
				$parent = null;
				break;
// Currently unimplemented
/*
			case PackageInfo::Actions['QUERY_INVENTORYADJUSTMENT']:
				$root = 'InventoryAdjustmentLineQuery';
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
		return 'InventoryAdjustmentLine';
	}
}
