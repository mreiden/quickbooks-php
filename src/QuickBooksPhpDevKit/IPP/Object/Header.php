<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Header extends BaseObject
{
	public function setTxnDate($value): bool
	{
		return $this->setDateType('TxnDate', $value);
	}

	public function getTxnDate(string $format = 'Y-m-d'): string
	{
		return $this->getDateType('TxnDate', $format);
	}

	public function setTotalAmt($amt)
	{
		return $this->setAmountType('TotalAmt', $amt);
	}

	protected function _order(): array
	{
		return [
			'DocNumber' => true,
			'TxnDate' => true,
			'Note' => true,
			'Status' => true,
			'VendorId' => true,
			'VendorName' => true,
			'CustomerId' => true,
			'CustomerName' => true,
			'JobId' => true,
			'JobName' => true,
			'RemitToId' => true,
			'RemitToName' => true,
			'ClassId' => true,
			'ClassName' => true,
			'SalesRepId' => true,
			'SalesRepName' => true,
			'SalesTaxCodeId' => true,
			'SalesTaxCodeName' => true,
			'PONumber' => true,
			'FOB' => true,
			'ShipDate' => true,
			'SubTotalAmt' => true,
			'TaxId' => true,
			'TaxName' => true,
			'TaxGroupId' => true,
			'TaxGroupName' => true,
			'TaxRate' => true,
			'TaxAmt' => true,
			'ToBePrinted' => true,
			'ToBeEmailed' => true,
			'Custom' => true,
			'PaymentMethodId' => true,
			'PaymentMethodName' => true,
			'TotalAmt' => true, 			// This needs to be almost last for Payments (or at least after PaymentMethodName)
			'BillAddr' => true,  			// Not part of SalesReceipt
			'ShipAddr' => true,
			'BillEmail' => true,
			'ShipMethodId' => true,
			'ShipMethodName' => true,
			'Balance' => true, 				// Not part of SalesReceipt
			'DepositToAccountId' => true,
			'DepositToAccountName' => true,
			'Detail' => true,
			'DiscountAmt' => true,
			'DiscountRate' => true,
			'DiscountAccountId' => true,
			'DiscountAccountName' => true,
			'DiscountTaxable' => true,
		];
	}
}
