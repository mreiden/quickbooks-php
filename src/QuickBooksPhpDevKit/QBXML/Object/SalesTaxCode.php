<?php declare(strict_types=1);

/**
 * QuickBooks SalesTaxCode object container
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
class SalesTaxCode extends AbstractQbxmlObject
{
	/**
	 * Create a new SalesTaxCode object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID of the SalesTaxCode
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID of the SalesTaxCode
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name of the SalesTaxCode
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name of the SalesTaxCode
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	/**
	 * Set this SalesTaxCode active or not
	 */
	public function setIsActive(bool $value)
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Tell whether or not this SalesTaxCode object is active
	 */
	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive');
	}

	public function setIsTaxable(bool $boolean)
	{
		return $this->setBooleanType('IsTaxable', $boolean);
	}

	public function getIsTaxable(): bool
	{
		return $this->getBooleanType('IsTaxable', true);
	}

	public function setDescription(string $Desc): bool
	{
		return $this->set('Desc', $Desc);
	}

	public function getDescription(): string
	{
		return $this->get('Desc');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_SALESTAXCODE'];
	}
}
