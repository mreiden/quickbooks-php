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
use QuickBooksPhpDevKit\IPP\Service;

class Qbclass extends Service
{
	public function findAll(Context $Context, string $realmID)
	{
		$xml = null;
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_CLASS, $xml);
	}

	/**
	 * Get a customer by ID
	 *
	 * @param QuickBooks_IPP_Context $Context
	 * @param string $realmID
	 * @param string $ID						The ID of the customer (this expects an IdType, which includes the domain)
	 * @return QuickBooks_IPP_Object_Class		The Class object
	 */
	public function findById(Context $Context, string $realmID, string $IDType)
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_CLASS, $IDType, $xml);
	}

	/**
	 * Add a new class to QuickBooks
	 */
	public function add(Context $Context, string $realmID, $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_CLASS, $Object);
	}

	public function query(Context $Context, string $realmID, string $query)
	{
		return parent::_query($Context, $realmID, $query);
	}
}
