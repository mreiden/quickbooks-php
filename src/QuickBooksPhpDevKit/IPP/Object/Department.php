<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Department extends BaseObject
{
	protected function _order(): array
	{
		return [
			'Id' => true,
			'MetaData' => true,
			'Name' => true,
			'DepartmentParentId' => true,
			'DepartmentParentName' => true,
			'Active' => true,
		];
	}
}
