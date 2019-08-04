<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Email extends BaseObject
{
	protected function _order(): array
	{
		return [
			'Id' => true,
			'Address' => true,
			'Default' => true,
			'Tag' => true,
		];
	}
}
