<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class DataRow extends BaseObject
{
	public function getColumnData(int $i)
	{
		return $this->get('ColData', $i);
	}
}
