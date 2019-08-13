<?php declare(strict_types=1);

/**
 * Schema object for: TaxCategoryAddRq
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
 * WARNING!!!: This file is generated by QuickBooksPhpDevKit\QBXML\Schema\Generator using the /data/qbposxmlops30.xml file in this package.
 */
final class TaxCategoryAddRq extends AbstractSchemaObject
{
	/**
	 * This QuickBooks Action is found only in the QuickBooks Point of Sale (QBPOS) sdk.
	 * @var bool
	 */
	protected $_isQbxmlPosOnly = true;

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
		'TaxCategoryAdd TaxCategory' => 'STRTYPE',
		'TaxCategoryAdd ShippingTaxCode' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'TaxCategoryAdd TaxCategory' => 20,
		'TaxCategoryAdd ShippingTaxCode' => 3,
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
		'TaxCategoryAdd',
		'TaxCategoryAdd TaxCategory',
		'TaxCategoryAdd ShippingTaxCode',
	];
}
