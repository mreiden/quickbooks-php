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
class RelatedUnit extends AbstractQbxmlObject
{
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	public function getName(): string
	{
		return $this->get('Name');
	}

	public function setAbbreviation(string $abbrev): bool
	{
		return $this->set('Abbreviation', $abbrev);
	}

	public function getAbbreviation(): string
	{
		return $this->get('Abbreviation');
	}

	public function setConversionRatio($ratio): bool
	{
		return $this->set('ConversionRatio', $ratio);
	}

	public function getConversionRatio()
	{
		return $this->get('ConversionRatio');
	}

	/**
	 * Tell the type of object this is
	 */
	public function object(): string
	{
		return 'RelatedUnit';
	}
}
