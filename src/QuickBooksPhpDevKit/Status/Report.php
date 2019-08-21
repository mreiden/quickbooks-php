<?php declare(strict_types=1);

/**
 *
 *
 * Copyright (c) 2010-04-16 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author Keith Palmer <Keith@ConsoliBYTE.com>
 *
 * @package QuickBooks
 * @subpackage Error
 */

namespace QuickBooksPhpDevKit\Status;

use QuickBooksPhpDevKit\{
	Driver\Factory,
	PackageInfo,
	SQL\Schema,
	Utilities,
};

/**
 *
 *
 *
 */
class Report
{
	public const MODE_QUEUE_ERRORS = 'queue-errors';
	public const MODE_QUEUE_RECORDS = 'queue-records';
	public const MODE_MIRROR_ERRORS = 'mirror-errors';
	public const MODE_MIRROR_RECORDS = 'mirror-records';

	public const STATUS_OK = 'OK';
	public const STATUS_NOTICE = 'Notice';
	public const STATUS_CAUTION = 'Caution';
	public const STATUS_WARNING = 'Warning';
	public const STATUS_DANGER = 'Danger';
	public const STATUS_UNKNOWN = 'Unknown';

	protected $_driver;

	public function __construct($dsn, array $config = [])
	{
		$this->_driver = Factory::create($dsn, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public function create(string $mode, ?string $user = null, ?string $date_from = null, ?string $date_to = null, bool $fetch_full_record = false, array $restrict = [])
	{
		$Driver = $this->_driver;

		if (!$user)
		{
			$user = $Driver->authDefault();
		}

		switch ($mode)
		{
			case static::MODE_QUEUE_ERRORS:
			case static::MODE_QUEUE_RECORDS:
				return $this->_createForQueue($mode, $user, $date_from, $date_to, $fetch_full_record, $restrict);

			case static::MODE_MIRROR_ERRORS:
			case static::MODE_MIRROR_RECORDS:
				return $this->_createForMirror($mode, $user, $date_from, $date_to, $fetch_full_record, $restrict);

			default:
				return false;
		}
	}

	/**
	 * Get information about the status of a connection to QuickBooks
	 *
	 * The returned array will look something like this:
	 * <pre>
	 * Array (
	 * 	[0] => danger 					// This is a constant, one of the static::STATUS_* constants
	 * 	[1] => ERROR: A connection has not been made in 54 days, 1 hours and 46 minutes! Contact support to get this issue resolved!
	 * 	[2] => 2010-03-19 12:26:41 		// This is the last time the given user logged in
	 * 	[3] => 2010-03-19 12:26:41 		// This is the last time the given user performed any action
	 * )
	 * </pre>
	 */
	public function status(?string $user = null, ?array $levels = []): array
	{
		$Driver = $this->_driver;

		if (!$user)
		{
			$user = $Driver->authDefault();
		}

		if (!count($levels))
		{
			$levels = [
				60 * 60 * 12 => [static::STATUS_NOTICE, 'Notice: A connection has not been made in %d days, %d hours and %d minutes.'],
				60 * 60 * 24 => [static::STATUS_CAUTION, 'Caution: A connection has not been made in %d days, %d hours and %d minutes.'],
				60 * 60 * 36 => [static::STATUS_WARNING, 'Warning! A connection has not been made in %d days, %d hours and %d minutes.'],
				60 * 60 * 48 => [static::STATUS_DANGER, 'DANGER! A connection has not been made in %d days, %d hours and %d minutes! Contact support to get this issue resolved!'],
			];
		}

		if (!isset($levels[0]))
		{
			$levels[0] = [static::STATUS_OK, 'Status is OK. Last connection made %d days, %d hours, and %d minutes ago.'];
		}

		if (!isset($levels[-1]))
		{
			$levels[-1] = [static::STATUS_UNKNOWN, 'Status is unknown.'];
		}

		//print_r($levels);

		// Find the status from the ticket table
		$last = $Driver->authLast($user);
		if (is_array($last))
		{
			krsort($levels);

			$ago = time() - strtotime($last[1]);

			$days = floor($ago / (60 * 60 * 24));
			$hours = floor(($ago - ($days * 60 * 60 * 24)) / 60.0 / 60.0);
			$minutes = max(1, floor(($ago - ($days * 60 * 60 * 24) - ($hours * 60 * 60)) / 60.0));

			$retr = null;

			foreach ($levels as $level => $tuple)
			{
				if ($level <= 0)
				{
					continue;
				}

				if ($ago > $level)
				{
					$retr = $tuple;
					break;
				}
			}

			if (!$retr)
			{
				$retr = $levels[0];
			}

			$retr[1] = sprintf($retr[1], $days, $hours, $minutes);

			$retr[] = $last[0];
			$retr[] = $last[1];

			return $retr;
		}

		return $levels[-1];
	}
	/*
	public function connection($type, $user = null)
	{

	}
	*/

	protected function &_createForQueue(string $mode, ?string $user, ?string $date_from, ?string $date_to, bool $fetch_full_record, array $restrict)
	{
		$Driver = $this->_driver;

		$report = [];

		$list = $Driver->queueReport($user, $date_from, $date_to);

		$statuses = [
			PackageInfo::Status['QUEUED'] => 'Queued',
			PackageInfo::Status['SUCCESS'] => 'Successfully processed',
			PackageInfo::Status['ERROR'] => 'Error',
			PackageInfo::Status['PROCESSING'] => 'Currently being processed',
			PackageInfo::Status['HANDLED'] => 'An error occurred, but the error was handled',
			PackageInfo::Status['CANCELLED'] => 'Cancelled',
			PackageInfo::Status['REMOVED'] => 'Removed from queue',
			PackageInfo::Status['NOOP'] => 'No operation occurred',
		];

		foreach ($list as $key => $arr)
		{
			$errnum = '';
			$errmsg = '';
			if ($arr['msg'] and
				$pos = strpos($arr['msg'], ':'))
			{
				$errnum = substr($arr['msg'], 0, $pos);
				$errmsg = substr($arr['msg'], $pos + 2);
			}
			else if ($arr['msg'])
			{
				$errnum = '?';
				$errmsg = $arr['msg'];
			}

			$record = [
				$arr['quickbooks_queue_id'],
				$arr['qb_action'],
				$arr['ident'],
				$arr['priority'],
				$statuses[$arr['qb_status']],
				$errnum,
				$errmsg,
				static::describe($errnum, $errmsg),
				$arr['enqueue_datetime'],
				$arr['dequeue_datetime'],
			];

			$report[] = $record;
		}

		return $report;
	}

	/**
	 *
	 *
	 *
	 */
	protected function &_createForMirror(string $mode, ?string $user, ?string $date_from, ?string $date_to, bool $fetch_full_record, array $restrict)
	{
		$Driver = $this->_driver;

		$report = [];

		$do_restrict = count($restrict) > 0;

		$actions = Utilities::listActions('*IMPORT*');
		//print_r($actions);
		//print_r($restrict);

		foreach ($actions as $action)
		{
			$object = Utilities::actionToObject($action);

			//print('checking object [' . $object . ']' . "<br />");

			if ($do_restrict &&
				!in_array($object, $restrict))
			{
				continue;
			}

			//print('doing object: ' . $object . '<br />');

			$pretty = $this->_prettyName($object);
			$report[$pretty] = [];

			Schema::mapPrimaryKey($object, Schema::MAP_TO_SQL, $table_and_field);

			//print_r($table_and_field);

			if (!empty($table_and_field[0]) &&
				!empty($table_and_field[1]))
			{
				$sql = "
					SELECT
						*
					FROM
						" . Sql::$TablePrefix['BASE'] . $table_and_field[0] . "
					WHERE ";

				if ($mode == static::MODE_MIRROR_ERRORS)
				{
					$sql .= " LENGTH(" . Sql::Field['ERROR_NUMBER'] . ") > 0 ";
				}
				else
				{
					$sql .= " 1 ";
				}

				if ($timestamp = strtotime($date_from) &&
					$timestamp > 0)
				{
					$sql .= " AND TimeCreated >= '" . date('Y-m-d H:i:s', $timestamp) . "' ";
				}

				if ($timestamp = strtotime($date_to) &&
					$timestamp > 0)
				{
					$sql .= " AND TimeCreated <= '" . date('Y-m-d H:i:s', $timestamp) . "' ";
				}

				$sql .= " ORDER BY qbsql_id DESC ";

				//print($sql);

				$errnum = 0;
				$errmsg = '';
				$res = $Driver->query($sql, $errnum, $errmsg);
				while ($arr = $Driver->fetch($res))
				{
					$record = null;
					if ($fetch_full_record)
					{
						$record = $arr;
					}

					if ($arr[Sql::Field['ERROR_NUMBER']])
					{
						$details = static::describe($arr[Sql::Field['ERROR_NUMBER']], $arr[Sql::Field['ERROR_MESSAGE']]);
					}
					else if ($arr[Sql::Field['RESYNC']] == $arr[Sql::Field['MODIFY']])
					{
						$details = 'Synced successfully.';
					}
					else if ($arr[Sql::Field['MODIFY']] > $arr[Sql::Field['RESYNC']])
					{
						$details = 'Waiting to sync.';
					}

					$report[$pretty][] = [
						$arr[Sql::Field['ID']],
						$this->_fetchSomeField($arr, ['ListID', 'TxnID']),
						$this->_fetchSomeField($arr, ['FullName', 'Name', 'RefNumber']),
						$this->_fetchSomeField($arr, ['TxnDate']),
						$this->_fetchSomeField($arr, ['Customer_FullName', 'Vendor_FullName']),
						$arr[Sql::Field['ERROR_NUMBER']],
						$arr[Sql::Field['ERROR_MESSAGE']],
						$details,
						$arr[Sql::Field['DEQUEUE_TIME']],
						$record,
					];
				}
			}
		}

		return $report;
	}

	protected function _fetchSomeField(array $arr, array $fields)
	{
		foreach ($fields as $field)
		{
			if (isset($arr[$field]))
			{
				return $arr[$field];
			}
		}

		return null;
	}

	/**
	 *
	 * @todo This might be better suited to the Utilities class in case we want to use it somewhere else
	 */
	protected function _prettyName(string $constant): string
	{
		//$constant = str_replace('Import', '', $constant);

		$strlen = strlen($constant);
		for ($i = 1; $i < $strlen; $i++)
		{
			if (strtoupper($constant[$i]) == $constant[$i])
			{
				$constant = substr($constant, 0, $i) . ' ' . substr($constant, $i);
				$i = $i + 2;
			}
		}

		return $constant;
	}

	public function HTML(string $mode, ?string $user = null, ?string $date_from = null, ?string $date_to = null, bool $fetch_full_record = false, array $restrict = [], bool $skip_empties = true): string
	{
		$report = $this->create($mode, $user, $date_from, $date_to, $fetch_full_record, $restrict);

		switch ($mode)
		{
			case static::MODE_MIRROR_RECORDS:
			case static::MODE_MIRROR_ERRORS:
				return $this->_htmlForMirror($report, $skip_empties);

			case static::MODE_QUEUE_RECORDS:
			case static::MODE_QUEUE_ERRORS:
				return $this->_htmlForQueue($report);

			default:
				return '';
		}
	}

	protected function _htmlForQueue(array $report): string
	{
		$html = implode(PackageInfo::$CRLF, [
			'<table>',
			'	<thead>',
			'		<tr>',
			'			<td>Queue ID</td>',
			'			<td>Action</td>',
			'			<td>Record ID</td>',
			'			<td>Priority</td>',
			'			<td>Status</td>',
			'			<td>Error Number</td>',
			'			<td>Error Message</td>',
			'			<td>Status Details</td>',
			'			<td>Queued</td>',
			'			<td>Processed</td>',
			'		</tr>',
			'	</thead>',
			'	<tbody>',
		]) . PackageInfo::$CRLF;


		foreach ($report as $record)
		{
			$html .= implode(PackageInfo::$CRLF, [
				'		<tr>',
				'			<td>' . $record[0] . '</td>',
				'			<td>' . $record[1] . '</td>',
				'			<td>' . $record[2] . '</td>',
				'			<td>' . $record[3] . '</td>',
				'			<td>' . $record[4] . '</td>',
				'			<td>' . $record[5] . '</td>',
				'			<td>' . $record[6] . '</td>',
				'			<td>' . $record[7] . '</td>',
				'			<td>' . $record[8] . '</td>',
				'			<td>' . $record[9] . '</td>',
				'		</tr>',
			]) . PackageInfo::$CRLF;
		}

		$html .= '	</tbody>' . PackageInfo::$CRLF;
		$html .= '</table>' . PackageInfo::$CRLF;

		return $html;
	}

	protected function _htmlForMirror(array $report, bool $skip_empties): string
	{
		$html = '';

		foreach ($report as $type => $records)
		{
			if ($skip_empties &&
				!count($records))
			{
				continue;
			}

			$html .= implode(PackageInfo::$CRLF, [
				'<table>',
				'	<thead>',
				'		<tr>',
				'			<td colspan="9">' . $type . '</td>',
				'		</tr>',
				'		<tr>',
				'			<td>SQL ID</td>',
				'			<td>ListID or TxnID</td>',
				'			<td>Name or RefNumber</td>',
				'			<td>Transaction Date</td>',
				'			<td>Entity Name</td>',
				'			<td>Error Number</td>',
				'			<td>Error Message</td>',
				'			<td>Status Details</td>',
				'			<td>Date/Time</td>',
				'		</tr>',
				'	</thead>',
				'	<tbody>',
			]) . PackageInfo::$CRLF;

			foreach ($records as $record)
			{
				$html .= implode(PackageInfo::$CRLF, [
					'		<tr>',
					'			<td>' . $record[0] . '</td>',
					'			<td>' . $record[1] . '</td>',
					'			<td>' . $record[2] . '</td>',
					'			<td>' . $record[3] . '</td>',
					'			<td>' . $record[4] . '</td>',
					'			<td>' . $record[5] . '</td>',
					'			<td>' . $record[6] . '</td>',
					'			<td>' . $record[7] . '</td>',
					'			<td>' . $record[8] . '</td>',
					'		</tr>',
				]) . PackageInfo::$CRLF;
			}

			$html .= '	</tbody>' . PackageInfo::$CRLF;
			$html .= '</table>' . PackageInfo::$CRLF;
			$html .= '<br />' . PackageInfo::$CRLF;
		}

		return $html;
	}
	/*
	public function XML($mode, $date_from = null, $date_to = null, $fetch_full_record = false, $restrict = [])
	{

	}
	*/

	/**
	 *
	 *
	 * @todo Make this better for error codes that get thrown for more than one different type of error.
	 *
	 */
	static public function describe($errcode, $errmsg): string
	{
		static $errs = [
			3100 => [
				'*' => 'QuickBooks "Name" fields must be unique across all Customers, Vendors, Employees, and Other Names. Is there another Customer, Vendor, Employee, or Other Name with the same name as this record?',
			],
		];

		if (isset($errs[$errcode]))
		{
			return $errs[$errcode]['*'];
		}

		return '';
	}
}
