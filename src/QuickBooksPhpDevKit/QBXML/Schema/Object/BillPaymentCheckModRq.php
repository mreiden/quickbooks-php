<?php declare(strict_types=1);

/**
 * Schema object for: BillPaymentCheckModRq
 *
 * @author "Keith Palmer Jr." <Keith@ConsoliByte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage QBXML
 */

namespace QuickBooksPhpDevKit\QBXML\Schema\Object;

use QuickBooksPhpDevKit\QBXML\Schema\AbstractSchemaObject;

/**
 * WARNING!!!  This file is auto-generated by QBXML\Schema\Generator and the data/qbxmlops130.xml schema
 */
class BillPaymentCheckModRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'BillPaymentCheckMod';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'TxnID' => 'IDTYPE',
			'EditSequence' => 'STRTYPE',
			'TxnDate' => 'DATETYPE',
			'BankAccountRef ListID' => 'IDTYPE',
			'BankAccountRef FullName' => 'STRTYPE',
			'Amount' => 'AMTTYPE',
			'ExchangeRate' => 'FLOATTYPE',
			'IsToBePrinted' => 'BOOLTYPE',
			'RefNumber' => 'STRTYPE',
			'Memo' => 'STRTYPE',
			'AppliedToTxnMod TxnID' => 'IDTYPE',
			'AppliedToTxnMod PaymentAmount' => 'AMTTYPE',
			'AppliedToTxnMod SetCredit CreditTxnID' => 'IDTYPE',
			'AppliedToTxnMod SetCredit TxnLineID' => 'IDTYPE',
			'AppliedToTxnMod SetCredit AppliedAmount' => 'AMTTYPE',
			'AppliedToTxnMod SetCredit Override' => 'BOOLTYPE',
			'AppliedToTxnMod DiscountAmount' => 'AMTTYPE',
			'AppliedToTxnMod DiscountAccountRef ListID' => 'IDTYPE',
			'AppliedToTxnMod DiscountAccountRef FullName' => 'STRTYPE',
			'AppliedToTxnMod DiscountClassRef ListID' => 'IDTYPE',
			'AppliedToTxnMod DiscountClassRef FullName' => 'STRTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'TxnID' => 0,
			'EditSequence' => 16,
			'TxnDate' => 0,
			'BankAccountRef ListID' => 0,
			'BankAccountRef FullName' => 159,
			'Amount' => 0,
			'ExchangeRate' => 0,
			'IsToBePrinted' => 0,
			'RefNumber' => 11,
			'Memo' => 4095,
			'AppliedToTxnMod TxnID' => 0,
			'AppliedToTxnMod PaymentAmount' => 0,
			'AppliedToTxnMod SetCredit CreditTxnID' => 0,
			'AppliedToTxnMod SetCredit TxnLineID' => 0,
			'AppliedToTxnMod SetCredit AppliedAmount' => 0,
			'AppliedToTxnMod SetCredit Override' => 0,
			'AppliedToTxnMod DiscountAmount' => 0,
			'AppliedToTxnMod DiscountAccountRef ListID' => 0,
			'AppliedToTxnMod DiscountAccountRef FullName' => 159,
			'AppliedToTxnMod DiscountClassRef ListID' => 0,
			'AppliedToTxnMod DiscountClassRef FullName' => 159,
			'IncludeRetElement' => 50,
		];

		return $paths;
	}

	protected function &_isOptionalPaths(): array
	{
		// This seems broken when a parent is optional but an element is required if the parent is included (See HostQueryRq IncludeMaxCapacity).
		static $paths = []; //'_isOptionalPaths ';

		return $paths;
	}

	protected function &_sinceVersionPaths(): array
	{
		static $paths = [
			'TxnID' => 999.99,
			'EditSequence' => 999.99,
			'TxnDate' => 999.99,
			'BankAccountRef ListID' => 999.99,
			'BankAccountRef FullName' => 999.99,
			'Amount' => 999.99,
			'ExchangeRate' => 8.0,
			'IsToBePrinted' => 999.99,
			'RefNumber' => 999.99,
			'Memo' => 999.99,
			'AppliedToTxnMod TxnID' => 999.99,
			'AppliedToTxnMod PaymentAmount' => 999.99,
			'AppliedToTxnMod SetCredit CreditTxnID' => 999.99,
			'AppliedToTxnMod SetCredit TxnLineID' => 999.99,
			'AppliedToTxnMod SetCredit AppliedAmount' => 999.99,
			'AppliedToTxnMod SetCredit Override' => 10.0,
			'AppliedToTxnMod DiscountAmount' => 999.99,
			'AppliedToTxnMod DiscountAccountRef ListID' => 999.99,
			'AppliedToTxnMod DiscountAccountRef FullName' => 999.99,
			'AppliedToTxnMod DiscountClassRef ListID' => 999.99,
			'AppliedToTxnMod DiscountClassRef FullName' => 999.99,
			'IncludeRetElement' => 999.99,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'TxnID' => false,
			'EditSequence' => false,
			'TxnDate' => false,
			'BankAccountRef ListID' => false,
			'BankAccountRef FullName' => false,
			'Amount' => false,
			'ExchangeRate' => false,
			'IsToBePrinted' => false,
			'RefNumber' => false,
			'Memo' => false,
			'AppliedToTxnMod TxnID' => false,
			'AppliedToTxnMod PaymentAmount' => false,
			'AppliedToTxnMod SetCredit CreditTxnID' => false,
			'AppliedToTxnMod SetCredit TxnLineID' => false,
			'AppliedToTxnMod SetCredit AppliedAmount' => false,
			'AppliedToTxnMod SetCredit Override' => false,
			'AppliedToTxnMod DiscountAmount' => false,
			'AppliedToTxnMod DiscountAccountRef ListID' => false,
			'AppliedToTxnMod DiscountAccountRef FullName' => false,
			'AppliedToTxnMod DiscountClassRef ListID' => false,
			'AppliedToTxnMod DiscountClassRef FullName' => false,
			'IncludeRetElement' => true,
		];

		return $paths;
	}

	/*
	protected function &_inLocalePaths(): array
	{
		static $paths = [
			'FirstName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
			'LastName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
		];

		return $paths;
	}
	*/

	protected function &_reorderPathsPaths(): array
	{
		static $paths = [
			'TxnID',
			'EditSequence',
			'TxnDate',
			'BankAccountRef',
			'BankAccountRef ListID',
			'BankAccountRef FullName',
			'Amount',
			'ExchangeRate',
			'IsToBePrinted',
			'RefNumber',
			'Memo',
			'AppliedToTxnMod',
			'AppliedToTxnMod TxnID',
			'AppliedToTxnMod PaymentAmount',
			'AppliedToTxnMod SetCredit CreditTxnID',
			'AppliedToTxnMod SetCredit TxnLineID',
			'AppliedToTxnMod SetCredit AppliedAmount',
			'AppliedToTxnMod SetCredit Override',
			'AppliedToTxnMod DiscountAmount',
			'AppliedToTxnMod DiscountAccountRef ListID',
			'AppliedToTxnMod DiscountAccountRef FullName',
			'AppliedToTxnMod DiscountClassRef ListID',
			'AppliedToTxnMod DiscountClassRef FullName',
			'IncludeRetElement',
		];

		return $paths;
	}
}
