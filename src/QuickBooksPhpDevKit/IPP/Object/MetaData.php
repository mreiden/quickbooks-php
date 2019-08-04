<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class MetaData extends BaseObject
{
	public function getLastUpdatedTime(string $format = 'Y-m-d H:i:s'): string
	{
		return $this->getDateType('LastUpdatedTime', $format);
	}

	protected function _order(): array
	{
		return [
			'CreatedBy' => true,
			'CreatedById' => true,
			'CreateTime' => true,
			'LastModifiedBy' => true,
			'LastModifiedById' => true,
			'LastUpdatedTime' => true,
		];
	}
}
