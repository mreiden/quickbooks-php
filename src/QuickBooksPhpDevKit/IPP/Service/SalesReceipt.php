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

use QuickBooksPhpDevKit\IPP\Object\SalesReceipt as ObjSalesReceipt;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class SalesReceipt extends Service
{
	public function update(Context $Context, string $realmID, string $IDType, ObjSalesReceipt $Object)
	{
		return parent::_update($Context, $realmID, IDS::RESOURCE_SALESRECEIPT, $Object, $IDType);
	}

	/**
	 * Add a new sales receipt to IDS/QuickBooks
	 *
	 * @param QuickBooks_IPP_Context $Context
	 * @param string $realmID
	 * @param QuickBooks_IPP_Object_SalesReceipt $Object		The sales receipt to add
	 * @return string											The Id value of the new sales receipt
	 */
	public function add(Context $Context, string $realmID, ObjSalesReceipt $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_SALESRECEIPT, $Object);
	}

	public function query(Context $Context, string $realm, ?string $query)
	{
		return parent::_query($Context, $realm, $query);
	}

	public function findById(Context $Context, string $realmID, $ID, ?string $domain = null): ?ObjSalesReceipt
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_SALESRECEIPT, $ID, $domain, $xml);
	}

	public function delete(Context $Context, string $realmID, string $IDType)
	{
		return parent::_delete($Context, $realmID, IDS::RESOURCE_SALESRECEIPT, $IDType);
	}

	public function void(Context $Context, string $realmID, string $IDType)
	{
		return parent::_void($Context, $realmID, IDS::RESOURCE_SALESRECEIPT, $IDType);
	}

	public function pdf(Context $Context, string $realmID, string $IDType)
	{
		return parent::_pdf($Context, $realmID, IDS::RESOURCE_SALESRECEIPT, $IDType);
	}

	public function send(Context $Context, string $realmID, string $IDType)
	{
		return parent::_send($Context, $realmID, IDS::RESOURCE_SALESRECEIPT, $IDType);
	}
}
