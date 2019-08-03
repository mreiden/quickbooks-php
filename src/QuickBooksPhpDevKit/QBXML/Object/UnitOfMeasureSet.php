<?php declare(strict_types=1);

/**
 * QuickBooks Unit of Measure Set object container
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
use QuickBooksPhpDevKit\QBXML\Object\UnitOfMeasureSet\DefaultUnit;
use QuickBooksPhpDevKit\QBXML\Object\UnitOfMeasureSet\RelatedUnit;

/**
 *
 */
class UnitOfMeasureSet extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks_Object_Class object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);

		// These things occur because it's a repeatable element who name doesn't do the *Add, *Mod, *Ret thing, trash these
		$unsets = [
			'RelatedUnit Name',
			'RelatedUnit Abbreviation',
			'RelatedUnit ConversionRatio',
			'DefaultUnit UnitUsedFor',
			'DefaultUnit Unit',
		];

		foreach ($unsets as $unset)
		{
			if (isset($this->_object[$unset]))
			{
				unset($this->_object[$unset]);
			}
		}
	}

	/**
	 * Set the ListID of the Class
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of the Class
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name of the class
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name of the class
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	/**
	 * Set this Class active or not
	 */
	public function setIsActive(bool $value): bool
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Tell whether or not this class object is active
	 */
	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive');
	}

	/**
	 *
	 *
	 *
	 */
	public function setUnitOfMeasureType(string $type): bool
	{
		return $this->set('UnitOfMeasureType', $type);
	}

	public function getUnitOfMeasureType(): string
	{
		return $this->get('UnitOfMeasureType');
	}

	public function setBaseUnitName(string $name): bool
	{
		return $this->set('BaseUnit Name', $name);
	}

	public function getBaseUnitName(): string
	{
		return $this->get('BaseUnit Name');
	}

	public function setBaseUnitAbbreviation(string $abbr): bool
	{
		return $this->set('BaseUnit Abbreviation', $abbr);
	}

	public function getBaseUnitAbbreviation(): string
	{
		return $this->get('BaseUnit Abbreviation');
	}


	public function addRelatedUnit(RelatedUnit $obj): bool
	{
		return $this->addListItem('RelatedUnit', $obj);
	}

	public function getRelatedUnit(int $i)
	{
		return $this->getListItem('RelatedUnit', $i);
	}

	public function listRelatedUnits(): array
	{
		return $this->getList('RelatedUnit');
	}


	public function addDefaultUnit(DefaultUnit $obj): bool
	{
		return $this->addListItem('DefaultUnit', $obj);
	}

	public function getDefaultUnit(int $i): DefaultUnit
	{
		return $this->getListItem('DefaultUnit', $i);
	}

	public function listDefaultUnits(): array
	{
		return $this->getList('DefaultUnit');
	}


	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_UNITOFMEASURESET'];
	}
}
