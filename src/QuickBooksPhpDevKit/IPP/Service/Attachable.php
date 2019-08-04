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
 * @author Ryan Bantz <ryan@rykelabs.com>
 *
 * @package QuickBooks
 * @subpackage IPP
 */

namespace QuickBooksPhpDevKit\IPP\Service;

use QuickBooksPhpDevKit\IPP\Object\Attachable as ObjAttachable;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class Attachable extends Service
{
	public function query(Context $Context, string $realm, string $query)
	{
		return parent::_query($Context, $realm, $query);
	}

	public function download(Context $Context, string $realmID, string $ID)
	{
		return parent::_download($Context, $realmID, IDS::RESOURCE_DOWNLOAD, $ID);
	}
}
