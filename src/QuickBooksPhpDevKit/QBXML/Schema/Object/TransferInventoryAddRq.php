<?php declare(strict_types=1);

/**
 * Schema object for: TransferInventoryAddRq
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
final class TransferInventoryAddRq extends AbstractSchemaObject
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
		'TransferInventoryAdd TxnDate' => 'DATETYPE',
		'TransferInventoryAdd RefNumber' => 'STRTYPE',
		'TransferInventoryAdd FromInventorySiteRef ListID' => 'IDTYPE',
		'TransferInventoryAdd FromInventorySiteRef FullName' => 'STRTYPE',
		'TransferInventoryAdd ToInventorySiteRef ListID' => 'IDTYPE',
		'TransferInventoryAdd ToInventorySiteRef FullName' => 'STRTYPE',
		'TransferInventoryAdd Memo' => 'STRTYPE',
		'TransferInventoryAdd ExternalGUID' => 'GUIDTYPE',
		'TransferInventoryAdd TransferInventoryLineAdd ItemRef ListID' => 'IDTYPE',
		'TransferInventoryAdd TransferInventoryLineAdd ItemRef FullName' => 'STRTYPE',
		'TransferInventoryAdd TransferInventoryLineAdd FromInventorySiteLocationRef ListID' => 'IDTYPE',
		'TransferInventoryAdd TransferInventoryLineAdd FromInventorySiteLocationRef FullName' => 'STRTYPE',
		'TransferInventoryAdd TransferInventoryLineAdd ToInventorySiteLocationRef ListID' => 'IDTYPE',
		'TransferInventoryAdd TransferInventoryLineAdd ToInventorySiteLocationRef FullName' => 'STRTYPE',
		'TransferInventoryAdd TransferInventoryLineAdd QuantityToTransfer' => 'QUANTYPE',
		'TransferInventoryAdd TransferInventoryLineAdd SerialNumber' => 'STRTYPE',
		'TransferInventoryAdd TransferInventoryLineAdd LotNumber' => 'STRTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'TransferInventoryAdd RefNumber' => 11,
		'TransferInventoryAdd FromInventorySiteRef FullName' => 31,
		'TransferInventoryAdd ToInventorySiteRef FullName' => 31,
		'TransferInventoryAdd Memo' => 4095,
		'TransferInventoryAdd TransferInventoryLineAdd FromInventorySiteLocationRef FullName' => 31,
		'TransferInventoryAdd TransferInventoryLineAdd ToInventorySiteLocationRef FullName' => 31,
		'TransferInventoryAdd TransferInventoryLineAdd SerialNumber' => 4095,
		'TransferInventoryAdd TransferInventoryLineAdd LotNumber' => 40,
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
		'TransferInventoryAdd TransferInventoryLineAdd FromInventorySiteLocationRef ListID' => 12.0,
		'TransferInventoryAdd TransferInventoryLineAdd FromInventorySiteLocationRef FullName' => 12.0,
		'TransferInventoryAdd TransferInventoryLineAdd ToInventorySiteLocationRef ListID' => 12.0,
		'TransferInventoryAdd TransferInventoryLineAdd ToInventorySiteLocationRef FullName' => 12.0,
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
		'TransferInventoryAdd',
		'TransferInventoryAdd TxnDate',
		'TransferInventoryAdd RefNumber',
		'TransferInventoryAdd FromInventorySiteRef ListID',
		'TransferInventoryAdd FromInventorySiteRef FullName',
		'TransferInventoryAdd ToInventorySiteRef ListID',
		'TransferInventoryAdd ToInventorySiteRef FullName',
		'TransferInventoryAdd Memo',
		'TransferInventoryAdd ExternalGUID',
		'TransferInventoryAdd',
		'TransferInventoryAdd TransferInventoryLineAdd',
		'TransferInventoryAdd TransferInventoryLineAdd ItemRef',
		'TransferInventoryAdd TransferInventoryLineAdd ItemRef ListID',
		'TransferInventoryAdd TransferInventoryLineAdd ItemRef FullName',
		'TransferInventoryAdd TransferInventoryLineAdd FromInventorySiteLocationRef ListID',
		'TransferInventoryAdd TransferInventoryLineAdd FromInventorySiteLocationRef FullName',
		'TransferInventoryAdd TransferInventoryLineAdd ToInventorySiteLocationRef ListID',
		'TransferInventoryAdd TransferInventoryLineAdd ToInventorySiteLocationRef FullName',
		'TransferInventoryAdd TransferInventoryLineAdd QuantityToTransfer',
		'TransferInventoryAdd TransferInventoryLineAdd SerialNumber',
		'TransferInventoryAdd TransferInventoryLineAdd LotNumber',
		'IncludeRetElement',
	];
}
