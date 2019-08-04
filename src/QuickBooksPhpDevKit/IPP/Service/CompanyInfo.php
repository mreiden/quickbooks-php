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

use QuickBooksPhpDevKit\Ipp\Context;
use QuickBooksPhpDevKit\Ipp\Object\CompanyInfo as ObjCompanyInfo;
use QuickBooksPhpDevKit\Ipp\Service;

class CompanyInfo extends Service
{
	/**
	 * Get a company by realmID
	 *
	 * @param QuickBooks_IPP_Context $Context
	 * @param string $realmID
	 * @return QuickBooks_IPP_Object_Customer	The customer object
	 */
	public function query(Context $Context, string $realm, string $query): ?array
	{
		return parent::_query($Context, $realm, $query);
	}

	public function get(Context $Context, string $realm): ?ObjCompanyInfo
	{
		if ($list = parent::_query($Context, $realm, "SELECT * FROM CompanyInfo"))
		{
			return $list[0];
		}

		return null;
	}
}
