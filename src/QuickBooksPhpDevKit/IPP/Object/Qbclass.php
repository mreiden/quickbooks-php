<?php
 declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Qbclass extends BaseObject
{
	protected function _order(): array
	{
		return [
			'Id' => true,
			'MetaData' => true,
			'Name' => true,
			'ClassParentId' => true,
			'ClassParentName' => true,
			'Active' => true,
		];
	}
}
