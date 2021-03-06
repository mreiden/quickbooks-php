<?php declare(strict_types=1);

/**
 * Schema object for: PriceAdjustmentAddRq
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
final class PriceAdjustmentAddRq extends AbstractSchemaObject
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
		'PriceAdjustmentAdd PriceAdjustmentName' => 'STRTYPE',
		'PriceAdjustmentAdd Comments' => 'STRTYPE',
		'PriceAdjustmentAdd Associate' => 'STRTYPE',
		'PriceAdjustmentAdd PriceLevelNumber' => 'ENUMTYPE',
		'PriceAdjustmentAdd PriceAdjustmentItemAdd ListID' => 'IDTYPE',
		'PriceAdjustmentAdd PriceAdjustmentItemAdd NewPrice' => 'AMTTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'PriceAdjustmentAdd PriceAdjustmentName' => 32,
		'PriceAdjustmentAdd Comments' => 300,
		'PriceAdjustmentAdd Associate' => 40,
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
		'IncludeRetElement' => 2.5,
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
		'PriceAdjustmentAdd',
		'PriceAdjustmentAdd PriceAdjustmentName',
		'PriceAdjustmentAdd Comments',
		'PriceAdjustmentAdd Associate',
		'PriceAdjustmentAdd PriceLevelNumber',
		'PriceAdjustmentAdd PriceAdjustmentItemAdd ListID',
		'PriceAdjustmentAdd PriceAdjustmentItemAdd NewPrice',
		'IncludeRetElement',
	];
}
