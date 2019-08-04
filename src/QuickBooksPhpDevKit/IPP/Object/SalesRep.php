<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class SalesRep extends BaseObject
{
	protected function _order(): array
	{
		return [
			'Id' => true,
			'MetaData' => true,
			'NameOf' => true,
			'Employee' => true,
			'Vendor' => true,
			'OtherName' => true,
			'Initials' => true,
		];
	}
}
