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

use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Object\Vendor as ObjVendor;
use QuickBooksPhpDevKit\IPP\Service;
use QuickBooksPhpDevKit\PackageInfo;

class Vendor extends Service
{
	public function findAll(Context $Context, string $realmID, ?string $query = null, int $page = 1, int $size = 50, array $options = []): ?array
	{
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_VENDOR, $query, null, $page, $size, '', $options);
	}

	public function add(Context $Context, string $realmID, $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_VENDOR, $Object);
	}

	public function query(Context $Context, string $realm, string $query): ?array
	{
		return parent::_query($Context, $realm, $query);
	}

	/**
	 * Updates vendor.
	 */
	public function update(Context $Context, string $realm, string $IDType, $Object): bool
	{
		return parent::_update($Context, $realm, IDS::RESOURCE_VENDOR, $Object, $IDType);
	}
}
