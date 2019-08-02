<?php declare(strict_types=1);

/**
 * Schema object for: BudgetSummaryReportQueryRq
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
class BudgetSummaryReportQueryRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = '';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'BudgetSummaryReportType' => 'ENUMTYPE',
			'DisplayReport' => 'BOOLTYPE',
			'FiscalYear' => 'INTTYPE',
			'BudgetCriterion' => 'ENUMTYPE',
			'ReportPeriod FromReportDate' => 'DATETYPE',
			'ReportPeriod ToReportDate' => 'DATETYPE',
			'ReportDateMacro' => 'ENUMTYPE',
			'ReportClassFilter ListID' => 'IDTYPE',
			'ReportClassFilter FullName' => 'STRTYPE',
			'ReportClassFilter ListIDWithChildren' => 'IDTYPE',
			'ReportClassFilter FullNameWithChildren' => 'STRTYPE',
			'SummarizeBudgetColumnsBy' => 'ENUMTYPE',
			'SummarizeBudgetRowsBy' => 'ENUMTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'BudgetSummaryReportType' => 0,
			'DisplayReport' => 0,
			'FiscalYear' => 0,
			'BudgetCriterion' => 0,
			'ReportPeriod FromReportDate' => 0,
			'ReportPeriod ToReportDate' => 0,
			'ReportDateMacro' => 0,
			'ReportClassFilter ListID' => 0,
			'ReportClassFilter FullName' => 0,
			'ReportClassFilter ListIDWithChildren' => 0,
			'ReportClassFilter FullNameWithChildren' => 0,
			'SummarizeBudgetColumnsBy' => 0,
			'SummarizeBudgetRowsBy' => 0,
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
			'BudgetSummaryReportType' => 999.99,
			'DisplayReport' => 999.99,
			'FiscalYear' => 999.99,
			'BudgetCriterion' => 999.99,
			'ReportPeriod FromReportDate' => 999.99,
			'ReportPeriod ToReportDate' => 999.99,
			'ReportDateMacro' => 999.99,
			'ReportClassFilter ListID' => 999.99,
			'ReportClassFilter FullName' => 999.99,
			'ReportClassFilter ListIDWithChildren' => 999.99,
			'ReportClassFilter FullNameWithChildren' => 999.99,
			'SummarizeBudgetColumnsBy' => 999.99,
			'SummarizeBudgetRowsBy' => 999.99,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'BudgetSummaryReportType' => false,
			'DisplayReport' => false,
			'FiscalYear' => false,
			'BudgetCriterion' => false,
			'ReportPeriod FromReportDate' => false,
			'ReportPeriod ToReportDate' => false,
			'ReportDateMacro' => false,
			'ReportClassFilter ListID' => true,
			'ReportClassFilter FullName' => true,
			'ReportClassFilter ListIDWithChildren' => false,
			'ReportClassFilter FullNameWithChildren' => false,
			'SummarizeBudgetColumnsBy' => false,
			'SummarizeBudgetRowsBy' => false,
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
			'BudgetSummaryReportType',
			'DisplayReport',
			'FiscalYear',
			'BudgetCriterion',
			'ReportPeriod',
			'ReportPeriod FromReportDate',
			'ReportPeriod ToReportDate',
			'ReportDateMacro',
			'ReportClassFilter',
			'ReportClassFilter ListID',
			'ReportClassFilter FullName',
			'ReportClassFilter ListIDWithChildren',
			'ReportClassFilter FullNameWithChildren',
			'SummarizeBudgetColumnsBy',
			'SummarizeBudgetRowsBy',
		];

		return $paths;
	}
}
