<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class OpenBalance extends BaseObject
{
	protected function _order(): array
	{
		return [
			'CurrencyCode' => true,
			'Amount' => true,
		];
	}
}
