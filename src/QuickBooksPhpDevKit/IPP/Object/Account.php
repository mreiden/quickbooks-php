<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Account extends BaseObject
{
	protected function _order(): array
	{
		return [
			'Id' => true,
			'MetaData' => true,
			'Name' => true,
			'AccountParentId' => true,
			'AccountParentName' => true,
			'Desc' => true,
			'Active' => true,
			'Type' => true,
			'Subtype' => true,
			'AcctNum' => true,
			'BankNum' => true,
			'OpeningBalance' => true,
			'OpeningBalanceDate' => true,
			'CurrentBalance' => true,
			'CurrentBalanceWithSubAccounts' => true,
		];
	}
}
