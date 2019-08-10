<?php declare(strict_types=1);

/**
 * QuickBooks ServiceItem object container
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
use QuickBooksPhpDevKit\QBXML\Object\SalesTaxGroupItem\ItemSalesTaxRef;

/**
 *
 */
class SalesTaxGroupItem extends AbstractQbxmlObject
{
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);

		// These two things occur because it's a repeatable element who name doesn't do the *Add, *Mod, *Ret thing, trash these
		if (isset($this->_object['ItemSalesTaxRef FullName']))
		{
			unset($this->_object['ItemSalesTaxRef FullName']);
		}

		if (isset($this->_object['ItemSalesTaxRef ListID']))
		{
			unset($this->_object['ItemSalesTaxRef ListID']);
		}
	}

	/**
	 * Set the ListID for this item
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID for this item
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name for this item
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive', true);
	}

	public function setIsActive(?bool $IsActive): bool
	{
		return $this->setBooleanType('IsActive', $IsActive);
	}

	/**
	 * Get the name for this item
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	public function setItemDesc(string $desc): bool
	{
		return $this->set('ItemDesc', $desc);
	}

	public function getItemDesc(): string
	{
		return $this->get('ItemDesc');
	}

	public function setDescription(string $desc): bool
	{
		return $this->set('ItemDesc', $desc);
	}

	public function getDescription(): string
	{
		return $this->get('ItemDesc');
	}

	public function addItemSalesTaxRef(ItemSalesTaxRef $obj): bool
	{
		return $this->addListItem('ItemSalesTaxRef', $obj);
	}

	public function getItemSalesTaxRef(int $i): ItemSalesTaxRef
	{
		return $this->getListItem('ItemSalesTaxRef', $i);
	}

	public function listItemSalesTaxRefs(): array
	{
		return $this->getList('ItemSalesTaxRef');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_SALESTAXGROUPITEM'];
	}
}
