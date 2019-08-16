<?php declare(strict_types=1);

/**
 *
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 */

namespace QuickBooksPhpDevKit\Map;

use QuickBooksPhpDevKit\Driver\Factory;
use QuickBooksPhpDevKit\Driver\Sql;
use QuickBooksPhpDevKit\Map;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\SQL\Schema;
use QuickBooksPhpDevKit\SQL\SqlObject;
use QuickBooksPhpDevKit\Utilities;

class Qbxml extends Map
{
	protected $_driver;

	public function __construct($dsn_or_Driver, array $driver_options = [])
	{
		if (is_object($dsn_or_Driver))
		{
			$this->_driver = $dsn_or_Driver;
		}
		else
		{
			$this->_driver = Factory::create($dsn_or_Driver, $driver_options);
		}
	}

	public function mark($mark_as, $object_or_action, $ID, $TxnID_or_ListID = null, $errnum = null, $errmsg = null, $mark_as_dequeued = true): bool
	{
		$Driver = $this->_driver;

		$object = Utilities::actionToObject($object_or_action);

		$table_and_field = [];

		// Convert to table and primary key, select qbsql id
		Schema::mapPrimaryKey($object, Schema::MAP_TO_SQL, $table_and_field);

		if (!empty($table_and_field[0]) && !empty($table_and_field[1]))
		{
			switch ($mark_as)
			{
				case Map::MARK_ADD:

					$arr = [];

					$where = [
						[Sql::Field['ID'] => $ID],
					];

					if ($TxnID_or_ListID)
					{
						$arr[$table_and_field[1]] = $TxnID_or_ListID;

						// Get the existing temporary ID
						$errnum = null;
						$errmsg = null;
						$existing = $Driver->fetch($Driver->query("SELECT " . $table_and_field[1] . " FROM " . Sql::$TablePrefix['BASE'] . $table_and_field[0] . " WHERE " . Sql::Field['ID'] . " = " . $ID, $errnum, $errmsg));

						if (!$existing)
						{
							return false;
						}

						$existing_TxnID_or_ListID = $existing[$table_and_field[1]];
					}

					$resync = true;
					$discov = true;

					if ($errnum)
					{
						$arr[Sql::Field['ERROR_NUMBER']] = $errnum;
						$arr[Sql::Field['ERROR_MESSAGE']] = $errmsg;

						// Don't mark it as synced/discovered if there was an error
						$resync = false;
						$discov = false;
					}

					/*
					if ($mark_as_dequeued)
					{
						$arr[Sql::Field['ENQUEUE_TIME']] = date('Y-m-d H:i:s');
					}
					*/

					$Driver->update(
						Sql::$TablePrefix['BASE'] . $table_and_field[0],
						$arr,
						$where,
						$resync,
						$discov);

					if ($TxnID_or_ListID)
					{
						$Object = new SqlObject($table_and_field[0], '', []);
						$Object->set($table_and_field[1], $TxnID_or_ListID);

						$action = Utilities::objectToAdd($object_or_action);

						$this->_updateRelatives($table_and_field[0], $action, $Object, $existing_TxnID_or_ListID);
					}

					break;
			}
		}

		return false;
	}

	public function flat(string $map, string $object_or_action, $ID)
	{
		$Driver = $this->_driver;

		if ($map == Map::MAP_QBXML)
		{
			$object = Utilities::actionToObject($object_or_action);

			$table_and_field = [];

			// Convert to table and primary key, select qbsql id
			Schema::mapPrimaryKey($object, Schema::MAP_TO_SQL, $table_and_field);

			if (!empty($table_and_field[0]) && !empty($table_and_field[1]))
			{
				$errnum = null;
				$errmsg = null;
				return $Driver->fetch($Driver->query("
					SELECT
						*
					FROM
						" . Sql::$TablePrefix['BASE'] . $table_and_field[0] . "
					WHERE
						" . Sql::Field['ID'] . " = " . (int) $ID, $errnum, $errmsg));
			}
		}
	}

	public function table(string $map, $object_or_action, $ID): ?string
	{
		$Driver = $this->_driver;

		if ($map == Map::MAP_QBXML)
		{
			$object = Utilities::actionToObject($object_or_action);

			$table_and_field = [];

			// Convert to table and primary key, select qbsql id
			Schema::mapPrimaryKey($object, Schema::MAP_TO_SQL, $table_and_field);

			if (!empty($table_and_field[0]) && !empty($table_and_field[1]))
			{
				return Sql::$TablePrefix['BASE'] . $table_and_field[0];
			}
		}

		return null;
	}

	public function adds(array $adds = [], bool $mark_as_queued = true, ?int $limit = null): array
	{
		$Driver = $this->_driver;

		$NOW = date('Y-m-d H:i:s');

		$sql_add = $adds;

		$list = [];

		//$Driver->log('Input is: ' . print_r($adds, true));

		// Check if any objects need to be pushed back to QuickBooks
		foreach ($sql_add as $action => $priority)
		{
			$object = Utilities::actionToObject($action);

			//$Driver->log('Action is: ' . $action . ', object is: ' . $object);

			$table_and_field = [];

			// Convert to table and primary key, select qbsql id
			Schema::mapPrimaryKey($object, Schema::MAP_TO_SQL, $table_and_field);

			$Driver->log('Searching table: ' . print_r($table_and_field, true) . ' for ADDED records.');

			//print_r($table_and_field);

			if (!empty($table_and_field[0]) && !empty($table_and_field[1]))
			{
				// For ADDs
				//	- Do not sync if to_skip = 1
				//	- Do not sync if to_delete = 1
				//	- Do not sync if last_errnum is not empty		@TODO Implement this

				switch ($table_and_field[0])
				{
					case 'customer':
						$priority_reduce = 'Parent_FullName';
						break;
					default:
						$priority_reduce = null;
				}

				$extras = '';
				if ($priority_reduce)
				{
					$extras = ', ' . $priority_reduce;
				}

				$sql = "
					SELECT
						" . Sql::Field['ID'] . ",
						" . Sql::Field['ERROR_NUMBER'] . " " . $extras . "
					FROM
						" . Sql::$TablePrefix['BASE'] . $table_and_field[0] . "
					WHERE
						" . Sql::Field['MODIFY'] . " IS NOT NULL AND
						" . Sql::Field['RESYNC'] . " IS NULL AND
						" . Sql::Field['TO_SKIP'] . " != 1 AND
						" . Sql::Field['TO_DELETE'] . " != 1 AND
						" . Sql::Field['FLAG_DELETED'] . " != 1 AND
						" . Sql::Field['MODIFY'] . " <= '" . $NOW . "' ";
				//		" . Sql::Field['TO_VOID'] . " != 1 ";

				//$Driver->log($sql);

				$errnum = 0;
				$errmsg = '';

				$count = 0;
				$res = $Driver->query($sql, $errnum, $errmsg);

				while ($arr = $Driver->fetch($res))
				{
					if (strlen($arr[Sql::Field['ERROR_NUMBER']]))
					{
						continue;
					}

					if (!isset($list[$action]))
					{
						$list[$action] = [];
					}

					$tmp_priority = $priority;
					if ($priority_reduce &&
						isset($arr[$priority_reduce]) &&
						!empty($arr[$priority_reduce]))
					{
						$tmp_priority = $priority - 1;
					}

					$list[$action][$arr[Sql::Field['ID']]] = $tmp_priority;

					$count++;

					if ($mark_as_queued)
					{
						// Make the record as having been ->enqueue()d
						$errnum = 0;
						$errmsg = '';
						$Driver->query("
							UPDATE
								" . Sql::$TablePrefix['BASE'] . $table_and_field[0] . "
							SET
								" . Sql::Field['ENQUEUE_TIME'] . " = '" . date('Y-m-d H:i:s') . "'
							WHERE
								" . Sql::Field['ID'] . " = " . $arr[Sql::Field['ID']], $errnum, $errmsg);
					}

					/*
					if (count($list[$action]) >= $limit)
					{
						break;
					}
					*/

					if ($limit > 0 && $count >= $limit)
					{
						break 2;
					}
				}
			}
		}

		return $list;
	}

	public function mods(array $mods = [], bool $mark_as_queued = true): array
	{
		return [];
	}

	public function imports(array $imports = []): array
	{
		return [];
	}

	public function queries(array $queries = []): array
	{
		return [];
	}

	/**
	 *
	 * @param unknown_type $table
	 * @param unknown_type $Object
	 * @param unknown_type $tmp_TxnID_or_ListID
	 */
	protected function _updateRelatives(string $table, string $action, $Object, string $tmp_TxnID_or_ListID): bool
	{
		$Driver = $this->_driver;

		//print('updating relatives' . "\n");

		// This should *ONLY* be used when we are ADDING records
		//	If it's an update, any relatives *should already have* the permanent ListID
		//	If it's an add, any relatives *have not yet been added* and thus can be marked modified without causing sync issues
		if (substr($action, -3, 3) != 'Add')
		{
			//print('returning false because of action: ' . $action . "\n");
			return false;
		}

		$map = [
			'invoice' => [
				'key' => 'TxnID',
				'relatives' => [
					//'estimate_linkedtxn' => 'ToTxnID:Type=Invoice',
					//'salesorder_linkedtxn' => 'ToTxnID:Type=Invoice',
					'receivepayment_appliedtotxn' => 'ToTxnID:TxnType=Invoice', // 'ToTxnID:Type=Invoice',
					'invoice_invoiceline' => 'Invoice_TxnID', //
					'dataext' => 'Entity_ListID:EntityType=Customer', 	// update the Entity_ListID where EntityType = 'Customer' (and the existing Entity_ListID is the old ListID)
				],
			],
			'receivepayment' => [
				'key' => 'TxnID',
				'relatives' => [
					'receivepayment_appliedtotxn' => 'ReceivePayment_TxnID',
				],
			],

			'salesreceipt' => [
				'key' => 'TxnID',
				'relatives' => [
					'salesreceipt_salesreceiptline' => 'SalesReceipt_TxnID',
				],
			],
			'salesorder' => [
				'key' => 'TxnID',
				'relatives' => [
					'salesorder_salesorderline' => 'SalesOrder_TxnID',
					'invoice_linkedtxn' => 'ToTxnID:TxnType=SalesOrder',
				],
			],
		];

		if (empty($map[$table]))
		{
			//print('returning false because of missing map: ' . $table . "\n");
			return false;
		}

		$TxnID_or_ListID = $Object->get($map[$table]['key']);
		foreach ($map[$table]['relatives'] as $relative_table => $relative_field)
		{
			$Driver->log('Now updating [' . $relative_table . '] for field [' . $relative_field . '] with value [' . $TxnID_or_ListID . ']', null, PackageInfo::LogLevel['DEBUG']);
			//print('updating realtive: ' . $relative_table . "\n");

			//$multipart = array( $relative_field => $extra['AddResponse_OldKey'] );
			//$tmp = new SqlObject($relative_table, null);

			//@todo Make the Boolean TRUE value used in the QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG field a constant,
			//      in case the sql driver used uses something other than 1 and 0.
			//$tmp->set($relative_field, $TxnID_or_ListID);
			//$Driver->update(Sql::$TablePrefix['BASE'] . $relative_table, $tmp, array( $multipart ), false);

			// First, if the record has already been modified, then we need to
			//	make sure that it stays marked modified. Otherwise, we need to
			//	not let this get modified. So, query for the existing record.

			$pos = false;
			$where = '';
			if (false !== ($pos = strpos($relative_field, ':')))
			{
				$tmp = substr($relative_field, $pos + 1);

				$relative_field = substr($relative_field, 0, $pos);

				$where = " AND " . str_replace('=', "='", $tmp) . "'";

				//print('TMP IS: [' . $where . ']');
				//exit;
			}

			$errnum = null;
			$errmsg = null;
			$sql = "
				UPDATE
					" . Sql::$TablePrefix['BASE'] . $relative_table . "
				SET
					" . $relative_field . " = '%s'
				WHERE
					" . $relative_field . " = '%s' " . $where;

			//print($sql . "\n\n");

			$Driver->query($sql, $errnum, $errmsg, null, null, [
				$TxnID_or_ListID,
				$tmp_TxnID_or_ListID,
			]);
		}

		return true;
	}
}
