<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Data extends BaseObject
{
	public function getRowCount(): int
	{
		return count($this->_data['DataRow']);
	}
}
