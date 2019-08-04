<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class BillEmail extends BaseObject
{
	protected function _order(): array
	{
		return [
			'Address' => true,
		];
	}
}
