<?php declare(strict_types=1);

/**
 * QuickBooks Item container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\{
	PackageInfo,
	QBXML\AbstractQbxmlObject,
};

/**
 *
 */
class Item extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\Item object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID of the Item
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of the Item
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
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
