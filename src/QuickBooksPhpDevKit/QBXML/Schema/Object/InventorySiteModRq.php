<?php declare(strict_types=1);

/**
 * Schema object for: InventorySiteModRq
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
final class InventorySiteModRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'InventorySiteMod';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'ListID' => 'IDTYPE',
		'EditSequence' => 'STRTYPE',
		'Name' => 'STRTYPE',
		'IsActive' => 'BOOLTYPE',
		'ParentSiteRef ListID' => 'IDTYPE',
		'ParentSiteRef FullName' => 'STRTYPE',
		'SiteDesc' => 'STRTYPE',
		'Contact' => 'STRTYPE',
		'Phone' => 'STRTYPE',
		'Fax' => 'STRTYPE',
		'Email' => 'STRTYPE',
		'SiteAddress Addr1' => 'STRTYPE',
		'SiteAddress Addr2' => 'STRTYPE',
		'SiteAddress Addr3' => 'STRTYPE',
		'SiteAddress Addr4' => 'STRTYPE',
		'SiteAddress Addr5' => 'STRTYPE',
		'SiteAddress City' => 'STRTYPE',
		'SiteAddress State' => 'STRTYPE',
		'SiteAddress PostalCode' => 'STRTYPE',
		'SiteAddress Country' => 'STRTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'EditSequence' => 16,
		'Name' => 31,
		'ParentSiteRef FullName' => 31,
		'SiteDesc' => 100,
		'Contact' => 41,
		'Phone' => 21,
		'Fax' => 21,
		'Email' => 1023,
		'SiteAddress Addr1' => 41,
		'SiteAddress Addr2' => 41,
		'SiteAddress Addr3' => 41,
		'SiteAddress Addr4' => 41,
		'SiteAddress Addr5' => 41,
		'SiteAddress City' => 31,
		'SiteAddress State' => 21,
		'SiteAddress PostalCode' => 13,
		'SiteAddress Country' => 31,
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
		'ParentSiteRef ListID' => 12.0,
		'ParentSiteRef FullName' => 12.0,
		'SiteAddress Addr4' => 2.0,
		'SiteAddress Addr5' => 6.0,
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
		'ListID',
		'EditSequence',
		'Name',
		'IsActive',
		'ParentSiteRef',
		'ParentSiteRef ListID',
		'ParentSiteRef FullName',
		'SiteDesc',
		'Contact',
		'Phone',
		'Fax',
		'Email',
		'SiteAddress',
		'SiteAddress Addr1',
		'SiteAddress Addr2',
		'SiteAddress Addr3',
		'SiteAddress Addr4',
		'SiteAddress Addr5',
		'SiteAddress City',
		'SiteAddress State',
		'SiteAddress PostalCode',
		'SiteAddress Country',
		'IncludeRetElement',
	];
}
