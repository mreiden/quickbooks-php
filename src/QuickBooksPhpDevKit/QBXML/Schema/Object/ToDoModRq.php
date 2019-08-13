<?php declare(strict_types=1);

/**
 * Schema object for: ToDoModRq
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
final class ToDoModRq extends AbstractSchemaObject
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
		'ToDoMod ListID' => 'IDTYPE',
		'ToDoMod EditSequence' => 'STRTYPE',
		'ToDoMod Notes' => 'STRTYPE',
		'ToDoMod IsActive' => 'BOOLTYPE',
		'ToDoMod Type' => 'ENUMTYPE',
		'ToDoMod Priority' => 'ENUMTYPE',
		'ToDoMod CustomerRef ListID' => 'IDTYPE',
		'ToDoMod CustomerRef FullName' => 'STRTYPE',
		'ToDoMod EmployeeRef ListID' => 'IDTYPE',
		'ToDoMod EmployeeRef FullName' => 'STRTYPE',
		'ToDoMod LeadRef ListID' => 'IDTYPE',
		'ToDoMod LeadRef FullName' => 'STRTYPE',
		'ToDoMod VendorRef ListID' => 'IDTYPE',
		'ToDoMod VendorRef FullName' => 'STRTYPE',
		'ToDoMod IsDone' => 'BOOLTYPE',
		'ToDoMod ReminderDate' => 'DATETYPE',
		'ToDoMod ReminderTime' => 'TIMEINTERVALTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'ToDoMod EditSequence' => 16,
		'ToDoMod Notes' => 4095,
		'ToDoMod CustomerRef FullName' => 209,
		'ToDoMod EmployeeRef FullName' => 31,
		'ToDoMod LeadRef FullName' => 209,
		'ToDoMod VendorRef FullName' => 41,
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
		'ToDoMod Type' => 13.0,
		'ToDoMod Priority' => 13.0,
		'ToDoMod ReminderTime' => 13.0,
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
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'ToDoMod',
		'ToDoMod ListID',
		'ToDoMod EditSequence',
		'ToDoMod Notes',
		'ToDoMod IsActive',
		'ToDoMod Type',
		'ToDoMod Priority',
		'ToDoMod CustomerRef ListID',
		'ToDoMod CustomerRef FullName',
		'ToDoMod EmployeeRef ListID',
		'ToDoMod EmployeeRef FullName',
		'ToDoMod LeadRef ListID',
		'ToDoMod LeadRef FullName',
		'ToDoMod VendorRef ListID',
		'ToDoMod VendorRef FullName',
		'ToDoMod IsDone',
		'ToDoMod ReminderDate',
		'ToDoMod ReminderTime',
		'IncludeRetElement',
	];
}
