<?php declare(strict_types=1);

/**
 * Schema object for: ItemPaymentAddRq
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
final class ItemPaymentAddRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'ItemPaymentAdd';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'Name' => 'STRTYPE',
		'BarCode BarCodeValue' => 'STRTYPE',
		'BarCode AssignEvenIfUsed' => 'BOOLTYPE',
		'BarCode AllowOverride' => 'BOOLTYPE',
		'IsActive' => 'BOOLTYPE',
		'ClassRef ListID' => 'IDTYPE',
		'ClassRef FullName' => 'STRTYPE',
		'ItemDesc' => 'STRTYPE',
		'DepositToAccountRef ListID' => 'IDTYPE',
		'DepositToAccountRef FullName' => 'STRTYPE',
		'PaymentMethodRef ListID' => 'IDTYPE',
		'PaymentMethodRef FullName' => 'STRTYPE',
		'ExternalGUID' => 'GUIDTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'Name' => 31,
		'BarCode BarCodeValue' => 50,
		'ClassRef FullName' => 159,
		'ItemDesc' => 4095,
		'DepositToAccountRef FullName' => 159,
		'PaymentMethodRef FullName' => 31,
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
		'BarCode BarCodeValue' => 12.0,
		'BarCode AssignEvenIfUsed' => 12.0,
		'BarCode AllowOverride' => 12.0,
		'ClassRef ListID' => 12.0,
		'ClassRef FullName' => 12.0,
		'ExternalGUID' => 8.0,
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
		'BarCode BarCodeValue' => ['AU'],
		'BarCode AssignEvenIfUsed' => ['AU'],
		'BarCode AllowOverride' => ['AU'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'Name',
		'BarCode',
		'BarCode BarCodeValue',
		'BarCode AssignEvenIfUsed',
		'BarCode AllowOverride',
		'IsActive',
		'ClassRef',
		'ClassRef ListID',
		'ClassRef FullName',
		'ItemDesc',
		'DepositToAccountRef',
		'DepositToAccountRef ListID',
		'DepositToAccountRef FullName',
		'PaymentMethodRef',
		'PaymentMethodRef ListID',
		'PaymentMethodRef FullName',
		'ExternalGUID',
		'IncludeRetElement',
	];
}
