<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;
use QuickBooksPhpDevKit\IPP\IDS;

class LinkedTxn extends BaseObject
{
	public function setTxnId($Id)
	{
		return $this->set('TxnId', IDS::usableIDType($Id));
	}
}
