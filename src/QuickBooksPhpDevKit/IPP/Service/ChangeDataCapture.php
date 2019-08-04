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

//use QuickBooksPhpDevKit\IPP\Object\ChangeDataCapture as ObjChangeDataCapture;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class ChangeDataCapture extends Service
{
	public function cdc(Context $Context, string $realmID, $entities, $timestamp, int $page = 1, ?int $size = null)
	{
		return parent::_cdc($Context, $realmID, $entities, $timestamp, $page, $size);
	}
}
