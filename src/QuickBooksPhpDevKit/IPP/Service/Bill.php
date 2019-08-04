<?php declare(strict_types=1);

/**
 *
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @license LICENSE.txt
 * @author Keith Palmer <Keith@ConsoliBYTE.com>
 *
 * @package QuickBooks
 * @subpackage IPP
 */

namespace QuickBooksPhpDevKit\IPP\Service;

use QuickBooksPhpDevKit\IPP\Object\Bill as ObjBill;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class Bill extends Service
{
	public function findAll(Context $Context, string $realmID): ?array
	{
		$xml = null;
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_BILL, $xml);
	}

	public function add(Context $Context, string $realmID, ObjBill $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_BILL, $Object);
	}

	public function delete(Context $Context, string $realmID, string $IDType)
	{
		return parent::_delete($Context, $realmID, IDS::RESOURCE_BILL, $IDType);
	}

	public function update(Context $Context, string $realmID, string $IDType, ObjBill $Object)
	{
		return parent::_update($Context, $realmID, IDS::RESOURCE_BILL, $Object, $IDType);
	}

	public function query(Context $Context, string $realmID, string $query)
	{
		return parent::_query($Context, $realmID, $query);
	}
}
