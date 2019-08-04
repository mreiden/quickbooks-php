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
 * @author Jared Cheney <jared@tsheets.com>
 *
 * @package QuickBooks
 * @subpackage IPP
 */

namespace QuickBooksPhpDevKit\IPP\Service;

use QuickBooksPhpDevKit\IPP\Object\TimeActivity as ObjTimeActivity;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class TimeActivity extends Service
{
	/*
	public function findAll(Context $Context, string $realmID, ?string $query = null, int $page = 1, int $size = 50, array $options = [])
	{
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_TIMEACTIVITY, $query, null, $page, $size, '', $options);
	}

	public function findById(Context $Context, string $realmID, string $IDType, ?string $query = null)
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_TIMEACTIVITY, $IDType, $xml, $query);
	}
	*/

	public function query(Context $Context, string $realm, string $query)
	{
		return parent::_query($Context, $realm, $query);
	}

	/**
	 * Delete a timeactivity from IDS/QuickBooks
	 *
	 *
	 */
	public function delete(Context $Context, string $realmID, string $IDType)
	{
		return parent::_delete($Context, $realmID, IDS::RESOURCE_TIMEACTIVITY, $IDType);
	}

	public function add(Context $Context, string $realmID, ObjTimeActivity $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_TIMEACTIVITY, $Object);
	}

	public function update(Context $Context, string $realmID, string $IDType, ObjTimeActivity $Object)
	{
		return parent::_update($Context, $realmID, IDS::RESOURCE_TIMEACTIVITY, $Object, $IDType);
	}
}
