<?php declare(strict_types=1);

/**
 * QuickBooks Item Sites Query container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\Generic;

/**
 *
 */
class ItemSites extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\ItemSites object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}



	/**
	 * Adds a ListID to the ItemSitesQuery
	 */
	public function addListID(string $ListID): bool
	{
		$obj = new Generic();
		$obj->setParentIsNode(false);
		$obj->set('ListID', $ListID);

		return $this->addListItem('ListID', $obj);
	}

	public function getListID(int $i)
	{
		return $this->getListItem('ListID', $i);
	}

	public function listListIDs(): array
	{
		return $this->getList('InvoiceLine');
	}



	/**
	 * Set the ItemTypeFilter
	 */
	public function setItemTypeFilter(string $type): bool
	{
		$valid = [
			'allexceptfixedasset' => 'AllExceptFixedAsset',
			'assembly' => 'Assembly',
			'discount' => 'Discount',
			'fixedasset' => 'FixedAsset',
			'inventory' => 'Inventory',
			'inventoryandassembly' => 'InventoryAndAssembly',
			'noninventory' => 'NonInventory',
			'othercharge' => 'OtherCharge',
			'payment' => 'Payment',
			'sales' => 'Sales',
			'salestax' => 'SalesTax',
			'service' => 'Service',
		];

		$type = strtolower(trim($type));
		if (!isset($valid[$type]))
		{
			throw new \Exception('ItemTypeFilter is invalid. See valid options in ' . __METHOD__);
		}

		return $this->set('ItemTypeFilter', $valid[$type]);
	}

	/**
	 * Get the ListID of the Item
	 */
	public function getItemTypeFilter(): string
	{
		return $this->get('ItemTypeFilter');
	}

	/**
	 * Set the name of this Item
	 */
	public function setName(string $name): bool
	{
		return $this->setFullName($name);
	}

	/**
	 * Get the name of this customer
	 */
	public function getName(): string
	{
		return $this->getFullName();
	}

	/**
	 * Set the full name of this Item
	 */
	public function setFullName(string $name): bool
	{
		return $this->set('FullName', $name);
	}

	/**
	 * Get the full name of this Item
	 */
	public function getFullName(): string
	{
		return $this->get('FullName');
	}

	public function getFromModifiedDate(): void
	{

	}

	public function setFromModifiedDate($date): void
	{

	}

	public function getToModifiedDate(): void
	{

	}

	public function setToModifiedDate($date): void
	{

	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_ITEM'];
	}
}
