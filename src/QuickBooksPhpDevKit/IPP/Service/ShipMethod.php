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

//use QuickBooksPhpDevKit\IPP\Object\ShipMethod as ObjShipMethod;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class ShipMethod extends Service
{
	public function findAll(Context $Context, string $realmID, ?string $query = null, int $page = 1, int $size = 50)
	{
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_SHIPMETHOD, $query, null, $page, $size);
	}

	public function map(Context $Context, string $realmID)
	{
		$list = $this->findAll($Context, $realmID);

		return parent::_map($list, 'Id', 'Name');
	}
}
