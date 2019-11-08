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
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage SQL
 */

namespace QuickBooksPhpDevKit\Callbacks\SQL;

use QuickBooksPhpDevKit\{
	Driver\Singleton,
	Driver\Sql,
	PackageInfo,
	SQL\Schema,
	SQL\SqlObject,
	Utilities,
	XML,
};

/**
 *
 */
class Errors
{
	/**
	 * @TODO Change this to return false by default, and only catch the specific errors we're concerned with.
	 *
	 */
	static public function catchall($requestID, string $user, string $action, $ident, ?array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg, ?array $config)
	{
		$Driver = Singleton::getInstance();

		$ignore = [
			PackageInfo::Actions['IMPORT_DELETEDTXN'] => true,
			PackageInfo::Actions['QUERY_DELETEDTXNS'] => true,
			PackageInfo::Actions['IMPORT_DELETEDLISTS'] => true,
			PackageInfo::Actions['QUERY_DELETEDLISTS'] => true,
			PackageInfo::Actions['VOID_TRANSACTION'] => true,
			PackageInfo::Actions['DELETE_TRANSACTION'] => true,
			PackageInfo::Actions['DELETE_LIST'] => true,
		];

		if (isset($ignore[$action]))
		{
			// Ignore errors for these requests
			return true;
		}

		/*
		$Parser = new XML($xml);
		$errnumTemp = 0;
		$errmsgTemp = '';
		$Doc = $Parser->parse($errnumTemp, $errmsgTemp);
		$Root = $Doc->getRoot();
		$emailStr = var_export($Root->children(), true);

		$List = $Root->getChildAt('QBXML QBXMLMsgsRs '.Utilities::actionToResponse($action));
		$Node = current($List->children());
		*/

		$map = [];
		$others = [];
		Schema::mapToSchema(trim(Utilities::actionToXMLElement($action)), Schema::MAP_TO_SQL, $map, $others);

		$table = null;
		$object = new SqlObject($map[0], trim(Utilities::actionToXMLElement($action)));
		if (!is_null($map[0]))
		{
			$table = $object->table();
		}

		$existing = null;

		if ($table && is_numeric($ident))
		{
			$multipart = [
				Sql::Field['ID'] => $ident,
			];

			$existing = $Driver->get(Sql::$TablePrefix['SQL_MIRROR'] . $table, $multipart);
		}

		switch ($errnum)
		{
			case 1:		// These errors occur when we search for something and it doesn't exist
			case 500:	// 	i.e. we query for invoices modified since xyz, but there are none that have been modified since then
				// This isn't really an error, just ignore it
				if ($action == PackageInfo::Actions['DERIVE_CUSTOMER'])
				{
					// Tried to derive, doesn't exist, add it
					$Driver->queueEnqueue(
						$user,
						PackageInfo::Actions['ADD_CUSTOMER'],
						$ident,
						true,
						Utilities::priorityForAction(PackageInfo::Actions['ADD_CUSTOMER']));
				}
				else if ($action == PackageInfo::Actions['DERIVE_INVOICE'])
				{
					// Tried to derive, doesn't exist, add it
					$Driver->queueEnqueue(
						$user,
						PackageInfo::Actions['ADD_INVOICE'],
						$ident,
						true,
						Utilities::priorityForAction(PackageInfo::Actions['ADD_INVOICE']));
				}
				else if ($action == PackageInfo::Actions['DERIVE_RECEIVEPAYMENT'])
				{
					// Tried to derive, doesn't exist, add it
					$Driver->queueEnqueue(
						$user,
						PackageInfo::Actions['ADD_RECEIVEPAYMENT'],
						$ident,
						true,
						Utilities::priorityForAction(PackageInfo::Actions['ADD_RECEIVEPAYMENT']));
				}
				return true;

			case 1000: // An internal error occurred
				// @todo Hopefully at some point we'll have a better idea of how to handle this error...
				return true;

			//case 3120:			// 3120 errors are handled in the 3210 error handler section
			//	break;
			case 3170:	// This list has been modified by another user.
			case 3175:
			case 3176:
			case 3180:
				// This error can occur in several different situations, so we test per situation
				if (false !== strpos($errmsg, 'list has been modified by another user') ||
					false !== strpos($errmsg, 'internals could not be locked') ||
					false !== strpos($errmsg, 'failed to acquire the lock') ||
					false !== strpos($errmsg, 'list element is in use'))
				{
					// This is *not* an error, we can just send the request again, and it'll go through just fine
					return true;
				}
				break;

			case 3200:
				// Ignore EditSequence errors (the record will be picked up and a conflict reported next time it runs... maybe?)
				if ($action == PackageInfo::Actions['MOD_CUSTOMER'] &&
					$existing)
				{
					// Queue up a derive customer request
					// Tried to derive, doesn't exist, add it
					$Driver->queueEnqueue(
						$user,
						PackageInfo::Actions['DERIVE_CUSTOMER'],
						$ident,
						true,
						9999,
						['ListID' => $existing['ListID']]);
				}
				else if ($action == PackageInfo::Actions['MOD_INVOICE'] &&
					$existing)
				{
					// Queue up a derive customer request
					// Tried to derive, doesn't exist, add it
					$Driver->queueEnqueue(
						$user,
						PackageInfo::Actions['DERIVE_INVOICE'],
						$ident,
						true,
						9999,
						['TxnID' => $existing['TxnID']]);
				}
				return true;

			case 3120:
			case 3210:
				//print_r($existing);
				//print('TXNID: [' . $existing['TxnID'] . ']');

				// 3210: The &quot;AppliedToTxnAdd payment amount&quot; field has an invalid value &quot;129.43&quot;. QuickBooks error message: You cannot pay more than the amount due.
				if ($action == PackageInfo::Actions['ADD_RECEIVEPAYMENT'] &&
					(false !== strpos($errmsg, 'pay more than the amount due') || false !== strpos($errmsg, 'cannot be found')) &&
					$existing)
				{
					// If this happens, we're going to try to re-submit the payment, *without* the AppliedToTxn element
					$db_errnum = null;
					$db_errmsg = null;

					$Driver->query("
						UPDATE
							" . Sql::$TablePrefix['SQL_MIRROR'] . "receivepayment_appliedtotxn
						SET
							qbsql_to_skip = 1
						WHERE
							ReceivePayment_TxnID = '%s' ",
						$db_errnum,
						$db_errmsg,
						null,
						null,
						[ $existing['TxnID'] ]);

					return true;
				}
				break;

			case 3250:			// This feature is not enabled or not available in this version of QuickBooks.
				// Do nothing (this can be safely ignored)
				return true;

			case 3260:			// Insufficient permission level to perform this action.
			case 3261:			// The integrated application has no permission to ac...
				// There's nothing we can do about this, if they don't grant the user permission, just skip it
				return true;

			case 3100:			// Name of List Element is already in use.
				break;

			case '0x8004040D':	// The ticket parameter is invalid (how does this happen!?!)
				return true;
		}

		// This is our catch-all which marks the item as errored out
		if (strstr($xml, 'statusSeverity="Info"') === false) // If it's NOT just an Info message.
		{
			$multipart = [Sql::Field['ID'] => $ident];
			$object->set(Sql::Field['ERROR_NUMBER'], $errnum);
			$object->set(Sql::Field['ERROR_MESSAGE'], $errmsg);

			// Do not set the resync field, we want resync and modified timestamps to be different
			$update_resync_field = false;
			$update_discov_field = false;
			$update_derive_field = false;

			if ($table &&
				is_numeric($ident))		// This catches cases where errors occur on IMPORT requests with ap9y8ag random idents
			{
				// Set the error message
				$Driver->update(Sql::$TablePrefix['SQL_MIRROR'] . $table, $object, [$multipart],
					$update_resync_field,
					$update_discov_field,
					$update_derive_field);
			}
		}

		// Please don't change this, it stops us from knowing what's actually
		//	going wrong. If an error occurs, we should either catch it if it's
		//	recoverable, or treated as a fatal error so we know about it and
		//	can address it later.
		//return false;

		// I'm changing it because otherwise the sync never completes if a
		//	single error occurs... we need a way to skip errored-out records
		return true;
	}
}

/*
$requestID = 'asdf';
$user = 'quickbooks';
$action = PackageInfo::Actions['ADD_RECEIVEPAYMENT'];
$ident = 1;
$extra = [];;
$err = null;

$xml = '<?xml version="1.0" ?>
<QBXML>
<QBXMLMsgsRs>
<ReceivePaymentAddRs
requestID="U2hpcE1ldGhvZEltcG9ydHxmMmMyNzk1OGQ5Y2UwMTZiYzViN2RmYTZlMDJlODM5NA=="
statusCode="3210" statusSeverity="Info" statusMessage="The &quot;AppliedToTxnAdd payment amount&quot; field has an invalid value &quot;129.43&quot;. QuickBooks error message: You cannot pay more than the amount due." />
</QBXMLMsgsRs>
</QBXML>';

$errnum = 3210;
$errmsg = 'The &quot;AppliedToTxnAdd payment amount&quot; field has an invalid value &quot;129.43&quot;. QuickBooks error message: You cannot pay more than the amount due.';
$config = [];;

$tmp = Singleton::getInstance('mysql://root:root@localhost/quickbooks_sql', [], [], PackageInfo::LogLevel['DEVELOP']);
self::catchall($requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg, $config);
*/
