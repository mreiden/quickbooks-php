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
 * @author Thomas Rientjes
 *
 * @package QuickBooks
 * @subpackage IPP
 */

namespace QuickBooksPhpDevKit\IPP\Service;

use QuickBooksPhpDevKit\IPP\Object\Department as ObjDepartment;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class Department extends Service
{
	public function findAll(Context $Context, string $realmID)
	{
		$xml = null;
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_DEPARTMENT, $xml);
	}

	/**
	 * Get a department by ID
	 */
	public function findById(Context $Context, string $realmID, string $IDType): ?ObjDepartment
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_DEPARTMENT, $IDType, $xml);
	}

	/**
	 * Add a new department to QuickBooks
	 * @return string The new ID of the created department
	 */
	public function add(Context $Context, string $realmID, ObjDepartment $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_DEPARTMENT, $Object);
	}

	public function query(Context $Context, string $realm, string $query)
	{
		return parent::_query($Context, $realm, $query);
	}
}
