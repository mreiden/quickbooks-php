<?php declare(strict_types=1);

/**
 * Schema object for: CustomDetailReportQueryRq
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
final class CustomDetailReportQueryRq extends AbstractSchemaObject
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
		'CustomDetailReportType' => 'ENUMTYPE',
		'DisplayReport' => 'BOOLTYPE',
		'ReportPeriod FromReportDate' => 'DATETYPE',
		'ReportPeriod ToReportDate' => 'DATETYPE',
		'ReportDateMacro' => 'ENUMTYPE',
		'ReportAccountFilter AccountTypeFilter' => 'ENUMTYPE',
		'ReportAccountFilter ListID' => 'IDTYPE',
		'ReportAccountFilter FullName' => 'STRTYPE',
		'ReportAccountFilter ListIDWithChildren' => 'IDTYPE',
		'ReportAccountFilter FullNameWithChildren' => 'STRTYPE',
		'ReportEntityFilter EntityTypeFilter' => 'ENUMTYPE',
		'ReportEntityFilter ListID' => 'IDTYPE',
		'ReportEntityFilter FullName' => 'STRTYPE',
		'ReportEntityFilter ListIDWithChildren' => 'IDTYPE',
		'ReportEntityFilter FullNameWithChildren' => 'STRTYPE',
		'ReportItemFilter ItemTypeFilter' => 'ENUMTYPE',
		'ReportItemFilter ListID' => 'IDTYPE',
		'ReportItemFilter FullName' => 'STRTYPE',
		'ReportItemFilter ListIDWithChildren' => 'IDTYPE',
		'ReportItemFilter FullNameWithChildren' => 'STRTYPE',
		'ReportClassFilter ListID' => 'IDTYPE',
		'ReportClassFilter FullName' => 'STRTYPE',
		'ReportClassFilter ListIDWithChildren' => 'IDTYPE',
		'ReportClassFilter FullNameWithChildren' => 'STRTYPE',
		'ReportTxnTypeFilter TxnTypeFilter' => 'ENUMTYPE',
		'ReportModifiedDateRangeFilter FromReportModifiedDate' => 'DATETYPE',
		'ReportModifiedDateRangeFilter ToReportModifiedDate' => 'DATETYPE',
		'ReportModifiedDateRangeMacro' => 'ENUMTYPE',
		'ReportDetailLevelFilter' => 'ENUMTYPE',
		'ReportPostingStatusFilter' => 'ENUMTYPE',
		'SummarizeRowsBy' => 'ENUMTYPE',
		'IncludeColumn' => 'ENUMTYPE',
		'IncludeAccounts' => 'ENUMTYPE',
		'ReportOpenBalanceAsOf' => 'ENUMTYPE',
		'ReportBasis' => 'ENUMTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
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
		'DisplayReport' => 3.0,
		'ReportDetailLevelFilter' => 3.0,
		'ReportPostingStatusFilter' => 3.0,
		'ReportBasis' => 2.1,
	];

	/**
	 * Field May Be Included Multiple Times
	 * @var bool[]
	 */
	protected $_isRepeatablePaths = [
		'ReportAccountFilter ListID' => true,
		'ReportAccountFilter FullName' => true,
		'ReportEntityFilter ListID' => true,
		'ReportEntityFilter FullName' => true,
		'ReportItemFilter ListID' => true,
		'ReportItemFilter FullName' => true,
		'ReportClassFilter ListID' => true,
		'ReportClassFilter FullName' => true,
		'ReportTxnTypeFilter TxnTypeFilter' => true,
		'IncludeColumn' => true,
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
		'CustomDetailReportType',
		'DisplayReport',
		'ReportPeriod',
		'ReportPeriod FromReportDate',
		'ReportPeriod ToReportDate',
		'ReportDateMacro',
		'ReportAccountFilter',
		'ReportAccountFilter AccountTypeFilter',
		'ReportAccountFilter ListID',
		'ReportAccountFilter FullName',
		'ReportAccountFilter ListIDWithChildren',
		'ReportAccountFilter FullNameWithChildren',
		'ReportEntityFilter',
		'ReportEntityFilter EntityTypeFilter',
		'ReportEntityFilter ListID',
		'ReportEntityFilter FullName',
		'ReportEntityFilter ListIDWithChildren',
		'ReportEntityFilter FullNameWithChildren',
		'ReportItemFilter',
		'ReportItemFilter ItemTypeFilter',
		'ReportItemFilter ListID',
		'ReportItemFilter FullName',
		'ReportItemFilter ListIDWithChildren',
		'ReportItemFilter FullNameWithChildren',
		'ReportClassFilter',
		'ReportClassFilter ListID',
		'ReportClassFilter FullName',
		'ReportClassFilter ListIDWithChildren',
		'ReportClassFilter FullNameWithChildren',
		'ReportTxnTypeFilter',
		'ReportTxnTypeFilter TxnTypeFilter',
		'ReportModifiedDateRangeFilter',
		'ReportModifiedDateRangeFilter FromReportModifiedDate',
		'ReportModifiedDateRangeFilter ToReportModifiedDate',
		'ReportModifiedDateRangeMacro',
		'ReportDetailLevelFilter',
		'ReportPostingStatusFilter',
		'SummarizeRowsBy',
		'IncludeColumn',
		'IncludeAccounts',
		'ReportOpenBalanceAsOf',
		'ReportBasis',
	];
}
