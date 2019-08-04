<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Report extends BaseObject
{
	protected $_report_name;

	public function __construct(string $name)
	{
		$this->_report_name = $name;
		parent::__construct();
	}

	public function getRowCount(): int
	{
		$Data = $this->_data['Data'][0];
		return $Data->getRowCount();
	}

	public function getColumnCount(): int
	{
		return count($this->_data['ColDesc']);
	}
}
