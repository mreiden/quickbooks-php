<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Phone extends BaseObject
{
	public const DEVICETYPE_LANDLINE = 'LandLine';
	public const DEVICETYPE_MOBILE = 'Mobile';
	public const DEVICETYPE_FAX = 'Fax';

	protected function _order(): array
	{
		return [
			'Id' => true,
			'DeviceType' => true,
			'CountryCode' => true,
			'AreaCode' => true,
			'ExchangeCode' => true,
			'Extension' => true,
			'FreeFormNumber' => true,
			'PIN' => true,
			'Default' => true,
			'Tag' => true,
		];
	}
}
