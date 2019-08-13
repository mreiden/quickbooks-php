<?php declare(strict_types=1);

/**
 * Schema object for: EntityQueryRq
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
final class EntityQueryRq extends AbstractSchemaObject
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
		'ListID' => 'IDTYPE',
		'FullName' => 'STRTYPE',
		'MaxReturned' => 'INTTYPE',
		'ActiveStatus' => 'ENUMTYPE',
		'FromModifiedDate' => 'DATETIMETYPE',
		'ToModifiedDate' => 'DATETIMETYPE',
		'NameFilter MatchCriterion' => 'ENUMTYPE',
		'NameFilter Name' => 'STRTYPE',
		'NameRangeFilter FromName' => 'STRTYPE',
		'NameRangeFilter ToName' => 'STRTYPE',
		'IncludeRetElement' => 'STRTYPE',
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
		'IncludeRetElement' => 4.0,
	];

	/**
	 * Field May Be Included Multiple Times
	 * @var bool[]
	 */
	protected $_isRepeatablePaths = [
		'ListID' => true,
		'FullName' => true,
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
		'ActiveStatus' => ['OE'],
		'IncludeRetElement' => ['OE'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'ListID',
		'FullName',
		'MaxReturned',
		'ActiveStatus',
		'FromModifiedDate',
		'ToModifiedDate',
		'NameFilter',
		'NameFilter MatchCriterion',
		'NameFilter Name',
		'NameRangeFilter',
		'NameRangeFilter FromName',
		'NameRangeFilter ToName',
		'IncludeRetElement',
	];
}
