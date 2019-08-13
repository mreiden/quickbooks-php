<?php declare(strict_types=1);

/**
 * Schema object for: OtherNameAddRq
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
final class OtherNameAddRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'OtherNameAdd';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'Name' => 'STRTYPE',
		'IsActive' => 'BOOLTYPE',
		'CompanyName' => 'STRTYPE',
		'Salutation' => 'STRTYPE',
		'FirstName' => 'STRTYPE',
		'MiddleName' => 'STRTYPE',
		'LastName' => 'STRTYPE',
		'OtherNameAddress Addr1' => 'STRTYPE',
		'OtherNameAddress Addr2' => 'STRTYPE',
		'OtherNameAddress Addr3' => 'STRTYPE',
		'OtherNameAddress Addr4' => 'STRTYPE',
		'OtherNameAddress Addr5' => 'STRTYPE',
		'OtherNameAddress City' => 'STRTYPE',
		'OtherNameAddress State' => 'STRTYPE',
		'OtherNameAddress PostalCode' => 'STRTYPE',
		'OtherNameAddress Country' => 'STRTYPE',
		'OtherNameAddress Note' => 'STRTYPE',
		'Phone' => 'STRTYPE',
		'AltPhone' => 'STRTYPE',
		'Fax' => 'STRTYPE',
		'Email' => 'STRTYPE',
		'Contact' => 'STRTYPE',
		'AltContact' => 'STRTYPE',
		'AccountNumber' => 'STRTYPE',
		'Notes' => 'STRTYPE',
		'ExternalGUID' => 'GUIDTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'Name' => 41,
		'CompanyName' => 41,
		'Salutation' => 15,
		'FirstName' => 25,
		'MiddleName' => 5,
		'LastName' => 25,
		'OtherNameAddress Addr1' => 41,
		'OtherNameAddress Addr2' => 41,
		'OtherNameAddress Addr3' => 41,
		'OtherNameAddress Addr4' => 41,
		'OtherNameAddress Addr5' => 41,
		'OtherNameAddress City' => 31,
		'OtherNameAddress State' => 21,
		'OtherNameAddress PostalCode' => 13,
		'OtherNameAddress Country' => 31,
		'OtherNameAddress Note' => 41,
		'Phone' => 21,
		'AltPhone' => 21,
		'Fax' => 21,
		'Email' => 1023,
		'Contact' => 41,
		'AltContact' => 41,
		'AccountNumber' => 99,
		'Notes' => 4095,
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
		'OtherNameAddress Addr4' => 2.0,
		'OtherNameAddress Addr5' => 6.0,
		'OtherNameAddress Note' => 6.0,
		'Notes' => 3.0,
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
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'Name',
		'IsActive',
		'CompanyName',
		'Salutation',
		'FirstName',
		'MiddleName',
		'LastName',
		'OtherNameAddress',
		'OtherNameAddress Addr1',
		'OtherNameAddress Addr2',
		'OtherNameAddress Addr3',
		'OtherNameAddress Addr4',
		'OtherNameAddress Addr5',
		'OtherNameAddress City',
		'OtherNameAddress State',
		'OtherNameAddress PostalCode',
		'OtherNameAddress Country',
		'OtherNameAddress Note',
		'Phone',
		'AltPhone',
		'Fax',
		'Email',
		'Contact',
		'AltContact',
		'AccountNumber',
		'Notes',
		'ExternalGUID',
		'IncludeRetElement',
	];
}
