<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Line extends BaseObject
{
	/*
	public function setQuantity($quantity): bool
	{
		return $this->set('Qty', (float) $quantity);
	}

	public function getQuantity()
	{
		return $this->get('Qty');
	}

	public function setDescription($description): bool
	{
		return $this->set('Desc', $description);
	}

	public function getDescription(): string
	{
		return $this->get('Desc');
	}
	*/

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
			'Custom2' => true,
			'ServiceDate' => true,
		];
	}
}
