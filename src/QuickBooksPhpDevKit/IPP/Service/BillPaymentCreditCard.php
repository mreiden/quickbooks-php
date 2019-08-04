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

use QuickBooksPhpDevKit\IPP\Object\BillPaymentCreditCard as ObjBillPaymentCreditCard;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class BillPaymentCreditCard extends Service
{
	public function findById(Context $Context, string $realmID, string $IDType, ?string $domain = null)
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_BILLPAYMENTCREDITCARD, $IDType, $domain, $xml);
	}

	public function findAll(Context $Context, string $realmID)
	{
		$xml = null;
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_BILLPAYMENTCREDITCARD, $xml);
	}
}
