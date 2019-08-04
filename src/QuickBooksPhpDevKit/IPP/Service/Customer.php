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
use QuickBooksPhpDevKit\IPP\Object\Customer as ObjCustomer;
use QuickBooksPhpDevKit\IPP\Service;
use QuickBooksPhpDevKit\PackageInfo;

class Customer extends Service
{
	public function findAll(Context $Context, string $realm, ?string $query = null, int $page = 1, int $size = 50, array $options = [])
	{
		return parent::_findAll($Context, $realm, IDS::RESOURCE_CUSTOMER, $query, null, $page, $size, '', $options);
	}

	/**
	 * Get a customer by ID
	 */
	public function findById(Context $Context, string $realm, string $IDType, ?string $query = null): ?ObjCustomer
	{
		$xml = null;
		return parent::_findById($Context, $realm, IDS::RESOURCE_CUSTOMER, $IDType, $xml, $query);
	}

	/**
	 * Get a customer by name
	 */
	public function findByName(Context $Context, string $realm, string $name): ?ObjCustomer
	{
		$xml = null;
		return parent::_findByName($Context, $realm, IDS::RESOURCE_CUSTOMER, $name, $xml);
	}

	/**
	 * Delete a customer from IDS/QuickBooks
	 */
	public function delete(Context $Context, string $realm, string $IDType)
	{
		return parent::_delete($Context, $realm, IDS::RESOURCE_CUSTOMER, $IDType);
	}

	public function add(Context $Context, string $realm, $Object)
	{
		return parent::_add($Context, $realm, IDS::RESOURCE_CUSTOMER, $Object);
	}

	public function update(Context $Context, string $realm, string $IDType, $Object): bool
	{
		return parent::_update($Context, $realm, IDS::RESOURCE_CUSTOMER, $Object, $IDType);
	}

	public function query(Context $Context, string $realm, string $query)
	{
		return parent::_query($Context, $realm, $query);
	}
}
