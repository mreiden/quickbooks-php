<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class BillAddr extends BaseObject
{
	public function setState($state)
	{
		return $this->setCountrySubDivisionCode($state);
	}

	public function getState()
	{
		return $this->getCountrySubDivisionCode();
	}

	protected function _order(): array
	{
		return [
			'Id' => true,
			'Line1' => true,
			'Line2' => true,
			'Line3' => true,
			'Line4' => true,
			'Line5' => true,
			'City' => true,
			'Country' => true,
			'CountrySubDivisionCode' => true,
			'PostalCode' => true,
			'PostalCodeSuffix' => true,
			'Default' => true,
			'Tag' => true,
		];
	}
}
