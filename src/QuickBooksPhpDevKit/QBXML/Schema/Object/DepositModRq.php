<?php declare(strict_types=1);

/**
 * Schema object for: DepositModRq
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
final class DepositModRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'DepositMod';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'TxnID' => 'IDTYPE',
		'EditSequence' => 'STRTYPE',
		'TxnDate' => 'DATETYPE',
		'DepositToAccountRef ListID' => 'IDTYPE',
		'DepositToAccountRef FullName' => 'STRTYPE',
		'Memo' => 'STRTYPE',
		'CashBackInfoMod AccountRef ListID' => 'IDTYPE',
		'CashBackInfoMod AccountRef FullName' => 'STRTYPE',
		'CashBackInfoMod Memo' => 'STRTYPE',
		'CashBackInfoMod Amount' => 'AMTTYPE',
		'CurrencyRef ListID' => 'IDTYPE',
		'CurrencyRef FullName' => 'STRTYPE',
		'ExchangeRate' => 'FLOATTYPE',
		'DepositLineMod TxnLineID' => 'IDTYPE',
		'DepositLineMod PaymentTxnID' => 'IDTYPE',
		'DepositLineMod PaymentTxnLineID' => 'IDTYPE',
		'DepositLineMod OverrideMemo' => 'STRTYPE',
		'DepositLineMod OverrideCheckNumber' => 'STRTYPE',
		'DepositLineMod OverrideClassRef ListID' => 'IDTYPE',
		'DepositLineMod OverrideClassRef FullName' => 'STRTYPE',
		'DepositLineMod EntityRef ListID' => 'IDTYPE',
		'DepositLineMod EntityRef FullName' => 'STRTYPE',
		'DepositLineMod AccountRef ListID' => 'IDTYPE',
		'DepositLineMod AccountRef FullName' => 'STRTYPE',
		'DepositLineMod Memo' => 'STRTYPE',
		'DepositLineMod CheckNumber' => 'STRTYPE',
		'DepositLineMod PaymentMethodRef ListID' => 'IDTYPE',
		'DepositLineMod PaymentMethodRef FullName' => 'STRTYPE',
		'DepositLineMod ClassRef ListID' => 'IDTYPE',
		'DepositLineMod ClassRef FullName' => 'STRTYPE',
		'DepositLineMod Amount' => 'AMTTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'EditSequence' => 16,
		'DepositToAccountRef FullName' => 159,
		'Memo' => 4095,
		'CashBackInfoMod AccountRef FullName' => 159,
		'CashBackInfoMod Memo' => 4095,
		'CurrencyRef FullName' => 64,
		'DepositLineMod OverrideMemo' => 4095,
		'DepositLineMod OverrideCheckNumber' => 11,
		'DepositLineMod OverrideClassRef FullName' => 159,
		'DepositLineMod EntityRef FullName' => 209,
		'DepositLineMod AccountRef FullName' => 159,
		'DepositLineMod Memo' => 4095,
		'DepositLineMod CheckNumber' => 11,
		'DepositLineMod PaymentMethodRef FullName' => 31,
		'DepositLineMod ClassRef FullName' => 159,
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
		'CurrencyRef ListID' => 8.0,
		'CurrencyRef FullName' => 8.0,
		'ExchangeRate' => 8.0,
		'IncludeRetElement' => 4.0,
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
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'TxnID',
		'EditSequence',
		'TxnDate',
		'DepositToAccountRef',
		'DepositToAccountRef ListID',
		'DepositToAccountRef FullName',
		'Memo',
		'CashBackInfoMod',
		'CashBackInfoMod AccountRef',
		'CashBackInfoMod AccountRef ListID',
		'CashBackInfoMod AccountRef FullName',
		'CashBackInfoMod Memo',
		'CashBackInfoMod Amount',
		'CurrencyRef',
		'CurrencyRef ListID',
		'CurrencyRef FullName',
		'ExchangeRate',
		'DepositLineMod',
		'DepositLineMod TxnLineID',
		'DepositLineMod PaymentTxnID',
		'DepositLineMod PaymentTxnLineID',
		'DepositLineMod OverrideMemo',
		'DepositLineMod OverrideCheckNumber',
		'DepositLineMod OverrideClassRef ListID',
		'DepositLineMod OverrideClassRef FullName',
		'DepositLineMod EntityRef ListID',
		'DepositLineMod EntityRef FullName',
		'DepositLineMod AccountRef ListID',
		'DepositLineMod AccountRef FullName',
		'DepositLineMod Memo',
		'DepositLineMod CheckNumber',
		'DepositLineMod PaymentMethodRef ListID',
		'DepositLineMod PaymentMethodRef FullName',
		'DepositLineMod ClassRef ListID',
		'DepositLineMod ClassRef FullName',
		'DepositLineMod Amount',
		'IncludeRetElement',
	];
}
