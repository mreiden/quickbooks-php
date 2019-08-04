<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Item extends BaseObject
{
	protected function _order(): array
	{
		return [
			'Id' => true,
			'MetaData' => true,
			'ItemParentId' => true,
			'Unique Identifier' => true,
			'ItemParentName' => true,
			'Name' => true,
			'Desc' => true,
			'Taxable' => true,
			'Active' => true,
			'UnitPrice' => true,
			'Type' => true,
			'UOMId' => true,
			'UOMAbbrv' => true,
			'IncomeAccountRef' => true,
			'PurchaseDesc' => true,
			'PurchaseCost' => true,
			'ExpenseAccountRef' => true,
			'COGSAccountRef' => true,
			'AssetAccountRef' => true,
			'PrefVendorRef' => true,
			'AvgCost' => true,
			'QtyOnHand' => true,
			'QtyOnPurchaseOrder' => true,
			'QtyOnSalesOrder' => true,
			'ReorderPoint' => true,
			'ManPartNum' => true,
		];
	}
}
