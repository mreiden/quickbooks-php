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

use QuickBooksPhpDevKit\IPP\Object\Purchase as ObjPurchase;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class Purchase extends Service
{
	public function add(Context $Context, string $realmID, ObjPurchase $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_PURCHASE, $Object);
	}

	public function update(Context $Context, string $realmID, string $IDType, ObjPurchase $Object)
	{
		return parent::_update($Context, $realmID, IDS::RESOURCE_PURCHASE, $Object, $IDType);
	}

	public function query(Context $Context, string $realmID, ?string $query)
	{
		return parent::_query($Context, $realmID, $query);
	}
}
