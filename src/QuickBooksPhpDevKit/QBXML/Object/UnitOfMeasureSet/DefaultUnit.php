<?php declare(strict_types=1);

/**
 *
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\UnitOfMeasureSet;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\UnitOfMeasureSet;

/**
 *
 *
 */
class DefaultUnit extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks UnitOfMeasureSet DefaultUnit object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setUnitUsedFor(string $str): bool
	{
		return $this->setUnitUsedFor('UnitUsedFor', $str);
	}

	public function getUnitUsedFor(): string
	{
		return $this->get('UnitUsedFor');
	}

	public function setUnit(string $unit): bool
	{
		return $this->set('Unit', $unit);
	}

	public function getUnit(): string
	{
		return $this->get('Unit');
	}

	/**
	 * Tell the type of object this is
	 */
	public function object(): string
	{
		return 'DefaultUnit';
	}
}
