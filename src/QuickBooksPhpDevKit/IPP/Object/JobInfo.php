<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class JobInfo extends BaseObject
{
	protected function _order(): array
	{
		return [
			'Status' => true,
			'StartDate' => true,
			'ProjectedEndDate' => true,
			'EndDate' => true,
			'Description' => true,
			'JobTypeId' => true,
			'JobTypeName' => true,
		];
	}
}
