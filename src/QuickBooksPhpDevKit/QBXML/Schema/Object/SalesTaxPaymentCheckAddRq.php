<?php declare(strict_types=1);

/**
 * Schema object for: SalesTaxPaymentCheckAddRq
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
final class SalesTaxPaymentCheckAddRq extends AbstractSchemaObject
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
		'SalesTaxPaymentCheckAdd PayeeEntityRef ListID' => 'IDTYPE',
		'SalesTaxPaymentCheckAdd PayeeEntityRef FullName' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd TxnDate' => 'DATETYPE',
		'SalesTaxPaymentCheckAdd BankAccountRef ListID' => 'IDTYPE',
		'SalesTaxPaymentCheckAdd BankAccountRef FullName' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd IsToBePrinted' => 'BOOLTYPE',
		'SalesTaxPaymentCheckAdd RefNumber' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Memo' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address Addr1' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address Addr2' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address Addr3' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address Addr4' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address Addr5' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address City' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address State' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address PostalCode' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address Country' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd Address Note' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd ExternalGUID' => 'GUIDTYPE',
		'SalesTaxPaymentCheckAdd SalesTaxPaymentCheckLineAdd ItemSalesTaxRef ListID' => 'IDTYPE',
		'SalesTaxPaymentCheckAdd SalesTaxPaymentCheckLineAdd ItemSalesTaxRef FullName' => 'STRTYPE',
		'SalesTaxPaymentCheckAdd SalesTaxPaymentCheckLineAdd Amount' => 'AMTTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'SalesTaxPaymentCheckAdd PayeeEntityRef FullName' => 209,
		'SalesTaxPaymentCheckAdd BankAccountRef FullName' => 159,
		'SalesTaxPaymentCheckAdd Memo' => 4095,
		'SalesTaxPaymentCheckAdd Address Addr1' => 41,
		'SalesTaxPaymentCheckAdd Address Addr2' => 41,
		'SalesTaxPaymentCheckAdd Address Addr3' => 41,
		'SalesTaxPaymentCheckAdd Address Addr4' => 41,
		'SalesTaxPaymentCheckAdd Address Addr5' => 41,
		'SalesTaxPaymentCheckAdd Address City' => 31,
		'SalesTaxPaymentCheckAdd Address State' => 21,
		'SalesTaxPaymentCheckAdd Address PostalCode' => 13,
		'SalesTaxPaymentCheckAdd Address Country' => 31,
		'SalesTaxPaymentCheckAdd Address Note' => 41,
		'SalesTaxPaymentCheckAdd SalesTaxPaymentCheckLineAdd ItemSalesTaxRef FullName' => 31,
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
		'SalesTaxPaymentCheckAdd Memo' => 12.0,
		'SalesTaxPaymentCheckAdd Address Addr1' => 12.0,
		'SalesTaxPaymentCheckAdd Address Addr2' => 12.0,
		'SalesTaxPaymentCheckAdd Address Addr3' => 12.0,
		'SalesTaxPaymentCheckAdd Address Addr4' => 12.0,
		'SalesTaxPaymentCheckAdd Address Addr5' => 12.0,
		'SalesTaxPaymentCheckAdd Address City' => 12.0,
		'SalesTaxPaymentCheckAdd Address State' => 12.0,
		'SalesTaxPaymentCheckAdd Address PostalCode' => 12.0,
		'SalesTaxPaymentCheckAdd Address Country' => 12.0,
		'SalesTaxPaymentCheckAdd Address Note' => 12.0,
		'SalesTaxPaymentCheckAdd ExternalGUID' => 9.0,
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
		'SalesTaxPaymentCheckAdd',
		'SalesTaxPaymentCheckAdd PayeeEntityRef',
		'SalesTaxPaymentCheckAdd PayeeEntityRef ListID',
		'SalesTaxPaymentCheckAdd PayeeEntityRef FullName',
		'SalesTaxPaymentCheckAdd TxnDate',
		'SalesTaxPaymentCheckAdd BankAccountRef ListID',
		'SalesTaxPaymentCheckAdd BankAccountRef FullName',
		'SalesTaxPaymentCheckAdd IsToBePrinted',
		'SalesTaxPaymentCheckAdd RefNumber',
		'SalesTaxPaymentCheckAdd Memo',
		'SalesTaxPaymentCheckAdd Address Addr1',
		'SalesTaxPaymentCheckAdd Address Addr2',
		'SalesTaxPaymentCheckAdd Address Addr3',
		'SalesTaxPaymentCheckAdd Address Addr4',
		'SalesTaxPaymentCheckAdd Address Addr5',
		'SalesTaxPaymentCheckAdd Address City',
		'SalesTaxPaymentCheckAdd Address State',
		'SalesTaxPaymentCheckAdd Address PostalCode',
		'SalesTaxPaymentCheckAdd Address Country',
		'SalesTaxPaymentCheckAdd Address Note',
		'SalesTaxPaymentCheckAdd ExternalGUID',
		'SalesTaxPaymentCheckAdd',
		'SalesTaxPaymentCheckAdd SalesTaxPaymentCheckLineAdd',
		'SalesTaxPaymentCheckAdd SalesTaxPaymentCheckLineAdd ItemSalesTaxRef',
		'SalesTaxPaymentCheckAdd SalesTaxPaymentCheckLineAdd ItemSalesTaxRef ListID',
		'SalesTaxPaymentCheckAdd SalesTaxPaymentCheckLineAdd ItemSalesTaxRef FullName',
		'SalesTaxPaymentCheckAdd SalesTaxPaymentCheckLineAdd Amount',
		'IncludeRetElement',
	];
}
