<?php declare(strict_types=1);

/**
 * Schema object for: GeneralDetailReportQueryRq
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
class GeneralDetailReportQueryRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = '';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'GeneralDetailReportType' => 'ENUMTYPE',
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

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'GeneralDetailReportType' => 0,
			'DisplayReport' => 0,
			'ReportPeriod FromReportDate' => 0,
			'ReportPeriod ToReportDate' => 0,
			'ReportDateMacro' => 0,
			'ReportAccountFilter AccountTypeFilter' => 0,
			'ReportAccountFilter ListID' => 0,
			'ReportAccountFilter FullName' => 0,
			'ReportAccountFilter ListIDWithChildren' => 0,
			'ReportAccountFilter FullNameWithChildren' => 0,
			'ReportEntityFilter EntityTypeFilter' => 0,
			'ReportEntityFilter ListID' => 0,
			'ReportEntityFilter FullName' => 0,
			'ReportEntityFilter ListIDWithChildren' => 0,
			'ReportEntityFilter FullNameWithChildren' => 0,
			'ReportItemFilter ItemTypeFilter' => 0,
			'ReportItemFilter ListID' => 0,
			'ReportItemFilter FullName' => 0,
			'ReportItemFilter ListIDWithChildren' => 0,
			'ReportItemFilter FullNameWithChildren' => 0,
			'ReportClassFilter ListID' => 0,
			'ReportClassFilter FullName' => 0,
			'ReportClassFilter ListIDWithChildren' => 0,
			'ReportClassFilter FullNameWithChildren' => 0,
			'ReportTxnTypeFilter TxnTypeFilter' => 0,
			'ReportModifiedDateRangeFilter FromReportModifiedDate' => 0,
			'ReportModifiedDateRangeFilter ToReportModifiedDate' => 0,
			'ReportModifiedDateRangeMacro' => 0,
			'ReportDetailLevelFilter' => 0,
			'ReportPostingStatusFilter' => 0,
			'SummarizeRowsBy' => 0,
			'IncludeColumn' => 0,
			'IncludeAccounts' => 0,
			'ReportOpenBalanceAsOf' => 0,
			'ReportBasis' => 0,
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
			'GeneralDetailReportType' => 999.99,
			'DisplayReport' => 3.0,
			'ReportPeriod FromReportDate' => 999.99,
			'ReportPeriod ToReportDate' => 999.99,
			'ReportDateMacro' => 999.99,
			'ReportAccountFilter AccountTypeFilter' => 999.99,
			'ReportAccountFilter ListID' => 999.99,
			'ReportAccountFilter FullName' => 999.99,
			'ReportAccountFilter ListIDWithChildren' => 999.99,
			'ReportAccountFilter FullNameWithChildren' => 999.99,
			'ReportEntityFilter EntityTypeFilter' => 999.99,
			'ReportEntityFilter ListID' => 999.99,
			'ReportEntityFilter FullName' => 999.99,
			'ReportEntityFilter ListIDWithChildren' => 999.99,
			'ReportEntityFilter FullNameWithChildren' => 999.99,
			'ReportItemFilter ItemTypeFilter' => 999.99,
			'ReportItemFilter ListID' => 999.99,
			'ReportItemFilter FullName' => 999.99,
			'ReportItemFilter ListIDWithChildren' => 999.99,
			'ReportItemFilter FullNameWithChildren' => 999.99,
			'ReportClassFilter ListID' => 999.99,
			'ReportClassFilter FullName' => 999.99,
			'ReportClassFilter ListIDWithChildren' => 999.99,
			'ReportClassFilter FullNameWithChildren' => 999.99,
			'ReportTxnTypeFilter TxnTypeFilter' => 999.99,
			'ReportModifiedDateRangeFilter FromReportModifiedDate' => 999.99,
			'ReportModifiedDateRangeFilter ToReportModifiedDate' => 999.99,
			'ReportModifiedDateRangeMacro' => 999.99,
			'ReportDetailLevelFilter' => 3.0,
			'ReportPostingStatusFilter' => 3.0,
			'SummarizeRowsBy' => 999.99,
			'IncludeColumn' => 999.99,
			'IncludeAccounts' => 999.99,
			'ReportOpenBalanceAsOf' => 999.99,
			'ReportBasis' => 2.1,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'GeneralDetailReportType' => false,
			'DisplayReport' => false,
			'ReportPeriod FromReportDate' => false,
			'ReportPeriod ToReportDate' => false,
			'ReportDateMacro' => false,
			'ReportAccountFilter AccountTypeFilter' => false,
			'ReportAccountFilter ListID' => true,
			'ReportAccountFilter FullName' => true,
			'ReportAccountFilter ListIDWithChildren' => false,
			'ReportAccountFilter FullNameWithChildren' => false,
			'ReportEntityFilter EntityTypeFilter' => false,
			'ReportEntityFilter ListID' => true,
			'ReportEntityFilter FullName' => true,
			'ReportEntityFilter ListIDWithChildren' => false,
			'ReportEntityFilter FullNameWithChildren' => false,
			'ReportItemFilter ItemTypeFilter' => false,
			'ReportItemFilter ListID' => true,
			'ReportItemFilter FullName' => true,
			'ReportItemFilter ListIDWithChildren' => false,
			'ReportItemFilter FullNameWithChildren' => false,
			'ReportClassFilter ListID' => true,
			'ReportClassFilter FullName' => true,
			'ReportClassFilter ListIDWithChildren' => false,
			'ReportClassFilter FullNameWithChildren' => false,
			'ReportTxnTypeFilter TxnTypeFilter' => true,
			'ReportModifiedDateRangeFilter FromReportModifiedDate' => false,
			'ReportModifiedDateRangeFilter ToReportModifiedDate' => false,
			'ReportModifiedDateRangeMacro' => false,
			'ReportDetailLevelFilter' => false,
			'ReportPostingStatusFilter' => false,
			'SummarizeRowsBy' => false,
			'IncludeColumn' => true,
			'IncludeAccounts' => false,
			'ReportOpenBalanceAsOf' => false,
			'ReportBasis' => false,
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
			'GeneralDetailReportType',
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

		return $paths;
	}
}