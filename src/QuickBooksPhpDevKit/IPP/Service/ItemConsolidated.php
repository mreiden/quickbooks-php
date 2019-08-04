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

use QuickBooksPhpDevKit\IPP\Object\ItemConsolidated as ObjItemConsolidated;
use QuickBooksPhpDevKit\IPP\Context;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\Service;

class ItemConsolidated extends Service
{
	public function findAll(Context $Context, string $realmID, ?string $query = null, int $page = 1, int $size = 50)
	{
		return parent::_findAll($Context, $realmID, IDS::RESOURCE_ITEMCONSOLIDATED, $query, null, $page, $size);
	}

	/**
	 * Get an item by ID
	 *
	 * @param QuickBooks_IPP_Context $Context
	 * @param string $realmID
	 * @param string $ID								The ID of the item (this expects an IdType, which includes the domain)
	 * @return QuickBooks_IPP_Object_ItemConsolidated	The item object
	 */
	public function findById(Context $Context, string $realmID, $ID): ?ObjItemConsolidated
	{
		$xml = null;
		return parent::_findById($Context, $realmID, IDS::RESOURCE_ITEMCONSOLIDATED, $ID, $xml);
	}

	/*
	public function findByNameContains($Context, $realmID, $contains)
	{
		$IPP = $Context->IPP();

		$resource = IDS::RESOURCE_ITEMCONSOLIDATED;

		$xml = '';
		$xml .= '<?xml version="1.0" encoding="UTF-8"?>' . QUICKBOOKS_CRLF;
		$xml .= '<' . $resource . 'Query xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '">' . QUICKBOOKS_CRLF;
		$xml .= '	<NameContains>' . QuickBooks_XML::encode($contains) . '</NameContains>' . QUICKBOOKS_CRLF;
		$xml .= '</' . $resource . 'Query>';

		return parent::_findAll($Context, $realmID, $resource, $xml);
	}
	*/

	/**
	 * Get an item by name
	 *
	 * @param QuickBooks_IPP_Context $Context
	 * @param string $realmID
	 * @param string $name								The name of the item
	 * @return QuickBooks_IPP_Object_ItemConsolidated	The customer object
	 */
	/*
	public function findByName($Context, $realmID, $name)
	{
		$list = $this->findByNameContains($Context, $realmID, $name);

		foreach ($list as $Item)
		{
			if (strtolower($Item->getName()) == strtolower($name))
			{
				return $Item;
			}
		}

		return false;
	}
	*/

	public function add(Context $Context, string $realmID, ObjItemConsolidated $Object)
	{
		return parent::_add($Context, $realmID, IDS::RESOURCE_ITEMCONSOLIDATED, $Object);
	}

	public function delete(Context $Context, string $realmID, string $IDType)
	{
		return parent::_delete($Context, $realmID, IDS::RESOURCE_ITEMCONSOLIDATED, $IDType);
	}
}
