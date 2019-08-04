<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Artush
 * Date: 18.01.2017
 * Time: 13:22
 */

namespace QuickBooksPhpDevKit\IPP\Service;

use QuickBooksPhpDevKit\IPP\Object\Deposit as ObjDeposit;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class Deposit extends Service
{
	public function add(Context $Context, string $realmID, ObjDeposit $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_DEPOSIT, $Object);
	}

	public function query(Context $Context, string $realmID, string $query)
	{
		return parent::_query($Context, $realmID, $query);
	}

	public function update(Context $Context, string $realmID, string $IDType, ObjDeposit $Object)
	{
		return parent::_update($Context, $realmID, IDS::RESOURCE_DEPOSIT, $Object, $IDType);
	}

	public function delete(Context $Context, string $realmID, string $IDType)
	{
		return parent::_delete($Context, $realmID, IDS::RESOURCE_DEPOSIT, $IDType);
	}

	public function void(Context $Context, string $realmID, string $IDType)
	{
		return parent::_void($Context, $realmID, IDS::RESOURCE_DEPOSIT, $IDType);
	}
}
