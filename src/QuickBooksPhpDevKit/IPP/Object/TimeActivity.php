<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class TimeActivity extends BaseObject
{
	protected function _defaults(): array
	{
		return [
		];
	}

	protected function _order(): array
	{
		/*
		return array(
			'Id' => true,
			'SyncToken' => true,
			'MetaData' => true,
			'Synchronized' => true,
			'Draft' => true,
			'TxnDate' => true,
			'NameOf' => true,
			'Employee' => true,
			'CustomerId' => true,
			'JobId' => true,
			'ItemId' => true,
			'ItemType' => true,
			'PayItemId' => true,
			'BillableStatus' => true,
			'HourlyRate' => true,
			'Hours' => true,
			'Minutes' => true,
			'Seconds' => true,
			'StartTime' => true,
			'EndTime' => true,
			'Description' => true,
			);
		*/

		return [
			'Id' => true,
			'SyncToken' => true,
			'MetaData' => true,
			'Synchronized' => true,
			'Draft' => true,
			'TxnDate' => true,
			'NameOf' => true,
			'Employee' => true,
			'CustomerId' => true,
			'CustomerName' => true, // added CustomerName
			'JobId' => true,
			'ItemId' => true,
			'ItemName' => true, // added ItemName
			'ItemType' => true,
			'ClassId' => true, // added ClassId
			'PayItemId' => true,
			'BillableStatus' => true,
			'HourlyRate' => true,
			'Hours' => true,
			'Minutes' => true,
			'Seconds' => true,
			'StartTime' => true,
			'EndTime' => true,
			'Description' => true,
		];
	}
}
