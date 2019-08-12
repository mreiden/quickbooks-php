<?php declare(strict_types=1);

/**
 * Schema object for: DataExtModRq
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
class DataExtModRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'DataExtMod';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'OwnerID' => 'GUIDTYPE',
			'DataExtName' => 'STRTYPE',
			'ListDataExtType' => 'ENUMTYPE',
			'ListObjRef ListID' => 'IDTYPE',
			'ListObjRef FullName' => 'STRTYPE',
			'TxnDataExtType' => 'ENUMTYPE',
			'TxnID' => 'IDTYPE',
			'TxnLineID' => 'IDTYPE',
			'OtherDataExtType' => 'ENUMTYPE',
			'DataExtValue' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'OwnerID' => 0,
			'DataExtName' => 31,
			'ListDataExtType' => 0,
			'ListObjRef ListID' => 0,
			'ListObjRef FullName' => 159,
			'TxnDataExtType' => 0,
			'TxnID' => 0,
			'TxnLineID' => 0,
			'OtherDataExtType' => 0,
			'DataExtValue' => 0,
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
			'OwnerID' => 999.99,
			'DataExtName' => 999.99,
			'ListDataExtType' => 999.99,
			'ListObjRef ListID' => 999.99,
			'ListObjRef FullName' => 999.99,
			'TxnDataExtType' => 999.99,
			'TxnID' => 999.99,
			'TxnLineID' => 3.0,
			'OtherDataExtType' => 999.99,
			'DataExtValue' => 999.99,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'OwnerID' => false,
			'DataExtName' => false,
			'ListDataExtType' => false,
			'ListObjRef ListID' => false,
			'ListObjRef FullName' => false,
			'TxnDataExtType' => false,
			'TxnID' => false,
			'TxnLineID' => false,
			'OtherDataExtType' => false,
			'DataExtValue' => false,
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
			'OwnerID',
			'DataExtName',
			'ListDataExtType',
			'ListObjRef',
			'ListObjRef ListID',
			'ListObjRef FullName',
			'TxnDataExtType',
			'TxnID',
			'TxnLineID',
			'OtherDataExtType',
			'DataExtValue',
		];

		return $paths;
	}
}