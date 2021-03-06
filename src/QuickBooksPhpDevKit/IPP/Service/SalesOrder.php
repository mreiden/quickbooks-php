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

use QuickBooksPhpDevKit\IPP\Object\SalesOrder as ObjSalesOrder;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class SalesOrder extends Service
{
	public function findAll(Context $Context, string $realmID, ?string $query = null, int $page = 1, int $size = 50)
	{
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_SALESORDER, $query, null, $page, $size);
	}

	/**
	 *
	 *
	 *
	 */
	public function findById(Context $Context, string $realmID, $ID, ?string $domain = null): ?ObjSalesOrder
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_SALESORDER, $ID, $domain, $xml);
	}

	public function add(Context $Context, string $realmID, ObjSalesOrder $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_SALESORDER, $Object);
	}

	public function query(Context $Context, string $realmID, ?string $query)
	{
		return parent::_query($Context, $realmID, $query);
	}
}
