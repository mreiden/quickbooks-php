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

use QuickBooksPhpDevKit\IPP\Object\PayrollItem as ObjPayrollItem;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class PayrollItem extends Service
{
	public function findAll(Context $Context, string $realmID, ?string $query = null, int $page = 1, int $size = 50, array $options = [])
	{
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_PAYROLLITEM, $query, null, $page, $size, '', $options);
	}

	public function findById(Context $Context, string $realmID, $ID)
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_PAYROLLITEM, $ID, $xml);
	}

	public function findByName(Context $Context, string $realmID, string $name)
	{
		$list = $this->findAll($Context, $realmID, $name);

		foreach ($list as $Item)
		{
			if (strtolower($Item->getName()) == strtolower($name))
			{
				return $Item;
			}
		}

		return false;
	}

	public function add(Context $Context, string $realmID, ObjPayrollItem $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_PAYROLLITEM, $Object);
	}

	public function delete(Context $Context, string $realmID, string $IDType)
	{
		return parent::_delete($Context, $realmID, IDS::RESOURCE_PAYROLLITEM, $IDType);
	}
}
