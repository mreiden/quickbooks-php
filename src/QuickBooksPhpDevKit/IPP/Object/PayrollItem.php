<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class PayrollItem extends BaseObject
{
	protected function _order(): array
	{
		return [
			'Id' => true,
			'MetaData' => true,
			'Unique Identifier' => true,
			'Name' => true,
			'Active' => true,
			'Type' => true,
		];
	}
}
