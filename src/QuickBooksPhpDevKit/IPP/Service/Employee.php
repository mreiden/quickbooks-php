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

use QuickBooksPhpDevKit\IPP\Object\Employee as ObjEmployee;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class Employee extends Service
{
	public function findAll(Context $Context, string $realmID, ?string $query = null, int $page = 1, int $size = 50, array $options = [])
	{
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_EMPLOYEE, $query, null, $page, $size, '', $options);
	}

	/**
	 * Get an employee by ID
	 *
	 * @param QuickBooks_IPP_Context $Context
	 * @param string $realmID
	 * @param string $ID						The ID of the customer (this expects an IdType, which includes the domain)
	 * @return QuickBooks_IPP_Object_Employee	The employee object
	 */
	public function findById(Context $Context, string $realmID, $ID): ?ObjEmployee
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_EMPLOYEE, $ID, null, $xml);
	}

	public function add(Context $Context, string $realmID, ObjEmployee $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_EMPLOYEE, $Object);
	}

	public function query(Context $Context, string $realmID, ?string $query)
	{
		return parent::_query($Context, $realmID, $query);
	}
}
