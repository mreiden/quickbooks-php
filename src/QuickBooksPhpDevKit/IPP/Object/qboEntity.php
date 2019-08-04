<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class qboEntity extends BaseObject
{
	protected function _order(): array
	{
		return [
			'qboId' => true,
			'qboEntityType' => true,
			'qboLastUpdatedTime' => true,
		];
	}
}
