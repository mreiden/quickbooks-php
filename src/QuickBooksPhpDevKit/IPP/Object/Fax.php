<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Fax extends BaseObject
{
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
