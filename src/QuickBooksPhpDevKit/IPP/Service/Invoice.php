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

class Invoice extends Service
{
	public function add(Context $Context, string $realmID, $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_INVOICE, $Object);
	}

	public function update(Context $Context, string $realmID, string $IDType, $Object)
	{
		return parent::_update($Context, $realmID, IDS::RESOURCE_INVOICE, $Object, $IDType);
	}

	public function query(Context $Context, string $realmID, string $query)
	{
		return parent::_query($Context, $realmID, $query);
	}

	public function delete(Context $Context, string $realmID, string $IDType)
	{
		return parent::_delete($Context, $realmID, IDS::RESOURCE_INVOICE, $IDType);
	}

	public function void(Context $Context, string $realmID, string $IDType)
	{
		return parent::_void($Context, $realmID, IDS::RESOURCE_INVOICE, $IDType);
	}

	public function pdf(Context $Context, string $realmID, string $IDType)
	{
		return parent::_pdf($Context, $realmID, IDS::RESOURCE_INVOICE, $IDType);
	}
}
