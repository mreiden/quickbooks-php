<?php declare(strict_types=1);

/**
 * Schema object for: LeadAddRq
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
final class LeadAddRq extends AbstractSchemaObject
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
		'LeadAdd FullName' => 'STRTYPE',
		'LeadAdd Status' => 'ENUMTYPE',
		'LeadAdd CompanyName' => 'STRTYPE',
		'LeadAdd MainPhone' => 'STRTYPE',
		'LeadAdd AdditionalContactRef ContactName' => 'STRTYPE',
		'LeadAdd AdditionalContactRef ContactValue' => 'STRTYPE',
		'LeadAdd Locations Location' => 'STRTYPE',
		'LeadAdd Locations LeadAddress Addr1' => 'STRTYPE',
		'LeadAdd Locations LeadAddress Addr2' => 'STRTYPE',
		'LeadAdd Locations LeadAddress Addr3' => 'STRTYPE',
		'LeadAdd Locations LeadAddress Addr4' => 'STRTYPE',
		'LeadAdd Locations LeadAddress Addr5' => 'STRTYPE',
		'LeadAdd LeadContacts Salutation' => 'STRTYPE',
		'LeadAdd LeadContacts FirstName' => 'STRTYPE',
		'LeadAdd LeadContacts MiddleName' => 'STRTYPE',
		'LeadAdd LeadContacts LastName' => 'STRTYPE',
		'LeadAdd LeadContacts JobTitle' => 'STRTYPE',
		'LeadAdd LeadContacts AdditionalContactRef ContactName' => 'STRTYPE',
		'LeadAdd LeadContacts AdditionalContactRef ContactValue' => 'STRTYPE',
		'LeadAdd LeadContacts IsPrimaryContact' => 'BOOLTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'LeadAdd FullName' => 41,
		'LeadAdd CompanyName' => 41,
		'LeadAdd MainPhone' => 21,
		'LeadAdd AdditionalContactRef ContactName' => 40,
		'LeadAdd AdditionalContactRef ContactValue' => 255,
		'LeadAdd Locations Location' => 32,
		'LeadAdd Locations LeadAddress Addr1' => 41,
		'LeadAdd Locations LeadAddress Addr2' => 41,
		'LeadAdd Locations LeadAddress Addr3' => 41,
		'LeadAdd Locations LeadAddress Addr4' => 41,
		'LeadAdd Locations LeadAddress Addr5' => 41,
		'LeadAdd LeadContacts Salutation' => 15,
		'LeadAdd LeadContacts FirstName' => 25,
		'LeadAdd LeadContacts MiddleName' => 5,
		'LeadAdd LeadContacts LastName' => 25,
		'LeadAdd LeadContacts JobTitle' => 41,
		'LeadAdd LeadContacts AdditionalContactRef ContactName' => 40,
		'LeadAdd LeadContacts AdditionalContactRef ContactValue' => 255,
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
		'LeadAdd Locations LeadAddress Addr4' => 2.0,
		'LeadAdd Locations LeadAddress Addr5' => 6.0,
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
		'LeadAdd',
		'LeadAdd FullName',
		'LeadAdd Status',
		'LeadAdd CompanyName',
		'LeadAdd MainPhone',
		'LeadAdd AdditionalContactRef ContactName',
		'LeadAdd AdditionalContactRef ContactValue',
		'LeadAdd Locations Location',
		'LeadAdd Locations LeadAddress Addr1',
		'LeadAdd Locations LeadAddress Addr2',
		'LeadAdd Locations LeadAddress Addr3',
		'LeadAdd Locations LeadAddress Addr4',
		'LeadAdd Locations LeadAddress Addr5',
		'LeadAdd LeadContacts Salutation',
		'LeadAdd LeadContacts FirstName',
		'LeadAdd LeadContacts MiddleName',
		'LeadAdd LeadContacts LastName',
		'LeadAdd LeadContacts JobTitle',
		'LeadAdd LeadContacts AdditionalContactRef ContactName',
		'LeadAdd LeadContacts AdditionalContactRef ContactValue',
		'LeadAdd LeadContacts IsPrimaryContact',
		'IncludeRetElement',
	];
}
