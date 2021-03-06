<?php declare(strict_types=1);

/**
 * Schema object for: ItemReceiptQueryRq
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
final class ItemReceiptQueryRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = '';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'TxnID' => 'IDTYPE',
		'RefNumber' => 'STRTYPE',
		'RefNumberCaseSensitive' => 'STRTYPE',
		'MaxReturned' => 'INTTYPE',
		'ModifiedDateRangeFilter FromModifiedDate' => 'DATETIMETYPE',
		'ModifiedDateRangeFilter ToModifiedDate' => 'DATETIMETYPE',
		'TxnDateRangeFilter FromTxnDate' => 'DATETYPE',
		'TxnDateRangeFilter ToTxnDate' => 'DATETYPE',
		'TxnDateRangeFilter DateMacro' => 'ENUMTYPE',
		'EntityFilter ListID' => 'IDTYPE',
		'EntityFilter FullName' => 'STRTYPE',
		'EntityFilter ListIDWithChildren' => 'IDTYPE',
		'EntityFilter FullNameWithChildren' => 'STRTYPE',
		'AccountFilter ListID' => 'IDTYPE',
		'AccountFilter FullName' => 'STRTYPE',
		'AccountFilter ListIDWithChildren' => 'IDTYPE',
		'AccountFilter FullNameWithChildren' => 'STRTYPE',
		'RefNumberFilter MatchCriterion' => 'ENUMTYPE',
		'RefNumberFilter RefNumber' => 'STRTYPE',
		'RefNumberRangeFilter FromRefNumber' => 'STRTYPE',
		'RefNumberRangeFilter ToRefNumber' => 'STRTYPE',
		'CurrencyFilter ListID' => 'IDTYPE',
		'CurrencyFilter FullName' => 'STRTYPE',
		'IncludeLineItems' => 'BOOLTYPE',
		'IncludeLinkedTxns' => 'BOOLTYPE',
		'IncludeRetElement' => 'STRTYPE',
		'OwnerID' => 'GUIDTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
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
		'RefNumberCaseSensitive' => 4.0,
		'EntityFilter ListID' => 2.0,
		'EntityFilter ListIDWithChildren' => 2.0,
		'AccountFilter ListID' => 2.0,
		'AccountFilter ListIDWithChildren' => 2.0,
		'CurrencyFilter ListID' => 2.0,
		'IncludeRetElement' => 4.0,
	];

	/**
	 * Field May Be Included Multiple Times
	 * @var bool[]
	 */
	protected $_isRepeatablePaths = [
		'TxnID' => true,
		'RefNumber' => true,
		'RefNumberCaseSensitive' => true,
		'EntityFilter ListID' => true,
		'EntityFilter FullName' => true,
		'AccountFilter ListID' => true,
		'AccountFilter FullName' => true,
		'RefNumberFilter RefNumber' => true,
		'CurrencyFilter ListID' => true,
		'CurrencyFilter FullName' => true,
		'IncludeRetElement' => true,
		'OwnerID' => true,
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
		'RefNumber',
		'RefNumberCaseSensitive',
		'MaxReturned',
		'ModifiedDateRangeFilter',
		'ModifiedDateRangeFilter FromModifiedDate',
		'ModifiedDateRangeFilter ToModifiedDate',
		'TxnDateRangeFilter',
		'TxnDateRangeFilter FromTxnDate',
		'TxnDateRangeFilter ToTxnDate',
		'TxnDateRangeFilter DateMacro',
		'EntityFilter',
		'EntityFilter ListID',
		'EntityFilter FullName',
		'EntityFilter ListIDWithChildren',
		'EntityFilter FullNameWithChildren',
		'AccountFilter',
		'AccountFilter ListID',
		'AccountFilter FullName',
		'AccountFilter ListIDWithChildren',
		'AccountFilter FullNameWithChildren',
		'RefNumberFilter',
		'RefNumberFilter MatchCriterion',
		'RefNumberFilter RefNumber',
		'RefNumberRangeFilter',
		'RefNumberRangeFilter FromRefNumber',
		'RefNumberRangeFilter ToRefNumber',
		'CurrencyFilter',
		'CurrencyFilter ListID',
		'CurrencyFilter FullName',
		'IncludeLineItems',
		'IncludeLinkedTxns',
		'IncludeRetElement',
		'OwnerID',
	];
}
