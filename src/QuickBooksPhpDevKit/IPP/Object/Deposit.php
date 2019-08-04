<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Artush
 * Date: 18.01.2017
 * Time: 13:19
 */

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Deposit extends BaseObject
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
			'TxnDate' => true,
			'CurrencyRef' => true,
			'CurrencyRef_name' => true,
			'PrivateNote' => true,
			'DepositToAccountRef' => true,
			'DepositToAccountRef_name' => true,
			'GlobalTaxCalculation' => true,
			'TotalAmt' => true,
		];
	}
}
