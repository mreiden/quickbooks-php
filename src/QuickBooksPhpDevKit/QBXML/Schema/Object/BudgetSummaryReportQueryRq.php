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
 * WARNING!!!: This file is generated by QuickBooksPhpDevKit\QBXML\Schema\Generator using the /data/qbxmlops130.xml file in this package.
 */
final class BudgetSummaryReportQueryRq extends AbstractSchemaObject
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
	];

	/**
	 * Field May Be Included Multiple Times
	 * @var bool[]
	 */
	protected $_isRepeatablePaths = [
		'ReportClassFilter ListID' => true,
		'ReportClassFilter FullName' => true,
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
}
