<?php declare(strict_types=1);

/**
 * Schema object for: VehicleMileageQueryRq
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
 * WARNING!!!  This file is auto-generated by QBXML\Schema\Generator and the data/qbxmlops130.xml schema
 */
class VehicleMileageQueryRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = '';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'TxnID' => 'IDTYPE',
			'MaxReturned' => 'INTTYPE',
			'ModifiedDateRangeFilter FromModifiedDate' => 'DATETIMETYPE',
			'ModifiedDateRangeFilter ToModifiedDate' => 'DATETIMETYPE',
			'TxnDateRangeFilter FromTxnDate' => 'DATETYPE',
			'TxnDateRangeFilter ToTxnDate' => 'DATETYPE',
			'TxnDateRangeFilter DateMacro' => 'ENUMTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'TxnID' => 0,
			'MaxReturned' => 0,
			'ModifiedDateRangeFilter FromModifiedDate' => 0,
			'ModifiedDateRangeFilter ToModifiedDate' => 0,
			'TxnDateRangeFilter FromTxnDate' => 0,
			'TxnDateRangeFilter ToTxnDate' => 0,
			'TxnDateRangeFilter DateMacro' => 0,
			'IncludeRetElement' => 50,
		];

		return $paths;
	}

	protected function &_isOptionalPaths(): array
	{
		// This seems broken when a parent is optional but an element is required if the parent is included (See HostQueryRq IncludeMaxCapacity).
		static $paths = []; //'_isOptionalPaths ';

		return $paths;
	}

	protected function &_sinceVersionPaths(): array
	{
		static $paths = [
			'TxnID' => 999.99,
			'MaxReturned' => 999.99,
			'ModifiedDateRangeFilter FromModifiedDate' => 999.99,
			'ModifiedDateRangeFilter ToModifiedDate' => 999.99,
			'TxnDateRangeFilter FromTxnDate' => 999.99,
			'TxnDateRangeFilter ToTxnDate' => 999.99,
			'TxnDateRangeFilter DateMacro' => 999.99,
			'IncludeRetElement' => 999.99,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'TxnID' => true,
			'MaxReturned' => false,
			'ModifiedDateRangeFilter FromModifiedDate' => false,
			'ModifiedDateRangeFilter ToModifiedDate' => false,
			'TxnDateRangeFilter FromTxnDate' => false,
			'TxnDateRangeFilter ToTxnDate' => false,
			'TxnDateRangeFilter DateMacro' => false,
			'IncludeRetElement' => true,
		];

		return $paths;
	}

	/*
	protected function &_inLocalePaths(): array
	{
		static $paths = [
			'FirstName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
			'LastName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
		];

		return $paths;
	}
	*/

	protected function &_reorderPathsPaths(): array
	{
		static $paths = [
			'TxnID',
			'MaxReturned',
			'ModifiedDateRangeFilter',
			'ModifiedDateRangeFilter FromModifiedDate',
			'ModifiedDateRangeFilter ToModifiedDate',
			'TxnDateRangeFilter',
			'TxnDateRangeFilter FromTxnDate',
			'TxnDateRangeFilter ToTxnDate',
			'TxnDateRangeFilter DateMacro',
			'IncludeRetElement',
		];

		return $paths;
	}
}
