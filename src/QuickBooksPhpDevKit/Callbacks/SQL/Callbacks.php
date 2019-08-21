<?php declare(strict_types=1);

/**
 * Static callback methods for the SQL mirror server
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 *	We dont' register the following callback handlers because the response is
 *	already included in other responses from other queries:
 * 	No registered handler for: ItemDiscountQuery
 * 	No registered handler for: ItemFixedAssetQuery
 * 	No registered handler for: ItemGroupQuery
 * 	No registered handler for: ItemInventoryAssemblyQuery
 * 	No registered handler for: ItemInventoryQuery
 * 	No registered handler for: ItemNonInventoryQuery
 * 	No registered handler for: ItemOtherChargeQuery
 * 	No registered handler for: ItemPaymentQuery
 * 	No registered handler for: ItemSalesTaxQuery
 * 	No registered handler for: ItemServiceQuery
 * 	No registered handler for: ItemDiscountQuery
 * 	No registered handler for: ItemFixedAssetQuery
 * 	No registered handler for: ItemGroupQuery
 * 	No registered handler for: ItemInventoryAssemblyQuery
 * 	No registered handler for: ItemInventoryQuery
 * 	No registered handler for: ItemNonInventoryQuery
 * 	No registered handler for: ItemOtherChargeQuery
 * 	No registered handler for: ItemPaymentQuery
 * 	No registered handler for: ItemSalesTaxQuery
 * 	No registered handler for: ItemServiceQuery
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @author Garrett Griffin <grgisme@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Server
 */

namespace QuickBooksPhpDevKit\Callbacks\SQL;

use QuickBooksPhpDevKit\{
	Cast,                              // Casts qbxml objects into the corresponding PHP class
	Callbacks as NonSqlCallbacks,      // NonSqlCallbacks::callHook is used to call the hooks
	Driver\Sql,                        // Provides SQL constants and configurion variables
	Driver\Singleton,                  // Provides singleton access to the database driver instances
	Map\Qbxml,                         // Mapper for qbXML schema
	PackageInfo,                       // Package constants and config variables
	SQL as QuickbooksSql,              // Has constants defining hooks
	SQL\Schema,                        // Schema class (provides mapping for XML schema to SQL schema)
	SQL\SqlObject,                     // Generic Sql Object class
	Utilities,                         // General Utilities
	XML,                               // Needed for some constants (e.g. XML::OK)
	XML\Parser,                        // Needed for some constants (e.g. XML::OK)
};
use QuickBooksPhpDevKit\WebConnector\Server\SQL as WebConnectorSQL;  // SQL Mirroring WebConnector Server


/**
 * Static callback methods for the SQL mirror server
 */
class Callbacks
{
	/**
	 * Hook which gets called when the Web Connector authenticates to the server
	 *
	 * @param string $requestID			Not applicable to this hook
	 * @param string $user				The username of the user who connected
	 * @param string $hook				The name of the hook which connected
	 * @param string $err				If an error occurs, you should return an error message here
	 * @param array $hook_data			An array of hook data
	 * @param array $callback_config	An array of callback configuration params
	 * @return boolean
	 */
	static public function onAuthenticate(?string $requestID, string $user, string $hook, string &$err, array $hook_data, array $callback_config): bool
	{
		// $requestID, $user, $hook, &$err, $hook_data, $callback_config

		// Driver instance
		$Driver = Singleton::getInstance();

		// Map instance
		$Map = new Qbxml($Driver);

		// Mode (read-only, write-only, read/write)
		$mode = $callback_config['mode'];

		// How often recurring events happen
		$run_every = $callback_config['recur'];

		// $map parameter
		$sql_map = $callback_config['map'];

		// Which things do you want to query? (QuickBooks => SQL database)
		/*
		$sql_query = [];
		foreach (Utilities::listActions('*QUERY*') as $action)
		{
			$sql_query[$action] = Utilities::priorityForAction($action);
		}

		$sql_query = static::_filterActions($sql_query, $callback_config['_only_query'], $callback_config['_dont_query'], PackageInfo::Actions['QUERY']);
		*/

		//$start = microtime(true);
		//$_start = microtime(true);

		$sql_import = [];
		$tmp = Utilities::listActions('*IMPORT*');
		//print('0.01 [' . (microtime(true) - $start) . ']' . "\n\n");
		foreach ($tmp as $action)
		{
			$sql_import[$action] = Utilities::priorityForAction($action);
		}
		//print('0.02 [' . (microtime(true) - $start) . ']' . "\n\n");

		$sql_import = static::_filterActions($sql_import, $callback_config['_only_import'], $callback_config['_dont_import'], PackageInfo::Actions['IMPORT']);
		//print('0.03 [' . (microtime(true) - $start) . ']' . "\n\n");

		//print('0.1 [' . (microtime(true) - $start) . ']' . "\n\n");

		// Which things you want to *add* to QuickBooks (SQL => QuickBooks (adds only!))
		//	@todo These should be changed to use Utilities::listActions('*ADD*')
		$sql_add = [];
		foreach (Utilities::listActions('*ADD*') as $action)
		{
			$sql_add[$action] = Utilities::priorityForAction($action);
		}

		$sql_add = static::_filterActions($sql_add, $callback_config['_only_add'], $callback_config['_dont_add'], PackageInfo::Actions['ADD']);

		//print('0.2 [' . (microtime(true) - $start) . ']' . "\n\n");

		// Which things you want to *modify* in QuickBooks (SQL => QuickBooks (modifys only!))
		//	@todo These should be changed to use Utilities::listActions('*MOD*')
		$sql_mod = [];
		foreach (Utilities::listActions('*MOD*') as $action)
		{
			$sql_mod[$action] = Utilities::priorityForAction($action);
		}

		$sql_mod = static::_filterActions($sql_mod, $callback_config['_only_modify'], $callback_config['_dont_modify'], PackageInfo::Actions['MOD']);

		//print('0.3 [' . (microtime(true) - $start) . ']' . "\n\n");

		// Which things you want to *audit* in QuickBooks (QuickBooks => SQL)
		$sql_audit = [];
		foreach (Utilities::listActions('*AUDIT*') as $action)
		{
			$sql_audit[$action] = Utilities::priorityForAction($action);
		}

		//print('1 [' . (microtime(true) - $start) . ']' . "\n\n"); $start = microtime(true);

		// Queueing class
		//$Queue = new Queue($dsn_or_conn);

		// List of all actions we're performing
		$actions = [];

		if ($mode == WebConnectorSQL::MODE_READONLY || $mode == WebConnectorSQL::MODE_READWRITE)
		{
			//print_r($sql_query);
			//print_r($sql_map);

			// Register recurring events for things you want to query
			//foreach ($sql_query as $action => $priority)
			foreach ($sql_import as $action => $priority)
			{
				//$Driver->log('Registering recurring event for: ' . $action, null, PackageInfo::LogLevel['DEBUG']);

				// Make sure that there are handlers registered for this recurring action
				if (!isset($sql_map[$action]))
				{
					//trigger_error('No registered handler for: ' . $action);
					continue;
				}

				//$Queue->recurring($run_every, $action, md5(__FILE__), $priority, null, $user);
				$Driver->recurEnqueue($user, $run_every, $action, md5(__FILE__), true, $priority);

				$actions[] = $action;
			}

			if (in_array(PackageInfo::Actions['QUERY_DELETEDLISTS'], $callback_config['_only_misc']))
			{
				// Also grab any deleted records
				$Driver->queueEnqueue($user, PackageInfo::Actions['QUERY_DELETEDLISTS'], 1, true, 0);
			}

			if (in_array(PackageInfo::Actions['QUERY_DELETEDTXNS'], $callback_config['_only_misc']))
			{
				$Driver->queueEnqueue($user, PackageInfo::Actions['QUERY_DELETEDTXNS'], 1, true, 0);
			}

			if (in_array(PackageInfo::Actions['DERIVE_INVENTORYLEVELS'], $callback_config['_only_misc']))
			{
				// Update inventory levels
				$Driver->queueEnqueue($user, PackageInfo::Actions['DERIVE_INVENTORYLEVELS'], 1, true, 0);
			}

			if (in_array(PackageInfo::Actions['DERIVE_INVENTORYASSEMBLYLEVELS'], $callback_config['_only_misc']))
			{
				// Update inventory assembly levels
				$Driver->queueEnqueue($user, PackageInfo::Actions['DERIVE_INVENTORYASSEMBLYLEVELS'], 1, true, 0);
			}
		}

		//print('2 [' . (microtime(true) - $start) . ']' . "\n\n"); $start = microtime(true);

		/*
		// Queue up the audit requests
		//	Audit requests pull in just the calculated TotalAmount and the
		//	TimeModified timestamp from transactions, and store these in
		//	the qbsql_audit_* fields so we can tell if there are transactions
		//	in QuickBooks that don't match up correctly to what's in the SQL
		//	database.
		foreach ($sql_audit as $action => $priority)
		{
			if (!isset($sql_map[$action]))
			{
				continue;
			}

			$Driver->queueEnqueue($user, $action, 1, true, $priority);
		}
		*/

		// Searching the tables can take a long time, you could potentially
		//	have 10 or 15 seconds between when we search for new customers, and
		//	when we search for new invoices. What we don't want to have happen
		//	is that we search for new customers to be queued up, then a new
		//	customer and invoice gets added, then we search for invoices to be
		//	queued up, queue up the invoice, but the customer it depends on
		//	didn't get queued up yet... results in an error!
		//
		// We avoid this by keeping track of when NOW is, and only queueing up
		//	things that are older than NOW()
		$NOW = date('Y-m-d H:i:s');

		// Limit to max adds by time per auth
		$time_limit = 15; 		// 60 *second* limit
		$time_start = time();	// When we started

		// Objects that need to be *ADDED* to QuickBooks
		if ($mode == WebConnectorSQL::MODE_WRITEONLY || $mode == WebConnectorSQL::MODE_READWRITE)
		{
			$mark_as_queued = false;
			$map = $Map->adds($sql_add, $mark_as_queued);

			//$Driver->log('ADDS: ' . print_r($map, true));

			// Go through each action in the returned map
			foreach ($map as $action => $list)
			{
				//$Driver->log('Now doing: ' . $action . ', ' . print_r($list, true));

				//$__start = microtime(true);

				// Go through each ID for each action
				$counter = 0;
				foreach ($list as $ID => $priority)
				{
					$counter++;

					if (time() - $time_start > $time_limit)
					{
						//print('HIT LIMIT SO WE\'RE BREAKING OUT OF HERE [' . $action . '] [ ' . $counter . ' of ' . count($list) . ']!' . "\n");
						break 2;
					}
										// Queue it up to be added to QuickBooks
					$Driver->queueEnqueue($user, $action, $ID, true, $priority);
				}

				//print('now ' . $action . ' [' . (microtime(true) - $__start) . ']' . "\n");
			}
		}

		//print('3 [' . (microtime(true) - $start) . ']' . "\n\n"); $start = microtime(true);

		// Objects that need to be *MODIFIED* within QuickBooks
		if ($mode == WebConnectorSQL::MODE_WRITEONLY || $mode == WebConnectorSQL::MODE_READWRITE)
		{
			// Check if any objects need to be pushed back to QuickBooks
			foreach ($sql_mod as $action => $priority)
			{
				$object = Utilities::actionToObject($action);

				$table_and_field = [];

				// Convert to table and primary key, select qbsql id
				Schema::mapPrimaryKey($object, Schema::MAP_TO_SQL, $table_and_field);

				//$Driver->log('Searching table: ' . print_r($table_and_field, true) . ' for MODIFIED records.', null, PackageInfo::LogLevel['DEBUG']);

				// If we managed to map a table, we need to search that table for changed records
				if (!empty($table_and_field[0]) && !empty($table_and_field[1]))
				{
					// For MODs:
					//	- Do not sync if to_delete = 1
					//	- Do not sync if to_skip = 1
					//	- Do not sync if an error occurred on this record

					$sql = "
						SELECT
							" . Sql::Field['ID'] . ",
							" . Sql::Field['ERROR_NUMBER'] . "
						FROM
							" . Sql::$TablePrefix['SQL_MIRROR'] . $table_and_field[0] . "
						WHERE
							" . Sql::Field['DISCOVER'] . " IS NOT NULL AND
							" . Sql::Field['RESYNC'] . " IS NOT NULL AND
							" . Sql::Field['MODIFY'] . " > " . Sql::Field['RESYNC'] . " AND
							" . Sql::Field['TO_DELETE'] . " != 1 AND
							" . Sql::Field['FLAG_DELETED'] . " != 1 AND
							" . Sql::Field['TO_VOID'] . " != 1 AND
							" . Sql::Field['TO_SKIP'] . " != 1 AND
							" . Sql::Field['MODIFY'] . " <= '" . $NOW . "' ";

					$errnum = 0;
					$errmsg = '';
					$res = $Driver->query($sql, $errnum, $errmsg);
					while ($arr = $Driver->fetch($res))
					{
						if (strlen($arr[Sql::Field['ERROR_NUMBER']]))
						{
							// Do not sync this record until the error is resolved

							continue;
						}

						// Queue up this MOD request
						$Driver->queueEnqueue($user, $action, $arr[Sql::Field['ID']], true, $priority);

						$actions[] = $action;

						// Mark the record as enqueued   - let's wait until the hashing is in for this
						//$Driver->query(..., $errnum, $errmsg);
					}
				}
			}
		}

		//print('4 [' . (microtime(true) - $start) . ']' . "\n\n"); $start = microtime(true);

		if ($mode == WebConnectorSQL::MODE_WRITEONLY || $mode == WebConnectorSQL::MODE_READWRITE)
		{
			// Check if any *voided* objects need to be voided in QuickBooks
			foreach ($sql_add as $action => $priority)
			{
				$object = Utilities::actionToObject($action);

				$dependency = null;
				if ($object == PackageInfo::Actions['OBJECT_BILL'])
				{
					// Bill VOID dependency is PurchaseOrderMod because we want to be able to manually close POs (but need to VOID the bills first)
					$dependency = PackageInfo::Actions['MOD_PURCHASEORDER'];
				}

				$priority = Utilities::priorityForAction(PackageInfo::Actions['VOID_TRANSACTION'], $dependency);

				$table_and_field = [];

				// Convert to table and primary key, select qbsql id
				Schema::mapPrimaryKey($object, Schema::MAP_TO_SQL, $table_and_field);

				//$Driver->log('Searching table: ' . print_r($table_and_field, true) . ' for VOIDED records.', null, PackageInfo::LogLevel['DEBUG']);

				if (!empty($table_and_field[0]))
				{
					$sql = "
						SELECT
							" . Sql::Field['ID'] . "
						FROM
							" . Sql::$TablePrefix['SQL_MIRROR'] . $table_and_field[0] . "
						WHERE
							" . Sql::Field['DISCOVER'] . " IS NOT NULL AND
							" . Sql::Field['TO_DELETE'] . " != 1 AND
							" . Sql::Field['FLAG_DELETED'] . " != 1 AND
							" . Sql::Field['TO_VOID'] . " = 1 AND
							" . Sql::Field['FLAG_VOIDED'] . " != 1 AND
							" . Sql::Field['MODIFY'] . " <= '" . $NOW . "' ";

					$errnum = 0;
					$errmsg = '';
					$res = $Driver->query($sql, $errnum, $errmsg);

					$extra = array(
						'object' => $object,
						);

					while ($arr = $Driver->fetch($res))
					{
						$Driver->queueEnqueue($user, PackageInfo::Actions['VOID_TRANSACTION'], $arr[Sql::Field['ID']], true, $priority, $extra);
					}
				}
			}
		}

		//print('5 [' . (microtime(true) - $start) . ']' . "\n\n"); $start = microtime(true);

		if ($mode == WebConnectorSQL::MODE_WRITEONLY || $mode == WebConnectorSQL::MODE_READWRITE)
		{
			// Check if any *deleted* objects need to be deleted from QuickBooks
			foreach ($sql_add as $action => $priority)
			{
				break;

				$priority = 1000 - $priority;
				$object = Utilities::actionToObject($action);

				$table_and_field = [];

				// Convert to table and primary key, select qbsql id
				Schema::mapPrimaryKey($object, Schema::MAP_TO_SQL, $table_and_field);

				// Delete if it's marked for deletion and it hasn't been deleted already
				if (!empty($table_and_field[0]))
				{
					$sql = "
						SELECT
							" . Sql::Field['ID'] . "
						FROM
							" . Sql::$TablePrefix['SQL_MIRROR'] . $table_and_field[0] . "
						WHERE
							 " . Sql::Field['TO_DELETE'] . " = 1 AND
							 " . Sql::Field['FLAG_DELETED'] . " != 1 AND
							 " . Sql::Field['MODIFY'] . " <= '" . $NOW . "' ";

					$errnum = 0;
					$errmsg = '';
					$res = $Driver->query($sql, $errnum, $errmsg);
					$key = Utilities::keyForAction($action);

					if ($key == 'ListID')
					{
						$useAction = 'ListDel';
					}
					else
					{
						$useAction = 'TxnDel';
					}

					$extra['objectType'] = $object;
					while ($arr = $Driver->fetch($res))
					{
						$Driver->queueEnqueue($user, $useAction, $extra['objectType'] . $arr[Sql::Field['ID']], true, $priority, $extra);
					}
				}
			}
		}

		//print('6 [' . (microtime(true) - $start) . ']' . "\n\n"); $start = microtime(true);

		/*
		// This makes sure that timestamps are set up for every action we're doing (fixes a bug where timestamps never get recorded on initial sync without iterator)
		foreach ($actions as $action)
		{
			$module = __CLASS__;

			$key_curr = static::_keySyncCurr($action);
			$key_prev = static::_keySyncPrev($action);

			$type = null;
			$opts = null;
			$curr_sync_datetime = $Driver->configRead($user, $module, $key_curr, $type, $opts);	// last sync started...
			$prev_sync_datetime = $Driver->configRead($user, $module, $key_prev, $type, $opts);	// last sync started...

			$datetime = Utilities::datetime('1983-01-02');

			if (!$curr_sync_datetime)
			{
				$Driver->configWrite($user, $module, $key_curr, $datetime, null);
			}

			if (!$prev_sync_datetime)
			{
				$Driver->configWrite($user, $module, $key_prev, $datetime, null);
			}
		}
		*/

		//print("\n\n" . 'here [ ' . (microtime(true) - $_start) . ']' . "\n\n\n");

		return true;
	}

	/**
	 * Restrict the queueing maps to "only do these actions" and "dont do these actions" maps
	 *
	 * @param array $action_to_priority
	 * @param array $only_do
	 * @param array $dont_do
	 * @return array
	 */
	static protected function _filterActions(array $action_to_priority, array $only_do, array $dont_do, $type): array
	{
		$start = microtime(true);
		foreach ($action_to_priority as $action => $priority)
		{
			//print('stepping 1... [' . (microtime(true) - $start) . ']' . "\n");
			$converted = Utilities::actionToObject($action);
			//print('stepping 2... [' . (microtime(true) - $start) . ']' . "\n");

			if (count($only_do) &&
				(false === array_search($action, $only_do) &&
				 false === array_search($converted, $only_do)))
			{
				unset($action_to_priority[$action]);
			}

			if (count($dont_do) &&
				(false !== array_search($action, $dont_do) ||
				false !== array_search($converted, $dont_do)))
			{
				unset($action_to_priority[$action]);
			}
		}
		//print("\n" . 'ending... [' . (microtime(true) - $start) . ']' . "\n\n");

		arsort($action_to_priority);

		//print_r($action_to_priority);

		return $action_to_priority;
	}

	/**
	 *
	 *
	 * @todo Implement error handling routines
	 *
	 * @param array $hooks
	 * @param string $hook
	 * @param string $requestID
	 * @param string $user
	 * @param string $err
	 * @param array $hook_data
	 * @param array $callback_config
	 * @return boolean
	 */
	protected static function _callHooks(&$hooks, string $hook, int $requestID, string $user, &$err, $hook_data, $callback_config = [])
	{
		$err = '';

		$Driver = Singleton::getInstance();

		return NonSqlCallbacks::callHook($Driver, $hooks, $hook, $requestID, $user, null, $err, $hook_data, $callback_config, __FILE__, __LINE__);
	}

	/**
	 * Returns TRUE if the current version if it's greater than or equal to the required version
	 */
	protected static function _requiredVersion(float $required, float $current, string $locale = PackageInfo::Locale['US'], $action = null)
	{
		if ($locale == PackageInfo::Locale['US'])
		{
			return $current >= $required;
		}

		return true;
	}

	/**
	 * Returns the supported qbxml version="..." string for this qbXML version and locale
	 *
	 * Notes:
	 *  - If it's a US version of QuickBooks, "6.0" will yield "6.0"
	 * 	- If it's a non-US version, "3.0" will yield "CA3.0" where "CA" is the locale code (in this case, for Canada)
	 *
	 * @param float $version
	 * @param string $locale
	 * @return string
	 */
	protected static function _version($version, $locale)
	{
		if ($locale == PackageInfo::Locale['US'])
		{
			return $version;
		}

		return $locale . $version;
	}

	/**
	 * Returns the string $element if the current version is greater than or equal to the required version
	 *
	 * @param float $required
	 * @param float $current
	 * @param string $element
	 * @return string
	 */
	protected static function _requiredVersionForElement($required, $current, $element, $locale = PackageInfo::Locale['US'], $action = null)
	{
		if ($locale = PackageInfo::Locale['US'])
		{
			if ($current >= $required)
			{
				return $element;
			}
		}

		return '';
	}

	/**
	 * Used to build the iterator part of the XML string for every qbXML *QueryRq method
	 *
	 * Note: Funtion is intentionally returning a string without a closing '>', reason being so that people writing
	 * 	new functions for this class won't be confused, since they have to call the function in the middle of an
	 * 	XML tag, makes sense for them to have a closing bracket there
	 *
	 * @param mixed $extra 		The $extra value that is passed to the method you're calling this from
	 * @return string
	 */
	protected static function _buildIterator($extra, $version = null, $locale = null)
	{
		$xml = "";

		if ($locale == PackageInfo::Locale['CA'] || $locale == PackageInfo::Locale['UK'] || $locale == PackageInfo::Locale['AU'])
		{
			return '';
		}

		if (is_array($extra) &&
			!empty($extra['iteratorID']))
		{
			$xml .= ' iterator="Continue" iteratorID="' . $extra['iteratorID'] . '" ';
		}
		else
		{
			$xml .= ' iterator="Start" ';
		}

		$xml .= '>' . "\n";
		$xml .= "\t" . '<MaxReturned>';

		if (is_array($extra) &&
			!empty($extra['maxReturned']))
		{
			$xml .= $extra['maxReturned'];
		}
		else
		{
			$xml .= WebConnectorSQL::$ITERATOR_MAXRETURNED;
		}

		$xml .= '</MaxReturned';

		return $xml;
	}

	public static function InventoryLevelsRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="8.0"?>
			<QBXML>
				<QBXMLMsgsRq onError="stopOnError">
					<GeneralSummaryReportQueryRq requestID="' . $requestID . '">

						<GeneralSummaryReportType>InventoryStockStatusByItem</GeneralSummaryReportType>
						<DisplayReport>false</DisplayReport>

						<!--
						<ReportItemFilter>
							<ItemTypeFilter>Inventory</ItemTypeFilter>
						</ReportItemFilter>
						-->

					</GeneralSummaryReportQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 * Handle an inventory stock status report from QuickBooks
	 */
	public static function InventoryLevelsResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $callback_config = [])
	{
		$Driver = Singleton::getInstance();

		$col_defs = [];

		//mysql_query("INSERT INTO quickbooks_log ( msg, log_datetime ) VALUES ( 'TESTING', NOW() ) ") or die(mysql_error());

		// First, find the column definitions
		$tmp = $xml;
		$find = 'ColDesc';
		while ($inner = static::_reportNextXML($tmp, $find))
		{
			$colID = static::_reportExtractColID($inner);
			$type = static::_reportExtractColType($inner);

			$col_defs[$colID] = $type;
		}

		//print_r($col_defs);
		//exit;

		$items = [];

		// Now, find the actual data
		$tmp = $xml;
		$find = 'DataRow';
		while ($inner = static::_reportNextXML($tmp, $find))
		{
			$item = [
				'FullName' => null,
				'Blank' => null, 					//
				'ItemDesc' => null, 				//
				'ItemVendor' => null, 				// Pref Vendor
				'ReorderPoint' => null, 			// Reorder Pt
				'QuantityOnHand' => null, 			// On Hand
				'SuggestedReorder' => null, 		// Order
				'QuantityOnOrder' => null, 			// On PO
				'QuantityOnSalesOrder' => null, 	// On Sales Order
				'EarliestReceiptDate' => null, 		// Next Deliv
				'SalesPerWeek' => null, 			// Sales/Week
			];

			$find2 = 'RowData';
			if ($tag = static::_reportNextTag($inner, $find2))
			{
				$value = static::_reportExtractColValue($tag);

				$item['FullName'] = $value;
			}

			$find3 = 'ColData';
			while ($tag = static::_reportNextTag($inner, $find3))
			{
				$colID = static::_reportExtractColID($tag);
				$value = static::_reportExtractColValue($tag);

				if (array_key_exists($colID, $col_defs))
				{
					$item[$col_defs[$colID]] = $value;
				}
			}

			//$items[] = $item;

			/*
			Inventory for "another inventory": Array
			(
				[FullName] => another inventory
				[Blank] => another inventory
				[ItemDesc] =>
				[ItemVendor] =>
				[ReorderPoint] => 5
				[QuantityOnHand] => 35
				[SuggestedReorder] => false
				[QuantityOnOrder] => 0
				[EarliestReceiptDate] =>
				[SalesPerWeek] => 0
			)
			*/

			$Driver->log('Inventory for "' . $item['FullName'] . '": ' . print_r($item, true), null, PackageInfo::LogLevel['DEBUG']);
			//$errnum = null;
			//$errmsg = null;
			//mysql_query("INSERT INTO quickbooks_log VALUES ( msg, log_datetime) VALUES ( '" . mysql_real_escape_string(print_r($item, true)) . "', NOW() ) ");
			// UPDATE item SET QuantityOnHand = x WHERE FullName = y, resync = NOW() AND qbsql_resync_datetime = qbsql_modify_timestamp
			// if (!affected_rows)
			// 	UPDATE item SET QuantityOnHand = x WHERE FullName = y 		// this was a modified item, so it needs to stay modified

			$sql1 = "
				UPDATE
					" . Sql::$TablePrefix['SQL_MIRROR'] . "iteminventory
				SET
					QuantityOnHand = " . (float) $item['QuantityOnHand'] . ",
					QuantityOnOrder = " . (float) $item['QuantityOnOrder'] . ",
					QuantityOnSalesOrder = " . (float) $item['QuantityOnSalesOrder'] . ",
					qbsql_resync_datetime = '%s',
					qbsql_modify_timestamp = '%s'
				WHERE
					FullName = '%s' AND
					qbsql_resync_datetime = qbsql_modify_timestamp ";

			$datetime = date('Y-m-d H:i:s');

			$vars1 = [ $datetime, $datetime, $item['FullName'] ];

			$errnum = null;
			$errmsg = null;
			$Driver->query($sql1, $errnum, $errmsg, 0, 1, $vars1);

			//$Driver->log($sql1, null, PackageInfo::LogLevel['DEBUG']);

			if (!$Driver->affected())
			{
				$sql2 = "
					UPDATE
						" . Sql::$TablePrefix['SQL_MIRROR'] . "iteminventory
					SET
						QuantityOnHand = " . (float) $item['QuantityOnHand'] . ",
						QuantityOnOrder = " . (float) $item['QuantityOnOrder'] . ",
						QuantityOnSalesOrder = " . (float) $item['QuantityOnSalesOrder'] . "
					WHERE
						FullName = '%s' ";

				$vars2 = [ $item['FullName'] ];

				$errnum = null;
				$errmsg = null;
				$Driver->query($sql2, $errnum, $errmsg, 0, 1, $vars2);

				//$Driver->log($sql2, null, PackageInfo::LogLevel['DEBUG']);
			}

			$hooks = [];
			if (isset($callback_config['hooks']))
			{
				$hooks = $callback_config['hooks'];
			}

			$Driver->log('CALLING THE HOOKS! ' . print_r($hooks, true), null, PackageInfo::LogLevel['VERBOSE']);

			// Call any hooks that occur when a record is updated
			$hook_data = [
				'hook' => QuickbooksSql::HOOK_SQL_INVENTORY,
				'user' => $user,
				'table' => Sql::$TablePrefix['SQL_MIRROR'] . 'iteminventory',
				'data' => $item,
			];

			$err = null;
			static::_callHooks($hooks, QuickbooksSql::HOOK_SQL_INVENTORY, $requestID, $user, $err, $hook_data, $callback_config);
		}

		//print_r($items);

		//$Driver->log('Inventory: ' . print_r($items, true), null, PackageInfo::LogLevel['VERBOSE']);
	}

	public static function InventoryAssemblyLevelsRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="8.0"?>
			<QBXML>
				<QBXMLMsgsRq onError="stopOnError">
					<GeneralSummaryReportQueryRq requestID="' . $requestID . '">

						<GeneralSummaryReportType>InventoryStockStatusByItem</GeneralSummaryReportType>
						<DisplayReport>false</DisplayReport>

						<ReportItemFilter>
							<ItemTypeFilter>Assembly</ItemTypeFilter>
						</ReportItemFilter>

					</GeneralSummaryReportQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 * Handle an inventory stock status report from QuickBooks
	 */
	public static function InventoryAssemblyLevelsResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $callback_config = [])
	{
		$Driver = Singleton::getInstance();

		$col_defs = [];

		//mysql_query("INSERT INTO quickbooks_log ( msg, log_datetime ) VALUES ( 'TESTING', NOW() ) ") or die(mysql_error());

		// First, find the column definitions
		$tmp = $xml;
		$find = 'ColDesc';
		while ($inner = static::_reportNextXML($tmp, $find))
		{
			$colID = static::_reportExtractColID($inner);
			$type = static::_reportExtractColType($inner);

			$col_defs[$colID] = $type;
		}

		//print_r($col_defs);
		//exit;

		$items = [];

		// Now, find the actual data
		$tmp = $xml;
		$find = 'DataRow';
		while ($inner = static::_reportNextXML($tmp, $find))
		{
			$item = [
				'FullName' => null,
				'Blank' => null, 					//
				'ItemDesc' => null, 				//
				'ItemVendor' => null, 				// Pref Vendor
				'ReorderPoint' => null, 			// Reorder Pt
				'QuantityOnHand' => null, 			// On Hand
				'SuggestedReorder' => null, 		// Order
				'QuantityOnOrder' => null, 			// On PO
				'QuantityOnSalesOrder' => null, 	// On Sales Order
				'EarliestReceiptDate' => null, 		// Next Deliv
				'SalesPerWeek' => null, 			// Sales/Week
			];

			$find2 = 'RowData';
			if ($tag = static::_reportNextTag($inner, $find2))
			{
				$value = static::_reportExtractColValue($tag);

				$item['FullName'] = $value;
			}

			$find3 = 'ColData';
			while ($tag = static::_reportNextTag($inner, $find3))
			{
				$colID = static::_reportExtractColID($tag);
				$value = static::_reportExtractColValue($tag);

				if (array_key_exists($colID, $col_defs))
				{
					$item[$col_defs[$colID]] = $value;
				}
			}

			//$items[] = $item;

			/*
			Inventory for "another inventory": Array
			(
				[FullName] => another inventory
				[Blank] => another inventory
				[ItemDesc] =>
				[ItemVendor] =>
				[ReorderPoint] => 5
				[QuantityOnHand] => 35
				[SuggestedReorder] => false
				[QuantityOnOrder] => 0
				[EarliestReceiptDate] =>
				[SalesPerWeek] => 0
			)
			*/

			$Driver->log('Inventory Assembly for "' . $item['FullName'] . '": ' . print_r($item, true), null, PackageInfo::LogLevel['DEBUG']);
			//$errnum = null;
			//$errmsg = null;
			//mysql_query("INSERT INTO quickbooks_log VALUES ( msg, log_datetime) VALUES ( '" . mysql_real_escape_string(print_r($item, true)) . "', NOW() ) ");
			// UPDATE item SET QuantityOnHand = x WHERE FullName = y, resync = NOW() AND qbsql_resync_datetime = qbsql_modify_timestamp
			// if (!affected_rows)
			// 	UPDATE item SET QuantityOnHand = x WHERE FullName = y 		// this was a modified item, so it needs to stay modified

			$sql1 = "
				UPDATE
					" . Sql::$TablePrefix['SQL_MIRROR'] . "iteminventoryassembly
				SET
					QuantityOnHand = " . (float) $item['QuantityOnHand'] . ",
					QuantityOnOrder = " . (float) $item['QuantityOnOrder'] . ",
					QuantityOnSalesOrder = " . (float) $item['QuantityOnSalesOrder'] . ",
					qbsql_resync_datetime = '%s',
					qbsql_modify_timestamp = '%s'
				WHERE
					FullName = '%s' AND
					qbsql_resync_datetime = qbsql_modify_timestamp ";

			$datetime = date('Y-m-d H:i:s');

			$vars1 = array( $datetime, $datetime, $item['FullName'] );

			$errnum = null;
			$errmsg = null;
			$Driver->query($sql1, $errnum, $errmsg, 0, 1, $vars1);

			//$Driver->log($sql1, null, PackageInfo::LogLevel['DEBUG']);

			if (!$Driver->affected())
			{
				$sql2 = "
					UPDATE
						" . Sql::$TablePrefix['SQL_MIRROR'] . "iteminventoryassembly
					SET
						QuantityOnHand = " . (float) $item['QuantityOnHand'] . ",
						QuantityOnOrder = " . (float) $item['QuantityOnOrder'] . ",
						QuantityOnSalesOrder = " . (float) $item['QuantityOnSalesOrder'] . "
					WHERE
						FullName = '%s' ";

				$vars2 = array( $item['FullName'] );

				$errnum = null;
				$errmsg = null;
				$Driver->query($sql2, $errnum, $errmsg, 0, 1, $vars2);

				//$Driver->log($sql2, null, PackageInfo::LogLevel['DEBUG']);
			}

			$hooks = [];
			if (isset($callback_config['hooks']))
			{
				$hooks = $callback_config['hooks'];
			}

			$Driver->log('CALLING THE HOOKS! ' . print_r($hooks, true), null, PackageInfo::LogLevel['VERBOSE']);

			// Call any hooks that occur when a record is updated
			$hook_data = [
				'hook' => QuickbooksSql::HOOK_SQL_INVENTORYASSEMBLY,
				'user' => $user,
				'table' => Sql::$TablePrefix['SQL_MIRROR'] . 'iteminventoryassembly',
				'data' => $item,
			];

			$err = null;
			static::_callHooks($hooks, QuickbooksSql::HOOK_SQL_INVENTORYASSEMBLY, $requestID, $user, $err, $hook_data, $callback_config);
		}

		//print_r($items);

		//$Driver->log('Inventory: ' . print_r($items, true), null, PackageInfo::LogLevel['VERBOSE']);
	}

	static protected function _reportExtractColID(string $xml): ?string
	{
		$find = 'colID="';
		if (false !== ($sta = strpos($xml, $find)))
		{
			$end = strpos($xml, '"', $sta + strlen($find));

			return substr($xml, $sta + strlen($find), $end - $sta - strlen($find));
		}

		return null;
	}

	static protected function _reportExtractColType(string $xml): ?string
	{
		$find = '<ColType>';
		if (false !== ($sta = strpos($xml, $find)))
		{
			$end = strpos($xml, '</ColType>', $sta + strlen($find));

			return substr($xml, $sta + strlen($find), $end - $sta - strlen($find));
		}

		return null;
	}

	static protected function _reportExtractColValue(string $xml): ?string
	{
		$find = 'value="';
		if (false !== ($sta = strpos($xml, $find)))
		{
			$end = strpos($xml, '"', $sta + strlen($find));

			return substr($xml, $sta + strlen($find), $end - $sta - strlen($find));
		}

		return null;
	}

	static protected function _reportNextTag(string &$xml, string $find)
	{
		if (false !== ($sta = strpos($xml, $find)))
		{
			$end = strpos($xml, ' />', $sta);
			/*if (false === $end)
			{
				$end = strpos($xml, '</' . $find);
			}*/

			if (false !== $end)
			{
				$data = substr($xml, $sta - 1, $end - $sta + 4);

				$xml = substr($xml, $end + 2);

				return $data;
			}
		}

		return false;
	}

	static protected function _reportNextXML(string &$xml, string $find)
	{
		if (false !== ($sta = strpos($xml, $find)))
		{
			$end = strpos($xml, '/' . $find);

			$data = substr($xml, $sta - 1, $end - $sta + strlen($find) + 3);

			$xml = substr($xml, $end + strlen($find));

			return $data;
		}

		return false;
	}

	/**
	 * Fetch a list of deleted things from QuickBooks
	 */
	public static function ListDeletedQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '';

		if (!static::_requiredVersion(2.0, $version, $locale, PackageInfo::Actions['DEL_LIST']))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml .= '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ListDeletedQueryRq requestID="' . $requestID . '">
						<ListDelType>Account</ListDelType>
						<ListDelType>BillingRate</ListDelType>
						<ListDelType>Class</ListDelType>
						<ListDelType>Customer</ListDelType>
						<ListDelType>CustomerMsg</ListDelType>
						<ListDelType>CustomerType</ListDelType>
						<ListDelType>DateDrivenTerms</ListDelType>
						<ListDelType>Employee</ListDelType>
						<ListDelType>ItemDiscount</ListDelType>
						<ListDelType>ItemFixedAsset</ListDelType>
						<ListDelType>ItemGroup</ListDelType>
						<ListDelType>ItemInventory</ListDelType>
						<ListDelType>ItemInventoryAssembly</ListDelType>
						<ListDelType>ItemNonInventory</ListDelType>
						<ListDelType>ItemOtherCharge</ListDelType>
						<ListDelType>ItemPayment</ListDelType>
						<ListDelType>ItemSalesTax</ListDelType>
						<ListDelType>ItemSalesTaxGroup</ListDelType>
						<ListDelType>ItemService</ListDelType>
						<ListDelType>ItemSubtotal</ListDelType>
						<ListDelType>JobType</ListDelType>
						<ListDelType>OtherName</ListDelType>
						<ListDelType>PaymentMethod</ListDelType>
						<ListDelType>PayrollItemNonWage</ListDelType>
						<ListDelType>PayrollItemWage</ListDelType>
						<ListDelType>PriceLevel</ListDelType>
						<ListDelType>SalesRep</ListDelType>
						<ListDelType>SalesTaxCode</ListDelType>
						<ListDelType>ShipMethod</ListDelType>
						<ListDelType>StandardTerms</ListDelType>
						<ListDelType>ToDo</ListDelType>
						<ListDelType>UnitOfMeasureSet</ListDelType>
						<ListDelType>Vehicle</ListDelType>
						<ListDelType>Vendor</ListDelType>
						<ListDelType>VendorType</ListDelType>
						' . static::_buildFilter($user, $action, $extra, true) . '
					</ListDeletedQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 * Handle a list of deleted items from QuickBooks
	 */
	public static function ListDeletedQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Driver = Singleton::getInstance();

		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ListDeletedQueryRs');

		foreach ($List->children() as $Node)
		{
			$map = [];
			$others = [];

			Schema::mapToSchema(trim(Utilities::objectToXMLElement($Node->getChildDataAt('ListDeletedRet ListDelType'))), Schema::MAP_TO_SQL, $map, $others);

			if (isset($map[0]))
			{
				$table = $map[0];

				$data = [
					'qbsql_flag_deleted' => 1,
				];

				$multipart = array( 'ListID' => $Node->getChildDataAt('ListDeletedRet ListID') );

				$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $table, $data, array( $multipart ));
			}
		}

		return true;
	}

	/**
	 * Fetch a list of deleted transactions from QuickBooks
	 */
	public static function TxnDeletedQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '';


		if (!static::_requiredVersion(2.0, $version, $locale, PackageInfo::Actions['DELETE_TRANSACTION']))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml .= '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<TxnDeletedQueryRq requestID="' . $requestID . '">
					<TxnDelType>ARRefundCreditCard</TxnDelType>
					<TxnDelType>Bill</TxnDelType>
					<TxnDelType>BillPaymentCheck</TxnDelType>
					<TxnDelType>BillPaymentCreditCard</TxnDelType>
					<TxnDelType>BuildAssembly</TxnDelType>
					<TxnDelType>Charge</TxnDelType>
					<TxnDelType>Check</TxnDelType>
					<TxnDelType>CreditCardCharge</TxnDelType>
					<TxnDelType>CreditCardCredit</TxnDelType>
					<TxnDelType>CreditMemo</TxnDelType>
					<TxnDelType>Deposit</TxnDelType>
					<TxnDelType>Estimate</TxnDelType>
					<TxnDelType>InventoryAdjustment</TxnDelType>
					<TxnDelType>Invoice</TxnDelType>
					<TxnDelType>ItemReceipt</TxnDelType>
					<TxnDelType>JournalEntry</TxnDelType>
					<TxnDelType>PayrollLiabilityAdjustment</TxnDelType>
					<TxnDelType>PayrollPriorPayment</TxnDelType>
					<TxnDelType>PayrollYearToDateAdjustment</TxnDelType>
					<TxnDelType>PurchaseOrder</TxnDelType>
					<TxnDelType>ReceivePayment</TxnDelType>
					<TxnDelType>SalesOrder</TxnDelType>
					<TxnDelType>SalesReceipt</TxnDelType>
					<TxnDelType>SalesTaxPaymentCheck</TxnDelType>
					<TxnDelType>TimeTracking</TxnDelType>
					<TxnDelType>VehicleMileage</TxnDelType>
					<TxnDelType>VendorCredit</TxnDelType>
						' . static::_buildFilter($user, $action, $extra, true) . '
					</TxnDeletedQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 * Handle a list of deleted transactions from QuickBooks
	 *
	 * @todo Actually delete the elements.
	 */
	public static function TxnDeletedQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Driver = Singleton::getInstance();

		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs TxnDeletedQueryRs');

		foreach($List->children() as $Node)
		{
			$map = [];
			$others = [];

			Schema::mapToSchema(trim(Utilities::objectToXMLElement($Node->getChildDataAt("TxnDeletedRet TxnDelType"))), Schema::MAP_TO_SQL, $map, $others);

			/*
			$sqlObject = new SqlObject($map[0], trim(Utilities::objectToXMLElement($Node->getChildDataAt("TxnDeletedRet TxnDelType"))));
			$table = $sqlObject->table();
			*/

			if (!empty($map[0]))
			{
				$table = $map[0];
				$data = array(
					'qbsql_flag_deleted' => 1,
					);
				$multipart = array( 'TxnID' => $Node->getChildDataAt('TxnDeletedRet TxnID') );

				$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $table, $data, array( $multipart ));
			}

		}
		return true;
	}

	/**
	 * Delete an object within QuickBooks
	 */
	public static function ListDelRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		$ID = str_replace($extra['objectType'], '', $ID);

		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . strtolower($extra['objectType']), [Sql::Field['ID'] => $ID]))
		{
			$Object = new SqlObject(null, null, $arr);

			$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ListDelRq requestID="' . $requestID . '">
						<ListDelType>' . $extra['objectType'] . '</ListDelType>
						<ListID>' . $Object->get('ListID') . '</ListID>
					</ListDelRq>
				</QBXMLMsgsRq>
			</QBXML>';

			return $xml;
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ListDelResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Driver = Singleton::getInstance();
		$Parser = new Parser($xml);

		$errnum = XML::ERROR_OK;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ListDelRs');
		$Node = $List;

		if ($errnum == XML::ERROR_OK)
		{
			/*
			// @TODO this is all broken because of field name changes
			$map = [];
			$others = [];
			Schema::mapToSchema(trim(Utilities::objectToXMLElement($extra['objectType'])), Schema::MAP_TO_SQL, $map, $others);
			$object = new SqlObject($map[0], trim(Utilities::objectToXMLElement($extra['objectType'])));

			$table = $sqlObject->table();
			$multipart = array(
				'ListID' => $Node->getChildDataAt('ListDelRs ListID')
				);

				$object->set(Sql::Field['FLAG_DELETED'], 1);
				$object->set('ListID', $Node->getChildDataAt('ListDelRs ListID'));
				$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $table, $object, array( $multipart ));

			// Now Delete/Flag all the children
			//$deleted = [];
			//static::_deleteChildren($table, $user, $action, $ID, $object, $extra, $deleted, $config, true, true);
			*/
		}

		return true;
	}

	/**
	 * Void a transaction
	 *
	 * @return string
	 */
	public static function TxnVoidRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();

		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . strtolower($extra['object']), [Sql::Field['ID'] => $ID]))
		{
			$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="stopOnError">
					<TxnVoidRq requestID="' . $requestID . '">
						<TxnVoidType>' . $extra['object'] . '</TxnVoidType>
						<TxnID>' . $arr['TxnID'] . '</TxnID>
					</TxnVoidRq>
				</QBXMLMsgsRq>
			</QBXML>';

			return $xml;
		}

		return '';
	}

	/**
	 * Receive a response from QuickBooks about a voided transaction
	 *
	 * @return boolean
	 */
	public static function TxnVoidResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Driver = Singleton::getInstance();

		// Figure out what SQL table this object came from
		$map = [];
		$others = [];
		Schema::mapToSchema(trim(Utilities::objectToXMLElement($extra['object'])), Schema::MAP_TO_SQL, $map, $others);

		$table = Sql::$TablePrefix['SQL_MIRROR'] . $map[0];

		// We just need to set the voided flag on the transaction
		$update = array(
			Sql::Field['FLAG_VOIDED'] => 1,
			'AmountDue' => 0.0,
			'Amount' => 0.0,
			'OpenAmount' => 0.0,
			'Amount' => 0.0,
			'Subtotal' => 0.0,
			'TotalAmount' => 0.0,
			'BalanceRemaining' => 0.0,
			'SalesTaxTotal' => 0.0,
			);

		$where = [
			[Sql::Field['ID'] => $ID],
		];

		// Update the SQL table to indicate it was voided
		$Driver->update($table, $update, $where);

		return true;
	}

	/**
	 *
	 *
	 *
	 */
	public static function TxnDelRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		$ID = str_replace($extra['objectType'], "", $ID);
		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . strtolower($extra['objectType']), [Sql::Field['ID'] => $ID]))
		{
			$Object = new SqlObject(null, null, $arr);

			$xml = '<?xml version="1.0" encoding="utf-8"?>
				<?qbxml version="' . static::_version($version, $locale) . '"?>
				<QBXML>
					<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
						<TxnDelRq requestID="' . $requestID . '">
							<TxnDelType>' . $extra['objectType'] . '</TxnDelType>
							<TxnID>' . $Object->get('TxnID') . '</TxnID>
						</TxnDelRq>
					</QBXMLMsgsRq>
				</QBXML>';

			return $xml;
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function TxnDelResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Driver = Singleton::getInstance();
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs TxnDelRs');
		$Node = $List;

		if ($errnum == 0)
		{
			$map = [];
			$others = [];
			Schema::mapToSchema(trim(Utilities::objectToXMLElement($extra['objectType'])), Schema::MAP_TO_SQL, $map, $others);
			$sqlObject = new SqlObject($map[0], trim(Utilities::objectToXMLElement($extra['objectType'])));
			$table = $sqlObject->table();
			$multipart = array( "TxnID" => $Node->getChildDataAt("TxnDelRs TxnID") );

			//$config['delete'] =

			//Check the delete mode and if desired, just flag them rather than remove the rows.
			// @todo Fix this wrong delete flag field

			//mysql_query("UPDATE qb_bill SET qbsql_to_delete = 0, qbsql_flag_deleted = 1 WHERE TxnID = '" . $Node->getChildDataAt('TxnDelRs TxnID') . "' LIMIT 1");

			/*
			if (isset($config['delete']) and
				 $config['delete'] == SQL::DELETE_FLAG)
			{
				//@todo Make the Boolean TRUE value used in the QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG field a constant,
				//      in case the sql driver used uses something other than 1 and 0.
				$sqlObject->set(QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG, 1);
				$sqlObject->set("TxnID", $Node->getChildDataAt("TxnDelRs TxnID"));
				$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $table, $sqlObject, array( $multipart ));
				//Now Delete/Flag all the children.
				static::_DeleteChildren($table, $user, $action, $ID, $sqlObject, $extra, $config, true, true);
			}
			else
			{
				//Otherwise we actually remove the rows.
				$Driver->delete(Sql::$TablePrefix['SQL_MIRROR'] . $table, array( $multipart ));
				$sqlObject->set("TxnID", $Node->getChildDataAt("TxnDelRs TxnID"));
				//Now Delete/Flag all the children.
				static::_DeleteChildren($table, $user, $action, $ID, $sqlObject, $extra, $config, true, true);
			}
			*/
		}

		return true;
	}

	/**
	 * Fetch derived fields for a customer (Balance, TotalBalance, etc.)
	 */
	public static function CustomerDeriveRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!empty($extra['ListID']))
		{
			$xml = '<?xml version="1.0" encoding="utf-8"?>
				<?qbxml version="' . $version . '"?>
				<QBXML>
					<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
						<CustomerQueryRq requestID="' . $requestID . '">
							<ListID>' . $extra['ListID'] . '</ListID>
						</CustomerQueryRq>
					</QBXMLMsgsRq>
				</QBXML>';

			return $xml;
		}
		else if (!empty($extra['FullName']))
		{
			$xml = '<?xml version="1.0" encoding="utf-8"?>
				<?qbxml version="' . $version . '"?>
				<QBXML>
					<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
						<CustomerQueryRq requestID="' . $requestID . '">
							<FullName>' . Cast::cast(PackageInfo::Actions['OBJECT_CUSTOMER'], 'FullName', $extra['FullName']) . '</FullName>
						</CustomerQueryRq>
					</QBXMLMsgsRq>
				</QBXML>';

			return $xml;
		}

		$err = '' . __METHOD__ . ' called without a proper $extra array: ' . print_r($extra, true);
		return '';		// Error occurred
	}

	/**
	 * Handle a derived field (Balance, TotalBalance, etc.) response for a customer, and update the record
	 */
	public static function CustomerDeriveResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		return static::_deriveResponse('QBXML QBXMLMsgsRs CustomerQueryRs', PackageInfo::Actions['OBJECT_CUSTOMER'], $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ItemDeriveRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ItemQueryRq requestID="' . $requestID . '">
						<ListID>' . $extra['ListID'] . '</ListID>
					</ItemQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function ItemDeriveResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		return static::_deriveResponse('QBXML QBXMLMsgsRs ItemQueryRs', PackageInfo::Actions['OBJECT_ITEM'], $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 * Fetch derived fields for a customer (Balance, TotalBalance, etc.)
	 */
	public static function InvoiceDeriveRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		// Try to fetch it from the database
		$Driver = Singleton::getInstance();

		if (!empty($extra['TxnID']))
		{
			$xml = '<?xml version="1.0" encoding="utf-8"?>
				<?qbxml version="' . $version . '"?>
				<QBXML>
					<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
						<InvoiceQueryRq requestID="' . $requestID . '">
							<TxnID>' . $extra['TxnID'] . '</TxnID>
						</InvoiceQueryRq>
					</QBXMLMsgsRq>
				</QBXML>';

			return $xml;
		}
		else if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'invoice', [Sql::Field['ID'] => $ID]))
		{
			$xml = '<?xml version="1.0" encoding="utf-8"?>
				<?qbxml version="' . $version . '"?>
				<QBXML>
					<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
						<InvoiceQueryRq requestID="' . $requestID . '">
							<TxnID>' . $arr['TxnID'] . '</TxnID>
						</InvoiceQueryRq>
					</QBXMLMsgsRq>
				</QBXML>';

			return $xml;
		}

		$err = '' . __METHOD__ . ' called without a proper $extra array: ' . print_r($extra, true);
		return '';		// Error occurred
	}

	/**
	 * Handle a derived fields (Balance, TotalBalance, etc.) response for an invoices
	 */
	public static function InvoiceDeriveResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		return static::_deriveResponse('QBXML QBXMLMsgsRs InvoiceQueryRs', PackageInfo::Actions['OBJECT_INVOICE'], $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 */
	protected static function _deriveResponse($path, $type, $requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		// Account. 	Balance, TotalBalance
		// Bill. 		IsPaid, OpenAmount, AmountDue
		// Charge. 		BalanceRemaining
		// CreditMemo.	IsPending, CreditRemaining

		$Driver = Singleton::getInstance();
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt($path);
		foreach ($List->children() as $Node)
		{
			switch ($type)
			{
				case PackageInfo::Actions['OBJECT_ITEM']:

					$xpath = '';
					$table = '';
					switch ($Node->name())
					{
						case 'ItemServiceRet':
							$xpath = $Node->name();
							$table = 'itemservice';
							break;
						case 'ItemInventoryRet':
							$xpath = $Node->name();
							$table = 'iteminventory';
							break;
						case 'ItemNonInventoryRet';
							$xpath = $Node->name();
							$table = 'itemnoninventory';
							break;
					}

					if ($xpath and $table)
					{
						$arr = array(
							'ListID' => $Node->getChildDataAt($xpath . ' ListID'),
							'FullName' => $Node->getChildDataAt($xpath . ' FullName'),
							'Parent_ListID' => $Node->getChildDataAt($xpath . ' ParentRef ListID'),
							'Parent_FullName' => $Node->getChildDataAt($xpath . ' ParentRef FullName'),
							'IsActive' => (int) ($Node->getChildDataAt($xpath . ' IsActive') == 'true'),
							'EditSequence' => $Node->getChildDataAt($xpath . ' EditSequence'),
							);

						$Driver->log('Updating DERIVED ' . $xpath . ' fields: ' . print_r($arr, true) . ' where qbsql_id = ' . $ID, null, PackageInfo::LogLevel['VERBOSE']);
						//mysql_query("INSERT INTO quickbooks_log ( msg ) VALUES ( '" . mysql_real_escape_string('Updating DERIVED ' . $xpath . ' fields: ' . print_r($arr, true) . ' where qbsql_id = ' . $ID) . "' ) ");

						$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $table,
							$arr,
							[[ 'qbsql_id' => $ID ]],
							true, 		// Don't mark as re-synced
							false, 		// Don't update the discov time
							true);		// Do mark it as re-derived
					}

					break;
				case PackageInfo::Actions['OBJECT_VENDOR']:

					// Vendor.


					break;
				case PackageInfo::Actions['OBJECT_CUSTOMER']:

					// Customer.	Balance, TotalBalance,

					$arr = [
						'ListID' => $Node->getChildDataAt('CustomerRet ListID'),
						'FullName' => $Node->getChildDataAt('CustomerRet FullName'),
						'Parent_ListID' => $Node->getChildDataAt('CustomerRet ParentRef ListID'),
						'Parent_FullName' => $Node->getChildDataAt('CustomerRet ParentRef FullName'),
						'IsActive' => (int) ($Node->getChildDataAt('CustomerRet IsActive') == 'true'),
						'EditSequence' => $Node->getChildDataAt('CustomerRet EditSequence'),
						'Balance' => $Node->getChildDataAt('CustomerRet Balance'),
						'TotalBalance' => $Node->getChildDataAt('CustomerRet TotalBalance'),
					];

					//$Driver->log('Updating DERIVED CUSTOMER fields: ' . print_r($arr, true) . ' where: ' . print_r($extra, true), null, PackageInfo::LogLevel['VERBOSE']);

					$Driver->log('Updating DERIVED CUSTOMER fields: ' . print_r($arr, true) . ' where qbsql_id = ' . $ID, null, PackageInfo::LogLevel['VERBOSE']);

					$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . 'customer',
						$arr,
						[[ 'qbsql_id' => $ID ]],
						true, 		// Don't mark as re-synced
						false, 		// Don't update the discov time
						true);		// Do mark it as re-derived

					/*
					// @todo Only do this if the customer actually needs to be modified
					// Now, make a request to *modify* the customer
					$priority = 9998;		// @todo this probably isn't a good choice of priorities
					$Driver->queueEnqueue(
						$user,
						PackageInfo::Actions['MOD_CUSTOMER'],
						$ID,
						true,
						$priority);
					*/

					/*
					if (!empty($extra['ListID']))
					{
						// Update the database
						$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . 'customer', $arr, array( $extra ),
							false, 		// Don't mark as re-synced
							false, 		// Don't update the discov time
							true);		// Do mark it as re-derived
					}
					*/
					break;

				case PackageInfo::Actions['OBJECT_INVOICE']:
					// Invoice.		IsPending, AppliedAmount, BalanceRemaining, IsPaid

					$existing = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'invoice', [Sql::Field['ID'] => $ID]);

					$arr = [
						'TxnID' => $Node->getChildDataAt('InvoiceRet TxnID'),
						'EditSequence' => $Node->getChildDataAt('InvoiceRet EditSequence'),
						'AppliedAmount' => $Node->getChildDataAt('InvoiceRet AppliedAmount'),
						'BalanceRemaining' => $Node->getChildDataAt('InvoiceRet BalanceRemaining'),
					];

					if ($Node->getChildDataAt('InvoiceRet IsPending') == 'true')
					{
						$arr['IsPending'] = 1;
					}
					else if ($Node->getChildDataAt('InvoiceRet IsPending') == 'false')
					{
						$arr['IsPending'] = 0;
					}

					if ($Node->getChildDataAt('InvoiceRet IsPaid') == 'true')
					{
						$arr['IsPaid'] = 1;
					}
					else if ($Node->getChildDataAt('InvoiceRet IsPaid') == 'false')
					{
						$arr['IsPaid'] = 0;
					}

					$Driver->log('Updating DERIVED INVOICE fields: ' . print_r($arr, true) . ' where: ' . print_r($extra, true), null, PackageInfo::LogLevel['VERBOSE']);

					$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . 'invoice',
						$arr,
						[[ 'qbsql_id' => $ID ]],
						true, 		// Don't mark as re-synced
						false, 		// Don't update the discov time
						true);		// Do mark it as re-derived

					/*
					// @todo Only do this if the invoice actually needs to be modified
					// Now, make a request to *modify* the customer
					$priority = 9998;		// @todo this probably isn't a good choice of priorities
					$Driver->queueEnqueue(
						$user,
						PackageInfo::Actions['MOD_INVOICE'],
						$ID,
						true,
						$priority);

					// Blow away all of the old line items... (eeek!)
					$errnum = null;
					$errmsg = null;
					$Driver->query("
						UPDATE
							" . Sql::$TablePrefix['SQL_MIRROR'] . "invoice_invoiceline
						SET
							qbsql_to_skip = 1
						WHERE
							Invoice_TxnID = '%s' ", $errnum, $errmsg, null, null, array( $arr['TxnID'] ));

					// This should probably be done through the "Map\Qbxml::mark()" method...
					$errnum = null;
					$errmsg = null;
					$Driver->query("
						UPDATE
							" . Sql::$TablePrefix['SQL_MIRROR'] . "invoice_invoiceline
						SET
							Invoice_TxnID = '%s' WHERE Invoice_TxnID = '%s' ", $errnum, $errmsg, null, null, array( $arr['TxnID'], $existing['TxnID'] ));
					*/

					break;
				default:
					return false;
			}
		}

		return true;
	}

	/**
	 *
	 *
	 *
	 */
	public static function CustomerAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();

		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'customer', [Sql::Field['ID'] => $ID]))
		{
			$Customer = new SqlObject('customer', null, $arr);

			if (!(is_numeric($Customer->get('CreditCardInfo_ExpirationMonth')) &&
				is_numeric($Customer->get('CreditCardInfo_ExpirationYear')) &&
				checkdate($Customer->get('CreditCardInfo_ExpirationMonth'), 1, $Customer->get('CreditCardInfo_ExpirationYear')) ))
			{
				$Customer->remove('CreditCardInfo_ExpirationMonth');
				$Customer->remove('CreditCardInfo_ExpirationYear');
			}

			return static::_AddRequest(PackageInfo::Actions['OBJECT_CUSTOMER'], $Customer, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function CustomerAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CustomerAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_addResponse(PackageInfo::Actions['OBJECT_CUSTOMER'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function InventoryAdjustmentAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();

		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'inventoryadjustment', [Sql::Field['ID'] => $ID]))
		{
			$InventoryAdjustment = new SqlObject('inventoryadjustment', null, $arr);

			return static::_AddRequest(PackageInfo::Actions['OBJECT_INVENTORYADJUSTMENT'], $InventoryAdjustment, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function InventoryAdjustmentAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs InventoryAdjustmentAddRs');

		$extra['is_add_response'] = true;
		static::_addResponse(PackageInfo::Actions['OBJECT_INVENTORYADJUSTMENT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function CustomerMsgAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($CustomerMsg = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'customermsg', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_CUSTOMERMSG'], $CustomerMsg, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function CustomerMsgAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CustomerMsgAddRs');

		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_CUSTOMERMSG'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function JournalEntryAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();

		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'journalentry', [Sql::Field['ID'] => $ID]))
		{
			$JournalEntry = new SqlObject('journalentry', null, $arr);

			// Some (all?) versions of QuickBooks don't support this tag...?
			$JournalEntry->remove('IsAdjustment');

			return static::_AddRequest(PackageInfo::Actions['OBJECT_JOURNALENTRY'], $JournalEntry, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function JournalEntryAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs JournalEntryAddRs');

		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_JOURNALENTRY'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function JournalEntryModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(6.0, $version))
		{
			// JournalEntryMod requests are not supported until 6.0
			return PackageInfo::Actions['SKIP'];
		}

		$Driver = Singleton::getInstance();
		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'journalentry', [Sql::Field['ID'] => $ID]))
		{
			$JournalEntry = new SqlObject('journalentry', null, $arr);

			return static::_AddRequest(PackageInfo::Actions['OBJECT_JOURNALENTRY'], $JournalEntry, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function JournalEntryModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs JournalEntryModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_JOURNALENTRY'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function CustomerModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'customer', [Sql::Field['ID'] => $ID]))
		{
			$Customer = new SqlObject('customer', null, $arr);

			if ( !(is_numeric($Customer->get('CreditCardInfo_ExpirationMonth')) &&
				is_numeric($Customer->get('CreditCardInfo_ExpirationYear')) &&
				checkdate($Customer->get('CreditCardInfo_ExpirationMonth'), 1, $Customer->get('CreditCardInfo_ExpirationYear')) ))
			{
				$Customer->remove('CreditCardInfo_ExpirationMonth');
				$Customer->remove('CreditCardInfo_ExpirationYear');
			}

			//print('THIS IS RUNNING' . "\n");

			// Set these fields to "blank" if they aren't being set in the qbXML request
			$clear = [
				'Phone',
				'AltPhone',
				'Fax',
				'AltFax',
				'Email',
				'Contact',
				'AltContact',
			];

			foreach ($clear as $field)
			{
				//if (!$Customer->exists($field))
				if (!$Customer->get($field))
				{
					//print('setting ' . $field . ' to NULL' . "\n");
					$Customer->set($field, WebConnectorSQL::$VALUE_CLEAR);
				}
			}

			return static::_AddRequest(PackageInfo::Actions['OBJECT_CUSTOMER'], $Customer, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function CustomerModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CustomerModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_CUSTOMER'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 */
	public static function ClassAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Class = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'class', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_CLASS'], $Class, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ClassAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ClassAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_CLASS'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	/**
	 *
	 */
	public static function DataExtAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		$xml = '';
		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'dataext', [Sql::Field['ID'] => $ID]))
		{
			$DataExt = new SqlObject('dataext', null, $arr);

			$xml .= '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<DataExtAddRq requestID="' . $requestID . '">
						<DataExtAdd>
							<OwnerID>' . $DataExt->get("OwnerID") . '</OwnerID>
							<DataExtName>' . $DataExt->get("DataExtName") . '</DataExtName>
					';
			if ($DataExt->get("EntityType") != null)
			{
				$xml .= '<ListDataExtType>' . $DataExt->get("DataExtType") . '</ListDataExtType>
						 <ListObjRef>
						 	<ListID>' . $DataExt->get("Entity_ListID") . '</ListID>
						 </ListObjRef>
						 ';
			}
			else
			{
				$base = str_ireplace(array("line", "group"), "", end(explode("_", $DataExt->get("TxnType"))));
				$xml .= '<TxnDataExtType>' . $base . '</TxnDataExtType>
						';
				if (stripos($DataExt->get("TxnType"), "line") !== false)
				{
					$table = strtolower($base) . "_" . strtolower($DataExt->get("TxnType"));
					if ($temp = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . $table, array( "TxnLineID" => $DataExt->get("Txn_TxnID") )))
					{
						$xml .= '<TxnID>' . $temp->get( $base . '_TxnID' ) . '</TxnID>
								 <TxnLineID>' . $DataExt->get("Txn_TxnID") . '</TxnLineID>
								';
					}
					else
					{
						return '';
					}
				}
				else
				{
					$xml .= '<TxnID>' . $DataExt->get("Txn_TxnID") . '</TxnID>
							';
				}
			}
			$xml .= '<DataExtValue>' . $DataExt->get("DataExtValue") . '</DataExtValue>
				  </DataExtAdd>
				</DataExtAddRq>
		      </QBXMLMsgsRq>
			</QBXML>';
		}

		return $xml;
	}

	/**
	 *
	 *
	 *
	 */
	public static function DataExtAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs DataExtAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_DATAEXT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	/**
	 *
	 */
	public static function DataExtModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		$xml = '';
		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'dataext', [Sql::Field['ID'] => $ID]))
		{
			$DataExt = new SqlObject('dataext', null, $arr);

			$xml .= '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<DataExtModRq requestID="' . $requestID . '">
						<DataExtMod>
							<OwnerID>' . $DataExt->get("OwnerID") . '</OwnerID>
							<DataExtName>' . $DataExt->get("DataExtName") . '</DataExtName>
					';
			if($DataExt->get("EntityType") != null)
			{
				$xml .= '<ListDataExtType>' . $DataExt->get("DataExtType") . '</ListDataExtType>
						 <ListObjRef>
						 	<ListID>' . $DataExt->get("Entity_ListID") . '</ListID>
						 </ListObjRef>
						 ';
			}
			else
			{
				$base = str_ireplace(array("line", "group"), "", end(explode("_", $DataExt->get("TxnType"))));
				$xml .= '<TxnDataExtType>' . $base . '</TxnDataExtType>
						';
				if(stripos($DataExt->get("TxnType"), "line") !== false)
				{
					$table = strtolower($base) . "_" . strtolower($DataExt->get("TxnType"));
					if($temp = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . $table, array( "TxnLineID" => $DataExt->get("Txn_TxnID") )))
					{
						$xml .= '<TxnID>' . $temp->get( $base . '_TxnID' ) . '</TxnID>
								 <TxnLineID>' . $DataExt->get("Txn_TxnID") . '</TxnLineID>
								';
					}
					else
						return '';
				}
				else
				{
					$xml .= '<TxnID>' . $DataExt->get("Txn_TxnID") . '</TxnID>
							';
				}
			}
			$xml .= '<DataExtValue>' . $DataExt->get("DataExtValue") . '</DataExtValue>
				  </DataExtMod>
				</DataExtModRq>
		      </QBXMLMsgsRq>
			</QBXML>';
		}

		return $xml;
	}

	/**
	 *
	 *
	 *
	 */
	public static function DataExtModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs DataExtModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_DATAEXT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 */
	public static function ShipMethodAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($ShipMethod = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'shipmethod', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SHIPMETHOD'], $ShipMethod, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ShipMethodAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ShipMethodAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SHIPMETHOD'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 */
	public static function PaymentMethodAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($PaymentMethod = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'paymentmethod', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_PAYMENTMETHOD'], $PaymentMethod, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function PaymentMethodAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs PaymentMethodAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_PAYMENTMETHOD'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function AccountAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'account', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_ACCOUNT'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function AccountAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs AccountAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_ACCOUNT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function AccountModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'account', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_ACCOUNT'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function AccountModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs AccountModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_ACCOUNT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemDiscountAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemdiscount', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_DISCOUNTITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemDiscountAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemDiscountAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_DISCOUNTITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemDiscountModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemdiscount', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_DISCOUNTITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemDiscountModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemDiscountModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_DISCOUNTITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemFixedAssetAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemfixedasset', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_FIXEDASSETITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemFixedAssetAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemFixedAssetAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_FIXEDASSETITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemFixedAssetModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemfixedasset', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_FIXEDASSETITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemFixedAssetModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemFixedAssetModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_FIXEDASSETITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemInventoryAssemblyAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'iteminventoryassembly', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_INVENTORYASSEMBLYITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemInventoryAssemblyAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Driver = Singleton::getInstance();
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemInventoryAssemblyAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_INVENTORYASSEMBLYITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);

		//$Driver->queueEnqueue($user, PackageInfo::Actions['QUERY_INVENTORYADJUSTMENT'], md5(__FILE__), true, Utilities::priorityForAction(PackageInfo::Actions['QUERY_INVENTORYADJUSTMENT']));
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemInventoryAssemblyModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'iteminventoryassembly', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_INVENTORYASSEMBLYITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemInventoryAssemblyModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemInventoryAssemblyModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_INVENTORYASSEMBLYITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemInventoryAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'iteminventory', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_INVENTORYITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemInventoryAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Driver = Singleton::getInstance();
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemInventoryAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_INVENTORYITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);

		//$Driver->queueEnqueue($user, PackageInfo::Actions['QUERY_INVENTORYADJUSTMENT'], md5(__FILE__), true, Utilities::priorityForAction(PackageInfo::Actions['QUERY_INVENTORYADJUSTMENT']));
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemInventoryModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'iteminventory', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_INVENTORYITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemInventoryModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemInventoryModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_INVENTORYITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	/**
	 *
	 *
	 *
	 */
	public static function ItemNonInventoryAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemnoninventory', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_NONINVENTORYITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemNonInventoryAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemNonInventoryAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_NONINVENTORYITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemNonInventoryModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemnoninventory', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_NONINVENTORYITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemNonInventoryModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemNonInventoryModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_NONINVENTORYITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemOtherChargeAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemothercharge', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_OTHERCHARGEITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemOtherChargeAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemOtherChargeAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_OTHERCHARGEITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemOtherChargeModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemothercharge', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_OTHERCHARGEITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemOtherChargeModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemOtherChargeModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_OTHERCHARGEITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemPaymentAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itempayment', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_PAYMENTITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemPaymentAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemPaymentAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_PAYMENTITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemPaymentModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itempayment', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_PAYMENTITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemPaymentModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemPaymentModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_PAYMENTITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemSalesTaxAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemsalestax', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESTAXITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemSalesTaxAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemSalesTaxAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESTAXITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemSalesTaxModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemsalestax', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESTAXITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemSalesTaxModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemSalesTaxModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESTAXITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemServiceAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemservice', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SERVICEITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemServiceAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemServiceAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SERVICEITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemServiceModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemservice', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SERVICEITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemServiceModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemServiceModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SERVICEITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemSubtotalAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemsubtotal', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SUBTOTALITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemSubtotalAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemSubtotalAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SUBTOTALITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemSubtotalModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'itemsubtotal', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SUBTOTALITEM'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function EmployeeAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'employee', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_EMPLOYEE'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function EmployeeAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs EmployeeAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_EMPLOYEE'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function EmployeeModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'employee', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_EMPLOYEE'], $arr, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function EmployeeModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs EmployeeModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_EMPLOYEE'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemSubtotalModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemSubtotalModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SUBTOTALITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function EstimateAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'estimate', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_ESTIMATE'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function EstimateAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs EstimateAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_ESTIMATE'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function EstimateModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'estimate', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_ESTIMATE'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function EstimateModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs EstimateModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_ESTIMATE'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function PurchaseOrderAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'purchaseorder', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_PURCHASEORDER'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function PurchaseOrderAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs PurchaseOrderAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_PURCHASEORDER'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function PurchaseOrderModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'purchaseorder', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_PURCHASEORDER'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function PurchaseOrderModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs PurchaseOrderModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_PURCHASEORDER'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ReceivePaymentAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'receivepayment', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_RECEIVEPAYMENT'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ReceivePaymentAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ReceivePaymentAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_addResponse(PackageInfo::Actions['OBJECT_RECEIVEPAYMENT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ReceivePaymentModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		//$args = func_get_args();
		//$Driver = Singleton::getInstance();
		//$Driver->log('got in: ' . print_r($args, true));

		$Driver = Singleton::getInstance();
		if ($ReceivePayment = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'receivepayment', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_RECEIVEPAYMENT'], $ReceivePayment, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function ReceivePaymentModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ReceivePaymentModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_RECEIVEPAYMENT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function InvoiceAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Invoice = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'invoice', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_INVOICE'], $Invoice, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function InvoiceAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs InvoiceAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_addResponse(PackageInfo::Actions['OBJECT_INVOICE'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 * Generate a qbXML InvoiceMod request to update an invoice
	 */
	public static function InvoiceModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		//$Driver = Singleton::getInstance();
		//$args = func_get_args();
		//$Driver->log('got in: ' . print_r($args, true));

		$Driver = Singleton::getInstance();
		if ($Invoice = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'invoice', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_INVOICE'], $Invoice, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 * Handle an InvoiceMod response from QuickBooks
	 */
	public static function InvoiceModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs InvoiceModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_INVOICE'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesReceiptAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($SalesReceipt = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'salesreceipt', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESRECEIPT'], $SalesReceipt, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesReceiptAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesReceiptAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESRECEIPT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 * Generate a qbXML InvoiceMod request to update an invoice
	 */
	public static function SalesReceiptModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($SalesReceipt = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'salesreceipt', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESRECEIPT'], $SalesReceipt, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 * Handle an InvoiceMod response from QuickBooks
	 */
	public static function SalesReceiptModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesReceiptModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESRECEIPT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	/**
	 *
	 *
	 *
	 */
	public static function CreditMemoAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Invoice = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'creditmemo', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_CREDITMEMO'], $Invoice, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function CreditMemoAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CreditMemoAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_CREDITMEMO'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 * Generate a qbXML InvoiceMod request to update an invoice
	 */
	public static function CreditMemoModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		//$Driver = Singleton::getInstance();
		//$args = func_get_args();
		//$Driver->log('got in: ' . print_r($args, true));

		$Driver = Singleton::getInstance();
		if ($Invoice = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'creditmemo', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_CREDITMEMO'], $Invoice, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 * Handle an InvoiceMod response from QuickBooks
	 */
	public static function CreditMemoModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CreditMemoModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_CREDITMEMO'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 * Generate a JobTypeAdd qbXML request to send to QuickBooks
	 */
	public static function JobTypeAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($JobType = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'jobtype', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_JOBTYPE'], $JobType, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 * Handle a JobTypeAdd response from QuickBooks
	 */
	public static function JobTypeAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs JobTypeAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_JOBTYPE'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 * Generate a qbXML SalesOrderAdd request to send to QuickBooks
	 */
	public static function SalesOrderAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'salesorder', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESORDER'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 * Handle a SalesOrderAdd response from QuickBooks
	 */
	public static function SalesOrderAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesOrderAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESORDER'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesOrderModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'salesorder', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESORDER'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesOrderModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesOrderModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESORDER'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesRepAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'salesrep', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESREP'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesRepAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesRepAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESREP'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesRepModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'salesrep', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESREP'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesRepModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesRepModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESREP'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesTaxCodeAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'salestaxcode', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESTAXCODE'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesTaxCodeAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesTaxCodeAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESTAXCODE'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesTaxCodeModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Account = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'salestaxcode', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_SALESTAXCODE'], $Account, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function SalesTaxCodeModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesTaxCodeModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESTAXCODE'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function BillAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Customer = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'bill', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_BILL'], $Customer, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function BillAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillAddRs');
		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_BILL'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function BillModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Bill = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'bill', [Sql::Field['ID'] => $ID]))
		{
			return static::_ModRequest(PackageInfo::Actions['OBJECT_BILL'], $Bill, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function BillModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		return static::_QueryResponse(PackageInfo::Actions['OBJECT_BILL'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function BillPaymentCheckAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($BillPaymentCheck = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'billpaymentcheck', [Sql::Field['ID'] => $ID]))
		{
			// Special handling for bill payment check add requests

			// If a RefNumber is printed, then it can't be set as IsToBePrinted
			if (!empty($BillPaymentCheck['RefNumber']))
			{
				unset($BillPaymentCheck['IsToBePrinted']);
			}
			else
			{
				unset($BillPaymentCheck['RefNumber']);
			}

			return static::_AddRequest(PackageInfo::Actions['OBJECT_BILLPAYMENTCHECK'], $BillPaymentCheck, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function BillPaymentCheckAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillPaymentCheckAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_BILLPAYMENTCHECK'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function BillPaymentCheckModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($BillPaymentCheck = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'billpaymentcheck', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_BILLPAYMENTCHECK'], $BillPaymentCheck, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function BillPaymentCheckModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillPaymentCheckModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_BILLPAYMENTCHECK'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function BillPaymentCreditCardAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($BillPaymentCreditCard = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'billpaymentcreditcard', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_BILLPAYMENTCREDITCARD'], $BillPaymentCreditCard, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function BillPaymentCreditCardAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillPaymentCreditCardAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_BILLPAYMENTCREDITCARD'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function VendorAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Vendor = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'vendor', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_VENDOR'], $Vendor, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function VendorAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs VendorAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_VENDOR'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function VendorModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Vendor = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'vendor', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_VENDOR'], $Vendor, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function VendorModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs VendorModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_VENDOR'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	/**
	 *
	 *
	 *
	 */
	public static function VendorCreditAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Vendor = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'vendorcredit', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_VENDORCREDIT'], $Vendor, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function VendorCreditAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs VendorCreditAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_VENDORCREDIT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function VendorCreditModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Vendor = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'vendorcredit', [Sql::Field['ID'] => $ID]))
		{
			return static::_AddRequest(PackageInfo::Actions['OBJECT_VENDORCREDIT'], $Vendor, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function VendorCreditModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs VendorCreditModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_VENDORCREDIT'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 * Send an *Add request to QuickBooks to add an object to the QuickBooks database
	 *
	 * @param string $type			The QuickBooks object type
	 * @param object $Object		The object to add to QuickBooks
	 * @param string $requestID		The requestID to use in the qbXML request
	 * @param string $user			The username of the Web Connector user
	 * @param string $action		The action type (example: "CustomerAdd", PackageInfo::Actions['ADD_CUSTOMER'])
	 * @param string $ID			The qbsql_id value of the object to add
	 * @param mixed $extra			Any extra data
	 * @param string $err			Set this to an error message if an error occurs
	 * @param integer $last_action_time			The UNIX timestamp indicating the last time this type of action occurred
	 * @param integer $last_actionident_time	The UNIX timestamp indicating the (last time this type of action and $ID value) occurred
	 * @param float $version		The maximum qbXML version supports
	 * @param string $locale		The QuickBooks locale (example: "US")
	 * @param array $config			Callback configuration information
	 * @return string				The qbXML string
	 */
	protected static function _AddRequest($type, $Object, $requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		// Driver instance...
		$Driver = Singleton::getInstance();

		$type = strtolower($type);

		// This should actually always happen now that we fixed the Driver->get method to return an array
		if (!is_object($Object) && is_array($Object))
		{
			$Object = new SqlObject(null, null, $Object);
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>' . PackageInfo::$CRLF;
		$xml .= '<?qbxml version="' . static::_version($version, $locale) . '"?>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<QBXML>' . PackageInfo::$CRLF;
		$xml .= "\t" . "\t" . '<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">' . PackageInfo::$CRLF;
		$xml .= "\t" . "\t" . "\t" . '<' . Utilities::actionToRequest($action) . ' requestID="' . $requestID . '">' . PackageInfo::$CRLF;

		$class = PackageInfo::NAMESPACE_QBXML_SCHEMA_OBJECT . "\\". Utilities::actionToRequest($action);
		$schema_object = new $class();

		//print_r($Object->asArray());
		//exit;

		$Node = new Node($action);
		foreach ($Object->asArray() as $field => $value)	// map the SQL fields back to XML
		{
			$map = '';
			$others = [];
			Schema::mapToSchema($type . '.' . $field, Schema::MAP_TO_XML, $map, $others);

			// Some drivers case fold all of the field names, which means that
			//	we can't depend on the field names to use in our XML requests
			//
			// If that happens, we need to go over to the schema object and
			//	try to fetch the correct, unfolded XML node name and set that
			//	instead.
			$unmapped = null;
			if ($Driver->foldsToLower())
			{
				$unmapped = $map;

				$retpos = strpos($map, 'Ret ');
				$retval = substr($map, 0, $retpos + 4);

				$map = substr($map, $retpos + 4);
				$map = $retval . $schema_object->unfold($map);
			}

			//print('dealing with: [' . $map . '] unmapped from (' . $unmapped . ')' . "\n");

			if (!$map)
			{
				// This schema field doesn't map to anything in QuickBooks...
				continue;
			}
			else if (!strlen($value))
			{
				// There's no value there, don't send it

				// There are some special cases here... addresses commonly get
				//	changes to set blank lines for some of the address lines,
				//	and we need to send these blank values to overwrite the
				//	existing values in QuickBooks.
				//
				// Here, we send these blank address lines only if at least one
				//	address line is being sent.
				$begi = substr($field, 0, -5);
				$last = substr($field, -5, 5);
				if (($last == 'Addr2' || $last == 'Addr3' || $last == 'Addr4' || $last == 'Addr5') &&
					strlen($Object->get($begi . 'Addr1')))
				{
					// ... but don't allow 4 or 5 if they set the city, state, zip, or country?
					//  EDIT: NEVER ALLOW ADDR4 OR ADDR5, IT JUST FUCKS SHIT UP! I HATE YOU INTUIT!
					//   WHAT DOES THIS MEAN?!?   "The &quot;address&quot; field has an invalid value &quot;&quot;.  QuickBooks error message: The parameter is incorrect."
					if (($last == 'Addr4' || $last == 'Addr5'))
					{
						continue;
					}

					/* and
						( strlen($Object->get($begi . 'City')) or strlen($Object->get($begi . 'State')) or strlen($Object->get($begi . 'Country')) or strlen($Object->get($begi . 'PostalCode')) ))
					{
						continue;
					}
					*/

					// <Judge Roy Snyder>... I'll allow it!
				}
				else
				{
					continue;
				}
			}
			else
			{
				if ($value == WebConnectorSQL::$VALUE_CLEAR)
				{
					$value = '';
				}

				$use_abbrevs = false;
				$htmlspecialchars = true;

				//print('THIS RAN [' . $value . ']');

				$value = Cast::cast(
					null,
					null,
					$value,
					$use_abbrevs,
					$htmlspecialchars);

				//print(' => [' . $value . ']' . "\n");
			}

			// Special handling for non-US versions of QuickBooks

			$begi = substr($field, 0, -5);
			$last = substr($field, -5, 5);
			if ($locale == PackageInfo::Locale['UK'] &&
				$last == 'State' &&
				!strlen($Object->get($begi . 'County')))		// UK *County* support instead of states
			{
				$Object->set($begi . 'County', $value);
				//print_r($map);
				$map = substr($map, 0, -5) . 'County';
				//die();
			}

			// OK, the paths look like this:
			// 	CustomerRet FirstName
			//
			// We don't need the 'CustomerRet' part of it, that's actually incorrect, so we'll strip it off
			$explode = explode(' ', $map);
			$first = trim(current($explode));
			$map = trim(implode(' ', array_slice($explode, 1)));

			if (stripos($action, 'add') !== false)
			{
				$map = str_replace('Ret', 'Add', $map);
			}
			else
			{
				$map = str_replace('Ret', 'Mod', $map);
			}

			//print('	OK, handling [' . $map . ']' . "\n");

			if (false === strpos($map, ' '))
			{
				if ($schema_object->exists($map))
				{
					$use_in_request = true;

					// If this version doesn't support this field, skip it
					if ($schema_object->sinceVersion($map) > $version)
					{
						$use_in_request = false;
					}

					switch ($schema_object->dataType($map))
					{
						case 'AMTTYPE':

							$value = str_replace(',', '', number_format($value, 2));

							break;
						case 'DATETYPE':

							if (!$value or $value == '0000-00-00')
							{
								$use_in_request = false;
							}
							else
							{
								$value = Utilities::date($value);
							}

							break;
						case 'DATETIMETYPE':

							if (!$value or $value == '0000-00-00 00:00:00')
							{
								$use_in_request = false;
							}
							else
							{
								$value = Utilities::datetime($value);
							}

							break;
						case 'BOOLTYPE':

							if ($value == 1)
							{
								$value = 'true';
							}
							else if ($value == 0)
							{
								$value = 'false';
							}
							else
							{
								$use_in_request = false;
							}

							break;
						default:
							break;
					}

					if ($use_in_request)
					{
						$Child = new Node($map);
						$Child->setData($value);
						$Node->addChild($Child);
					}
				}
				else
				{
					// ignore it
				}
			}
			else
			{
				$parts = explode(' ', $map);
				foreach($parts as $key => $part)
				{
					if (stripos($action, 'Mod') !== false)
					{
						if ($part == 'SalesAndPurchase' || $part == 'SalesOrPurchase')
						{
							$parts[$key] = $part . 'Mod';
						}
					}
					else if (stripos($action, 'Add') !== false)
					{

					}
				}

				$map = implode(' ', $parts);
				if ($schema_object->exists($map))
				{
					$use_in_request = true;

					// If this version doesn't support this field, skip it
					if ($schema_object->sinceVersion($map) > $version)
					{
						$use_in_request = false;
					}

					switch ($schema_object->dataType($map))
					{
						case 'AMTTYPE':

							$value = str_replace(',', '', number_format($value, 2));

							break;
						case 'DATETYPE':

							if (!$value or $value == '0000-00-00')
							{
								$use_in_request = false;
							}
							else
							{
								$value = Utilities::date($value);
							}

							break;
						case 'DATETIMETYPE':

							if (!$value or $value == '0000-00-00 00:00:00')
							{
								$use_in_request = false;
							}
							else
							{
								$value = Utilities::datetime($value);
							}

							break;
						case 'BOOLTYPE':

							if ($value == 1)
							{
								$value = 'true';
							}
							else if ($value == 0)
							{
								$value = 'false';
							}
							else
							{
								$use_in_request = false;
							}

							break;
						default:
							break;
					}

					if ($use_in_request)
					{
						$Node->setChildDataAt($action . ' ' . $map, $value, true);
					}
				}
			}
		}

		// Get the child tables here - make sure they're in the proper order for the xml schema
		$children = static::_getChildTables(strtolower($type));

		//print('for type: [' . $type . ']');
		//print_r($children);
		//exit;

		if (!empty($children)) // Get the rows
		{
			$data = static::_queryChildren($children, $Object->get($children[0]['parent']));
		}
		else
		{
			// @todo Why not just assign an empty array here...?
			$data = $children;
		}

		$nodes = static::_childObjectsToXML(strtolower($type), $action, $data);

		//print('NODES:');
		//print_r($nodes);
		//exit;

		if (count($nodes))
		{
			foreach ($nodes as $nd)
			{
				$Node->addChild($nd);
			}
		}
		else
		{
			// If we're adding a payment, but not applying it to anything, set IsAutoApply to TRUE
			if ($action == PackageInfo::Actions['ADD_RECEIVEPAYMENT'])
			{
				$Node->setChildDataAt($action . ' IsAutoApply', 'true', true);
			}
			else if ($action == PackageInfo::Actions['MOD_RECEIVEPAYMENT'])
			{

			}
		}

		//print('ACTION IS: [' . $action . ']');

		//print_r($Node);

		$xml .= $Node->asXML(XML::XML_PRESERVE);

		// Bad hack...
		$xml = str_replace('&amp;#', '&#', $xml);
		$xml = str_replace('&amp;amp;', '&amp;', $xml);
		$xml = str_replace('&amp;quot;', '&quot;', $xml);

		$xml .= '</' . Utilities::actionToRequest($action) .'>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
 	 *
 	 *
 	 */
	protected static function _ModRequest($type, $Object, $requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		return static::_AddRequest($type, $Object, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
	}

	protected static function _ChildObjectsToXML($type, $action, $children, $parentPath = '')
	{
		$Driver = Singleton::getInstance();

		$nodes = [];

		$class = PackageInfo::NAMESPACE_QBXML_SCHEMA_OBJECT . "\\" . Utilities::actionToRequest($action);
		$schema_object = new $class();

		$usePath = '';
		if ($parentPath != '')
		{
			$usePath .= $parentPath . ' ';
		}

		foreach ($children as $child)
		{
			// Figure out which LinkedTxn method should be used...
			if (strpos($child['table'], "linkedtxn") !== false)
			{
				if (stripos($action, "add") !== false)
				{
					$part = preg_replace("/add/i", "", $action);
					$part .= "LineAdd";
				}
				else if (stripos($action, 'mod') !== false)
				{
					$part = preg_replace("/mod/i", "", $action);
					$part .= "LineMod";
				}

				if ($schema_object->exists($usePath . 'LinkToTxnID'))
				{
					$Node = new Node("LinkToTxnID", $child['data']->get("ToTxnID"));
					$nodes[count($nodes)] = $Node;
					continue;
				}
				else if ($schema_object->exists($action . ' ' . $part . ' ' . 'LinkToTxn'))
				{
					$Node = new Node("LinkToTxnID", $child['data']->get("ToTxnID"));
					$nodes[count($nodes)] = $Node;
					continue;
				}
				else if ($schema_object->exists($usePath . 'LinkedTxn'))
				{
					$Node = new Node("LinkToTxnID", $child['data']->get("ToTxnID"));
					$nodes[count($nodes)] = $Node;
					continue;
				}
				else if ($schema_object->exists($action . ' ' . $part . ' ' . 'LinkedTxn'))
				{
					$Node = new Node("LinkToTxnID", $child['data']->get("ToTxnID"));
					$nodes[count($nodes)] = $Node;
					continue;
				}
				else if ($schema_object->exists($usePath . 'ApplyCheckToTxnAdd'))
				{
					$Node = new Node("ApplyCheckToTxnAdd");
					$Node->setChildDataAt($Node->name() . ' ' . 'TxnID', $child['data']->get("ToTxnID"));
					$Node->setChildDataAt($Node->name() . ' ' . 'Amount', $child['data']->get("ToTxnID"));
					$nodes[count($nodes)] = $Node;
					continue;
				}
				else if ($schema_object->exists($usePath . 'ApplyCheckToTxnMod'))
				{
					$Node = new Node("ApplyCheckToTxnMod");
					$Node->setChildDataAt($Node->name() . ' ' . 'TxnID', $child['data']->get("ToTxnID"));
					$Node->setChildDataAt($Node->name() . ' ' . 'Amount', $child['data']->get("ToTxnID"));
					$nodes[count($nodes)] = $Node;
					continue;
				}
				else
				{
					continue;
				}
			}
			else if (strpos($child['table'], "dataext") !== false)
			{
				continue;
			}

			$map = '';
			$others = [];
			Schema::mapToSchema($child['table'] . '.*', Schema::MAP_TO_XML, $map, $others);

			$map = str_replace(' *', '', $map);

			$explode = explode(' ', $map);
			$first = trim(current($explode));
			$map = trim(implode(' ', array_slice($explode, 1)));

			if (stripos($action, 'add') !== false)
			{
				$map = str_replace('Ret', 'Add', $map);
			}
			else
			{
				$map = str_replace('Ret', 'Mod', $map);
			}

			// Journal entries have an unusual JournalEntryMod syntax. Instead of
			//	the typical CreditLineMod and DebitLineMod entries, they instead
			//	have just a single combined entry, JournalLineMod.
			if ($action == PackageInfo::Actions['MOD_JOURNALENTRY'])
			{
				if ($child['table'] == 'journalentry_journaldebitline' or $child['table'] == 'journalentry_journalcreditline')
				{
					$map = 'JournalLineMod';
				}
			}

			$Node = new Node($map);
			/*
				$retArr[$index]["table"] = $table;
				$retArr[$index]["data"] = SqlObject($table, null, $arr);
				$retArr[$index]["children"]
			*/

			foreach ($child['data']->asArray() as $field => $value)	// map the SQL fields back to XML
			{
				$map = '';
				$others = [];
				Schema::mapToSchema($child['table'] . '.' . $field, Schema::MAP_TO_XML, $map, $others);

				if ($Driver->foldsToLower())
				{
					$retpos = strpos($map, 'Ret ');
					$retval = substr($map, 0, $retpos + 4);

					$map = substr($map, $retpos + 4);

					if (stripos($action, 'add') !== false)
					{
						$map = str_replace('Ret ', 'Add ', $map);
					}
					else
					{
						$map = str_replace('Ret ', 'Mod ', $map);
					}

					//print('unfolding: {' . $map . '}' . "\n");

					$map = $schema_object->unfold($map);

					//print('	unfolded to: [' . $map . ']' . "\n");
				}

				//print($field . ' => ' . $value . "\n");
				//print_r($map);
				//print("\n\n");

				if (!$map || !strlen($value))
				{
					continue;
				}

				// OK, the paths look like this:
				// 	CustomerRet FirstName
				//
				// We don't need the 'CustomerRet' part of it, that's actually incorrect, so we'll strip it off
				$explode = explode(' ', $map);
				$first = trim(current($explode));
				$map = trim(implode(' ', array_slice($explode, 1)));

				if (stripos($action, "add") !== false)
				{
					$map = str_replace("Ret", "Add", $map);
				}
				else
				{
					$map = str_replace("Ret", "Mod", $map);
				}

				$map = preg_replace("/.*".$Node->name()." /", "", $map);

				/*
				if (strtolower($Node->name()) == "estimatelinemod" and
					strpos($map, 'TxnLineID') !== false )
				{
					$value = -1;
				}
				*/

				if (false === strpos($map, ' '))
				{
					if ($schema_object->exists($usePath . $Node->name() . ' ' . $map))
					{
						$use_in_request = true;

						switch ($schema_object->dataType($usePath . $Node->name() . ' ' . $map))
						{
							case 'AMTTYPE':

								$value = str_replace(',', '', number_format($value, 2));

								break;
							case 'BOOLTYPE':

								if ($value == 1)
								{
									$value = 'true';
								}
								else if ($value == 0)
								{
									$value = 'false';
								}
								else
								{
									$use_in_request = false;
								}

								break;
							default:
								break;
						}

						if ($use_in_request)
						{
							$Child = new Node($map);
							$Child->setData($value);
							$Node->addChild($Child);
						}
					}
					else
					{
						// ignore it
					}
				}
				else
				{
					// Please see comments about JournalEntries above!
					if ($action == PackageInfo::Actions['MOD_JOURNALENTRY'])
					{
						$map = str_replace(array( 'JournalCreditLine ', 'JournalDebitLine ' ), '', $map);
					}

					if ($schema_object->exists($usePath . $Node->name() . ' ' . $map))
					{
						$use_in_request = true;

						switch ($schema_object->dataType($usePath . $Node->name() . ' ' . $map))
						{
							case 'AMTTYPE':

								$value = str_replace(',', '', number_format($value, 2));

								break;
							case 'BOOLTYPE':

								if ($value == 1)
								{
									$value = 'true';
								}
								else if ($value == 0)
								{
									$value = 'false';
								}
								else
								{
									$use_in_request = false;
								}

								break;
							default:
								break;
						}

						if ($use_in_request)
						{
							$Node->setChildDataAt($Node->name() . ' ' . $map, $value, true);
						}
					}
				}
			}

			$tNodes = static::_ChildObjectsToXML(strtolower($child['table']), $action, $child['children'], $usePath . $Node->name());

			foreach ($tNodes as $tn)
			{
				$Node->addChild($tn);
			}

			$nodes[count($nodes)] = $Node;
		}

		return $nodes;

	}

	public static function AccountImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '';

		$xml .= '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<AccountQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</AccountQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function AccountImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);

		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs AccountQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('account', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 *
	 */
	public static function AccountQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!empty($extra['ListID']))
		{
			$tag = '<ListID>' . $extra['ListID'] . '</ListID>';
		}
		else if (!empty($extra['FullName']))
		{
			$tag = '<FullName>' . Cast::cast(PackageInfo::Actions['OBJECT_ACCOUNT'], 'FullName', $extra['FullName']) . '</FullName>';
		}
		else if (!empty($extra['FromModifiedDate']) &&
			!empty($extra['ToModifiedDate']))
		{
			$tag = '';
			$tag .= '<FromModifiedDate>' . $extra['FromModifiedDate'] . '</FromModifiedDate>';
			$tag .= '<ToModifiedDate>' . $extra['ToModifiedDate'] . '</ToModifiedDate>';
		}
		else
		{
			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<AccountQueryRq>
						' . $tag . '
					</AccountQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function AccountQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$extra['is_query_response'] = true;
		return static::AccountImportResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 */
	public static function BillPaymentCheckImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{

		if (!static::_requiredVersion(2.0, $version, $locale, PackageInfo::Actions['QUERY_BILLPAYMENTCHECK']))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<BillPaymentCheckQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</BillPaymentCheckQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 * @todo The $type parameter to _QueryResponse should be from a mapping, not a constant, to support custom mapping later on
	 *
	 */
	public static function BillPaymentCheckImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillPaymentCheckQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('billpaymentcheck', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function BillPaymentCreditCardImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(2.0, $version, $locale, PackageInfo::Actions['QUERY_BILLPAYMENTCREDITCARD']))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<BillPaymentCreditCardQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</BillPaymentCreditCardQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 * @todo The $type parameter to _QueryResponse should be from a mapping, not a constant, to support custom mapping later on
	 *
	 */
	public static function BillPaymentCreditCardImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillPaymentCreditCardQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('billpaymentcreditcard', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function BillQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!empty($extra['TxnID']))
		{
			$tag = '<TxnID>' . $extra['TxnID'] . '</TxnID>';
		}
		else if (!empty($extra['RefNumber']))
		{
			$tag = '<RefNumber>' . $extra['RefNumber'] . '</RefNumber>';
		}
		else if (!empty($extra['FromModifiedDate']) and
			!empty($extra['ToModifiedDate']))
		{
			$tag = '';
			$tag .= '<ModifiedDateRangeFilter>';
			$tag .= '	<FromModifiedDate>' . $extra['FromModifiedDate'] . '</FromModifiedDate>';
			$tag .= '	<ToModifiedDate>' . $extra['ToModifiedDate'] . '</ToModifiedDate>';
			$tag .= '</ModifiedDateRangeFilter>';
		}
		else
		{
			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<BillQueryRq>
						' . $tag . '
					</BillQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function BillQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$extra['is_query_response'] = true;
		return static::BillImportResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function BillImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$iterator = static::_buildIterator($extra, $version, $locale);
		if (!$iterator)
		{

			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<BillQueryRq requestID="' . $requestID . '" ' . $iterator . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						<IncludeLinkedTxns>true</IncludeLinkedTxns>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</BillQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function BillImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('bill', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	public static function BillToPayQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(2.0, $version, $locale, PackageInfo::Actions['QUERY_BILLTOPAY']))
		{
			return PackageInfo::Actions['SKIP'];
		}

		// We pass a blank ListID, because it was discovered that this will get ALL the BillToPay entries rather than only a few.
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<BillToPayQueryRq requestID="' . $requestID . '">
						<PayeeEntityRef>
							<ListID></ListID>
						</PayeeEntityRef>
					</BillToPayQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function BillToPayQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillToPayQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('billtopay', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function BillingRateQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(6.0, $version, $locale, PackageInfo::Actions['QUERY_BILLINGRATE']))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<BillingRateQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</BillingRateQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function BillingRateQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BillingRateQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('billingrate', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function BuildAssemblyQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(5.0, $version, $locale, PackageInfo::Actions['QUERY_BUILDASSEMBLY']))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<BuildAssemblyQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						' . static::_requiredVersionForElement(5.0, $version, '<IncludeComponentLineItems>true</IncludeLineItems>') . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</BuildAssemblyQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function BuildAssemblyQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs BuildAssemblyQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['OBJECT_CHECK'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function CheckAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Check = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'check', [Sql::Field['ID'] => $ID]))
		{
			// Special handling for check add requests

			// If a RefNumber is printed, then it can't be set as IsToBePrinted
			if (!empty($Check['RefNumber']))
			{
				unset($Check['IsToBePrinted']);
			}
			else
			{
				unset($Check['RefNumber']);
			}

			return static::_AddRequest(PackageInfo::Actions['OBJECT_CHECK'], $Check, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function CheckAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CheckAddRs');

		$extra['IsAddResponse'] = true;
		$extra['is_add_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_CHECK'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function CheckModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$Driver = Singleton::getInstance();
		if ($Check = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'check', [Sql::Field['ID'] => $ID]))
		{
			// Special handling for check add requests

			// If a RefNumber is printed, then it can't be set as IsToBePrinted
			if (!empty($Check['RefNumber']))
			{
				unset($Check['IsToBePrinted']);
			}
			else
			{
				unset($Check['RefNumber']);
			}

			return static::_AddRequest(PackageInfo::Actions['OBJECT_CHECK'], $Check, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config);
		}

		return '';
	}

	/**
	 *
	 *
	 *
	 */
	public static function CheckModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CheckModRs');

		$extra['IsModResponse'] = true;
		$extra['is_mod_response'] = true;
		static::_QueryResponse(PackageInfo::Actions['OBJECT_CHECK'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function CheckImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<CheckQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(7.0, $version, '<IncludeLinkedTxns>true</IncludeLinkedTxns>', $locale) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>', $locale) . '
					</CheckQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function CheckImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CheckQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['OBJECT_CHECK'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 */
	public static function JournalEntryImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<JournalEntryQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>', $locale) . '
					</JournalEntryQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function JournalEntryImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs JournalEntryQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['OBJECT_JOURNALENTRY'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ChargeImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(2.0, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ChargeQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						' . static::_requiredVersionForElement(3.0, $version, '<IncludeLinkedTxns>true</IncludeLinkedTxns>', $locale) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>', $locale) . '
					</ChargeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function ChargeImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ChargeQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('charge', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ClassImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ClassQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</ClassQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function ClassImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ClassQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('class', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function HostImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<HostQueryRq requestID="' . $requestID . '">
						<IncludeListMetaData>
							<IncludeMaxCapacity>true</IncludeMaxCapacity>
						</IncludeListMetaData>
					</HostQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 * @todo The $type parameter to _QueryResponse should be from a mapping, not a constant, to support custom mapping later on
	 *
	 *
	 */
	public static function HostImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs HostQueryRs');

		$extra['is_import_response'] = true;

		return static::_QueryResponse('host', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function PreferencesImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<PreferencesQueryRq requestID="' . $requestID . '"></PreferencesQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 * @todo The $type parameter to _QueryResponse should be from a mapping, not a constant, to support custom mapping later on
	 *
	 *
	 */
	public static function PreferencesImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs PreferencesQueryRs');

		//print_r($List);

		$extra['is_import_response'] = true;

		return static::_QueryResponse('preferences', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function CompanyImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<CompanyQueryRq requestID="' . $requestID . '">
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>', $locale) . '
					</CompanyQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 * @todo The $type parameter to _QueryResponse should be from a mapping, not a constant, to support custom mapping later on
	 *
	 *
	 */
	public static function CompanyImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CompanyQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		return static::_QueryResponse('company', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function CreditCardChargeImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<CreditCardChargeQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</CreditCardChargeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function CreditCardChargeImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CreditCardChargeQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('creditcardcharge', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function CreditCardCreditImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<CreditCardCreditQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</CreditCardCreditQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function CreditCardCreditImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CreditCardCreditQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('creditcardcredit', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	public static function CreditMemoImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<CreditMemoQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(1.1, $version, '<IncludeLinkedTxns>true</IncludeLinkedTxns>') . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</CreditMemoQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function CreditMemoImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CreditMemoQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		return static::_QueryResponse('creditmemo', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function CustomerMsgImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<CustomerMsgQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</CustomerMsgQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 * @todo The $type parameter to _QueryResponse should be from a mapping, not a constant, to support custom mapping later on
	 *
	 *
	 */
	public static function CustomerMsgImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CustomerMsgQueryRs');

		$extra['is_import_response'] = true;

		return static::_QueryResponse('customermsg', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function CustomerImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$iterator = static::_buildIterator($extra, $version, $locale);

		$Driver = Singleton::getInstance();

		if (!$iterator)
		{
			// This version of QuickBooks *does not support iterators*, so instead of using
			//	these IMPORT requests, we'll just queue up a bunch of Query requests instead
			// @todo This may actually change the behavior of things a bit, because _query_response may be handled slightly differently than _import_response... but will it matter?

			$key_prev = static::_keySyncPrev($action);

			$module = __CLASS__;

			//$action = null;
			$type = null;
			$opts = null;
			// 					configRead($user, $module, $key, &$type, &$opts)
			$prev_sync_datetime = $Driver->configRead($user, $module, $key_prev, $type, $opts);	// last sync started...

			// Calculate the # of days ago the last sync was...
			$prev_sync_timestamp = strtotime($prev_sync_datetime);

			$hours_ago = (time() - $prev_sync_timestamp) / 60.0 / 60.0;

			$every_six_hours = 60 * 60 * 6;

			for ($i = $prev_sync_timestamp; $i <= time(); $i = $i + $every_six_hours)
			{
				$extra = [
					'FromModifiedDate' => Utilities::datetime($i),
					'ToModifiedDate' => Utilities::datetime($i + $every_six_hours),
				];

				// Queue up some requests
				$Driver->queueEnqueue(
					$user,
					PackageInfo::Actions['QUERY_CUSTOMER'],
					null,
					true,
					Utilities::priorityForAction($action),
					$extra);
			}

			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<CustomerQueryRq requestID="' . $requestID . '" ' . $iterator . '>
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</CustomerQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 * @todo The $type parameter to _QueryResponse should be from a mapping, not a constant, to support custom mapping later on
	 *
	 *
	 */
	public static function CustomerImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CustomerQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		return static::_QueryResponse('customer', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function CustomerQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!empty($extra['ListID']))
		{
			$tag = '<ListID>' . $extra['ListID'] . '</ListID>';
		}
		else if (!empty($extra['FullName']))
		{
			$tag = '<FullName>' . Cast::cast(PackageInfo::Actions['OBJECT_CUSTOMER'], 'FullName', $extra['FullName']) . '</FullName>';
		}
		else if (!empty($extra['FromModifiedDate']) and
			!empty($extra['ToModifiedDate']))
		{
			$tag = '';
			$tag .= '<FromModifiedDate>' . $extra['FromModifiedDate'] . '</FromModifiedDate>';
			$tag .= '<ToModifiedDate>' . $extra['ToModifiedDate'] . '</ToModifiedDate>';
		}
		else
		{
			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<CustomerQueryRq>
						' . $tag . '
					</CustomerQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function CustomerQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$extra['is_query_response'] = true;

		return static::CustomerImportResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function CustomerTypeImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<CustomerTypeQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</CustomerTypeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 * @todo The $type parameter to _QueryResponse should be from a mapping, not a constant, to support custom mapping later on
	 *
	 */
	public static function CustomerTypeImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs CustomerTypeQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('customertype', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 *
	 */
	public static function DataExtDefQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(2.0, $version, $locale, PackageInfo::Actions['QUERY_DATAEXTDEF']))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<DataExtDefQueryRq requestID="' . $requestID . '">
						<OwnerID>0</OwnerID>
					</DataExtDefQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';
		/*
						<AssignToObject>Customer</AssignToObject>
						<AssignToObject>Employee</AssignToObject>
						<AssignToObject>Vendor</AssignToObject>
						<AssignToObject>Item</AssignToObject>

		 */

		return $xml;
	}

	public static function DataExtDefQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs DataExtDefQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('DataExtDef', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	public static function DateDrivenTermsQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<DateDrivenTermsQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</DateDrivenTermsQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 * @todo The $type parameter to _QueryResponse should be from a mapping, not a constant, to support custom mapping later on
	 *
	 */
	public static function DateDrivenTermsQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs DateDrivenTermsQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('datedriventerms', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	public static function DepositImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '';

		if (!static::_requiredVersion(2.0, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml .= '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<DepositQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</DepositQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function DepositImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs DepositQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('deposit', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function EmployeeImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '';

		$xml .= '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<EmployeeQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</EmployeeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function EmployeeImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs EmployeeQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('employee', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function EstimateImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$iterator = static::_buildIterator($extra, $version, $locale);
		if (!$iterator)
		{
			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<EstimateQueryRq requestID="' . $requestID . '" ' . $iterator . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(3.0, $version, '<IncludeLinkedTxns>true</IncludeLinkedTxns>') . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</EstimateQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function EstimateImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs EstimateQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('estimate', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	public static function InventoryAdjustmentImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(2.0, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		// min version 2.0
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<InventoryAdjustmentQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</InventoryAdjustmentQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function InventoryAdjustmentImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs InventoryAdjustmentQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('inventoryadjustment', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	public static function InvoiceImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$iterator = static::_buildIterator($extra, $version, $locale);
		if (!$iterator)
		{
			// Doesn't support iterators

			// Queue up by month

			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<InvoiceQueryRq requestID="' . $requestID . '" ' . $iterator . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(1.1, $version, '<IncludeLinkedTxns>true</IncludeLinkedTxns>') . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</InvoiceQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function InvoiceImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs InvoiceQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('invoice', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function InvoiceQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$tag1 = '';
		$tag2 = '';

		if (!empty($extra['TxnID']))
		{
			$tag1 = '';
			$tag1 .= '<TxnID>' . $extra['TxnID'] . '</TxnID>';
		}
		else if (!empty($extra['RefNumber']))
		{
			$tag1 = '';
			$tag1 .= '<RefNumber>' . $extra['RefNumber'] . '</RefNumber>';
		}
		else if (!empty($extra['Entity_FullName']))
		{
			$tag2 = '';
			$tag2 .= '<MaxReturned>' . WebConnectorSQL::$ITERATOR_MAXRETURNED . '</MaxReturned>';
			$tag2 .= '<EntityFilter>' . PackageInfo::$CRLF;
			$tag2 .= "\t" . '<FullName>' . Cast::cast(PackageInfo::Actions['OBJECT_INVOICE'], 'EntityFilter FullName', $extra['Entity_FullName']) . '</FullName>' . PackageInfo::$CRLF;
			$tag2 .= '</EntityFilter>' . PackageInfo::$CRLF;
		}
		else if (!empty($extra['Entity_ListID']))
		{
			$tag2 = '';
			$tag2 .= '<MaxReturned>' . WebConnectorSQL::$ITERATOR_MAXRETURNED . '</MaxReturned>';
			$tag2 .= '<EntityFilter>' . PackageInfo::$CRLF;
			$tag2 .= "\t" . '<ListID>' . $extra['Entity_ListID'] . '</ListID>' . PackageInfo::$CRLF;
			$tag2 .= '</EntityFilter>' . PackageInfo::$CRLF;
		}
		else if (!empty($extra['FromModifiedDate']) &&
			!empty($extra['ToModifiedDate']))
		{
			$tag2 = '';
			$tag2 .= '<MaxReturned>' . WebConnectorSQL::$ITERATOR_MAXRETURNED . '</MaxReturned>';
			$tag2 .= '<ModifiedDateRangeFilter>' . PackageInfo::$CRLF;
			$tag2 .= "\t" . '<FromModifiedDate>' . Utilities::datetime($extra['FromModifiedDate']) . '</FromModifiedDate>' . PackageInfo::$CRLF;
			$tag2 .= "\t" . '<ToModifiedDate>' . Utilities::datetime($extra['ToModifiedDate']) . '</ToModifiedDate>' . PackageInfo::$CRLF;
			$tag2 .= '</ModifiedDateRangeFilter>' . PackageInfo::$CRLF;
		}
		else
		{
			return PackageInfo::Actions['NOOP'];
		}

		// <MaxReturned>' . WebConnectorSQL::$ITERATOR_MAXRETURNED . '</MaxReturned>

		//  ' . static::_buildIterator($extra) . '

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<InvoiceQueryRq requestID="' . $requestID . '">
						' . $tag1 . '
						' . $tag2 . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(1.1, $version, '<IncludeLinkedTxns>true</IncludeLinkedTxns>') . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</InvoiceQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function InvoiceQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		// Let the import response function know that the update should be applied as if this is a query response
		$extra['is_query_response'] = true;

		// Pass off processing to the invoice import response functino
		static::InvoiceImportResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ItemServiceImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ItemServiceQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</ItemServiceQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function ItemServiceImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);

		$Root = $Doc->getRoot();
		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemServiceQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['OBJECT_SERVICEITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ItemNonInventoryImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ItemNonInventoryQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</ItemNonInventoryQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function ItemNonInventoryImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);

		$Root = $Doc->getRoot();
		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemNonInventoryQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['OBJECT_NONINVENTORYITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ItemInventoryImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ItemInventoryQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</ItemInventoryQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function ItemInventoryImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);

		$Root = $Doc->getRoot();
		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemInventoryQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['OBJECT_INVENTORYITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ItemInventoryAssemblyImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ItemInventoryAssemblyQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</ItemInventoryAssemblyQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function ItemInventoryAssemblyImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);

		$Root = $Doc->getRoot();
		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemInventoryAssemblyQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['QUERY_INVENTORYASSEMBLYITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ItemSalesTaxImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ItemSalesTaxQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</ItemSalesTaxQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function ItemSalesTaxImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);

		$Root = $Doc->getRoot();
		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemSalesTaxQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESTAXITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ItemSalesTaxGroupImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ItemSalesTaxGroupQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</ItemSalesTaxGroupQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function ItemSalesTaxGroupImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);

		$Root = $Doc->getRoot();
		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemSalesTaxGroupQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['OBJECT_SALESTAXGROUPITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function ItemImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ItemQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</ItemQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function ItemImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse(PackageInfo::Actions['OBJECT_ITEM'], $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	public static function ItemReceiptImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ItemReceiptQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						<IncludeLinkedTxns>true</IncludeLinkedTxns>
					</ItemReceiptQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function ItemReceiptImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ItemReceiptQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('itemreceipt', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}


	public static function JobTypeQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<JobTypeQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</JobTypeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function JobTypeQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs JobTypeQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('jobtype', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 *
	 */
	public static function PaymentMethodImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<PaymentMethodQueryRq requestID="' . $requestID . '">
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
					</PaymentMethodQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function PaymentMethodImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs PaymentMethodQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('paymentmethod', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function PayrollItemWageImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<PayrollItemWageQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</PayrollItemWageQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function PayrollItemWageImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs PayrollItemWageQueryRs');

		$extra['is_import_response'] = true;

		static::_QueryResponse('payrollitemwage', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function PayrollItemNonWageImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<PayrollItemNonWageQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</PayrollItemNonWageQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function PayrollItemNonWageImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs PayrollItemNonWageQueryRs');

		$extra['is_import_response'] = true;

		static::_QueryResponse('payrollitemnonwage', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 */
	public static function PriceLevelImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(4.0, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<PriceLevelQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</PriceLevelQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function PriceLevelImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs PriceLevelQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('pricelevel', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 */
	public static function PurchaseOrderImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$iterator = static::_buildIterator($extra, $version, $locale);
		if (!$iterator)
		{
			// Doesn't support iterators?

			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<PurchaseOrderQueryRq requestID="' . $requestID . '" ' . $iterator . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(3.0, $version, '<IncludeLinkedTxns>true</IncludeLinkedTxns>') . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</PurchaseOrderQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function PurchaseOrderImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs PurchaseOrderQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('purchaseorder', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function PurchaseOrderQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!empty($extra['TxnID']))
		{
			$tag = '<TxnID>' . $extra['TxnID'] . '</TxnID>';
		}
		else if (!empty($extra['RefNumber']))
		{
			$tag = '<RefNumber>' . $extra['RefNumber'] . '</RefNumber>';
		}
		else if (!empty($extra['FromModifiedDate']) &&
			!empty($extra['ToModifiedDate']))
		{
			$tag = '';
			$tag .= '<ModifiedDateRangeFilter>';
			$tag .= '	<FromModifiedDate>' . $extra['FromModifiedDate'] . '</FromModifiedDate>';
			$tag .= '	<ToModifiedDate>' . $extra['ToModifiedDate'] . '</ToModifiedDate>';
			$tag .= '</ModifiedDateRangeFilter>';
		}
		else
		{
			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<PurchaseOrderQueryRq>
						' . $tag . '
					</PurchaseOrderQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function PurchaseOrderQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$extra['is_query_response'] = true;
		return static::PurchaseOrderImportResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 */
	public static function ReceivePaymentImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$iterator = static::_buildIterator($extra);

		if (!$iterator)
		{
			// Iterators are not supported...

			return PackageInfo::Actions['NOOP'];
		}

		if (!static::_requiredVersion(1.1, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ReceivePaymentQueryRq requestID="' . $requestID . '" ' . $iterator . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</ReceivePaymentQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function ReceivePaymentImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ReceivePaymentQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('receivepayment', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function ReceivePaymentQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		//$iterator = static::_buildIterator($extra);

		if (!static::_requiredVersion(1.1, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$tag1 = '';
		$tag2 = '';

		if (!empty($extra['TxnID']))
		{
			$tag1 = '';
			$tag1 .= '<TxnID>' . $extra['TxnID'] . '</TxnID>';
		}
		else if (!empty($extra['RefNumber']))
		{
			$tag1 = '';
			$tag1 .= '<RefNumber>' . $extra['RefNumber'] . '</RefNumber>';
		}
		else if (!empty($extra['Entity_FullName']))
		{
			$tag2 = '';
			$tag2 .= '<MaxReturned>' . WebConnectorSQL::$ITERATOR_MAXRETURNED . '</MaxReturned>';
			$tag2 .= '<EntityFilter>' . PackageInfo::$CRLF;
			$tag2 .= "\t" . '<FullName>' . Cast::cast(PackageInfo::Actions['OBJECT_INVOICE'], 'EntityFilter FullName', $extra['Entity_FullName']) . '</FullName>' . PackageInfo::$CRLF;
			$tag2 .= '</EntityFilter>' . PackageInfo::$CRLF;
		}
		else if (!empty($extra['Entity_ListID']))
		{
			$tag2 = '';
			$tag2 .= '<MaxReturned>' . WebConnectorSQL::$ITERATOR_MAXRETURNED . '</MaxReturned>';
			$tag2 .= '<EntityFilter>' . PackageInfo::$CRLF;
			$tag2 .= "\t" . '<ListID>' . $extra['Entity_ListID'] . '</ListID>' . PackageInfo::$CRLF;
			$tag2 .= '</EntityFilter>' . PackageInfo::$CRLF;
		}
		else if (!empty($extra['FromModifiedDate']) and
			!empty($extra['ToModifiedDate']))
		{
			$tag2 = '';
			$tag2 .= '<MaxReturned>' . WebConnectorSQL::$ITERATOR_MAXRETURNED . '</MaxReturned>';
			$tag2 .= '<ModifiedDateRangeFilter>' . PackageInfo::$CRLF;
			$tag2 .= "\t" . '<FromModifiedDate>' . Utilities::datetime($extra['FromModifiedDate']) . '</FromModifiedDate>' . PackageInfo::$CRLF;
			$tag2 .= "\t" . '<ToModifiedDate>' . Utilities::datetime($extra['ToModifiedDate']) . '</ToModifiedDate>' . PackageInfo::$CRLF;
			$tag2 .= '</ModifiedDateRangeFilter>' . PackageInfo::$CRLF;
		}
		else
		{
			return PackageInfo::Actions['NOOP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ReceivePaymentQueryRq>
						' . $tag1 . '
						' . $tag2 . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</ReceivePaymentQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function ReceivePaymentQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$extra['is_query_response'] = true;
		return static::ReceivePaymentImportResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function SalesOrderImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(2.1, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		// IncludeLinkedTxns is actually qbXML versions 2.0 or greater... but
		//	since this entire action is qbXML versions 2.1 or greater, we can
		//	just assume it'll be present.

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<SalesOrderQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						<IncludeLinkedTxns>true</IncludeLinkedTxns>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</SalesOrderQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function SalesOrderImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesOrderQueryRs');

		$extra['is_import_response'] = true;
		static::_QueryResponse('salesorder', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	/**
	 *
	 *
	 */
	public static function SalesReceiptImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<SalesReceiptQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</SalesReceiptQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function SalesReceiptImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesReceiptQueryRs');

		$extra['is_import_response'] = true;
		static::_QueryResponse('salesreceipt', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}

	public static function SalesRepImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<SalesRepQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</SalesRepQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function SalesRepImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesRepQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('salesrep', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	public static function SalesTaxCodeImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<SalesTaxCodeQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</SalesTaxCodeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function SalesTaxCodeImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs SalesTaxCodeQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('salestaxcode', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	public static function ShipMethodImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<ShipMethodQueryRq requestID="' . $requestID . '">
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
					</ShipMethodQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function ShipMethodImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs ShipMethodQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('shipmethod', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	/**
	 *
	 *
	 *
	 */
	public static function TermsImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<TermsQueryRq requestID="' . $requestID . '">
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
					</TermsQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function TermsImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs TermsQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('terms', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}




	public static function TimeTrackingImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<TimeTrackingQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
					</TimeTrackingQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function TimeTrackingImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs TimeTrackingQueryRs');

		$extra['is_import_response'] = true;

		static::_QueryResponse('timetracking', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}




	public static function UnitOfMeasureSetQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(7.0, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<UnitOfMeasureSetQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</UnitOfMeasureSetQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function UnitOfMeasureSetQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs UnitOfMeasureSetQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('unitofmeasureset', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	public static function VehicleMileageQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(6.0, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<VehicleMileageQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
					</VehicleMileageQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function VehicleMileageQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs VehicleMileageQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('vehiclemileage', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}




	public static function VehicleImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(6.0, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<VehicleQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</VehicleQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function VehicleImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs VehicleQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('vehicle', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}




	public static function VendorCreditImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<VendorCreditQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						' . static::_buildFilter($user, $action, $extra, true) . '
						<IncludeLineItems>true</IncludeLineItems>
						' . static::_requiredVersionForElement(2.0, $version, '<IncludeLinkedTxns>true</IncludeLinkedTxns>') . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</VendorCreditQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	public static function VendorCreditImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs VendorCreditQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('vendorcredit', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	public static function VendorTypeImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<VendorTypeQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</VendorTypeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function VendorTypeImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs VendorTypeQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('vendortype', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	public static function VendorImportRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . static::_version($version, $locale) . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<VendorQueryRq requestID="' . $requestID . '" ' . static::_buildIterator($extra) . '>
						<ActiveStatus>All</ActiveStatus>
						' . static::_buildFilter($user, $action, $extra) . '
						' . static::_requiredVersionForElement(2.0, $version, '<OwnerID>0</OwnerID>') . '
					</VendorQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function VendorImportResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs VendorQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('vendor', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}





	public static function WorkersCompCodeQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = [])
	{
		if (!static::_requiredVersion(7.0, $version))
		{
			return PackageInfo::Actions['SKIP'];
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="' . WebConnectorSQL::$ON_ERROR . '">
					<WorkersCompCodeQueryRq requestID="' . $requestID . '">
						' . static::_buildFilter($user, $action, $extra) . '
					</WorkersCompCodeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		return $xml;
	}

	/**
	 *
	 *
	 */
	public static function WorkersCompCodeQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $config = [])
	{
		$Parser = new Parser($xml);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		$Root = $Doc->getRoot();

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs WorkersCompCodeQueryRs');

		if (!isset($extra['is_query_response']))
		{
			$extra['is_import_response'] = true;
		}

		static::_QueryResponse('workerscompcode', $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config);
	}



	/*
	No registered handler for: CreditCardMemoQuery
	No registered handler for: DataExtDefQuery


	No registered handler for: StandardTermsQuery
	No registered handler for: TemplateQuery
	No registered handler for: TransactionQuery
	No registered handler for: quickbooks-query-null
	*/


	/**
	 *
	 * @todo Make the Boolean TRUE value used in the QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG field a constant, in case the sql driver used uses something other than 1 and 0.
	 * @todo Change all ListID and TxnID instances to use the Utilities::actionToKey function.
	 */
	protected static function _getChildTables(string $table)
	{
		$map_children = [
			'account' => [
				'id_field' => 'ListID',
				'children' => [
					'account_taxlineinfo' => 'Account_ListID',
					'dataext' => 'Entity_ListID',
				],
			],
			'bill' => [
				'id_field' => 'TxnID',
				'children' => [
					'bill_linkedtxn' => 'Bill_TxnID',
					'bill_expenseline' => 'Bill_TxnID',
					'bill_itemgroupline' => 'Bill_TxnID',
					'bill_itemline' => 'Bill_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],
			'bill_itemgroupline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'bill_itemgroupline_itemline' => 'Bill_ItemGroupLine_TxnLineID',
				],
			],
			'billingrate' => [
				'id_field' => 'ListID',
				'children' => [
					'billingrate_billingrateperitem' => 'BillingRate_ListID',
				],
			],
			'billpaymentcheck' => [
				'id_field' => 'TxnID',
				'children' => [
					'billpaymentcheck_appliedtotxn' => 'BillPaymentCheck_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],
			'billpaymentcreditcard' => [
				'id_field' => 'TxnID',
				'children' => [
					'billpaymentcreditcard_appliedtotxn' => 'BillPaymentCreditCard_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],
			'charge' => [
				'id_field' => 'TxnID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				]
			],
			'check' => [
				'id_field' => 'TxnID',
				'children' => [
					'check_expenseline' => 'Check_TxnID',
					'check_itemgroupline' => 'Check_TxnID',
					'check_itemline' => 'Check_TxnID',
					'check_linkedtxn' => 'Check_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'check_itemgroupline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'check_itemgroupline_itemline' => 'Check_ItemGroupLine_TxnLineID',
				],
			],

			'company' => [
				'id_field' => 'CompanyName',
				'children' => [
					'company_subscribedservices_service' => 'Company_CompanyName',
				],
			],

			'creditcardcharge' => [
				'id_field' => 'TxnID',
				'children' => [
					'creditcardcharge_expenseline' => 'CreditCardCharge_TxnID',
					'creditcardcharge_itemgroupline' => 'CreditCardCharge_TxnID',
					'creditcardcharge_itemline' => 'CreditCardCharge_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'creditcardcharge_itemgroupline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'creditcardcharge_itemgroupline_itemline' => 'CreditCardCharge_ItemGroupLine_TxnLineID',
				],
			],

			'creditcardcredit' => [
				'id_field' => 'TxnID',
				'children' => [
					'creditcardcredit_expenseline' => 'CreditCardCredit_TxnID',
					'creditcardcredit_itemgroupline' => 'CreditCardCredit_TxnID',
					'creditcardcredit_itemline' => 'CreditCardCredit_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'creditcardcredit_itemgroupline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'creditcardcredit_itemgroupline_itemline' => 'CreditCardCredit_ItemGroupLine_TxnLineID',
				],
			],

			'creditmemo' => [
				'id_field' => 'TxnID',
				'children' => [
					'creditmemo_creditmemoline' => 'CreditMemo_TxnID',
					'creditmemo_creditmemolinegroup' => 'CreditMemo_TxnID',
					'creditmemo_linkedtxn' => 'FromTxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'creditmemolinegroup' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
					'creditmemo_creditmemolinegroup_creditmemoline' => 'CreditMemo_CreditMemoLineGroup_TxnLineID',
				],
			],

			'creditmemolinegroup_creditmemoline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'customer' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'deposit' => [
				'id_field' => 'TxnID',
				'children' => [
					'deposit_depositline' => 'Deposit_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'employee' => [
				'id_field' => 'ListID',
				'children' => [
					'employee_earnings' => 'Employee_ListID',
					'dataext' => 'Entity_ListID',
				],
			],

			'estimate' => [
				'id_field' => 'TxnID',
				'children' => [
					'estimate_linkedtxn' => 'FromTxnID',
					'estimate_estimateline' => 'Estimate_TxnID',
					'estimate_estimatelinegroup' => 'Estimate_TxnID',
					'dataext' => 'Entity_ListID',
				],
			],

			'estimate_estimatelinegroup' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
					'estimate_estimatelinegroup_estimateline' => 'Estimate_EstimateLineGroup_TxnLineID',
				],
			],

			'estimate_estimateline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'estimate_estimatelinegroup_estimateline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'inventoryadjustment' => [
				'id_field' => 'TxnID',
				'children' => [
					'inventoryadjustment_inventoryadjustmentline' => 'InventoryAdjustment_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'invoice' => [
				'id_field' => 'TxnID',
				'children' => [
					'invoice_linkedtxn' => 'FromTxnID',
					'invoice_invoiceline' => 'Invoice_TxnID',
					'invoice_invoicelinegroup' => 'Invoice_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'invoice_invoiceline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'invoice_invoicelinegroup' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
					'invoice_invoicelinegroup_invoiceline' => 'Invoice_InvoiceLineGroup_TxnLineID',
				],
			],

			'invoice_invoicelinegroup_invoiceline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'iteminventory' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'iteminventoryassembly' => [
				'id_field' => 'ListID',
				'children' => [
					'iteminventoryassembly_iteminventoryassemblyline' => 'ItemInventoryAssembly_ListID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'itemnoninventory' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'itemdiscount' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'itemfixedasset' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'itemgroup' => [
				'id_field' => 'ListID',
				'children' => [
					'itemgroup_itemgroupline' => 'ItemGroup_ListID',
					'dataext' => 'Entity_ListID',
				],
			],

			'itemothercharge' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'itempayment' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'itemreceipt' => [
				'id_field' => 'TxnID',
				'children' => [
					'itemreceipt_linkedtxn' => 'FromTxnID',
					'itemreceipt_expenseline' => 'ItemReceipt_TxnID',
					'itemreceipt_itemgroupline' => 'ItemReceipt_TxnID',
					'itemreceipt_itemline' => 'ItemReceipt_TxnID',
					'dataext' => 'Entity_ListID',
				],
			],

			'itemreceipt_itemgroupline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'itemreceipt_itemgroupline_itemline' => 'ItemReceipt_ItemGroupLine_TxnLineID',
				],
			],

			'itemsalestaxgroup' => [
				'id_field' => 'ListID',
				'children' => [
					'itemsalestaxgroup_itemsalestax' => 'ItemSalesTaxGroup_ListID',
					'dataext' => 'Entity_ListID',
				],
			],

			'itemservice' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'itemsubtotal' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'journalentry' => [
				'id_field' => 'TxnID',
				'children' => [
					'journalentry_journalcreditline' => 'JournalEntry_TxnID',
					'journalentry_journaldebitline' => 'JournalEntry_TxnID',
					'dataext' => 'Entity_ListID',
				],
			],

			'pricelevel' => [
				'id_field' => 'ListID',
				'children' => [
					'pricelevel_pricelevelperitem' => 'PriceLevel_ListID',
				],
			],

			'purchaseorder' => [
				'id_field' => 'TxnID',
				'children' => [
					'purchaseorder_linkedtxn' => 'FromTxnID',
					'purchaseorder_linkedtxn' => 'PurchaseOrder_TxnID',
					'purchaseorder_purchaseorderline' => 'PurchaseOrder_TxnID',
					'purchaseorder_purchaseorderlinegroup' => 'PurchaseOrder_TxnID',
					'dataext' => 'Entity_ListID',
				],
			],

			'purchaseorder_purchaseorderline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'purchaseorder_purchaseorderlinegroup' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
					'purchaseorder_purchaseorderlinegroup_purchaseorderline' => 'PurchaseOrder_PurchaseOrderLineGroup_TxnLineID',
				],
			],

			'purchaseorder_purchaseorderlinegroup_purchaseorderline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'receivepayment' => [
				'id_field' => 'TxnID',
				'children' => [
					'receivepayment_appliedtotxn' => 'ReceivePayment_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'salesorder' => [
				'id_field' => 'TxnID',
				'children' => [
					'salesorder_linkedtxn' => 'FromTxnID',
					'salesorder_salesorderline' => 'SalesOrder_TxnID',
					'salesorder_salesorderlinegroup' => 'SalesOrder_TxnID',
					'dataext' => 'Entity_ListID',
				],
			],

			'salesorder_salesorderline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'salesorder_salesorderlinegroup' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
					'salesorder_salesorderlinegroup_salesorderline' => 'SalesOrder_SalesOrderLineGroup_TxnLineID',
				],
			],

			'salesorder_salesorderlinegroup_salesorderline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'salesreceipt' => [
				'id_field' => 'TxnID',
				'children' => [
					'salesreceipt_salesreceiptline' => 'SalesReceipt_TxnID',
					'salesreceipt_salesreceiptlinegroup' => 'SalesReceipt_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'salesreceipt_salesreceiptline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'salereceipt_salesreceiptlinegroup' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
					'salesreceipt_salesreceiptlinegroup_salesreceiptline' => 'SalesReceipt_SalesReceiptLineGroup_TxnLineID',
				],
			],

			'salesreceipt_salesreceiptlinegroup_salesreceiptline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'unitofmeasureset' => [
				'id_field' => 'ListID',
				'children' => [
					'unitofmeasureset_defaultunit' => 'UnitOfMeasureSet_ListID',
					'unitofmeasureset_relatedunit' => 'UnitOfMeasureSet_ListID',
				],
			],

			'vendor' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			'vendorcredit' => [
				'id_field' => 'TxnID',
				'children' => [
					'vendorcredit_linkedtxn' => 'FromTxnID',
					'vendorcredit_expenseline' => 'VendorCredit_TxnID',
					'vendorcredit_itemline' => 'VendorCredit_TxnID',
					'vendorcredit_itemgroupline' => 'VendorCredit_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'vendorcredit_itemgroupline' => [
				'id_field' => 'TxnLineID',
				'children' => [
					'vendorcredit_itemgroupline_itemline' => 'VendorCredit_ItemGroupLine_TxnLineID',
				],
			],

			'workerscompcode' => [
				'id_field' => 'ListID',
				'children' => [
					'workerscompcode_ratehistory' => 'WorkersCompCode_ListID',
				],
			],
		];

		// If no children, return empty array
		if (!isset($map_children[$table]))
		{
			return [];
		}

		$ret = [];
		foreach ($map_children[$table]['children'] as $key => $value)
		{
			$index = count($ret);
			$ret[$index] = [
				'tableName' => $key,
				'table' => $key,
				'relField' => $value,
				'rel' => $value,
				'parentKey' => $map_children[$table]['id_field'],
				'parent' => $map_children[$table]['id_field'],
				'children' => static::_getChildTables($key),
			];
		}

		return $ret;
	}

	/**
	 *
	 *
	 * @todo Make the Boolean TRUE value used in the QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG field a constant, in case the sql driver used uses something other than 1 and 0.
	 * @todo Change all ListID and TxnID instances to use the Utilities::actionToKey function.
	 */
	protected static function _queryChildren($children, $keyID)
	{
		/*
			$retArr[$index]['tableName'] = $key;
			$retArr[$index]['relField'] = $value;
			$retArr[$index]['parentKey'] = $get_children_map[$table]['id_field'];
			$retArr[$index]['children'] = static::_GetChildrenTables($key);
		*/

		$Driver = Singleton::getInstance();

		if (empty($children))
		{
			return $children;
		}

		$ret = [];

		foreach ($children as $child)
		{
			$sort = Sql::Field['ID'] . ' ASC ';
			switch (strtolower($child['table']))
			{
				case 'invoice_invoiceline':					// @TODO There are a whole lot of other line item tables missing the SortOrder field still...
				case 'salesreceipt_salesreceiptline':
				case 'purchaseorder_purchaseorderline':
				case 'estimate_estimateline':
				case 'bill_itemline':
				case 'bill_expenseline':
				case 'iteminventoryassembly_iteminventoryassemblyline':
					$sort = ' SortOrder ASC ';
					break;
			}

			$table = $child['table'];
			$sql = "
				SELECT
					*
				FROM
					" . Sql::$TablePrefix['SQL_MIRROR'] . $table . "
				WHERE
					 " . $child['rel'] . " = '" . $keyID . "'
				ORDER BY
					" . $sort;

			$errnum = 0;
			$errmsg = '';

			//print($sql);

			$res = $Driver->query($sql, $errnum, $errmsg);
			while ($arr = $Driver->fetch($res))
			{
				//print_r($arr);

				if (!empty($arr[Sql::Field['TO_SKIP']]))
				{
					// If this record has been marked as to skip, do not
					// 	include it among the children
					//	(this can happen when you're dealing with line items
					//	that you want to show in a web GUI, but not be actually
					//	sent to QuickBooks because they really come from a
					//	LinkedTxn and you don't want to end up with duplicate
					//	line items (one set from the LinkedTxn and one set
					//	from these actulal line item records...)...)

					continue;
				}

				//print_r($arr);

				// Some special cases for certain tables
				switch ($table)
				{
					case 'receivepayment_appliedtotxn':
					case 'billpaymentcheck_appliedtotxn':
					case 'billpaymentcreditcard_appliedtotxn':

						if (!empty($arr['Amount']))
						{
							$arr['PaymentAmount'] = $arr['Amount'];
						}

						break;
				}

				/*
				$index = count($ret);
				$ret[$index] = [];
				$ret[$index]['table'] = $table;
				$ret[$index]['data'] = new SqlObject($table, null, $arr);
				if (!empty($child['children']))
				{
					$ret[$index]['children'] = static::_queryChildren($child['children'], $arr[$child['children'][0]['parent']]);
				}
				else
				{
					$ret[$index]['children'] = [];
				}
				*/

				$children = [];
				if (!empty($child['children']))
				{
					/*
					// This fixes a case-sensitivity issue with PostgreSQL... probably need to look into this more...
					// @TODO FIX CASE SENSITIVITY ISSUE HERE WITH QBXML
					if ($child['children'][0]['parent'] == 'TxnLineID' and
						empty($arr['TxnLineID']))
					{
						$child['children'][0]['parent'] = 'txnlineid';
					}
					*/


					if (empty($arr[$child['children'][0]['parent']]))
					{
						$arr[$child['children'][0]['parent']] = null;
					}



					$children = static::_queryChildren($child['children'], $arr[$child['children'][0]['parent']]);
				}

				$ret[] = [
					'table' => $table,
					'data' => new SqlObject($table, null, $arr),
					'children' => $children,
				];
			}
		}

		return $ret;
	}

	/**
	 * Update relatives which might be referring to an out-of-date temporary ListID or TxnID
	 *
	 * When you initially insert things into the SQL database for insertion
	 * into QuickBooks, you have to make up a unique TxnID or ListID so you can
	 * link other things to it. When QuickBooks then receives that record, the
	 * record is updated with the correct TxnID or ListID. This function also
	 * updates any records that it might be linked to. i.e.:
	 * 	If a customer is added to QuickBooks, and you receive the real ListID,
	 * 		then you also want to make sure the temporary ListID is changed to
	 * 		the real ListID in these places:
	 * 			invoice.Customer_ListID
	 * 			estimate.Customer_ListID
	 * 			receivepayment.Customer_ListID
	 * 			etc. etc. etc.
	 */
	protected static function _updateRelatives(string $table, string $user, string $action, $ID, $object, array $extra, array $callback_config = [], bool $deleteDataExt = false, bool $fullDelete = false)
	{
		$update_relatives_map = [
			'account' => [
				'id_field' => 'ListID',
				'relatives' => [
					'invoice' => 'ARAccount_ListID',
				],
			],
			'bill' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'billpaymentcheck_appliedtotxn' => 'ToTxnID',
					'billpaymentcreditcard_appliedtotxn' => 'ToTxnID',
					//'dataext' => 'Txn_TxnID',
				],
			],

			/*
			'billingrate' => [
				'id_field' => 'ListID',
				'relatives' => [
					'billingrate_billingrateperitem' => 'BillingRate_ListID',
				],
			],

			'billpaymentcheck' => [
				'id_field' => 'ListID',
				'relatives' => [
					'billpaymentcheck_appliedtotxn' => 'FromTxnID',
					'dataext' => 'Entity_ListID',
				],
			],

			'billpaymentcreditcard' => [
				'id_field' => 'ListID',
				'relatives' => [
					'billpaymentcreditcard_appliedtotxn' => 'FromTxnID',
					'dataext' => 'Entity_ListID',
				],
			),

			'charge' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'check' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'check_expenseline' => 'Check_TxnID',
					'check_itemgroupline' => 'Check_TxnID',
					'check_itemgroupline_itemline' => 'Check_TxnID',
					'check_itemline' => 'Check_TxnID',
					'check_linkedtxn' => 'FromTxnID',
					'dataext' => 'Txn_TxnID',
				],
			),

			'company' => [
				'id_field' => 'CompanyName',
					'relatives' => [
						'company_subscribedservices_service' => 'Company_CompanyName',
					],
				],

			'creditcardcharge' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'creditcardcharge_expenseline' => 'CreditCardCharge_TxnID',
					'creditcardcharge_itemgroupline' => 'CreditCardCharge_TxnID',
					'creditcardcharge_itemgroupline_itemline' => 'CreditCardCharge_TxnID',
					'creditcardcharge_itemline' => 'CreditCardCharge_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'creditcardcredit' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'creditcardcredit_expenseline' => 'CreditCardCredit_TxnID',
					'creditcardcredit_itemgroupline' => 'CreditCardCredit_TxnID',
					'creditcardcredit_itemgroupline_itemline' => 'CreditCardCredit_TxnID',
					'creditcardcredit_itemline' => 'CreditCardCredit_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'creditmemo' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'creditmemo_creditmemoline' => 'CreditMemo_TxnID',
					'creditmemo_creditmemolinegroup' => 'CreditMemo_TxnID',
					'creditmemo_creditmemolinegroup_creditmemoline' => 'CreditMemo_TxnID',
					'creditmemo_linkedtxn' => 'FromTxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'creditmemolinegroup' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'creditmemolinegroup_creditmemoline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],
			*/

			'customer' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate' => 'Customer_ListID',
					'salesorder' => 'Customer_ListID',
					'purchaseorder_purchaseorderline' => 'Customer_ListID',
					'invoice' => 'Customer_ListID',
					'receivepayment' => 'Customer_ListID',
					'purchaseorder' => 'ShipToEntity_ListID',
					'salesreceipt' => 'Customer_ListID',
				],
			],

			/*
			'deposit' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'deposit_depositline' => 'Deposit_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],
			*/
			'employee' => [
				'id_field' => 'ListID',
				'relatives' => [
					'salesrep' => 'SalesRepEntity_ListID',
				],
			],
			/*
			'estimate' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'estimate_estimateline' => 'Estimate_TxnID',
					'estimate_estimatelinegroup' => 'Estimate_TxnID',
					'estimate_estimatelinegroup_estimateline' => 'Estimate_TxnID',
					'invoice_linkedtxn' => 'FromTxnID',
					'dataext' => 'Entity_ListID',
				],
			],

			'estimate_estimatelinegroup' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'estimate_estimateline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'estimate_estimatelinegroup' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'estimate_estimatelinegroup_estimateline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'inventoryadjustment' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'inventoryadjustment_inventoryadjustmentline' => 'InventoryAdjustment_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],
			*/

			'invoice' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'estimate_linkedtxn' => 'ToTxnID',
					'salesorder_linkedtxn' => 'ToTxnID',
					'receivepayment_appliedtotxn' => 'ToTxnID', // 'ToTxnID:Type=Invoice',
				],
			],
			/*
			'invoice_invoiceline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],


			'invoice_invoicelinegroup' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'invoice_invoicelinegroup_invoiceline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'itemgroup' => [
				'id_field' => 'ListID',
				'relatives' => [
					'itemgroup_itemgroupline' => 'ItemGroup_ListID',
					'dataext' => 'Entity_ListID',
				],
			],
			*/
			'iteminventory' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate_estimateline' => 'Item_ListID',
					'salesorder_salesorderline' => 'Item_ListID',
					'purchaseorder_purchaseorderline' => 'Item_ListID',
					'invoice_invoiceline' => 'Item_ListID',
				],
			],
			/*
			'iteminventoryassembly' => [
				'id_field' => 'ListID',
				'relatives' => [
					'iteminventoryassembly_iteminventoryassemblyline' => 'ItemInventoryAssembly_ListID',
					'dataext' => 'Txn_TxnID',
				],
			],
			*/
			'itemnoninventory' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate_estimateline' => 'Item_ListID',
					'salesorder_salesorderline' => 'Item_ListID',
					'purchaseorder_purchaseorderline' => 'Item_ListID',
					'invoice_invoiceline' => 'Item_ListID',
				],
			],

			'itemdiscount' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate_estimateline' => 'Item_ListID',
					'salesorder_salesorderline' => 'Item_ListID',
					'purchaseorder_purchaseorderline' => 'Item_ListID',
					'invoice_invoiceline' => 'Item_ListID',
				],
			],

			'itemfixedasset' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate_estimateline' => 'Item_ListID',
					'salesorder_salesorderline' => 'Item_ListID',
					'purchaseorder_purchaseorderline' => 'Item_ListID',
					'invoice_invoiceline' => 'Item_ListID',
				],
			],

			'itemothercharge' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate_estimateline' => 'Item_ListID',
					'salesorder_salesorderline' => 'Item_ListID',
					'purchaseorder_purchaseorderline' => 'Item_ListID',
					'invoice_invoiceline' => 'Item_ListID',
				],
			],

			'itempayment' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate_estimateline' => 'Item_ListID',
					'salesorder_salesorderline' => 'Item_ListID',
					'purchaseorder_purchaseorderline' => 'Item_ListID',
					'invoice_invoiceline' => 'Item_ListID',
				],
			],
			/*
			'itemreceipt' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'itemreceipt_expenseline' => 'ItemReceipt_TxnID',
					'itemreceipt_itemgroupline' => 'ItemReceipt_TxnID',
					'itemreceipt_itemgroupline_itemline' => 'ItemReceipt_TxnID',
					'itemreceipt_itemline' => 'ItemReceipt_TxnID',
					'itemreceipt_linkedtxn' => 'FromTxnID',
					'dataext' => 'Entity_ListID',
				],
			],
			*/
			'itemsalestax' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate_estimateline' => 'Item_ListID',
					'salesorder_salesorderline' => 'Item_ListID',
					'purchaseorder_purchaseorderline' => 'Item_ListID',
					'invoice_invoiceline' => 'Item_ListID',
				],
			],
			/*
			'itemsalestaxgroup' => [
				'id_field' => 'ListID',
				'relatives' => [
					'itemsalestaxgroup_itemsalestax' => 'ItemSalesTaxGroup_ListID',
					'dataext' => 'Entity_ListID',
				],
			],
			*/
			'itemservice' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate_estimateline' => 'Item_ListID',
					'salesorder_salesorderline' => 'Item_ListID',
					'purchaseorder_purchaseorderline' => 'Item_ListID',
					'invoice_invoiceline' => 'Item_ListID',
				],
			],

			'itemsubtotal' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate_estimateline' => 'Item_ListID',
					'salesorder_salesorderline' => 'Item_ListID',
					'purchaseorder_purchaseorderline' => 'Item_ListID',
					'invoice_invoiceline' => 'Item_ListID',
				],
			],
			/*
			'journalentry' => [
			'id_field' => 'TxnID',
				'relatives' => [
					'journalentry_journalcreditline' => 'JournalEntry_TxnID',
					'journalentry_journaldebitline' => 'JournalEntry_TxnID',
					'dataext' => 'Entity_ListID',
				],
			],

			'pricelevel' => [
				'id_field' => 'ListID',
					'relatives' => [
					'pricelevel_pricelevelperitem' => 'PriceLevel_ListID',
				],
			],
			*/
			'purchaseorder' => [
				'id_field' => 'TxnID',
				'relatives' => [
					//'estimate' => 'PONumber',
					//'salesorder' => 'PONumber'
					'bill_linkedtxn' => 'ToTxnID',
				],
			],
			/*
			'purchaseorder_purchaseorderline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'purchaseorder_purchaseorderlinegroup' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'purchaseorder_purchaseorderlinegroup_purchaseorderline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'receievepayment' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'receivepayment_appliedtotxn' => 'FromTxnID',
					'dataext' => 'Txn_TxnID'
				],
			],

			'salesorder' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'salesorder_salesorderline' => 'SalesOrder_TxnID',
					'salesorder_salesorderlinegroup' => 'SalesOrder_TxnID',
					'salesorder_salesorderlinegroup_salesorderline' => 'SalesOrder_TxnID',
					'salesorder_linkedtxn' => 'FromTxnID',
					'dataext' => 'Entity_ListID',
				],
			],

			'salesorder_salesorderline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'salesorder_salesorderlinegroup' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'salesorder_salesorderlinegroup_salesorderline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'salesreceipt' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'salesreceipt_salesreceiptline' => 'SalesReceipt_TxnID',
					'salesreceipt_salesreceiptlinegroup' => 'SalesReceipt_TxnID',
					'salesreceipt_salesreceiptlinegroup_salesreceiptline' => 'SalesReceipt_TxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'salesreceipt_salesreceiptline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'salereceipt_salesreceiptlinegroup' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],

			'salesreceipt_salesreceiptlinegroup_salesreceiptline' => [
				'id_field' => 'TxnLineID',
				'relatives' => [
					'dataext' => 'Txn_TxnID',
				],
			],
			*/
			'salesrep' => [
				'id_field' => 'ListID',
				'relatives' => [
					'estimate' => 'SalesRep_ListID',
					'salesorder' => 'SalesRep_ListID',
					'invoice' => 'SalesRep_ListID',
				],
			],

			'salestaxcode' => [
				'id_field' => 'ListID',
				'relatives' => [
					'iteminventory' => 'SalesTaxCode_ListID',
					'iteminventoryassembly' => 'SalesTaxCode_ListID',
					'itemnoninventory' => 'SalesTaxCode_ListID',
					'itemothercharge' => 'SalesTaxCode_ListID',
					'itemservice' => 'SalesTaxCode_ListID',
					'customer' => 'SalesTaxCode_ListID',
					'estimate' => 'CustomerSalesTaxCode_ListID',
					'salesorder' => 'CustomerSalesTaxCode_ListID',
					'invoice' => 'CustomerSalesTaxCode_ListID',
				],
			],
			/*
			'unitofmeasureset' => [
				'id_field' => 'ListID',
				'relatives' => [
					'unitofmeasureset_defaultunit' => 'UnitOfMeasureSet_ListID',
					'unitofmeasureset_relatedunit' => 'UnitOfMeasureSet_ListID',
				],
			],
			*/
			'vendor' => [
				'id_field' => 'ListID',
				'relatives' => [
					'purchaseorder' => 'Vendor_ListID',
				],
			],
			/*
			'vendorcredit' => [
				'id_field' => 'TxnID',
				'relatives' => [
					'vendorcredit_expenseline' => 'VendorCredit_TxnID',
					'vendorcredit_itemline' => 'VendorCredit_TxnID',
					'vendorcredit_itemgroupline' => 'VendorCredit_TxnID',
					'vendorcredit_itemgroupline_itemline' => 'VendorCredit_TxnID',
					'vendorcredit_linkedtxn' => 'FromTxnID',
					'dataext' => 'Txn_TxnID',
				],
			],

			'workerscompcode' => [
				'id_field' => 'ListID',
				'relatives' => [
					'workerscompcode_ratehistory' => 'WorkersCompCode_ListID',
				],
			],
			*/
		];

		$Driver = Singleton::getInstance();

		if (!isset($update_relatives_map[$table]))
		{
			//$Driver->log('Record has no RELATIVES...?', null, PackageInfo::LogLevel['NORMAL']);
			return false;
		}

		if (!isset($extra['AddResponse_OldKey']))
		{
			//$Driver->log('Missing key for RELATIVE update...?', null, PackageInfo::LogLevel['NORMAL']);
			return false;
		}

		$TxnID_or_ListID = $object->get($update_relatives_map[$table]['id_field']);
		foreach ($update_relatives_map[$table]['relatives'] as $relative_table => $relative_field)
		{
			//$Driver->log('Now updating [' . $relative_table . '] for field [' . $relative_field . '] with value [' . $TxnID_or_ListID . ']', null, PackageInfo::LogLevel['DEBUG']);

			$multipart = [$relative_field => $extra['AddResponse_OldKey']];
			$tmp = new SqlObject($relative_table, null);

			//@todo Make the Boolean TRUE value used in the QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG field a constant,
			//      in case the sql driver used uses something other than 1 and 0.
			$tmp->set($relative_field, $TxnID_or_ListID);
			$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $relative_table, $tmp, [$multipart], false);
		}
	}

	/**
	 *
	 * @todo Make the Boolean TRUE value used in the QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG field a constant, in case the sql driver used uses something other than 1 and 0.
	 * @todo Change all ListID and TxnID instances to use the Utilities::actionToKey function.
	 */
	protected static function _deleteChildren(string $table, string $user, string $action, $ID, $object, array $extra, &$deleted, array $callback_config = [], bool $deleteDataExt = false, bool $fullDelete = false)
	{
		$delete_children_map = [
			'account' => [
				'id_field' => 'ListID',
				'children' => [
					'account_taxlineinfo' => 'Account_ListID',
					'dataext' => 'Entity_ListID',
				],
			],
			'bill' => [
				'id_field' => 'TxnID',
				'children' => [
					'bill_expenseline' => 'Bill_TxnID',
					'bill_itemgroupline' => 'Bill_TxnID',
					'bill_itemgroupline_itemline' => 'Bill_TxnID',
					'bill_itemline' => 'Bill_TxnID',
					'bill_linkedtxn' => 'FromTxnID',
					// This doesn't look right ^^^
					'dataext' => 'Txn_TxnID',
				],
			],

			"billingrate" => [
				"id_field" => "ListID",
				"children" => [
					"billingrate_billingrateperitem" => "BillingRate_ListID",
				],
			],

			"billpaymentcheck" => [
				"id_field" => "ListID",
				"children" => [
					"billpaymentcheck_appliedtotxn" => "FromTxnID",
					"dataext" => "Entity_ListID",
				],
			],

			"billpaymentcreditcard" => [
				"id_field" => "ListID",
				"children" => [
					"billpaymentcreditcard_appliedtotxn" => "FromTxnID",
					"dataext" => "Entity_ListID",
				],
			],

			"charge" => [
				"id_field" => "TxnID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"check" => [
				"id_field" => "TxnID",
				"children" => [
					"check_expenseline" => "Check_TxnID",
					"check_itemgroupline" => "Check_TxnID",
					"check_itemgroupline_itemline" => "Check_TxnID",
					"check_itemline" => "Check_TxnID",
					"check_linkedtxn" => "FromTxnID",
					"dataext" => "Txn_TxnID",
				],
			],

			"company" => [
				"id_field" => "CompanyName",
				"children" => [
					"company_subscribedservices_service" => "Company_CompanyName",
				],
			],

			"creditcardcharge" => [
				"id_field" => "TxnID",
				"children" => [
					"creditcardcharge_expenseline" => "CreditCardCharge_TxnID",
					"creditcardcharge_itemgroupline" => "CreditCardCharge_TxnID",
					"creditcardcharge_itemgroupline_itemline" => "CreditCardCharge_TxnID",
					"creditcardcharge_itemline" => "CreditCardCharge_TxnID",
					"dataext" => "Txn_TxnID",
				],
			],

			"creditcardcredit" => [
				"id_field" => "TxnID",
				"children" => [
					"creditcardcredit_expenseline" => "CreditCardCredit_TxnID",
					"creditcardcredit_itemgroupline" => "CreditCardCredit_TxnID",
					"creditcardcredit_itemgroupline_itemline" => "CreditCardCredit_TxnID",
					"creditcardcredit_itemline" => "CreditCardCredit_TxnID",
					"dataext" => "Txn_TxnID",
				],
			],

			"creditmemo" => [
				"id_field" => "TxnID",
				"children" => [
					"creditmemo_creditmemoline" => "CreditMemo_TxnID",
					"creditmemo_creditmemolinegroup" => "CreditMemo_TxnID",
					"creditmemo_creditmemolinegroup_creditmemoline" => "CreditMemo_TxnID",
					"creditmemo_linkedtxn" => "FromTxnID",
					"dataext" => "Txn_TxnID",
				],
			],

			"creditmemolinegroup" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"creditmemolinegroup_creditmemoline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			'customer' => [
				'id_field' => 'ListID',
				'children' => [
					'dataext' => 'Entity_ListID',
				],
			],

			"deposit" => [
				"id_field" => "TxnID",
				"children" => [
					"deposit_depositline" => "Deposit_TxnID",
					"dataext" => "Txn_TxnID",
				],
			],

			"employee" => [
				"id_field" => "ListID",
				"children" => [
					"employee_earnings" => "Employee_ListID",
					"dataext" => "Entity_ListID",
				],
			],

			'estimate' => [
				'id_field' => 'TxnID',
				'children' => [
					'estimate_estimateline' => 'Estimate_TxnID',
					'estimate_estimatelinegroup' => 'Estimate_TxnID',
					'estimate_estimatelinegroup_estimateline' => 'Estimate_TxnID',
					//'invoice_linkedtxn" => "FromTxnID",
					'dataext' => 'Entity_ListID',
				]
			],

			"estimate_estimatelinegroup" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"estimate_estimateline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"estimate_estimatelinegroup" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"estimate_estimatelinegroup_estimateline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"inventoryadjustment" => [
				"id_field" => "TxnID",
				"children" => [
					"inventoryadjustment_inventoryadjustmentline" => "InventoryAdjustment_TxnID",
					"dataext" => "Txn_TxnID",
				],
			],

			"invoice" => [
				"id_field" => "TxnID",
				"children" => [
					"invoice_invoiceline" => "Invoice_TxnID",
					"invoice_invoicelinegroup" => "Invoice_TxnID",
					"invoice_invoicelinegroup_invoiceline" => "Invoice_TxnID",
					"invoice_linkedtxn" => "FromTxnID",
					"dataext" => "Txn_TxnID",
				],
			],

			"invoice_invoiceline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"invoice_invoicelinegroup" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"invoice_invoicelinegroup_invoiceline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"itemgroup" => [
				"id_field" => "ListID",
				"children" => [
					"itemgroup_itemgroupline" => "ItemGroup_ListID",
					"dataext" => "Entity_ListID",
				],
			],

			"iteminventory" => [
				"id_field" => "ListID",
				"children" => [
					"dataext" => "Entity_ListID",
				],
			],

			"iteminventoryassembly" => [
				"id_field" => "ListID",
				"children" => [
					"iteminventoryassembly_iteminventoryassemblyline" => "ItemInventoryAssembly_ListID",
					"dataext" => "Txn_TxnID",
				],
			],

			"itemnoninventory" => [
				"id_field" => "ListID",
				"children" => [
					"dataext" => "Entity_ListID",
				],
			],

			"itemdiscount" => [
				"id_field" => "ListID",
				"children" => [
					"dataext" => "Entity_ListID",
				],
			],

			"itemfixedasset" => [
				"id_field" => "ListID",
				"children" => [
					"dataext" => "Entity_ListID",
				],
			],

			"itemothercharge" => [
				"id_field" => "ListID",
				"children" => [
					"dataext" => "Entity_ListID",
				],
			],

			"itempayment" => [
				"id_field" => "ListID",
				"children" => [
					"dataext" => "Entity_ListID",
				],
			],

			"itemreceipt" => [
				"id_field" => "TxnID",
				"children" => [
					"itemreceipt_expenseline" => "ItemReceipt_TxnID",
					"itemreceipt_itemgroupline" => "ItemReceipt_TxnID",
					"itemreceipt_itemgroupline_itemline" => "ItemReceipt_TxnID",
					"itemreceipt_itemline" => "ItemReceipt_TxnID",
					"itemreceipt_linkedtxn" => "FromTxnID",
					"dataext" => "Entity_ListID",
				],
			],

			"itemsalestaxgroup" => [
				"id_field" => "ListID",
				"children" => [
					"itemsalestaxgroup_itemsalestax" => "ItemSalesTaxGroup_ListID",
					"dataext" => "Entity_ListID",
				],
			],

			"itemservice" => [
				"id_field" => "ListID",
				"children" => [
					"dataext" => "Entity_ListID",
				],
			],

			"itemsubtotal" => [
				"id_field" => "ListID",
				"children" => [
					"dataext" => "Entity_ListID",
				],
			],

			"journalentry" => [
				"id_field" => "TxnID",
				"children" => [
					"journalentry_journalcreditline" => "JournalEntry_TxnID",
					"journalentry_journaldebitline" => "JournalEntry_TxnID",
					"dataext" => "Entity_ListID",
				],
			],

			"pricelevel" => [
				"id_field" => "ListID",
				"children" => [
					"pricelevel_pricelevelperitem" => "PriceLevel_ListID",
				],
			],

			"purchaseorder" => [
				"id_field" => "TxnID",
				"children" => [
					"purchaseorder_purchaseorderline" => "PurchaseOrder_TxnID",
					"purchaseorder_purchaseorderlinegroup" => "PurchaseOrder_TxnID",
					"purchaseorder_purchaseorderlinegroup_purchaseorderline" => "PurchaseOrder_TxnID",
					"purchaseorder_linkedtxn" => "FromTxnID",
					"dataext" => "Entity_ListID",
				],
			],

			"purchaseorder_purchaseorderline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"purchaseorder_purchaseorderlinegroup" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"purchaseorder_purchaseorderlinegroup_purchaseorderline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"receievepayment" => [
				"id_field" => "TxnID",
				"children" => [
					"receivepayment_appliedtotxn" => "FromTxnID",
					'receivepayment_appliedtotxn' => 'ReceivePayment_TxnID',
					"dataext" => "Txn_TxnID",
				],
			],

			"salesorder" => [
				"id_field" => "TxnID",
				"children" => [
					"salesorder_salesorderline" => "SalesOrder_TxnID",
					"salesorder_salesorderlinegroup" => "SalesOrder_TxnID",
					"salesorder_salesorderlinegroup_salesorderline" => "SalesOrder_TxnID",
					"salesorder_linkedtxn" => "FromTxnID",
					"dataext" => "Entity_ListID",
				],
			],

			"salesorder_salesorderline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"salesorder_salesorderlinegroup" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"salesorder_salesorderlinegroup_salesorderline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"salesreceipt" => [
				"id_field" => "TxnID",
				"children" => [
					"salesreceipt_salesreceiptline" => "SalesReceipt_TxnID",
					"salesreceipt_salesreceiptlinegroup" => "SalesReceipt_TxnID",
					"salesreceipt_salesreceiptlinegroup_salesreceiptline" => "SalesReceipt_TxnID",
					"dataext" => "Txn_TxnID",
				],
			],

			"salesreceipt_salesreceiptline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"salereceipt_salesreceiptlinegroup" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"salesreceipt_salesreceiptlinegroup_salesreceiptline" => [
				"id_field" => "TxnLineID",
				"children" => [
					"dataext" => "Txn_TxnID",
				],
			],

			"unitofmeasureset" => [
				"id_field" => "ListID",
				"children" => [
					"unitofmeasureset_defaultunit" => "UnitOfMeasureSet_ListID",
					"unitofmeasureset_relatedunit" => "UnitOfMeasureSet_ListID",
				],
			],

			"vendor" => [
				"id_field" => "ListID",
				"children" => [
					"dataext" => "Entity_ListID",
				],
			],

			"vendorcredit" => [
				"id_field" => "TxnID",
				"children" => [
					"vendorcredit_expenseline" => "VendorCredit_TxnID",
					"vendorcredit_itemline" => "VendorCredit_TxnID",
					"vendorcredit_itemgroupline" => "VendorCredit_TxnID",
					"vendorcredit_itemgroupline_itemline" => "VendorCredit_TxnID",
					"vendorcredit_linkedtxn" => "FromTxnID",
					"dataext" => "Txn_TxnID",
				],
			],

			"workerscompcode" => [
				"id_field" => "ListID",
				"children" => [
					"workerscompcode_ratehistory" => "WorkersCompCode_ListID",
				],
			],
		];

		// This stores a list of TxnLineID => qbsql_id mappings that were deleted
		$deleted = [];

		//
		$Driver = Singleton::getInstance();

		if (!isset($delete_children_map[$table]))
		{
			return false;
		}
		else
		{
			$TxnID_or_ListID = $object->get($delete_children_map[$table]['id_field']);
			foreach ($delete_children_map[$table]['children'] as $key => $value)
			{
				//print('key: '); print_r($key); print("\n");
				//print('value: '); print_r($value); print("\n");

				// @todo Fix this wrong delete flag field
				// If we are actually deleting an entire element, then we need to check the delete mode and if desired, just flag them rather than remove the rows.
				if ($fullDelete &&
					isset($callback_config['delete']) &&
					$callback_config['delete'] == WebConnectorSQL::DELETE_FLAG)
				{
					if ($key == 'dataext' && !$deleteDataExt)
					{
						continue;
					}

					$multipart = [$value => $TxnID_or_ListID];

					$order = [];
					if (substr($key, -4, 4) == 'line')
					{
						$order = array( 'SortOrder' => 'ASC', 'TxnLineID' => 'ASC' );

						if ($key == 'iteminventoryassembly_iteminventoryassemblyline')
						{
							unset($order['TxnLineID']);
						}
					}

					$obj = new SqlObject($table, null);

					// Get a list of stuff that's going to be deleted
					$list = $Driver->select(Sql::$TablePrefix['SQL_MIRROR'] . $key, $multipart, $order);
					foreach ($list as $arr)
					{
						if (isset($arr[PackageInfo::QbId['TXNLINEID']]))
						{
							$deleted[$key][PackageInfo::QbId['TXNLINEID']][$arr[PackageInfo::QbId['TXNLINEID']]] = [
								$arr[Sql::Field['ID']],
								$arr[Sql::Field['USERNAME_ID']],
								$arr[Sql::Field['EXTERNAL_ID']]
							];
						}
					}

					// @todo Make the Boolean TRUE value used in the QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG field a constant,
					//      in case the sql driver used uses something other than 1 and 0.
					//$obj->set(QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG, 1);
					//$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $key, $obj, array( $multipart ));
				}
				else
				{
					// Otherwise we actually remove the rows.
					if ($key == 'dataext' && !$deleteDataExt)
					{
						continue;
					}

					$multipart = [$value => $TxnID_or_ListID];

					$order = [];
					if (substr($key, -4, 4) == 'line')
					{
						$order = ['SortOrder' => 'ASC', 'TxnLineID' => 'ASC'];

						if ($key == 'iteminventoryassembly_iteminventoryassemblyline')
						{
							unset($order['TxnLineID']);
						}
					}

					//print_r($multipart);

					//
					// Get a list of stuff that's going to be deleted
					//

					// These are things that have a permenent TxnID (they've been synced to QB before)
					$list = $Driver->select(Sql::$TablePrefix['SQL_MIRROR'] . $key, $multipart, $order );
					foreach ($list as $arr)
					{
						if (isset($arr[PackageInfo::QbId['TXNLINEID']]))
						{
							$deleted[$key][PackageInfo::QbId['TXNLINEID']][$arr[PackageInfo::QbId['TXNLINEID']]] = [
								$arr[Sql::Field['ID']],
								$arr[Sql::Field['USERNAME_ID']],
								$arr[Sql::Field['EXTERNAL_ID']]
							];
						}
					}

					// These are things that were using a temporary TxnID, and now have a perm TxnID (it just got synced to QuickBooks)
					if (isset($extra['is_add_response']))
					{
						$multipart_tmp = array( $value => $extra['AddResponse_OldKey'] );
						$list = $Driver->select(Sql::$TablePrefix['SQL_MIRROR'] . $key, $multipart_tmp, $order);

						foreach ($list as $arr)
						{
							if (isset($arr[PackageInfo::QbId['TXNLINEID']]))
							{
								$deleted[$key][PackageInfo::QbId['TXNLINEID']][$arr[PackageInfo::QbId['TXNLINEID']]] = [
									$arr[Sql::Field['ID']],
									$arr[Sql::Field['USERNAME_ID']],
									$arr[Sql::Field['EXTERNAL_ID']]
								];
							}
						}
					}

					//print_r($list);
					//print("\n\n\n");

					// This query deletes anything with an existing TxnID (i.e. this was UPDATEing QuickBooks)
					$Driver->delete(Sql::$TablePrefix['SQL_MIRROR'] . $key, array( $multipart ));

					// This query deletes anything with a new TxnID (i.e. the TxnID was temporary, and
					//	now it's permenent because it's been ADDed to QuickBooks, so we need to delete
					//	the child records with the temporary TxnID)
					if (isset($extra['IsAddResponse']) or isset($extra['is_add_response']))
					{
						$multipart_tmp = array( $value => $extra['AddResponse_OldKey'] );
						$Driver->delete(Sql::$TablePrefix['SQL_MIRROR'] . $key, array( $multipart_tmp ));
					}
				}
			}
		}

		//print_r($deleted);
	}

	protected static function _addResponse(string $type, $List, int $requestID, string $user, string $action, $ID, array $extra, ?string &$err, $last_action_time, $last_actionident_time, ?string $xml, array $idents, array $callback_config = [])
	{
		// Call our hooks here, we just *added* something to QuickBooks

		return static::_queryResponse($type, $List, $requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $callback_config);
	}

	/**
	 *
	 *
	 *
	 */
	protected static function _queryResponse(string $type, $List, int $requestID, string $user, string $action, $ID, array $extra, ?string &$err, $last_action_time, $last_actionident_time, ?string $xml, array $idents, array $callback_config = [])
	{
		$type = strtolower($type);

		$Driver = Singleton::getInstance();
		$objects = [];

		// For each one of the objects we got back in the qbXML response...
		foreach ($List->children() as $Node)
		{
			// If this object is a base-level object, we're going to keep track of it's TxnID or
			//	ListID so that we can use it to tie child elements back to this base-level
			//	element (about 20 or 30 lines below this is that code)

			// Child records get deleted, and then re-created with the same
			//	qbsql_id values so that we don't muck up people's associated
			//	records. This keeps track of deleted records so we can re-create
			//	the records with the same qbsql_id values.
			$deleted = [];

			// Convert the XML nodes to objects, based on the XML to SQL schema definitions in Schema.php
			$objects = [];
			static::_transformToSQLObjects('', $Node, $objects);

			//print_r($objects);
			//exit;

			// For each object we created from the XML nodes...
			// (might have created more than one, e.g. an Invoice, plus 10 InvoiceLines, plus a Invoice_DataExt, etc.)

			/*
			if (count($objects) > 1)
			{
				print_r($objects);
				exit;
			}
			*/

			// This keeps track of whether or not we're ignoring this entire batch of UPDATES/INSERTS
			$ignore_this_and_its_children = false;

			foreach ($objects as $key => $object)
			{
				$Object =& $object;

				if ($ignore_this_and_its_children)
				{
					// If we're supposed to ignore this object and it's children, then just continue
					continue;
				}

				$table = $Object->table();
				$path = $Object->path();
				$map = [];
				Schema::mapPrimaryKey($path, Schema::MAP_TO_SQL, $map);

				// Special hack for preferences
				if ($Object->table() == 'preferences')
				{
					$map = ['qb_preferences', 'qbsql_external_id'];
				}

				//print_r($Object);
				//print_r($path);
				//print_r($map);
				//exit;

				//
				if ($table && count($map) && $map[0] && $map[1])
				{
					$addMapTest = [];
					$addMapTestOthers = [];
					Schema::mapToSchema(trim(Utilities::actionToXMLElement($action)), Schema::MAP_TO_SQL, $addMapTest, $addMapTestOthers);

					if ((!isset($extra['IsAddResponse']) && !isset($extra['is_add_response'])) || !(count($addMapTest) && $addMapTest[0]) || $map[0] != $addMapTest[0])
					{
						// GARRETT'S bug Fix -- Arrays with primary keys consisting of multiple fields weren't updating properly
						// due to failure to check for arrays.
						$multipart = [];
						if (is_array($map[1]))
						{
							foreach($map[1] as $table_name)
							{
								$multipart[$table_name] = $object->get($table_name);
							}
						}
						else
						{
							$multipart[ $map[1] ] = $object->get( $map[1] );
						}
					}
					else
					{
						$multipart[ Sql::Field['ID'] ] = $ID;
					}

					$hooks = [];
					if (isset($callback_config['hooks']))
					{
						$hooks = $callback_config['hooks'];
					}

					if ($tmp = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . $table, $multipart ))
					{
						$actually_do_update = false;
						$actually_do_updaterelatives = false;
						$actually_do_deletechildren = false;



						if (isset($tmp[Utilities::keyForAction($action)]))
						{
							// I have no idea what this does or what this is for....
							// > EDIT: This keeps track of what the old TxnID or ListID is, so that we can use it to update relative tables

							$extra['AddResponse_OldKey'] = $tmp[Utilities::keyForAction($action)];
							$extra['temporary_TxnID_or_ListID_or_LineID'] = $tmp[Utilities::keyForAction($action)];
						}

						if (empty($extra['AddResponse_OldKey']) &&
							Utilities::keyForAction($action) == 'TxnID' &&
							isset($tmp['TxnLineID']))
						{
							//$extra['AddResponse_OldKey'] = $tmp->get("TxnLineID");
							$extra['AddResponse_OldKey'] = $tmp['TxnLineID'];
							$extra['temporary_TxnID_or_ListID_or_LineID'] = $tmp['TxnLineID'];
						}

						// Make sure a conflict mode has been selected
						if (empty($callback_config['conflicts']))
						{
							$callback_config['conflicts'] = null;
						}

						if (empty($callback_config['mode']))
						{
							$callback_config['mode'] = WebConnectorSQL::MODE_READONLY;
						}

						if (isset($extra['is_query_response']) ||
							isset($extra['is_import_response']) ||
							isset($extra['is_mod_response']) ||
							isset($extra['is_add_response']))
						{
							// @TODO There should probably be some conflict handling code below to handle conflicts

							$actually_do_update = true;
							$actually_do_deletechildren = true;
							$actually_do_updaterelatives = true;
						}

						//$Driver->log('Diagnostics for incoming: is_query[' . !empty($extra['is_query_response']) . '], is_import[' . !empty($extra['is_import_response']) . '], is_mod[' . !empty($extra['is_mod_response']) . '], is_add[' . !empty($extra['is_add_response']) . '], conflict mode: ' . $callback_config['conflicts'] . '', null, PackageInfo::LogLevel['DEVELOP']);

						// Conflict handling code
						// @todo I think this should only apply to query and import, right? I mean, if it's a mod or add, then
						//	*of course* it was modified after resynced, that's how we knew to send it back to QuickBooks...
						if ($tmp[Sql::Field['MODIFY']] > $tmp[Sql::Field['RESYNC']] &&
							$callback_config['mode'] != WebConnectorSQL::MODE_READONLY)
						{
							// CONFLICT resolution code

							switch ($callback_config['conflicts'])
							{
								case WebConnectorSQL::CONFLICT_NEWER:

									$msg = 'Conflict mode: (newer) ' . $callback_config['conflicts'] . ' is not supported right now.';
									trigger_error($msg);
									die($msg);

								case WebConnectorSQL::CONFLICT_QUICKBOOKS:

									// QuickBooks is master, so remove all existing child records of this record, then apply the QuickBooks version update

									$actually_do_deletechildren = true;
									$actually_do_update = true;

									//static::_DeleteChildren($table, $user, $action, $ID, $object, $extra);
									//$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $table, $object, array( $multipart ));

									break;
								case WebConnectorSQL::CONFLICT_CALLBACK:

									$msg = 'Conflict mode: (callback) ' . $callback_config['conflicts'] . ' is not supported right now.';
									trigger_error($msg);
									die($msg);

									break;
								case WebConnectorSQL::CONFLICT_SQL:

									// The SQL table is the master table, but we have an out-of-date EditSequence value
									//	In this case, what we want to do is update our record to the latest EditSequence value,
									//	and then re-queue the object so that it gets updated the next time the sync runs to
									//	the values from the SQL record

									$tmp_editsequence_update = new SqlObject($table, null);
									$tmp_editsequence_update->set('EditSequence', $object->get('EditSequence'));

									// *Just* update the EditSequence value
									$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $table, $tmp_editsequence_update, array( $multipart ), false);

									// Re-queue it so the conflict gets resolved
									$Driver->queueEnqueue($user, Utilities::convertActionToMod($action), $tmp[Sql::Field['ID']], true, WebConnectorSQL::$CONFLICT_QUEUE_PRIORITY, $extra);

									break;
								case WebConnectorSQL::CONFLICT_LOG:
								default:

									if (isset($extra['IsModResponse']) || isset($extra['is_mod_response']) || isset($extra['is_add_response']))
									{
										// If it's actually a mod response, then this isn't actually a conflict, it's just the mod response happening normally

										$actually_do_update = true;
										$actually_do_deletechildren = true;
										$actually_do_updaterelatives = true;
									}
									else
									{

										// Log it...?
										$Driver->log('Conflict occurred at: ' . $table, null, PackageInfo::LogLevel['NORMAL']);
									}

									break;
							}
						}

						//print_r($object);
						//print_r($tmp);

						// If the EditSequence has not changed since the last time this record was updated,
						//	then we can just skip this update because everything should already be up to
						//	date.
						//
						// This works around a very important issue as a result of Mod requests. When a Mod
						//	request is issued and succeeds, it updates the record. Then, on the next Query
						//	request, the record will be re-imported because the DateModified timestamp was
						//	updated as a result of the Mod request. However, if the record is modified
						//	by the end-user in between that Mod request and Import, the changes the user
						//	made will be overwritten/a conflict will occur *even though the Query response
						//	was only due to a Mod request that we sent ourselves* and the record in
						//	QuickBooks never actually changed between the Mod and the Query.
						if (empty($extra['is_query_response']) && 					// However, if is_query_response is set this was a forced-update (like when a balance updates, the EditSequence doesn't change but the record *does* need to be updated)
							isset($tmp['EditSequence']) && 						// Check if EditSequence is set, qb_company doesn't have this field
							$tmp['EditSequence'] == $object->get('EditSequence'))
						{
							$actually_do_update = false;
							$actually_do_deletechildren = false;
							$actually_do_updaterelatives = false;

							//$Driver->log('Ignoring UPDATE: ' . $table . ': ' . print_r($object, true) . ' due to EditSequence equality.', null, PackageInfo::LogLevel['DEVELOP']);

							// Make sure we ignore the children too (invoice lines, data exts, etc.)
							$ignore_this_and_its_children = true;
						}

						if ($callback_config['mode'] == WebConnectorSQL::MODE_WRITEONLY)
						{
							// In WRITE-ONLY mode, we only write changes to QuickBooks, but never read them back

							// (but should we update the EditSequence still?)

							$actually_do_update = false;
							$actually_do_deletechildren = false;
							$actually_do_updaterelatives = false;
						}

						//$deleted = [];
						if ($actually_do_deletechildren)
						{
							static::_deleteChildren($table, $user, $action, $ID, $object, $extra, $deleted);
							//$Driver->log('Immediately after deleting: ' . print_r($deleted, true));
						}

						if ($actually_do_updaterelatives)
						{
							static::_updateRelatives($table, $user, $action, $ID, $object, $extra);
						}

						if ($actually_do_update)
						{
							// This handles setting certain special fields (SortOrder, booleans, etc.)
							static::_massageUpdateRecord($table, $object);

							//print('applying updates, and with these deletes: ');
							//print_r($deleted);

							$object->set(Sql::Field['MODIFY'], date('Y-m-d H:i:s'));

							//$Driver->log('Applying UPDATE: ' . $table . ': ' . print_r($object, true) . ', where: ' . print_r($multipart, true), null, PackageInfo::LogLevel['DEVELOP']);
							$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $table, $object, array( $multipart ));

							$qbsql_id = null;
							if (!empty($multipart[Sql::Field['ID']]))			// I'm not sure why this would ever be empty...?
							{
								$qbsql_id = $multipart[[Sql::Field['ID'] => $ID]];
							}

							// Call any hooks that occur when a record is updated
							$hook_data = [
								'hook' => QuickbooksSql::HOOK_SQL_UPDATE,
								'user' => $user,
								'table' => Sql::$TablePrefix['SQL_MIRROR'] . $table,
								'object' => $object,
								'data' => $object->asArray(),
								'qbsql_id' => $qbsql_id,
								'where' => array( $multipart ),
							];

							$err = null;
							static::_callHooks($hooks, QuickbooksSql::HOOK_SQL_UPDATE, $requestID, $user, $err, $hook_data, $callback_config);
						}
						else
						{
							//$Driver->log('Skipping UPDATE: ' . $table . ': ' . print_r($object, true) . ', where: ' . print_r($multipart, true), null, PackageInfo::LogLevel['DEVELOP']);
						}

						if ($actually_do_update && isset($extra['is_add_response']))
						{
							// It's an add response, call the hooks
							$qbsql_id = null;
							if (!empty($multipart[[Sql::Field['ID'] => $ID]]))			// I'm not sure why this would ever be empty...?
							{
								$qbsql_id = $multipart[[Sql::Field['ID'] => $ID]];
							}

							// Call any hooks that occur when a record is updated
							$hook_data = [
								'hook' => QuickbooksSql::HOOK_QUICKBOOKS_INSERT,
								'user' => $user,
								'table' => Sql::$TablePrefix['SQL_MIRROR'] . $table,
								'object' => $object,
								'data' => $object->asArray(),
								'qbsql_id' => $qbsql_id,
								'where' => array( $multipart ),
							];

							$err = null;
							static::_callHooks($hooks, QuickbooksSql::HOOK_QUICKBOOKS_INSERT, $requestID, $user, $err, $hook_data, $callback_config);
						}
						else if ($actually_do_update && isset($extra['is_mod_response']))
						{
							// It's an add response, call the hooks
							$qbsql_id = null;
							if (!empty($multipart[[Sql::Field['ID'] => $ID]]))			// I'm not sure why this would ever be empty...?
							{
								$qbsql_id = $multipart[[Sql::Field['ID'] => $ID]];
							}

							// Call any hooks that occur when a record is updated
							$hook_data = [
								'hook' => QuickbooksSql::HOOK_QUICKBOOKS_UPDATE,
								'user' => $user,
								'table' => Sql::$TablePrefix['SQL_MIRROR'] . $table,
								'object' => $object,
								'data' => $object->asArray(),
								'qbsql_id' => $qbsql_id,
								'where' => array( $multipart ),
							];

							$err = null;
							static::_callHooks($hooks, QuickbooksSql::HOOK_QUICKBOOKS_UPDATE, $requestID, $user, $err, $hook_data, $callback_config);
						}
					}
					else
					{
						// The record *DOES NOT* exist in the current table, so just INSERT it

						if ($callback_config['mode'] != WebConnectorSQL::MODE_WRITEONLY)
						{
							// This handles setting certain special fields (booleans, SortOrder, etc.)
							static::_massageInsertRecord($table, $object);

							//$Driver->log('DELETED: ' . print_r($deleted, true) . ', table: [' . $table . ']');

							// This makes sure that re-inserted child records are re-inserted with the
							//	same qbsql_id values
							if (isset($deleted[$table][PackageInfo::QbId['TXNLINEID']][$object->get(PackageInfo::QbId['TXNLINEID'])][0]))
							{
								$tmp = $deleted[$table][PackageInfo::QbId['TXNLINEID']][$object->get(PackageInfo::QbId['TXNLINEID'])];
								unset($deleted[$table][PackageInfo::QbId['TXNLINEID']][$object->get(PackageInfo::QbId['TXNLINEID'])]);		// Can't use this anymore after it's been used for an INSERT

								$object->set([Sql::Field['ID'] => $ID], $tmp[0]);
								$object->set(Sql::Field['USERNAME_ID'], $tmp[1]);
								$object->set(Sql::Field['EXTERNAL_ID'], $tmp[2]);
							}
							else if (isset($deleted[$table][PackageInfo::QbId['TXNLINEID']]) &&
								count($deleted[$table][PackageInfo::QbId['TXNLINEID']]) > 0)
							{
								// We deleted some child from this table, and what we deleted *should*
								//	have been sent to QuickBooks and received from QuickBooks in the
								//	same order... so we should be able to just fetch the next deleted
								//	thing, and re-use that qbsql_id value

								reset($deleted[$table][PackageInfo::QbId['TXNLINEID']]);
								$tmp = array_shift($deleted[$table][PackageInfo::QbId['TXNLINEID']]); 	// Remove it from the list so it can't be used anymore

								$object->set([Sql::Field['ID'] => $ID], $tmp[0]);
								$object->set(Sql::Field['USERNAME_ID'], $tmp[1]);
								$object->set(Sql::Field['EXTERNAL_ID'], $tmp[2]);
							}

							if ('' == $object->get(Sql::Field['USERNAME_ID']))
							{
								$object->set(Sql::Field['USERNAME_ID'], null);
							}

							if ('' == $object->get(Sql::Field['EXTERNAL_ID']))
							{
								$object->set(Sql::Field['EXTERNAL_ID'], null);
							}

							//print_r($object);

							//$Driver->log('Applying INSERT: ' . $table . ': ' . print_r($object, true), null, PackageInfo::LogLevel['DEVELOP']);

							$object->set(Sql::Field['MODIFY'], date('Y-m-d H:i:s'));

							$Driver->insert(Sql::$TablePrefix['SQL_MIRROR'] . $table, $object);
							$last = $Driver->last();

							// Call any hooks that occur when a record is inserted
							$hook_data = [
								'hook' => QuickbooksSql::HOOK_SQL_INSERT,
								'user' => $user,
								'table' => Sql::$TablePrefix['SQL_MIRROR'] . $table,
								'object' => $object,
								'data' => $object->asArray(),
								'qbsql_id' => $last,
							];

							$err = null;
							static::_callHooks($hooks, QuickbooksSql::HOOK_SQL_INSERT, $requestID, $user, $err, $hook_data, $callback_config);
						}
						else
						{
							//$Driver->log('Skipping INSERT: ' . $table . ': ' . print_r($object, true), null, PackageInfo::LogLevel['DEVELOP']);
						}
					}

					// Triggered actions
					//	Receive Payment => reload any linked invoices
					//	Invoice => reload the customer
					//	Purchase Order => reload the vendor
					static::_triggerActions($user, $table, $Object, $action);
				}
			}
		}

		// Find out if we need to iterate further to get more results
		$matches = [];
		//$iterator_count = ereg('iteratorRemainingCount="([0-9]*)" iteratorID="([^"]*)"', $xml, $matches);
		$matched_iteratorID = XML::extractTagAttribute('iteratorID', $xml);
		$matched_iteratorRemainingCount = XML::extractTagAttribute('iteratorRemainingCount', $xml);

		// If an iterator was used and there's results remaining
		if ($matched_iteratorID && $matched_iteratorRemainingCount > 0)
		{
			$extra = [ 'iteratorID' => $matched_iteratorID ]; // Set the iteratorID to be used

			/*
			// What is this code trying to do...? This doesn't look right...
			if ( (int) $matches[1] < WebConnectorSQL::$ITERATOR_MAXRETURNED)
			{
				$extra['maxReturned'] = (int) $matches[1];
			}
			*/

			// 		queueEnqueue($user, $action, $ident, $replace = true, $priority = 0, $extra = null, $qbxml = null)
			$Driver->queueEnqueue($user, $action, '', true, WebConnectorSQL::$ITERATOR_PRIORITY, $extra);  // Queue up another go!
		}
		else
		{
			// We're done with this iterator!

			// When the current iterator started...
			$module = __CLASS__;
			$type = null;
			$opts = null;

			// 					configRead($user, $module, $key, &$type, &$opts)
			$curr_sync_datetime = $Driver->configRead($user, $module, static::_keySyncCurr($action), $type, $opts);	// last sync started...

			//print('WRITING: [' . $curr_sync_datetime . '] from /' . $module . '/ {' . static::_keySyncCurr($action) . '}');

			// Start of the iteration, update the previous timestamp to NOW
			$Driver->configWrite($user, $module, static::_keySyncPrev($action), $curr_sync_datetime, null);
		}
	}

	/**
	 *
	 *
	 */
	protected static function _triggerActions($user, $table, $Object, $action = null)
	{
		$Driver = Singleton::getInstance();

		// Be *CAREFUL* here, you don't want to trigger an infinite loop of
		//	high-priority Query requests! i.e.:
		//	ReceivePayment_AppliedToTxn requests an InvoiceQuery
		//	Invoice_LinkedTxn requests a ReceivePaymentQuery
		//	ReceivePayment_AppliedToTxn requests an InvoiceQuery
		//	... wash rinse repeat

		$priority = 9999;
		if ($action)
		{
			$priority = Utilities::priorityForAction($action) - 1;
		}

		// Account. 	Balance, TotalBalance
		// Bill. 		IsPaid, OpenAmount, AmountDue
		// Charge. 		BalanceRemaining
		// CreditMemo.	IsPending, CreditRemaining
		// Customer.	Balance, TotalBalance,
		// Invoice.		IsPending, AppliedAmount, BalanceRemaining, IsPaid

		//$Driver->log('Running triggered actions for: [' . $table . ']', null, PackageInfo::LogLevel['DEBUG']);

		switch (strtolower($table))
		{
			case 'receivepayment_appliedtotxn':

				// Fetch the linked invoice
				$where = array(
					'TxnID' => $Object->get('ToTxnID'),
					);

				// @todo WARNING WARNING WARNING THIS DOES NOT WORK, I DONT KNOW WHY!

				/*
				if ($Object->get('TxnType') == 'Invoice' and
					$arr = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . 'invoice', $where))
				{
					$Driver->log('Running triggered actions: derive invoice, derive customer', null, PackageInfo::LogLevel['DEBUG']);

					// Fetch the derived fields from the invoice, because the invoice needs it's balance updated
					$Driver->queueEnqueue($user, PackageInfo::Actions['DERIVE_INVOICE'], null, true, $priority,
						array( 'TxnID' => $arr['TxnID'] ) );

					// Fetch the derived fields from the customer, balance updated
					$Driver->queueEnqueue($user, PackageInfo::Actions['DERIVE_CUSTOMER'], null, true, $priority,
						array( 'ListID' => $arr['Customer_ListID'] ) );
				}
				*/

				break;
			case 'receivepayment':
			case 'invoice':

				// A customer has an updated invoice or payment, so the Customer Balance changed

				/*
				$Driver->log('Running triggered actions: derive customer', null, PackageInfo::LogLevel['DEBUG']);

				$Driver->queueEnqueue($user, PackageInfo::Actions['DERIVE_CUSTOMER'], null, true, $priority,
					array( 'ListID' => $Object->get('Customer_ListID') ) );
				*/

				break;
			case 'bill':
			case 'billpaymentcheck':
			case 'billpaymentcreditcard':

				// We paid a bill, so the Vendor Balance has changed



				break;
		}
	}



	/**
	 *
	 */
	protected static function _massageUpdateRecord(string $table, &$object)
	{
		$retr = static::_massageInsertRecord($table, $object);

		$parts = [
			'_Addr1',
			'_Addr2',
			'_Addr3',
			'_Addr4',
			'_Addr5',
			'_City',
			'_State',
			'_PostalCode',
			'_Country',
			'_Note',
		];

		$isset = [];

		foreach (['ShipAddress', 'BillAddress', 'VendorAddress'] as $addrtype)
		{
			foreach ($parts as $part)
			{
				if ($object->get($addrtype . $part))
				{
					$isset[$addrtype] = true;
					break;
				}
			}
		}

		//$Driver = Singleton::getInstance();
		//$Driver->log('issets: ' . print_r($isset, true));

		foreach ($isset as $addrtype => $true)
		{
			foreach ($parts as $part)
			{
				if (!$object->get($addrtype . $part))
				{
					$object->set($addrtype . $part, '');
				}
			}
		}

		//$Driver->log('object: ' . print_r($object, true));

		return $retr;
	}

	protected static function _massageBoolean($value)
	{

	}

	/**
	 *
	 */
	protected static function _massageInsertRecord(string $table, &$object)
	{
		$Driver = Singleton::getInstance();
		$table = strtolower($table);

		// This is a list of "Boolean" fields in QuickBooks
		//	QuickBooks sends us boolean value as the strings "true" and
		//	"false", so we need to convert them to 1 and 0 so that we
		//	can store this in an INT field in the database (not all
		//	databases support a BOOLEAN type)
		$qb_to_sql_booleans = [
			'EmployeePayrollInfo_IsUsingTimeDataToCreatePaychecks',
			'EmployeePayrollInfo_ClearEarnings',
			'EmployeePayrollInfo_SickHours_IsResettingHoursEachNewYear',
			'EmployeePayrollInfo_VacationHours_IsResettingHoursEachNewYear',
			'IsAdjustment',
			'IsActive',
			'IsBillable',
			'IsBilled',
			'IsClosed',
			'IsFinanceCharge',
			'IsFullyInvoiced',
			'IsFullyReceived',
			'IsManuallyClosed',
			'IsPaid',
			'IsPending',
			'IsPrintItemsInGroup',
			'IsToBeEmailed',
			'IsToBePrinted',
			'IsSampleCompany',
			'IsTaxable',
			'IsVendorEligibleFor1099',
			'AccountingPrefs_IsUsingAccountNumbers',
			'AccountingPrefs_IsRequiringAccounts',
			'AccountingPrefs_IsUsingClassTracking',
			'AccountingPrefs_IsUsingAuditTrail',
			'AccountingPrefs_IsAssigningJournalEntryNumbers',
			'FinanceChargePrefs_IsAssessingForOverdueCharges',
			'FinanceChargePrefs_IsMarkedToBePrinted',
			'JobsAndEstimatesPrefs_IsUsingEstimates',
			'JobsAndEstimatesPrefs_IsUsingProgressInvoicing',
			'JobsAndEstimatesPrefs_IsPrintingItemsWithZeroAmounts',
			'MultiCurrencyPrefs_IsMultiCurrencyOn',
			'MultiLocationInventoryPrefs_IsMultiLocationInventoryAvailable',
			'MultiLocationInventoryPrefs_IsMultiLocationInventoryEnabled',
			'PurchasesAndVendorsPrefs_IsUsingInventory',
			'PurchasesAndVendorsPrefs_IsAutomaticallyUsingDiscounts',
			'SalesAndCustomersPrefs_IsTrackingReimbursedExpensesAsIncome',
			'SalesAndCustomersPrefs_IsAutoApplyingPayments',
			'SalesAndCustomersPrefs_PriceLevels_IsUsingPriceLevels',
			'SalesAndCustomersPrefs_PriceLevels_IsRoundingSalesPriceUp',
			'SalesTaxPrefs_IsUsingVendorTaxCode',
			'SalesTaxPrefs_IsUsingCustomerTaxCode',
			'SalesTaxPrefs_IsUsingAmountsIncludeTax',
			'CurrentAppAccessRights_IsAutomaticLoginAllowed',
			'CurrentAppAccessRights_IsPersonalDataAccessAllowed',
			'IsAutomaticLogin',
		];

		// Cast QuickBooks booleans (strings, "true" and "false") to database booleans (tinyint 1 and 0)
		foreach ($qb_to_sql_booleans as $qb_field_boolean)
		{
			$qb_bool = $object->get($qb_field_boolean, false);

			if ($qb_bool !== false)
			{
				if ($Driver->hasTrueBoolean()) {
					$qb_bool_val_sql = filter_var($qb_bool, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
				} else {
					$qb_bool_val_sql = filter_var($qb_bool, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
				}
				$object->set($qb_field_boolean, $qb_bool_val_sql);
			}
		}

		// Some types of objects need some special custom field handling
		$map = [
			'invoice_invoiceline' => 'Invoice_TxnID',
			'purchaseorder_purchaseorderline' => 'PurchaseOrder_TxnID',
			'salesreceipt_salesreceiptline' => 'SalesReceipt_TxnID',
			'estimate_estimateline' => 'Estimate_TxnID',
			'bill_itemline' => 'Bill_TxnID',
			'bill_expenseline' => 'Bill_TxnID',
		];

		switch ($table)
		{
			case 'invoice_invoiceline':
			case 'purchaseorder_purchaseorderline':
			case 'salesreceipt_salesreceiptline':
			case 'estimate_estimateline':
			case 'bill_itemline':
			case 'bill_expenseline':

				// Set the SortOrder for the line items
				if (isset($map[$table]))
				{
					$TxnID = $object->get($map[$table]);

					if ($TxnID)
					{
						$errnum = 0;
						$errmsg = '';
						$res = $Driver->query("
							SELECT
								MAX(SortOrder) AS max_sort_order
							FROM
								" . Sql::$TablePrefix['SQL_MIRROR'] . $table . "
							WHERE
								" . $map[$table] . " = '" . $TxnID . "' ", $errnum, $errmsg);
						$arr = $Driver->fetch($res);

						$object->set('SortOrder', (int) $arr['max_sort_order'] + 1);
					}
				}

				break;
		}

		return true;
	}

	/**
	 *
	 *
	 * @TODO Clean this up!
	 */
	protected static function _transformToSQLObjects($curpath, $Node, &$objects, $current = null, $extra = [])
	{
		if ($Node->childCount())
		{
			//print('LOOKING AT [' . strtolower(trim(trim($curpath) . ', ' . $Node->name())) . ']' . "\n");

			//
			switch (strtolower(trim(trim($curpath) . ' ' . $Node->name())))
			{
				case 'accountret':

					if (!isset($extra['ListID']))
					{
						$extra['ListID'] = $Node->getChildDataAt('AccountRet ListID');
					}

					if (empty($extra['FullName']))
					{
						$extra['FullName'] = $Node->getChildDataAt('AccountRet FullName');
					}

					$extra['EntityListID'] = $extra['ListID'];
					$extra['EntityType'] = 'Account';

					break;
				case 'billingrateret':

					if (!isset($extra['ListID']))
					{
						$extra['ListID'] = $Node->getChildDataAt('BillingRateRet ListID');
					}

					break;
				case 'billpaymentcheckret':

					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('BillPaymentCheckRet TxnID');
					}

					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'BillPaymentCheck';
					//$extra['ExchangeRate'] = $Node->getChildDataAt('BillPaymentCheckRet ExchangeRate');
					//$extra['AmountInHomeCurrency'] = $Node->getChildDataAt('BillPaymentCheckRet AmountInHomeCurrency');

					break;
				case 'billpaymentcreditcardret':

					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('BillPaymentCreditCardRet TxnID');
					}

					break;
				case 'billret':

					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('BillRet TxnID');
					}

					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'Bill';

					break;
				case 'billret itemgrouplineret':

					if (!isset($extra['ListID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('ItemGroupLineRet TxnLineID');
					}

					break;
				case 'chargeret':

					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('ChargeRet TxnID');
					}

					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'Charge';

					break;
				case 'checkret':

					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('CheckRet TxnID');
					}

					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'Check';

					break;
				case 'checkret itemgrouplineret':

					if (!isset($extra['TxnLineID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('ItemGroupLineRet TxnLineID');
					}

					break;
				case 'companyret':
					if (!isset($extra['CompanyName']))
					{
						$extra['CompanyName'] = $Node->getChildDataAt('CompanyRet CompanyName');
					}

					$extra['EntityListID'] = $extra['CompanyName'];
					$extra['EntityType'] = 'Company';

					break;
				case 'creditcardchargeret':

					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('CreditCardChargeRet TxnID');
					}

					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'CreditCardCharge';

					break;
				case 'creditcardchargeret itemgrouplineret':

					if (!isset($extra['TxnLineID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('ItemGroupLineRet TxnLineID');
					}

					break;
				case 'creditcardcreditret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('CreditCardCreditRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'CreditCardCredit';
					break;
				case 'creditcardcreditret itemgrouplineret':
					if (!isset($extra['TxnLineID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('ItemGroupLineRet TxnLineID');
					}
					break;
				case 'creditmemoret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('CreditMemoRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'CreditMemo';
					break;
				case 'creditmemoret creditmemolinegroupret':
					if (!isset($extra['TxnLineID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('CreditMemoLineGroupRet TxnLineID');
					}
					break;
				case 'customerret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('CustomerRet ListID');
					}
					$extra['EntityType'] = 'Customer';
					break;
				case 'dataextdefret':
					if (!isset($extra['DataExtName']))
					{
						$extra['DataExtName'] = $Node->getChildDataAt('DataExtDefRet DataExtName');
					}
					if (!isset($extra['OwnerID']))
					{
						$extra['OwnerID'] = $Node->getChildDataAt('DataExtDefRet OwnerID');
					}
					break;
				case 'depositret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('DepositRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'Deposit';
					break;
				case 'employeeret':
					if (!isset($extra['ListID']))
					{
						$extra['ListID'] = $Node->getChildDataAt('EmployeeRet ListID');
					}
					$extra['EntityListID'] = $extra['ListID'];
					$extra['EntityType'] = 'Employee';
					break;
				case 'employeeret employeepayrollinfo earnings':
					$extra['EntityType'] = 'Earnings';
					break;
				case 'estimateret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('EstimateRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'Estimate';
					break;
				case 'estimateret estimatelineret':
					if(!isset($extra['Txn_TxnID']))
						$extra['Txn_TxnID'] = $Node->getChildDataAt('EstimateLineRet TxnLineID');
					$extra['TxnType'] = 'EstimateLine';
					break;
				case 'estimateret estimatelinegroupret':
					if (!isset($extra['TxnLineID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('EstimateLineGroupRet TxnLineID');
					}
					$extra['Txn_TxnID'] = $extra['TxnLineID'];
					$extra['TxnType'] = 'EstimateLineGroup';
					break;
				case 'estimateret estimatelinegroupret estimatelineret':
					$extra['Txn_TxnID'] = $Node->getChildDataAt('EstimateLineRet TxnLineID');
					$extra['TxnType'] = 'EstimateLineGroup_EstimateLine';
					break;
				case 'inventoryadjustmentret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('InventoryAdjustmentRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'InventoryAdjustment';
					break;
				case 'invoiceret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('InvoiceRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'Inventory';
					break;
				case 'invoiceret invoicelineret':
					$extra['Txn_TxnID'] = $Node->getChildDataAt('InvoiceLineRet TxnLineID');
					$extra['TxnType'] = 'InvoiceLine';
					break;
				case 'invoiceret invoicelinegroupret':
					if (!isset($extra['TxnLineID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('InvoiceLineGroupRet TxnLineID');
					}
					$extra['Txn_TxnID'] = $extra['TxnLineID'];
					$extra['TxnType'] = 'InvoiceLineGroup';
					break;
				case 'invoiceret invoicelinegroupret invoicelineret':
					$extra['Txn_TxnID'] = $Node->getChildDataAt('InvoiceLineRet TxnLineID');
					$extra['TxnType'] = 'InvoiceLineGroup_InvoiceLine';
					break;
				case 'itemgroupret':
					if (!isset($extra['ListID']))
					{
						$extra['ListID'] = $Node->getChildDataAt('ItemGroupRet ListID');
					}
					$extra['EntityListID'] = $extra['ListID'];
					$extra['EntityType'] = 'ItemGroup';
					break;
				case 'iteminventoryret':
					$extra['EntityListID'] = $Node->getChildDataAt('ItemInventoryRet ListID');
					$extra['EntityType'] = 'ItemInventory';
					break;
				case 'iteminventoryassemblyret':
					if (!isset($extra['ListID']))
					{
						$extra['ListID'] = $Node->getChildDataAt('ItemInventoryAssemblyRet ListID');
					}
					$extra['EntityListID'] = $extra['ListID'];
					$extra['EntityType'] = 'ItemInventoryAssembly';
					break;
				case 'itemnoninventoryret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('ItemNonInventoryRet ListID');
					}
					$extra['EntityType'] = 'ItemNonInventory';
					break;
				case 'itemdiscountret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('ItemDiscountRet ListID');
					}
					$extra['EntityType'] = 'ItemDiscount';
					break;
				case 'itemfixedassetret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('ItemFixedAssetRet ListID');
					}
					$extra['EntityType'] = 'ItemFixedAsset';
					break;
				case 'itemotherchargeret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('ItemOtherChargeRet ListID');
					}
					$extra['EntityType'] = 'ItemOtherCharge';
					break;
				case 'itempaymentret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('ItemPaymentRet ListID');
					}
					$extra['EntityType'] = 'ItemPayment';
					break;
				case 'itemreceiptret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('ItemReceiptRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'ItemReceipt';
					break;
				case 'itemreceiptret itemreceiptlinegroupret':
					if (!isset($extra['TxnLineID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('ItemReceiptLineGroupRet TxnLineID');
					}
					break;
				case 'itemsalestaxret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('ItemSalesTaxRet ListID');
					}
					$extra['EntityType'] = 'ItemSalesTax';
					break;
				case 'itemsalestaxgroupret':
					if (!isset($extra['ListID']))
					{
						$extra['ListID'] = $Node->getChildDataAt('ItemSalesTaxGroupRet ListID');
					}
					$extra['EntityListID'] = $extra['ListID'];
					$extra['EntityType'] = 'ItemSalesTaxGroup';
					break;
				case 'itemserviceret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('ItemServiceRet ListID');
					}
					$extra['EntityType'] = 'ItemService';
					break;
				case 'itemsubtotalret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('ItemSubtotalRet ListID');
					}
					$extra['EntityType'] = 'ItemSubtotal';
					break;
				case 'journalentryret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('JournalEntryRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'JournalEntry';
					break;
				case 'pricelevelret':
					if (!isset($extra['ListID']))
					{
						$extra['ListID'] = $Node->getChildDataAt('PriceLevelRet ListID');
					}
					break;
				case 'purchaseorderret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('PurchaseOrderRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'PurchaseOrder';
					break;
				case 'purchaseorderret purchaseorderlineret':
					$extra['Txn_TxnID'] = $Node->getChildDataAt('PurchaseOrderLineRet TxnLineID');
					$extra['TxnType'] = 'PurchaseOrderLine';
					break;
				case 'purchaseorderret purchaseorderlinegroupret':
					if (!isset($extra['TxnLineID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('PurchaseOrderLineGroupRet TxnLineID');
					}
					$extra['Txn_TxnID'] = $extra['TxnLineID'];
					$extra['TxnType'] = 'PurchaseOrderLineGroup';
					break;
				case 'purchaseorderret purchaseorderlinegroupret purchaseorderlineret':
					$extra['Txn_TxnID'] = $Node->getChildDataAt('PurchaseOrderLineRet TxnLineID');
					$extra['TxnType'] = 'PurchaseOrderLineGroup_PurchaseOrderLine';
					break;
				case 'receivepaymentret':

					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('ReceivePaymentRet TxnID');
					}

					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'ReceivePayment';

					break;
				case 'salesorderret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('SalesOrderRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'SalesOrder';
					break;
				case 'salesorderret salesorderlineret':
					$extra['Txn_TxnID'] = $Node->getChildDataAt('SalesOrderLineRet TxnLineID');
					$extra['TxnType'] = 'SalesOrderLine';
					break;
				case 'salesorderret salesorderlinegroupret':
					$extra['TxnLineID'] = $Node->getChildDataAt('SalesOrderLineGroupRet TxnLineID');
					$extra['Txn_TxnID'] = $extra['TxnLineID'];
					$extra['TxnType'] = 'SalesOrderLineGroup';
					break;
				case 'salesorderret salesorderlinegroupret salesorderlineret':
					$extra['Txn_TxnID'] = $Node->getChildDataAt('SalesOrderLineRet TxnLineID');
					$extra['TxnType'] = 'SalesOrderLineGroup_SalesOrderLine';
					break;
				case 'salesreceiptret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('SalesReceiptRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'SalesReceipt';
					break;
				case 'salesreceiptret salesreceiptlineret':
					$extra['Txn_TxnID'] = $Node->getChildDataAt('SalesReceiptLineRet TxnLineID');
					$extra['TxnType'] = 'SalesReceiptLine';
					break;
				case 'salesreceiptret salesreceiptlinegroupret':
					$extra['TxnLineID'] = $Node->getChildDataAt('SalesReceiptLineGroupRet TxnLineID');
					$extra['Txn_TxnID'] = $extra['TxnLineID'];
					$extra['TxnType'] = 'SalesReceiptLineGroup';
					break;
				case 'salesreceiptret salesreceiptlinegroupret salesreceiptlineret':
					$extra['Txn_TxnID'] = $Node->getChildDataAt('SalesReceiptLineRet TxnLineID');
					$extra['TxnType'] = 'SalesReceiptLineGroup_SalesReceiptLine';
					break;
				case 'unitofmeasuresetret':
					if(!isset($extra['ListID']))
					{
						$extra['ListID'] = $Node->getChildDataAt('UnitOfMeasureSetRet ListID');
					}
					break;
				case 'vendorret':
					if (!isset($extra['EntityListID']))
					{
						$extra['EntityListID'] = $Node->getChildDataAt('VendorRet ListID');
					}
					$extra['EntityType'] = 'Vendor';
					break;
				case 'vendorcreditret':
					if (!isset($extra['TxnID']))
					{
						$extra['TxnID'] = $Node->getChildDataAt('VendorCreditRet TxnID');
					}
					$extra['Txn_TxnID'] = $extra['TxnID'];
					$extra['TxnType'] = 'VendorCredit';
					break;
				case 'vendorcreditret itemgrouplineret':
					if (!isset($extra['TxnLineID']))
					{
						$extra['TxnLineID'] = $Node->getChildDataAt('ItemGroupLineRet TxnLineID');
					}
					break;
				case 'workerscompcoderet':
					if (!isset($extra['ListID']))
					{
						$extra['ListID'] = $Node->getChildDataAt('WorkersCompCodeRet ListID');
					}
					break;
			}

			foreach ($Node->children() as $Child)
			{
				$merge = false;
				$others = [];
				switch ($Child->name())
				{
					case 'AppliedToTxnRet':
					case 'BillingRatePerItemRet':
					case 'CreditMemoLineRet':
					case 'CreditMemoLineGroupRet':
					case 'DataExtRet':
					case 'AssignToObject':
					case 'DefaultUnit':
					case 'DepositLineRet':
					case 'EstimateLineRet':
					case 'EstimateLineGroupRet':
					case 'ExpenseLineRet':
					case 'InvoiceAdjustmentLineRet':
					case 'InvoiceLineRet':
					case 'InvoiceLineGroupRet':
					case 'InventoryAdjustmentLineRet':
					case 'ItemGroupRet':
					case 'ItemGroupLine':
					case 'ItemGroupLineRet':
					case 'ItemInventoryAssemblyLineRet':
					case 'ItemInventoryAssemblyLine':
					case 'ItemLineRet':
					case 'ItemSalesTaxGroupRet':
					//case 'ItemSalesTaxRef':
					case 'LinkedTxn':
					case 'PriceLevelPerItemRet':
					case 'PurchaseOrderLineRet':
					case 'PurchaseOrderLineGroupRet':
					case 'RelatedUnit':
					case 'SalesOrderLineRet':
					case 'SalesOrderLineGroupRet':
					case 'SalesReceiptLineRet':
					case 'SalesReceiptLineGroupRet':
					case 'Service':
					case 'SubscribedServices':
					case 'TaxLineInfoRet':
					case 'EmployeePayrollInfo':
					case 'Earnings':

						// * * * WARNING WARNING WARNING * * *
						// The next line of code causes problems with some responses
						//	because it converts our associative array to turn into a
						//	numeric array. This causes objects to get cut into multiple
						//	pieces:
						//
						// array(
						// 	'account' => array( 'Name' => 'test', 'ListID' => 1234' ),
						//	0 => array( account_taxlineinforet data here  ),
						//	1 => array( 'CashFlowClassification' => 'abc' ),		// this is the other half of the Account data from the 'account' associative array key
						// )
						//
						// Do not make a change to this code without double checking syncs
						//	and talking this over with Keith first please! Thanks!
						//
						// Previously we were using this line, but it was causing problems:
						//$others = array_values($objects);

						// This line of code seems to work OK
						$others = $objects;



						$objects = [];

						$merge = true;
						break;
				}

				static::_transformToSQLObjects($curpath . ' ' . $Node->name(), $Child, $objects, null, $extra);

				// * * * WARNING * * *
				//	Please see notes above about object chunking problems which might be related to the code below
				if ($merge)
				{
					$objects = array_values($objects);
					$objects = array_merge($others, $objects);
				}
				//

				//echo '<br />&nbsp;&nbsp;&nbsp;';
				//print_r($objects);
				//echo '<br /><br />';
			}
		}
		else
		{
			$map = [];
			$others = [];
			Schema::mapToSchema(trim($curpath . ' ' . $Node->name()), Schema::MAP_TO_SQL, $map, $others);

			//Okay so if the first element is a child element that is custom mapped, it'll end up creating the object with an incorrect path.
			//print('map for: {' . $curpath . ' ' . $Node->name() . "} [" . $map[0] . "]\n");
			//print_r($map);

			if ($map[0] and !isset($objects[$map[0]]))
			{
				//print('creating new object: ' . $map[0] . "\n");
				//print_r($objects);

				$tempMap = [];
				$tempOthers = [];
				Schema::mapToSchema(trim($curpath), Schema::MAP_TO_SQL, $tempMap, $tempOthers);

				if ($map[0] == 'dataextdef_assigntoobject')
				{
					$objects[$map[0]] = new SqlObject($map[0], trim($curpath . ' ' . $Node->name()));
				}
				else
				{
					$objects[$map[0]] = new SqlObject($map[0], trim($curpath));
				}

				// Some tables, such 'Invoice_InvoiceLine', won't have data in the SQL schema that
				// 	directly links them back to the 'Invoice' record they're part of. Thus, we need
				//	to add a few schema fields, and then here we set those fields to values from the
				//	parent of these objects so that they get tied to the correct 'Invoice' in the
				//	database table 'Invoice_InvoiceLine'
				$table = $objects[$map[0]]->table();
				switch (strtolower($table))
				{
					case 'account_taxlineinfo':
						$objects[$map[0]]->set('Account_ListID', $extra['ListID']);
						$objects[$map[0]]->set('Account_FullName', $extra['FullName']);
						break;
					case 'billingrate_billingrateperitem':
						$objects[$map[0]]->set('BillingRate_ListID', $extra['ListID']);
						break;
					case 'billpaymentcheck_appliedtotxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('BillPaymentCheck_TxnID', $extra['TxnID']);
						break;
					case 'billpaymentcreditcard_appliedtotxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('BillPaymentCreditCard_TxnID', $extra['TxnID']);
						break;
					case 'bill_linkedtxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('Bill_TxnID', $extra['TxnID']);
						break;
					case 'bill_expenseline':
						$objects[$map[0]]->set('Bill_TxnID', $extra['TxnID']);
						break;
					case 'bill_itemline':
						$objects[$map[0]]->set('Bill_TxnID', $extra['TxnID']);
						break;
					case 'bill_itemgroupline':
						$objects[$map[0]]->set('Bill_TxnID', $extra['TxnID']);
						break;
					case 'bill_itemgroupline_itemline':
						$objects[$map[0]]->set('Bill_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('Bill_ItemGroupLine_TxnLineID', $extra['TxnLineID']);
						break;
					case 'check_linkedtxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('Check_TxnID', $extra['TxnID']);
						break;
					case 'check_expenseline':
						$objects[$map[0]]->set('Check_TxnID', $extra['TxnID']);
						break;
					case 'check_itemline':
						$objects[$map[0]]->set('Check_TxnID', $extra['TxnID']);
						break;
					case 'check_itemgroupline':
						$objects[$map[0]]->set('Check_TxnID', $extra['TxnID']);
						break;
					case 'check_itemgroupline_itemline':
						$objects[$map[0]]->set('Check_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('Check_ItemGroupLine_TxnLineID', $extra['TxnLineID']);
						break;
					case 'company_subscribedservices_service':
						$objects[$map[0]]->set('Company_CompanyName', $extra['CompanyName']);
						break;
					case 'creditcardcharge_expenseline':
						$objects[$map[0]]->set('CreditCardCharge_TxnID', $extra['TxnID']);
						break;
					case 'creditcardcharge_itemline':
						$objects[$map[0]]->set('CreditCardCharge_TxnID', $extra['TxnID']);
						break;
					case 'creditcardcharge_itemgroupline':
						$objects[$map[0]]->set('CreditCardCharge_TxnID', $extra['TxnID']);
						break;
					case 'creditcardcharge_itemgroupline_itemline':
						$objects[$map[0]]->set('CreditCardCharge_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('CreditCardCharge_ItemGroupLine_TxnLineID', $extra['TxnLineID']);
						break;
					case 'creditcardcredit_expenseline':
						$objects[$map[0]]->set('CreditCardCredit_TxnID', $extra['TxnID']);
						break;
					case 'creditcardcredit_itemline':
						$objects[$map[0]]->set('CreditCardCredit_TxnID', $extra['TxnID']);
						break;
					case 'creditcardcredit_itemgroupline':
						$objects[$map[0]]->set('CreditCardCredit_TxnID', $extra['TxnID']);
						break;
					case 'creditcardcredit_itemgroupline_itemline':
						$objects[$map[0]]->set('CreditCardCredit_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('CreditCardCredit_ItemGroupLine_TxnLineID', $extra['TxnLineID']);
						break;
					case 'creditmemo_linkedtxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('CreditMemo_TxnID', $extra['TxnID']);
						break;
					case 'creditmemo_creditmemoline':
						$objects[$map[0]]->set('CreditMemo_TxnID', $extra['TxnID']);
						break;
					case 'creditmemo_creditmemolinegroup':
						$objects[$map[0]]->set('CreditMemo_TxnID', $extra['TxnID']);
						break;
					case 'creditmemo_creditmemolinegroup_creditmemoline':
						$objects[$map[0]]->set('CreditMemo_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('CreditMemo_CreditMemoLineGroup_TxnLineID', $extra['TxnLineID']);
						break;
					case 'dataext':

						if (!empty($extra['EntityType']))
						{
							$objects[$map[0]]->set('EntityType', $extra['EntityType']);
							$objects[$map[0]]->set('Entity_ListID', $extra['EntityListID']);
						}
						else
						{
							$objects[$map[0]]->set('TxnType', $extra['TxnType']);
							$objects[$map[0]]->set('Txn_TxnID', $extra['Txn_TxnID']);
						}

						break;
					case 'dataextdef_assigntoobject':

						if (!empty($extra['DataExtName']))
						{
							$objects[$map[0]]->set('DataExtDef_DataExtName', $extra['DataExtName']);
						}

						if (!empty($extra['OwnerID']) or (isset($extra['OwnerID']) and $extra['OwnerID'] == 0))
						{
							$objects[$map[0]]->set('DataExtDef_OwnerID', $extra['OwnerID']);
						}

						break;
					case 'deposit_depositline':
						$objects[$map[0]]->set('Deposit_TxnID', $extra['TxnID']);
						break;
					case 'employee_earnings':
						$objects[$map[0]]->set('Employee_ListID', $extra['ListID']);
						break;
					case 'estimate_linkedtxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('Estimate_TxnID', $extra['TxnID']);
						break;
					case 'estimate_estimateline':
						$objects[$map[0]]->set('Estimate_TxnID', $extra['TxnID']);
						break;
					case 'estimate_estimatelinegroup':
						$objects[$map[0]]->set('Estimate_TxnID', $extra['TxnID']);
						break;
					case 'estimate_estimatelinegroup_estimateline':
						$objects[$map[0]]->set('Estimate_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('Estimate_EstimateLineGroup_TxnLineID', $extra['TxnLineID']);
						break;
					case 'inventoryadjustment_inventoryadjustmentline':
						$objects[$map[0]]->set('InventoryAdjustment_TxnID', $extra['TxnID']);
						break;
					case 'invoice_linkedtxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('Invoice_TxnID', $extra['TxnID']);
						break;
					case 'invoice_invoiceline':
						$objects[$map[0]]->set('Invoice_TxnID', $extra['TxnID']);
						break;
					case 'invoice_invoicelinegroup':
						$objects[$map[0]]->set('Invoice_TxnID', $extra['TxnID']);
						break;
					case 'invoice_invoicelinegroup_invoiceline':
						$objects[$map[0]]->set('Invoice_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('Invoice_InvoiceLineGroup_TxnLineID', $extra['TxnLineID']);
						break;
					case 'itemgroup_itemgroupline':
						$objects[$map[0]]->set('ItemGroup_ListID', $extra['ListID']);
						break;
					case 'iteminventoryassembly_iteminventoryassemblyline':
						$objects[$map[0]]->set('ItemInventoryAssembly_ListID', $extra['ListID']);
						break;
					case 'itemreceipt_linkedtxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('ItemReceipt_TxnID', $extra['TxnID']);
						break;
					case 'itemreceipt_expenseline':
						$objects[$map[0]]->set('ItemReceipt_TxnID', $extra['TxnID']);
						break;
					case 'itemreceipt_itemline':
						$objects[$map[0]]->set('ItemReceipt_TxnID', $extra['TxnID']);
						break;
					case 'itemreceipt_itemgroupline':
						$objects[$map[0]]->set('ItemReceipt_TxnID', $extra['TxnID']);
						break;
					case 'itemreceipt_itemgroupline_itemline':
						$objects[$map[0]]->set('ItemReceipt_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('ItemReceipt_ItemGroupLine_TxnLineID', $extra['TxnLineID']);
						break;
					case 'itemsalestaxgroup_itemsalestax':
						$objects[$map[0]]->set('ItemSalesTaxGroup_ListID', $extra['ListID']);
						break;
					case 'journalentry_journalcreditline':
						$objects[$map[0]]->set('JournalEntry_TxnID', $extra['TxnID']);
						break;
					case 'journalentry_journaldebitline':
						$objects[$map[0]]->set('JournalEntry_TxnID', $extra['TxnID']);
						break;
					case 'pricelevel_pricelevelperitem':
						$objects[$map[0]]->set('PriceLevel_ListID', $extra['ListID']);
						break;
					case 'purchaseorder_linkedtxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('PurchaseOrder_TxnID', $extra['TxnID']);
						break;
					case 'purchaseorder_purchaseorderline':
						$objects[$map[0]]->set('PurchaseOrder_TxnID', $extra['TxnID']);
						break;
					case 'purchaseorder_purchaseorderlinegroup':
						$objects[$map[0]]->set('PurchaseOrder_TxnID', $extra['TxnID']);
						break;
					case 'purchaseorder_purchaseorderlinegroup_purchaseorderline':
						$objects[$map[0]]->set('PurchaseOrder_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('PurchaseOrder_PurchaseOrderLineGroup_TxnLineID', $extra['TxnLineID']);
						break;
					case 'receivepayment_linkedtxn':
					case 'receivepayment_appliedtotxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('ReceivePayment_TxnID', $extra['TxnID']);
						break;
					case 'salesorder_linkedtxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('SalesOrder_TxnID', $extra['TxnID']);
						break;
					case 'salesorder_salesorderline':
						$objects[$map[0]]->set('SalesOrder_TxnID', $extra['TxnID']);
						break;
					case 'salesorder_salesorderlinegroup':
						$objects[$map[0]]->set('SalesOrder_TxnID', $extra['TxnID']);
						break;
					case 'salesorder_salesorderlinegroup_salesorderline':
						$objects[$map[0]]->set('SalesOrder_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('SalesOrder_SalesOrderLineGroup_TxnLineID', $extra['TxnLineID']);
						break;
					case 'salesreceipt_salesreceiptline':
						$objects[$map[0]]->set('SalesReceipt_TxnID', $extra['TxnID']);
						break;
					case 'salesreceipt_salesreceiptlinegroup':
						$objects[$map[0]]->set('SalesReceipt_TxnID', $extra['TxnID']);
						break;
					case 'salesreceipt_salesreceiptlinegroup_salesreceiptline':
						$objects[$map[0]]->set('SalesReceipt_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('SalesReceipt_SalesReceiptLineGroup_TxnLineID', $extra['TxnLineID']);
						break;
					case 'unitofmeasureset_relatedunit':
						$objects[$map[0]]->set('UnitOfMeasureSet_ListID', $extra['ListID']);
						break;
					case 'unitofmeasureset_defaultunit':
						$objects[$map[0]]->set('UnitOfMeasureSet_ListID', $extra['ListID']);
						break;
					case 'vendorcredit_linkedtxn':
						$objects[$map[0]]->set('FromTxnID', $extra['TxnID']);
						$objects[$map[0]]->set('VendorCredit_TxnID', $extra['TxnID']);
						break;
					case 'vendorcredit_expenseline':
						$objects[$map[0]]->set('VendorCredit_TxnID', $extra['TxnID']);
						break;
					case 'vendorcredit_itemline':
						$objects[$map[0]]->set('VendorCredit_TxnID', $extra['TxnID']);
						break;
					case 'vendorcredit_itemgroupline':
						$objects[$map[0]]->set('VendorCredit_TxnID', $extra['TxnID']);
						break;
					case 'vendorcredit_itemgroupline_itemline':
						$objects[$map[0]]->set('VendorCredit_TxnID', $extra['TxnID']);
						$objects[$map[0]]->set('VendorCredit_ItemGroupLine_TxnLineID', $extra['TxnLineID']);
						break;
					case 'workerscompcode_ratehistory':
						$objects[$map[0]]->set('WorkersCompCode_ListID', $extra['ListID']);
						break;
				}
			}

			if (isset($objects[$map[0]]))
			{
				$tempMap = [];
				$tempOthers = [];
				Schema::mapToSchema(trim($curpath), Schema::MAP_TO_SQL, $tempMap, $tempOthers);

				if ($map[0] == 'dataextdef_assigntoobject')
				{
					if ($objects[$map[0]]->path() != trim($curpath . ' ' . $Node->name()) &&
						strlen(trim($curpath)) < strlen($objects[$map[0]]->path()))
					{
						$objects[$map[0]]->change(trim($curpath . ' ' . $Node->name()));
					}
				}
				else
				{
					if ($objects[$map[0]]->path() != trim($curpath) &&
						strlen(trim($curpath)) < strlen($objects[$map[0]]->path()))
					{
						$objects[$map[0]]->change(trim($curpath));
					}
				}

				$objects[$map[0]]->set($map[1], $Node->data());
			}
		}
	}

	/**
	 *
	 *
	 * @param string $action
	 * @return string
	 */
	protected static function _keySyncCurr($action)
	{
		return $action . '-curr';
	}

	/**
	 * Return the configuration key used for the previous sync operation (quickbooks_config.key field value)
	 *
	 * We store a previous and a current datetime stamp for use with iterators.
	 * We need to know the previous time the action was run so that we know
	 * when we need to look for modifications from. We need to know the current
	 * time the action started to run on each subsequent call for the iterator.
	 *
	 * @param string $action
	 * @return string
	 */
	protected static function _keySyncPrev($action)
	{
		return $action . '-prev';
	}

	/**
	 * Used to build the xml that limits the results to only updated results
	 *
	 * @param string $user		Same deal: pass along the $user parameter
	 * @param string $action	Ditto with $action parameter
	 * @param array $extra		Simply pass in the $extra value that is passed to the function you're calling this from.
	 * @param boolean $filter_wrap
	 * @return string
	 */
	protected static function _buildFilter($user, $action, $extra, $filter_wrap = false)
	{
		$Driver = Singleton::getInstance();
		$xml = '';
		$type = '';

		$key_prev = static::_keySyncPrev($action);
		$key_curr = static::_keySyncCurr($action);

		$module = __CLASS__;

		//$action = null;
		$type = null;
		$opts = null;
		// 					configRead($user, $module, $key, &$type, &$opts)
		$prev_sync_datetime = $Driver->configRead($user, $module, $key_prev, $type, $opts);	// last sync started...

		if (!$prev_sync_datetime)
		{
			// If this query has *never* run before, let's get *all* of the records
			$timestamp = time() - (60 * 60 * 24 * 365 * 25);
			$prev_sync_datetime = date('Y-m-d', $timestamp) . 'T' . date('H:i:s', $timestamp);
			$extra = [];			// If an iterator exists, get rid of it (this should *never* happen... how could it?)

			//			configWrite($user, $module, $key, $value, $type, $opts
			$Driver->configWrite($user, $module, $key_prev, $prev_sync_datetime, null);
		}

		// @TODO MAKE SURE THIS DOESN'T BREAK ANYTHING!
		$prev_sync_datetime = date('Y-m-d', strtotime($prev_sync_datetime) - 600) . 'T' . date('H:i:s', strtotime($prev_sync_datetime) - 600);

		if (!is_array($extra) or
			empty($extra['iteratorID'])) 	// Checks to see if this is the first iteration or not
		{
			// Start of a new iterator!

			// Store when we started to do this iterator (this will become the $prev_sync_datetime after we finish with this iterator)
			$curr_sync_datetime = date('Y-m-d') . 'T' . date('H:i:s');
			$Driver->configWrite($user, $module, $key_curr, $curr_sync_datetime, null);

			if ($filter_wrap)
			{
				if ($action == PackageInfo::Actions['QUERY_DELETEDLISTS'] or $action == PackageInfo::Actions['QUERY_DELETEDTXNS'])
				{
					$xml .= '<DeletedDateRangeFilter>' . "\n";
					$xml .= '	<FromDeletedDate>' . $prev_sync_datetime . '</FromDeletedDate>' . "\n";
					$xml .= '</DeletedDateRangeFilter>' . "\n";
				}
				else
				{
					$xml .= '<ModifiedDateRangeFilter>' . "\n";
					$xml .= '	<FromModifiedDate>' . $prev_sync_datetime . '</FromModifiedDate>' . "\n";
					$xml .= '</ModifiedDateRangeFilter>' . "\n";
				}
			}
			else
			{
				$xml .= '<FromModifiedDate>' . $prev_sync_datetime . '</FromModifiedDate>';
			}
		}
		else 	// ... otherwise use what we found in previous time stamp
		{
			if ($filter_wrap)
			{
				if ($action == PackageInfo::Actions['QUERY_DELETEDLISTS'] or $action == PackageInfo::Actions['QUERY_DELETEDTXNS'])
				{
					$xml .= '<DeletedDateRangeFilter>' . "\n";
					$xml .= '	<FromDeletedDate>' . $prev_sync_datetime . '</FromDeletedDate>' . "\n";
					$xml .= '</DeletedDateRangeFilter>' . "\n";
				}
				else
				{
					$xml .= '<ModifiedDateRangeFilter>' . "\n";
					$xml .= '	<FromModifiedDate>' . $prev_sync_datetime . '</FromModifiedDate>' . "\n";
					$xml .= '</ModifiedDateRangeFilter>' . "\n";
				}
			}
			else
			{
				$xml .= '<FromModifiedDate>' . $prev_sync_datetime . '</FromModifiedDate>';
			}
		}

		return $xml;
	}
}

/*
$requestID = null;
$user = 'quickbooks';
$action = 'CustomerQuery';
$ID = '1234';
$extra = [];
$err = '';
$last_action_time = time();
$last_actionident_time = time();
//$xml = file_get_contents('/Users/kpalmer/Projects/QuickBooks/docs/responses/CustomerQuery.xml');
$xml = '';
$idents = [];

$tmp = Singleton::getInstance('pgsql://postgres:password@localhost/quickbooks', [], [], PackageInfo::LogLevel['DEVELOP']);
print(static::CustomerImportRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config = []));
*/

/*
$xml = '<?xml version="1.0" ?>
<QBXML>
<QBXMLMsgsRs>
<InvoiceQueryRs requestID="SW52b2ljZVF1ZXJ5fGJlZmUwNWJlYjg0NDEyZWU1NjE0MGRhMzMwZjE0ZTdj" statusCode="0" statusSeverity="Info" statusMessage="Status OK" iteratorRemainingCount="0" iteratorID="{6fcbb18b-68cf-4adb-bf40-164ac0bb4ec0}">
	<InvoiceRet>
		<TxnID>4-1232328070</TxnID>
		<TimeCreated>2009-01-18T20:21:10-05:00</TimeCreated>
		<TimeModified>2009-01-18T20:21:10-05:00</TimeModified>
		<EditSequence>1232328070</EditSequence>
		<TxnNumber>2</TxnNumber>
		<CustomerRef>
		<ListID>40000-1232328070</ListID>
		<FullName>Keith Palmer:QuickBooks Integration</FullName>
		</CustomerRef>
		<ARAccountRef>
		<ListID>2A0000-1232328069</ListID>
		<FullName>Accounts Receivable</FullName>
		</ARAccountRef>
		<TemplateRef>
		<ListID>10000-1232327561</ListID>
		<FullName>Intuit Product Invoice</FullName>
		</TemplateRef>
		<TxnDate>2009-01-18</TxnDate>
		<BillAddress>
		<Addr1>Consolibyte Solutions</Addr1>
		<Addr2>Keith R Palmer</Addr2>
		</BillAddress>
		<IsPending>false</IsPending>
		<IsFinanceCharge>false</IsFinanceCharge>
		<TermsRef>
		<ListID>20000-1232327614</ListID>
		<FullName>Net 30</FullName>
		</TermsRef>
		<DueDate>2009-01-18</DueDate>
		<SalesRepRef>
		<ListID>10000-1232327936</ListID>
		<FullName>SBD</FullName>
		</SalesRepRef>
		<ShipDate>2009-01-18</ShipDate>
		<Subtotal>100.00</Subtotal>
		<SalesTaxPercentage>0.00</SalesTaxPercentage>
		<SalesTaxTotal>0.00</SalesTaxTotal>
		<AppliedAmount>-55.00</AppliedAmount>
		<BalanceRemaining>45.00</BalanceRemaining>
		<Memo>Opening balance</Memo>
		<IsPaid>false</IsPaid>
		<IsToBePrinted>false</IsToBePrinted>
		<CustomerSalesTaxCodeRef>
		<ListID>10000-1232327562</ListID>
		<FullName>Tax</FullName>
		</CustomerSalesTaxCodeRef>

		<LinkedTxn>
			<TxnID>10-1232328309</TxnID>
			<TxnType>ReceivePayment</TxnType>
			<TxnDate>2009-01-18</TxnDate>
			<RefNumber>123</RefNumber>
			<LinkType>AMTTYPE</LinkType>
			<Amount>-55.00</Amount>
		</LinkedTxn>

		<InvoiceLineRet>
			<TxnLineID>6-1232328070</TxnLineID>
			<Desc>Opening balance</Desc>
			<Rate>100</Rate>
			<Amount>100.00</Amount>
		</InvoiceLineRet>

	</InvoiceRet>
	<InvoiceRet>
		<TxnID>7-1232328243</TxnID>
		<TimeCreated>2009-01-18T20:24:03-05:00</TimeCreated>
		<TimeModified>2009-01-18T20:24:03-05:00</TimeModified>
		<EditSequence>1232328243</EditSequence>
		<TxnNumber>3</TxnNumber>
		<CustomerRef>
		<ListID>40000-1232328070</ListID>
		<FullName>Keith Palmer:QuickBooks Integration</FullName>
		</CustomerRef>
		<ARAccountRef>
		<ListID>2A0000-1232328069</ListID>
		<FullName>Accounts Receivable</FullName>
		</ARAccountRef>
		<TemplateRef>
		<ListID>10000-1232327561</ListID>
		<FullName>Intuit Product Invoice</FullName>
		</TemplateRef>
		<TxnDate>2009-01-18</TxnDate>
		<RefNumber>1</RefNumber>
		<BillAddress>
		<Addr1>Consolibyte Solutions</Addr1>
		<Addr2>Keith R Palmer</Addr2>
		</BillAddress>
		<IsPending>false</IsPending>
		<IsFinanceCharge>false</IsFinanceCharge>
		<TermsRef>
		<ListID>20000-1232327614</ListID>
		<FullName>Net 30</FullName>
		</TermsRef>
		<DueDate>2009-02-17</DueDate>
		<SalesRepRef>
		<ListID>10000-1232327936</ListID>
		<FullName>SBD</FullName>
		</SalesRepRef>
		<ShipDate>2009-01-18</ShipDate>
		<Subtotal>183.00</Subtotal>
		<ItemSalesTaxRef>
		<ListID>20000-1232327975</ListID>
		<FullName>CT State Tax</FullName>
		</ItemSalesTaxRef>
		<SalesTaxPercentage>6.00</SalesTaxPercentage>
		<SalesTaxTotal>10.98</SalesTaxTotal>
		<AppliedAmount>0.00</AppliedAmount>
		<BalanceRemaining>193.98</BalanceRemaining>
		<Memo>Test Memo</Memo>
		<IsPaid>false</IsPaid>
		<CustomerMsgRef>
		<ListID>30000-1232327614</ListID>
		<FullName>All work is complete!</FullName>
		</CustomerMsgRef>
		<IsToBePrinted>true</IsToBePrinted>
		<CustomerSalesTaxCodeRef>
		<ListID>10000-1232327562</ListID>
		<FullName>Tax</FullName>
		</CustomerSalesTaxCodeRef>

		<InvoiceLineRet>
			<TxnLineID>9-1232328243</TxnLineID>
			<ItemRef>
			<ListID>30000-1232328157</ListID>
			<FullName>Test Service Item 1</FullName>
			</ItemRef>
			<Desc>Test Service Item Description</Desc>
			<Quantity>1</Quantity>
			<Rate>39.00</Rate>
			<Amount>39.00</Amount>
			<SalesTaxCodeRef>
			<ListID>10000-1232327562</ListID>
			<FullName>Tax</FullName>
			</SalesTaxCodeRef>
		</InvoiceLineRet>

		<InvoiceLineRet>
			<TxnLineID>A-1232328243</TxnLineID>
			<ItemRef>
			<ListID>40000-1232328225</ListID>
			<FullName>Test Inventory Part 2</FullName>
			</ItemRef>
			<Desc>Test inventory part.</Desc>
			<Quantity>2</Quantity>
			<Rate>72.00</Rate>
			<Amount>144.00</Amount>
			<SalesTaxCodeRef>
			<ListID>10000-1232327562</ListID>
			<FullName>Tax</FullName>
			</SalesTaxCodeRef>
		</InvoiceLineRet>

	</InvoiceRet>
</InvoiceQueryRs>
</QBXMLMsgsRs>
</QBXML>';
*/

/*
$requestID = 'Q3VzdG9tZXJBZGR8Mw==';
$user = 'quickbooks';
$action = PackageInfo::Actions['ADD_CUSTOMER'];
$ID = 3;
$extra = [];
$err = '';
$last_action_time = time();
$last_actionident_time = time();
//$xml = file_get_contents('/Users/kpalmer/Projects/QuickBooks/docs/responses/CustomerQuery.xml');
$xml = '<?xml version="1.0" ?>
<QBXML>
	<QBXMLMsgsRs>
		<CustomerAddRs requestID="' . $requestID . '" statusCode="3180" statusSeverity="Error" statusMessage="There was an error when saving a Customer list, element &quot;Newcastle Tenants Federation&quot;. QuickBooks error message: This list has been modified by another user." />
	</QBXMLMsgsRs>
</QBXML>';
$idents = [];

$tmp = Singleton::getInstance('mysql://root:root@localhost/quickbooks_sql', [], [], PackageInfo::LogLevel['DEVELOP']);
//print(static::CustomerAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config = []));

$errnum = 3180;
$errmsg = 'There was an error when saving a Customer list, element &quot;Newcastle Tenants Federation&quot;. QuickBooks error message: This record has an error in it.';
$config = [];

print(QuickBooks_Callbacks_SQL_Errors::catchall($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg, $config));
*/

/*
$requestID = 'Q3VzdG9tZXJBZGR8Mw==';
$user = 'quickbooks';
$action = PackageInfo::Actions['MOD_CUSTOMER'];
$ID = 4;
$extra = [];
$err = '';
$last_action_time = time();
$last_actionident_time = time();
$xml = '';
$idents = [];

$tmp = Singleton::getInstance('mysql://root:root@localhost/quickbooks_sql', [], [], PackageInfo::LogLevel['DEVELOP']);
print(static::CustomerModRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config = []));
*/

/*
$requestID = '38';
$user = 'quickbooks';
$action = PackageInfo::Actions['ADD_INVENTORYADJUSTMENT'];
$ID = 1;
$extra = [];
$err = '';
$last_action_time = time();
$last_actionident_time = time();
$xml = '';
$idents = [];

$tmp = Singleton::getInstance('mysql://root:Fuirseet1@localhost/saas_329_bizelo_qbus_557', [], [], PackageInfo::LogLevel['DEVELOP']);
print(static::InventoryAdjustmentAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config = []));
*/


/*
$requestID = 'Q3VzdG9tZXJBZGR8Mw==';
$user = 'quickbooks';
$action = PackageInfo::Actions['ADD_CUSTOMER'];
$ID = 11;
$extra = [];
$err = '';
$last_action_time = time();
$last_actionident_time = time();
$xml = '<?xml version="1.0" ?>
<QBXML>
<QBXMLMsgsRs>
<GeneralSummaryReportQueryRs statusCode="0" statusSeverity="Info" statusMessage="Status OK">
<ReportRet>
<ReportTitle>Inventory Stock Status by Item</ReportTitle>
<ReportSubtitle>February 1 - 7, 2011</ReportSubtitle>
<ReportBasis>Accrual</ReportBasis>
<NumRows>6</NumRows>
<NumColumns>9</NumColumns>
<NumColTitleRows>1</NumColTitleRows>
<ColDesc colID="1" dataType="STRTYPE">
<ColTitle titleRow="1" />
<ColType>Blank</ColType>
</ColDesc>
<ColDesc colID="2" dataType="STRTYPE">
<ColTitle titleRow="1" value="Item Description" />
<ColType>ItemDesc</ColType>
</ColDesc>
<ColDesc colID="3" dataType="STRTYPE">
<ColTitle titleRow="1" value="Pref Vendor" />
<ColType>ItemVendor</ColType>
</ColDesc>
<ColDesc colID="4" dataType="QUANTYPE">
<ColTitle titleRow="1" value="Reorder Pt" />
<ColType>ReorderPoint</ColType>
</ColDesc>
<ColDesc colID="5" dataType="QUANTYPE">
<ColTitle titleRow="1" value="On Hand" />
<ColType>QuantityOnHand</ColType>
</ColDesc>
<ColDesc colID="6" dataType="BOOLTYPE">
<ColTitle titleRow="1" value="Order" />
<ColType>SuggestedReorder</ColType>
</ColDesc>
<ColDesc colID="7" dataType="QUANTYPE">
<ColTitle titleRow="1" value="On PO" />
<ColType>QuantityOnOrder</ColType>
</ColDesc>
<ColDesc colID="8" dataType="DATETYPE">
<ColTitle titleRow="1" value="Next Deliv" />
<ColType>EarliestReceiptDate</ColType>
</ColDesc>
<ColDesc colID="9" dataType="QUANTYPE">
<ColTitle titleRow="1" value="Sales/Week" />
<ColType>SalesPerWeek</ColType>
</ColDesc>
<ReportData>
<TextRow rowNumber="1" value="Inventory" />
<DataRow rowNumber="2">
<RowData rowType="item" value="another inventory" />
<ColData colID="1" value="another inventory" />
<ColData colID="4" value="5" />
<ColData colID="5" value="35" />
<ColData colID="6" value="false" />
<ColData colID="7" value="0" />
<ColData colID="9" value="0" />
</DataRow>
<TextRow rowNumber="3" value="test inventory part" />
<DataRow rowNumber="4">
<RowData rowType="item" value="test inventory part:test sub product" />
<ColData colID="1" value="test sub product" />
<ColData colID="5" value="5" />
<ColData colID="6" value="false" />
<ColData colID="7" value="0" />
<ColData colID="9" value="0" />
</DataRow>
<DataRow rowNumber="5">
<RowData rowType="item" value="test inventory part" />
<ColData colID="1" value="test inventory part - Other" />
<ColData colID="4" value="5" />
<ColData colID="5" value="12" />
<ColData colID="6" value="false" />
<ColData colID="7" value="5" />
<ColData colID="8" value="2010-09-27" />
<ColData colID="9" value="0" />
</DataRow>
<SubtotalRow rowNumber="6">
<RowData rowType="item" value="test inventory part" />
<ColData colID="1" value="Total test inventory part" />
<ColData colID="5" value="17" />
<ColData colID="7" value="5" />
<ColData colID="9" value="0" />
</SubtotalRow>
</ReportData>
</ReportRet>
</GeneralSummaryReportQueryRs>
</QBXMLMsgsRs>
</QBXML>';
$idents = [];

$tmp = Singleton::getInstance('mysql://root:root@localhost/quickbooks_sql', [], [], PackageInfo::LogLevel['DEVELOP']);
print(static::InventoryLevelsResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents, $config = []));
*/
