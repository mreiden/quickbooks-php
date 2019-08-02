<?php declare(strict_types=1);

/**
 * Schema object for: PriceLevelQueryRq
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
class PriceLevelQueryRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = '';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'ListID' => 'IDTYPE',
			'FullName' => 'STRTYPE',
			'MaxReturned' => 'INTTYPE',
			'ActiveStatus' => 'ENUMTYPE',
			'FromModifiedDate' => 'DATETIMETYPE',
			'ToModifiedDate' => 'DATETIMETYPE',
			'NameFilter MatchCriterion' => 'ENUMTYPE',
			'NameFilter Name' => 'STRTYPE',
			'NameRangeFilter FromName' => 'STRTYPE',
			'NameRangeFilter ToName' => 'STRTYPE',
			'ItemRef ListID' => 'IDTYPE',
			'ItemRef FullName' => 'STRTYPE',
			'CurrencyFilter ListID' => 'IDTYPE',
			'CurrencyFilter FullName' => 'STRTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'ListID' => 0,
			'FullName' => 0,
			'MaxReturned' => 0,
			'ActiveStatus' => 0,
			'FromModifiedDate' => 0,
			'ToModifiedDate' => 0,
			'NameFilter MatchCriterion' => 0,
			'NameFilter Name' => 0,
			'NameRangeFilter FromName' => 0,
			'NameRangeFilter ToName' => 0,
			'ItemRef ListID' => 0,
			'ItemRef FullName' => 0,
			'CurrencyFilter ListID' => 0,
			'CurrencyFilter FullName' => 0,
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
			'ListID' => 999.99,
			'FullName' => 999.99,
			'MaxReturned' => 999.99,
			'ActiveStatus' => 999.99,
			'FromModifiedDate' => 999.99,
			'ToModifiedDate' => 999.99,
			'NameFilter MatchCriterion' => 999.99,
			'NameFilter Name' => 999.99,
			'NameRangeFilter FromName' => 999.99,
			'NameRangeFilter ToName' => 999.99,
			'ItemRef ListID' => 999.99,
			'ItemRef FullName' => 999.99,
			'CurrencyFilter ListID' => 999.99,
			'CurrencyFilter FullName' => 999.99,
			'IncludeRetElement' => 999.99,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'ListID' => true,
			'FullName' => true,
			'MaxReturned' => false,
			'ActiveStatus' => false,
			'FromModifiedDate' => false,
			'ToModifiedDate' => false,
			'NameFilter MatchCriterion' => false,
			'NameFilter Name' => false,
			'NameRangeFilter FromName' => false,
			'NameRangeFilter ToName' => false,
			'ItemRef ListID' => true,
			'ItemRef FullName' => true,
			'CurrencyFilter ListID' => true,
			'CurrencyFilter FullName' => true,
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
			'ListID',
			'FullName',
			'MaxReturned',
			'ActiveStatus',
			'FromModifiedDate',
			'ToModifiedDate',
			'NameFilter',
			'NameFilter MatchCriterion',
			'NameFilter Name',
			'NameRangeFilter',
			'NameRangeFilter FromName',
			'NameRangeFilter ToName',
			'ItemRef',
			'ItemRef ListID',
			'ItemRef FullName',
			'CurrencyFilter',
			'CurrencyFilter ListID',
			'CurrencyFilter FullName',
			'IncludeRetElement',
		];

		return $paths;
	}
}
