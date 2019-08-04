<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Vendor extends BaseObject
{
	protected function _defaults(): array
	{
		return [
			'TypeOf' => 'Person',
		];
	}
}
