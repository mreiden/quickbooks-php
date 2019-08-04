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

class Item extends Service
{
	public function findAll(Context $Context, string $realmID, ?string $query = null, int $page = 1, int $size = 50, array $options = [])
	{
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_ITEM, $query, null, $page, $size, '', $options);
	}

	public function findById(Context $Context, string $realmID, $ID)
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_ITEM, $ID, $xml);
	}

	/**
	 * Find an item by name
	 */
	public function findByName(Context $Context, string $realmID, string $name)
	{
		$IPP = $Context->IPP();

		if ($IPP->flavor() == IDS::FLAVOR_DESKTOP)
		{
			for ($i = 0; $i < 999; $i++)
			{
				$list = $this->findAll($Context, $realmID, $name, $i, 50);

				foreach ($list as $Item)
				{
					if (strtolower($Item->getName()) == strtolower($name))
					{
						return $Item;
					}
				}
			}

			return false;
		}

		$xml = null;
		return parent::_findByName($Context, $realmID, IDS::RESOURCE_ITEM, $name, $xml);
	}

	public function add(Context $Context, string $realmID, $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_ITEM, $Object);
	}

	public function update(Context $Context, string $realmID, string $IDType, $Object)
	{
		return parent::_update($Context, $realmID, IDS::RESOURCE_ITEM, $Object, $IDType);
	}

	public function delete(Context $Context, string $realmID, string $IDType)
	{
		return parent::_delete($Context, $realmID, IDS::RESOURCE_ITEM, $IDType);
	}

	public function query(Context $Context, string $realmID, string $query)
	{
		return parent::_query($Context, $realmID, $query);
	}
}
