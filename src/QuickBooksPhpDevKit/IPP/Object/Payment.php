<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Payment extends BaseObject
{
	protected function _defaults(): array
	{
		return [
			//'TypeOf' => 'Person',
		];
	}

	protected function _order(): array
	{
		return [
			'Id' => true,
			'SyncToken' => true,
			'MetaData' => true,
			'CustomField' => true,
			'Header' => true,
			'Line' => true,
		];
	}
}
