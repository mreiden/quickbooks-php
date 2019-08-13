<?php declare(strict_types=1);

/**
 * Schema object for: AccountAddRq
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
final class AccountAddRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'AccountAdd';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'Name' => 'STRTYPE',
		'IsActive' => 'BOOLTYPE',
		'ParentRef ListID' => 'IDTYPE',
		'ParentRef FullName' => 'STRTYPE',
		'AccountType' => 'ENUMTYPE',
		'DetailAccountType' => 'ENUMTYPE',
		'AccountNumber' => 'STRTYPE',
		'BankNumber' => 'STRTYPE',
		'Desc' => 'STRTYPE',
		'OpenBalance' => 'AMTTYPE',
		'OpenBalanceDate' => 'DATETYPE',
		'SalesTaxCodeRef ListID' => 'IDTYPE',
		'SalesTaxCodeRef FullName' => 'STRTYPE',
		'TaxLineID' => 'INTTYPE',
		'CurrencyRef ListID' => 'IDTYPE',
		'CurrencyRef FullName' => 'STRTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'Name' => 31,
		'AccountNumber' => 7,
		'BankNumber' => 25,
		'Desc' => 200,
		'SalesTaxCodeRef FullName' => 3,
		'CurrencyRef FullName' => 64,
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
		'SalesTaxCodeRef ListID' => 6.0,
		'SalesTaxCodeRef FullName' => 6.0,
		'TaxLineID' => 7.0,
		'CurrencyRef ListID' => 8.0,
		'CurrencyRef FullName' => 8.0,
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
		'IsActive' => ['OE'],
		'DetailAccountType' => ['US','CA','UK','AU'],
		'BankNumber' => ['OE'],
		'SalesTaxCodeRef ListID' => ['US','OE'],
		'SalesTaxCodeRef FullName' => ['US','OE'],
		'TaxLineID' => ['UK','OE'],
		'CurrencyRef ListID' => ['OE'],
		'CurrencyRef FullName' => ['OE'],
		'IncludeRetElement' => ['OE'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'Name',
		'IsActive',
		'ParentRef',
		'ParentRef ListID',
		'ParentRef FullName',
		'AccountType',
		'DetailAccountType',
		'AccountNumber',
		'BankNumber',
		'Desc',
		'OpenBalance',
		'OpenBalanceDate',
		'SalesTaxCodeRef',
		'SalesTaxCodeRef ListID',
		'SalesTaxCodeRef FullName',
		'TaxLineID',
		'CurrencyRef',
		'CurrencyRef ListID',
		'CurrencyRef FullName',
		'IncludeRetElement',
	];
}
