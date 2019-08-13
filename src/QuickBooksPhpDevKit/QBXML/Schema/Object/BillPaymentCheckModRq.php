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
 * WARNING!!!: This file is generated by QuickBooksPhpDevKit\QBXML\Schema\Generator using the /data/qbxmlops130.xml file in this package.
 */
final class BillPaymentCheckModRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'BillPaymentCheckMod';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
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

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'EditSequence' => 16,
		'BankAccountRef FullName' => 159,
		'RefNumber' => 11,
		'Memo' => 4095,
		'AppliedToTxnMod DiscountAccountRef FullName' => 159,
		'AppliedToTxnMod DiscountClassRef FullName' => 159,
		'IncludeRetElement' => 50,
	];

	/**
	 * Field is optional (may be ommitted)
	 * @var bool[]
	 */
	protected $_isOptionalPaths = []; //'_isOptionalPaths ';

	/**
	 * Field Available Since QBXML Version #
	 * @var float[]
	 */
	protected $_sinceVersionPaths = [
		'ExchangeRate' => 8.0,
		'AppliedToTxnMod SetCredit Override' => 10.0,
		'AppliedToTxnMod DiscountClassRef ListID' => 11.0,
		'AppliedToTxnMod DiscountClassRef FullName' => 11.0,
	];

	/**
	 * Field May Be Included Multiple Times
	 * @var bool[]
	 */
	protected $_isRepeatablePaths = [
		'IncludeRetElement' => true,
	];

	/**
	 * Field Is Excluded From These Locales
	 *
	 * QuickBooks labels are QBD, QBCA, QBUK, QBAU, and QBOE but these are mapped to
	 * US, CA, UK, AU, and OE to match what is in the PackageInfo::Locale array.
	 * @var string[][]
	 */
	protected $_localeExcludedPaths = [
		'AppliedToTxnMod SetCredit TxnLineID' => ['US','CA','UK','AU'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
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
}
