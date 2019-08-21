<?php declare(strict_types=1);

/**
 * QuickBooks GeneralDetailReport object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\{
	PackageInfo,
	QBXML\AbstractQbxmlObject,
};

/**
 * QBXML\Object\GeneralDetailReport class definition
 */
class GeneralDetailReport extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks GeneralDetailReport object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_GENERALDETAILREPORT'];
	}
}
