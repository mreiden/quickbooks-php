<?php declare(strict_types=1);

/**
 * QuickBooks TxnDeleted object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;

/**
 *
 */
class TxnDeleted extends AbstractQbxmlObject
{
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the TxnDelType for this item
	 */
	public function setTxnDelType(string $type): bool
	{
		$valid = [
			'arrefundcreditcard' => 'ARRefundCreditCard',
			'bill' => 'Bill',
			'billpaymentcheck' => 'BillPaymentCheck',
			'billpaymentcreditcard' => 'BillPaymentCreditCard',
			'buildassembly' => 'BuildAssembly',
			'charge' => 'Charge',
			'check' => 'Check',
			'creditcardcharge' => 'CreditCardCharge',
			'creditcardcredit' => 'CreditCardCredit',
			'creditmemo' => 'CreditMemo',
			'deposit' => 'Deposit',
			'estimate' => 'Estimate',
			'inventoryadjustment' => 'InventoryAdjustment',
			'invoice' => 'Invoice',
			'itemreceipt' => 'ItemReceipt',
			'journalentry' => 'JournalEntry',
			'purchaseorder' => 'PurchaseOrder',
			'receivepayment' => 'ReceivePayment',
			'salesorder' => 'SalesOrder',
			'salesreceipt' => 'SalesReceipt',
			'salestaxpaymentcheck' => 'SalesTaxPaymentCheck',
			'timetracking' => 'TimeTracking',
			'transferinventory' => 'TransferInventory',
			'vehiclemileage' => 'VehicleMileage',
			'vendorcredit' => 'VendorCredit',
		];

		$type = strtolower(trim($type));
		if (!isset($valid[$type]))
		{
			throw new \Exception('TxnDelType is invalid.  See valid options in ' . __METHOD__);
		}

		return $this->set('TxnDelType', $valid[$type]);
	}

	/**
	 * Get the TxnDelType for this item
	 */
	public function getTxnDelType(): string
	{
		return $this->get('TxnDelType');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_TXNDELETED'];
	}
}
