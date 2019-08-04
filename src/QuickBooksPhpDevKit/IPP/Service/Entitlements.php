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

use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\Service;

class Entitlements extends Service
{
	public function info(Context $Context, string $realmID)
	{
		$list = parent::_entitlements($Context, $realmID);

		return $list['_i'];
	}

	public function entitlements(Context $Context, string $realmID)
	{
		$list = parent::_entitlements($Context, $realmID);

		return $list['_e'];
	}
}
