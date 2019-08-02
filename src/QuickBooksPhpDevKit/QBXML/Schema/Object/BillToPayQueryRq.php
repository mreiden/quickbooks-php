<?php declare(strict_types=1);

/**
 * Schema object for: BillToPayQueryRq
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
class BillToPayQueryRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = '';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'PayeeEntityRef ListID' => 'IDTYPE',
			'PayeeEntityRef FullName' => 'STRTYPE',
			'APAccountRef ListID' => 'IDTYPE',
			'APAccountRef FullName' => 'STRTYPE',
			'DueDate' => 'DATETYPE',
			'CurrencyFilter ListID' => 'IDTYPE',
			'CurrencyFilter FullName' => 'STRTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'PayeeEntityRef ListID' => 0,
			'PayeeEntityRef FullName' => 209,
			'APAccountRef ListID' => 0,
			'APAccountRef FullName' => 209,
			'DueDate' => 0,
			'CurrencyFilter ListID' => 0,
			'CurrencyFilter FullName' => 209,
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
			'PayeeEntityRef ListID' => 999.99,
			'PayeeEntityRef FullName' => 999.99,
			'APAccountRef ListID' => 999.99,
			'APAccountRef FullName' => 999.99,
			'DueDate' => 999.99,
			'CurrencyFilter ListID' => 999.99,
			'CurrencyFilter FullName' => 999.99,
			'IncludeRetElement' => 4.0,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'PayeeEntityRef ListID' => false,
			'PayeeEntityRef FullName' => false,
			'APAccountRef ListID' => false,
			'APAccountRef FullName' => false,
			'DueDate' => false,
			'CurrencyFilter ListID' => false,
			'CurrencyFilter FullName' => false,
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
			'PayeeEntityRef',
			'PayeeEntityRef ListID',
			'PayeeEntityRef FullName',
			'APAccountRef',
			'APAccountRef ListID',
			'APAccountRef FullName',
			'DueDate',
			'CurrencyFilter',
			'CurrencyFilter ListID',
			'CurrencyFilter FullName',
			'IncludeRetElement',
		];

		return $paths;
	}
}
