<?php declare(strict_types=1);

/**
 * QuickBooks GeneralSummaryReport object container
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
 * QBXML\Object\GeneralSummaryReport class definition
 */
class GeneralSummaryReport extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks GeneralSummaryReport object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ReportType
	 */
	public function setReportType(string $type): bool
	{
		return $this->set('GeneralSummaryReportType', $type);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_GENERALSUMMARYREPORT'];
	}
}
