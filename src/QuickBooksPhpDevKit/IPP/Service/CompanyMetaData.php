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

use QuickBooksPhpDevKit\IPP\Object\CompanyMetaData as ObjCompanyMetaData;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class CompanyMetaData extends Service
{
	/**
	 * Get company meta data by realmID
	 */
	public function findById(Context $Context, string $realmID): ?ObjCompanyMetaData
	{
		$xml = null;

		// WATCH OUT!   We pass in the realmID as ID value
		return parent::_findById($Context, $realmID, QuickBooks_IPP_IDS::RESOURCE_COMPANYMETADATA, $realmID, $xml);
	}
}
