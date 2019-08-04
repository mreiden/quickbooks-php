<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Artush
 * Date: 18.01.2017
 * Time: 13:23
 */

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class DepositLineDetail extends BaseObject
{

	protected function _order(): array
	{
		return [
			'Id' => true,
			'Desc' => true,
			'GroupMember' => true,
			'Amount' => true,
			'ClassId' => true,
			'ClassName' => true,
			'Taxable' => true,
			'ItemId' => true,
			'ItemName' => true,
			'ItemType' => true,
			'UnitPrice' => true,
			'RatePercent' => true,
			'PriceLevelId' => true,
			'PriceLevelName' => true,
			'Qty' => true,
			'UOMId' => true,
			'UOMAbbrv' => true,
			'OverrideItemAccountId' => true,
			'OverrideItemAccountName' => true,
			'DiscountId' => true,
			'DiscountName' => true,
			'SalesTaxCodeId' => true,
			'SalesTaxCodeName' => true,
			'Custom1' => true,
			'Entity_name' => true,
			'AccountRef_name' => true,

			'Entity' => true,
			'Entity_type' => true,
			'AccountRef' => true,
		];
	}
}
