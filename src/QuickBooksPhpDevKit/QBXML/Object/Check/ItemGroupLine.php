<?php declare(strict_types=1);

/**
 * Check class for QuickBooks
 *
 * @author Keith Palmer Jr. <keith@ConsoliBYTE.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\Check;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\{
	AbstractQbxmlObject,
	Object\Check,
};

/**
 *
 */
class ItemGroupLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks_Object_Check_ItemGroupLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	// Path: ItemGroupRef ListID, datatype:

	/**
	 * Set the ItemGroupRef ListID for the Check
	 */
	public function setItemGroupListID(string $ListID): bool
	{
		return $this->set('ItemGroupRef ListID', $ListID);
	}

	/**
	 * Get the ItemGroupRef ListID for the Check
	 */
	public function getItemGroupListID(): ?string
	{
		return $this->get('ItemGroupRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setItemGroupApplicationID($value): bool
	{
		return $this->set('ItemGroupRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ITEMGROUP'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: ItemGroupRef FullName, datatype:

	/**
	 * Set the ItemGroupRef FullName for the Check
	 */
	public function setItemGroupName(string $FullName): bool
	{
		return $this->set('ItemGroupRef FullName', $FullName);
	}

	/**
	 * Get the ItemGroupRef FullName for the Check
	 */
	public function getItemGroupName(): ?string
	{
		return $this->get('ItemGroupRef FullName');
	}

	// Path: Desc, datatype:

	/**
	 * Set the Desc for the Check
	 */
	public function setDesc(string $value): bool
	{
		return $this->set('Desc', $value);
	}

	/**
	 * Get the Desc for the Check
	 */
	public function getDesc(): ?string
	{
		return $this->get('Desc');
	}

	/**
	 * @see self::setDesc()
	 */
	public function setDescription(string $value): bool
	{
		$this->setDesc($value);
	}

	/**
	 * @see self::getDesc()
	 */
	public function getDescription(): ?string
	{
		$this->getDesc();
	}
	// Path: Quantity, datatype:

	/**
	 * Set the Quantity for the Check
	 */
	public function setQuantity($value): bool
	{
		return $this->set('Quantity', (float) $value);
	}

	/**
	 * Get the Quantity for the Check
	 */
	public function getQuantity()
	{
		return $this->get('Quantity');
	}

	// Path: UnitOfMeasure, datatype:

	/**
	 * Set the UnitOfMeasure for the Check
	 */
	public function setUnitOfMeasure(string $value): bool
	{
		return $this->set('UnitOfMeasure', $value);
	}

	/**
	 * Get the UnitOfMeasure for the Check
	 */
	public function getUnitOfMeasure(): ?string
	{
		return $this->get('UnitOfMeasure');
	}

	public function object(): string
	{
		return 'ItemGroupLine';
	}
}
