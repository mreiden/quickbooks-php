<?php declare(strict_types=1);

/**
 * Schema object for: TransferModRq
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
class TransferModRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = '';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'TransferMod TxnID' => 'IDTYPE',
			'TransferMod EditSequence' => 'STRTYPE',
			'TransferMod TxnDate' => 'DATETYPE',
			'TransferMod TransferFromAccountRef ListID' => 'IDTYPE',
			'TransferMod TransferFromAccountRef FullName' => 'STRTYPE',
			'TransferMod TransferToAccountRef ListID' => 'IDTYPE',
			'TransferMod TransferToAccountRef FullName' => 'STRTYPE',
			'TransferMod ClassRef ListID' => 'IDTYPE',
			'TransferMod ClassRef FullName' => 'STRTYPE',
			'TransferMod Amount' => 'AMTTYPE',
			'TransferMod Memo' => 'STRTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'TransferMod TxnID' => 0,
			'TransferMod EditSequence' => 16,
			'TransferMod TxnDate' => 0,
			'TransferMod TransferFromAccountRef ListID' => 0,
			'TransferMod TransferFromAccountRef FullName' => 159,
			'TransferMod TransferToAccountRef ListID' => 0,
			'TransferMod TransferToAccountRef FullName' => 159,
			'TransferMod ClassRef ListID' => 0,
			'TransferMod ClassRef FullName' => 159,
			'TransferMod Amount' => 0,
			'TransferMod Memo' => 4095,
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
			'TransferMod TxnID' => 999.99,
			'TransferMod EditSequence' => 999.99,
			'TransferMod TxnDate' => 999.99,
			'TransferMod TransferFromAccountRef ListID' => 999.99,
			'TransferMod TransferFromAccountRef FullName' => 999.99,
			'TransferMod TransferToAccountRef ListID' => 999.99,
			'TransferMod TransferToAccountRef FullName' => 999.99,
			'TransferMod ClassRef ListID' => 999.99,
			'TransferMod ClassRef FullName' => 999.99,
			'TransferMod Amount' => 999.99,
			'TransferMod Memo' => 999.99,
			'IncludeRetElement' => 999.99,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'TransferMod TxnID' => false,
			'TransferMod EditSequence' => false,
			'TransferMod TxnDate' => false,
			'TransferMod TransferFromAccountRef ListID' => false,
			'TransferMod TransferFromAccountRef FullName' => false,
			'TransferMod TransferToAccountRef ListID' => false,
			'TransferMod TransferToAccountRef FullName' => false,
			'TransferMod ClassRef ListID' => false,
			'TransferMod ClassRef FullName' => false,
			'TransferMod Amount' => false,
			'TransferMod Memo' => false,
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
			'TransferMod',
			'TransferMod TxnID',
			'TransferMod EditSequence',
			'TransferMod TxnDate',
			'TransferMod TransferFromAccountRef ListID',
			'TransferMod TransferFromAccountRef FullName',
			'TransferMod TransferToAccountRef ListID',
			'TransferMod TransferToAccountRef FullName',
			'TransferMod ClassRef ListID',
			'TransferMod ClassRef FullName',
			'TransferMod Amount',
			'TransferMod Memo',
			'IncludeRetElement',
		];

		return $paths;
	}
}