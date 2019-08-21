<?php declare(strict_types=1);

/**
 * Various QuickBooks related utility methods
 *
 * Copyright (c) 2010-04-16 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 */

namespace QuickBooksPhpDevKit;

use Ramsey\Uuid\Uuid;
use QuickBooksPhpDevKit\{
	Driver\Factory,
	PackageInfo,
	XML,
};

/**
 * Various QuickBooks related utilities
 *
 * All methods are static
 */
class Utilities
{
	/**
	 * Parse a DSN style connection string
	 *
	 * @param string $dsn		The DSN connection string
	 * @param string $part		If you want just a specific part of the string, choose which part here: scheme, host, port, user, pass, query, fragment
	 * @return mixed 			An array or a string, depending on if you wanted the whole thing parsed or just a piece of it
	 */
	static public function parseDSN($dsn, array $defaults = [], ?string $part = null)
	{
		if (!is_array($dsn) && !is_string($dsn))
		{
			throw new \Exception('DSN must be an array or a dsn string but got (' . (is_object($dsn) ? get_class($dsn) : gettype($dsn)) . ').');
		}

		$map_url_to_param = [
			'scheme' => 'backend',
			'host' => 'host',
			'port' => 'port',
			'user' => 'username',
			'pass' => 'password',
			'path' => 'database',
		];

		if (is_array($dsn))
		{
			if (empty($dsn['backend']) || empty($dsn['database']))
			{
				// A DSN must specify a backend (e.g MySQLi, PgSQL, SQLite3) and a database name
				return null;
			}

			// Merge dsn with the provided defaults
			$dsn = array_merge($defaults, $dsn);
		}
		else
		{
			// Some DSN strings look like this:		filesystem:///path/to/file
			//	parse_url() will not parse this *unless* we provide some sort of hostname (in this case, null)
			$dsn = str_replace(':///', '://null/', $dsn);

			$defaults = array_merge([
				'scheme' => '',
				'host' => '',
				'port' => 0,
				'user' => '',
				'pass' => '',
				'path' => '',
				'query' => '',
				'fragment' => '',
			], $defaults);

			$parse = array_merge($defaults, parse_url($dsn));

			$parse['user'] = urldecode($parse['user']);
			$parse['pass'] = urldecode($parse['pass']);

			$dsn = [
				'backend' => $parse['scheme'],
				'database' => substr($parse['path'], 1),
				'host' => $parse['host'],
				'port' => $parse['port'],
				'username' => $parse['user'],
				'password' => $parse['pass'],
			];
		}

		if (null !== $part)
		{
			if (isset($dsn[$part]))
			{
				return $dsn[$part];
			}
			else if (array_key_exists($part, $map_url_to_param) && isset($dsn[$map_url_to_param[$part]]))
			{
				// Map parse_url part to the dsn key (e.g. user -> username)
				return $dsn[$map_url_to_param[$part]];
			}

			// $part does not exist
			return null;
		}

		return $dsn;
	}

	/**
	 * Mask certain sensitive data from occuring in output/logs
	 */
	static public function mask(string $message): string
	{
		$masks = [
			'<SessionTicket>',
			'<ConnectionTicket>',
			'<CreditCardNumber>',
			'<CardSecurityCode>',
			'<AppID>',
			'<strPassword>',
		];

		foreach ($masks as $key)
		{
			if ($key{0} == '<')
			{
				// It's an XML tag
				$contents = XML::extractTagContents(trim($key, '<> '), $message);
				if (!is_null($contents))
				{
					$masked = str_repeat('x', min(strlen($contents), 12)) . substr($contents, 12);

					$message = str_replace($key . $contents . '</' . trim($key, '<> ') . '>', $key . $masked . '</' . trim($key, '<> ') . '>', $message);
				}
			}
		}

		return $message;
	}

	/**
	 * Write a message to the log (via the back-end driver)
	 *
	 * @param string $dsn		The DSN connection, connection string, or configuration array for the logger
	 * @param string $msg		The message to log
	 * @param integer $lvl		The message log level
	 * @return boolean			Whether or not the message was logged
	 */
	static public function log($dsn, string $msg, int $lvl = PackageInfo::LogLevel['NORMAL']): bool
	{
		$Driver = self::driverFactory($dsn);

		// Mask important data
		$msg = self::mask($msg);

		return $Driver->log($msg, null, $lvl);
	}

	/**
	 * Extract the requestID attribute from an XML stream
	 *
	 * @param string $xml	The XML stream to look for a requestID attribute in
	 * @return mixed		The request ID
	 */
	static public function extractRequestID(string $xml): ?int
	{
		$look = [];

		if (false !== ($start = strpos($xml, ' requestID="')) &&
			false !== ($end = strpos($xml, '"', $start + 12)))
		{
			$id = filter_var(substr($xml, $start + 12, $end - $start - 12), FILTER_VALIDATE_INT);

			return false !== $id ? $id : null;
		}

		return null;
	}

	/**
	 * Create an instance of a driver class from a DSN connection string *or* a connection resource
	 *
	 * You can actually pass in *either* a DSN-style connection string OR an already connected database resource
	 * 	- mysqli://user:pass@localhost:port/database
	 * 	- $var (Resource ID #XYZ, valid MySQL connection resource)
	 *
	 * @param mixed $dsn_or_conn	A DSN-style connection string or a PHP resource
	 * @param array $config			An array of configuration options for the driver
	 * @param array $hooks			An array mapping hooks to user-defined hook functions to call
	 * @param integer $log_level
	 */
	static public function driverFactory($dsn_or_conn, array $config = [], array $hooks = [], int $log_level = null): Driver
	{
		return Factory::create($dsn_or_conn, $config, $hooks, $log_level);
	}

	/**
	 *
	 *
	 * @param string $module
	 * @param string $key
	 * @param mixed $value
	 * @param string $type
	 * @param array $opts
	 * @return boolean
	 */
	static public function configWrite($dsn, string $user, string $module, string $key, $value, ?string $type = null, ?array $opts = null): bool
	{
		if ($Driver = self::driverFactory($dsn))
		{
			return $Driver->configWrite($user, $module, $key, $value, $type, $opts);
		}

		return false;
	}

	/**
	 *
	 *
	 * @param string $module
	 * @param string $key
	 * @param string $type
	 * @param array $opts
	 * @return mixed
	 */
	static public function configRead($dsn, string $user, string $module, string $key, ?string &$type, ?array &$opts)
	{
		if ($Driver = self::driverFactory($dsn))
		{
			return $Driver->configRead($user, $module, $key, $type, $opts);
		}

		return false;
	}

	/**
	 * Convert a time interval to a number of seconds (i.e.: "1 hour" => 600, "3 hours" => 10800, "2 minutes" => 120, etc.)
	 *
	 * @param mixed $interval
	 * @return integer
	 */
	static public function intervalToSeconds($interval): ?int
	{
		if (false === filter_var($interval, FILTER_VALIDATE_INT))
		{
			// Interval is not an integer, so try to convert
			// We'll just let php try this instead of doing it ouselves
			$interval = strtotime($interval);
			$now = strtotime('now');

			if (false === filter_var($interval, FILTER_VALIDATE_INT))
			{
				// Unable to convert given interval
				return null;
			}
			$interval = intval(round($interval - $now));
		}

		return $interval;
	}

	/**
	 * Check if a given IP address lies within a CIDR range
	 *
	 * @param string $remoteaddr		The remote machine's IP address (example: 192.168.1.4)
	 * @param string $CIDR				A CIDR network address (example: 192.168.0.0/24)
	 */
	static protected function _checkCIDR(string $remoteaddr, string $CIDR): bool
	{
		$remoteaddr_long = ip2long($remoteaddr);

		list ($net, $mask) = explode('/', $CIDR);
		$ip_net = ip2long($net);
		$ip_mask = ~((1 << (32 - $mask)) - 1);

		$remoteaddr_net = $remoteaddr_long & $ip_mask;

		return $remoteaddr_net == $ip_net;
	}

	/**
	 * Check if a given remote address (IP address) is allowed based on allow and deny arrays
	 *
	 * @param string $remoteaddr	The remote IP address to check
	 * @param array $arr_allow		An array of allowed ip addresses and/or CIDR blocks
	 * @param array $arr_deny		An array of denied ip addresses and/or CIDR blocks
	 */
	static public function checkRemoteAddress(string $remoteaddr, array $arr_allow, array $arr_deny): bool
	{
		$allowed = true;

		if (count($arr_allow))
		{
			// only allow these addresses
			$allowed = false;

			foreach ($arr_allow as $allow)
			{
				if (false !== strpos($allow, '/'))
				{
					// CIDR notation

					if (self::_checkCIDR($remoteaddr, $allow))
					{
						$allowed = true;
						break;
					}
				}
				else if (filter_var($allow, FILTER_VALIDATE_IP) !== false)
				{
					// IP address (IPv4 or IPv6)

					if ($remoteaddr == $allow)
					{
						$allowed = true;
						break;
					}
				}
			}

			if (!$allowed)
			{
				return false;
			}
		}

		if (count($arr_deny))
		{
			// do *not* allow these addresses
			foreach ($arr_deny as $deny)
			{
				if (false !== strpos($deny, '/'))
				{
					// CIDR notation

					if (self::_checkCIDR($remoteaddr, $deny))
					{
						return false;
					}
				}
				else if (filter_var($deny, FILTER_VALIDATE_IP) !== false)
				{
					// IP address (IPv4 or IPv6)

					if ($remoteaddr == $deny)
					{
						return false;
					}
				}
			}
		}

		return $allowed;
	}


	/**
	 * Create a user for the QuickBooks Web Connector SOAP server
	 *
	 * @param string $dsn		A DSN-style connection string for the back-end driver
	 * @param string $username	The username for the new user
	 * @param string $password	The password for the new user
	 * @param string $company_file
	 * @param string $wait_before_next_update
	 * @param string $min_run_every_n_seconds
	 */
	static public function createUser($dsn, string $username, string $password, ?string $company_file = null, $wait_before_next_update = null, ?int $min_run_every_n_seconds = null): bool
	{
		$driver = self::driverFactory($dsn);

		return $driver->authCreate($username, $password, $company_file, $wait_before_next_update, $min_run_every_n_seconds);
	}

	/**
	 * Disable a user for the QuickBooks Web Connector SOAP server
	 *
	 * @param string $dsn		A DSN-style connection string
	 * @param string $username	The username for the user to disable
	 */
	static public function disableUser($dsn, string $username): bool
	{
		$driver = self::driverFactory($dsn);

		return $driver->authDisable($username);
	}

	/**
	 * Enable a user for the QuickBooks Web Connector SOAP server
	 *
	 * @param string $dsn		A DSN-style connection string
	 * @param string $username	The username for the user to disable
	 */
	static public function enableUser($dsn, string $username): bool
	{
		$driver = self::driverFactory($dsn);

		return $driver->authEnable($username);
	}

	/**
	 * Generate a unique hash from a bunch of variables
	 *
	 * @param mixed $mixed1
	 * @param mixed $mixed2
	 * @param mixed $mixed3
	 * @param mixed $mixed4
	 * @param mixed $mixed5
	 */
	static public function generateUniqueHash($mixed1, $mixed2 = null, $mixed3 = null, $mixed4 = null, $mixed5 = null): string
	{
		return md5(json_encode($mixed1) . json_encode($mixed2) . json_encode($mixed3) . json_encode($mixed4) . json_encode($mixed5));
	}

	/**
	 * Initialize the backend driver
	 *
	 * Initialization should only be done once, and is used to take care of
	 * things like creating the database schema, etc.
	 *
	 * @param string|connection $dsn	A DSN-style connection string or database connection object.
	 * @param array $driver_options		Database driver options
	 * @return boolean
	 */
	static public function initialize($dsn, array $driver_options = [], array $init_options = []): bool
	{
		$Driver = self::driverFactory($dsn, $driver_options);

		return $Driver->initialize($init_options);
	}

	/**
	 * Tell whether or not a driver has been initialized
	 */
	static public function initialized($dsn, array $driver_options = []): bool
	{
		$Driver = self::driverFactory($dsn, $driver_options);

		return $Driver->initialized();
	}

	/**
	 *
	 */
	static public function date($date = null): string
	{
		$format = 'Y-m-d';

		if (null === $date)
		{
			// Return the current date since no date was provided
			return date($format);
		}

		$date = (string) $date;
		if (ctype_digit($date) && strlen($date) > 6)
		{
			// Assume this is a unix timestamp
			return date($format, (int) $date);
		}

		return date($format, strtotime($date));
	}

	/**
	 *
	 */
	static public function datetime($datetime = null): string
	{
		$format = 'Y-m-d\TH:i:s';

		if (null === $datetime)
		{
			// Return the current datetime since no datetime was provided
			return date($format);
		}

		$datetime = (string) $datetime;
		if (ctype_digit($datetime) && strlen($datetime) > 6)
		{
			return date($format, (int) $datetime);
		}

		return date($format, strtotime($datetime));
	}

	/**
	 * Tell if a pattern matches a string or not (Windows-compatible version of www.php.net/fnmatch)
	 */
	static public function fnmatch(string $pattern, string $str): bool
	{
		if (function_exists('fnmatch'))
		{
			return fnmatch($pattern, $str, FNM_CASEFOLD);
		}

		$arr = [
			'\*' => '.*',
			'\?' => '.'
		];

		return preg_match('#^' . strtr(preg_quote($pattern, '#'), $arr) . '$#i', $str);
	}

	/**
	 * List all of the QuickBooks object types supported by the framework
	 */
	static public function listObjects(?string $filter = null, bool $return_keys = false, bool $order_for_mapping = false): array
	{
		static $cache = [];

		$crunch = $filter . '[' . $return_keys . '[' . $order_for_mapping;

		if (isset($cache[$crunch]))
		{
			return $cache[$crunch];
		}

		$constants = [];
		foreach (PackageInfo::Actions as $constant => $value)
		{
			if (preg_match('/^OBJECT_[A-Z]+$/', $constant))
			{
				//fwrite(STDERR, "\n$constant");
				if (false === $return_keys)
				{
					$constant = $value;
				}

				if ($filter)
				{
					if (self::fnmatch($filter, $constant))
					{
						$constants[] = $constant;
					}
				}
				else
				{
					$constants[] = $constant;
				}
			}
		}

		if ($order_for_mapping)
		{
			// Sort with the very longest values first, to the shortest values last
			usort($constants, function($a, $b){ return strlen($b) <=> strlen($a); });
		}
		else
		{
			sort($constants);
		}

		$cache[$crunch] = $constants;

		return $constants;
	}

	/**
	 * Convert a QuickBooks action to a QuickBooks object type (i.e.: PackageInfo::Actions['ADD_CUSTOMER'] gets converted to PackageInfo::Actions['OBJECT_CUSTOMER'])
	 */
	static public function actionToObject(string $action): ?string
	{
		static $cache = [];
		static $flipped_actions = null;

		if (isset($cache[$action]))
		{
			//print('returning cached [' . $action . ']' . "\n");
			return $cache[$action];
		}

		if (null === $flipped_actions)
		{
			$flipped_actions = array_flip(PackageInfo::Actions);
		}

		// Ret objects are objects 'Ret'urned by QuickBooks and should be mapped to the object type
		if (substr(strtolower($action), -3) === 'ret')
		{
			$action = substr($action, 0, -3);
		}

		if (array_key_exists($action, $flipped_actions))
		{
			$object_key = preg_replace('/^[A-Z]+_/', 'OBJECT_', $flipped_actions[$action]);
			if (array_key_exists($object_key, PackageInfo::Actions))
			{
				$type = PackageInfo::Actions[$object_key];
				$cache[$action] = $type;

				return $type;
			}
			else if (preg_match('/^OBJECT_((?:[A-Z]+_)+[A-Z]+$)/', $object_key, $matches))
			{
				$object_key_nodashes = 'OBJECT_'. str_replace('_', '', $matches[1]);
				if (array_key_exists($object_key_nodashes, PackageInfo::Actions))
				{
					$type = PackageInfo::Actions[$object_key_nodashes];
					$cache[$action] = $type;

					return $type;
				}
			}
		}

		return null;
	}

	/**
	 * Generate a GUID
	 *
	 * Note: This is used for tickets too, so it *must* be a RANDOM GUID!
	 */
	static public function GUID(bool $surround = false): string
	{
		$guid = (Uuid::uuid4())->toString();
		if ($surround)
		{
			$guid = '{' . $guid . '}';
		}

		return $guid;
	}

	/**
	 * Try to guess the queueing priority for this action
	 *
	 * @param string $action		The action you're trying to guess for
	 * @param string $dependency	If the action depends on another action (i.e. a DataExtAdd for a CustomerAdd) you can pass the dependency here
	 * @return integer				A best guess at the proper priority
	 */
	static public function priorityForAction(string $action, ?string $dependency = null): int
	{
		// low priorities up here (*lots* of dependencies)
		static $priorities = [
			PackageInfo::Actions['DELETE_TRANSACTION'],

			PackageInfo::Actions['VOID_TRANSACTION'],

			PackageInfo::Actions['DEL_DATAEXT'],
			PackageInfo::Actions['MOD_DATAEXT'],
			PackageInfo::Actions['ADD_DATAEXT'],

			PackageInfo::Actions['MOD_JOURNALENTRY'],
			PackageInfo::Actions['ADD_JOURNALENTRY'],

			PackageInfo::Actions['MOD_RECEIVEPAYMENT'],
			PackageInfo::Actions['ADD_RECEIVEPAYMENT'],

			PackageInfo::Actions['MOD_BILLPAYMENTCHECK'],
			PackageInfo::Actions['ADD_BILLPAYMENTCHECK'],

			//PackageInfo::Actions['MOD_BILLPAYMENTCREDITCARD'],
			PackageInfo::Actions['ADD_BILLPAYMENTCREDITCARD'],

			PackageInfo::Actions['MOD_BILL'],
			PackageInfo::Actions['ADD_BILL'],

			PackageInfo::Actions['MOD_PURCHASEORDER'],
			PackageInfo::Actions['ADD_PURCHASEORDER'],

			PackageInfo::Actions['MOD_INVOICE'],
			PackageInfo::Actions['ADD_INVOICE'],

			PackageInfo::Actions['MOD_SALESORDER'],
			PackageInfo::Actions['ADD_SALESORDER'],

			PackageInfo::Actions['MOD_ESTIMATE'],
			PackageInfo::Actions['ADD_ESTIMATE'],

			PackageInfo::Actions['ADD_INVENTORYADJUSTMENT'],

			PackageInfo::Actions['ADD_CREDITMEMO'],
			PackageInfo::Actions['MOD_CREDITMEMO'],

			PackageInfo::Actions['ADD_ITEMRECEIPT'],
			PackageInfo::Actions['MOD_ITEMRECEIPT'],

			PackageInfo::Actions['MOD_SALESRECEIPT'],
			PackageInfo::Actions['ADD_SALESRECEIPT'],

			PackageInfo::Actions['ADD_SALESTAXITEM'],
			PackageInfo::Actions['MOD_SALESTAXITEM'],

			PackageInfo::Actions['ADD_DISCOUNTITEM'],
			PackageInfo::Actions['MOD_DISCOUNTITEM'],

			PackageInfo::Actions['ADD_OTHERCHARGEITEM'],
			PackageInfo::Actions['MOD_OTHERCHARGEITEM'],

			PackageInfo::Actions['MOD_NONINVENTORYITEM'],
			PackageInfo::Actions['ADD_NONINVENTORYITEM'],

			PackageInfo::Actions['MOD_INVENTORYITEM'],
			PackageInfo::Actions['ADD_INVENTORYITEM'],

			PackageInfo::Actions['MOD_INVENTORYASSEMBLYITEM'],
			PackageInfo::Actions['ADD_INVENTORYASSEMBLYITEM'],

			PackageInfo::Actions['MOD_SERVICEITEM'],
			PackageInfo::Actions['ADD_SERVICEITEM'],

			PackageInfo::Actions['MOD_PAYMENTITEM'],
			PackageInfo::Actions['ADD_PAYMENTITEM'],

			PackageInfo::Actions['MOD_SALESREP'],
			PackageInfo::Actions['ADD_SALESREP'],

			PackageInfo::Actions['MOD_EMPLOYEE'],
			PackageInfo::Actions['ADD_EMPLOYEE'],

			//PackageInfo::Actions['MOD_SALESTAXCODE'], 		// The SDK doesn't support this
			PackageInfo::Actions['ADD_SALESTAXCODE'],

			PackageInfo::Actions['MOD_VENDOR'],
			PackageInfo::Actions['ADD_VENDOR'],

			PackageInfo::Actions['MOD_JOB'],
			PackageInfo::Actions['ADD_JOB'],

			PackageInfo::Actions['MOD_CUSTOMER'],
			PackageInfo::Actions['ADD_CUSTOMER'],

			PackageInfo::Actions['MOD_ACCOUNT'],
			PackageInfo::Actions['ADD_ACCOUNT'],

			//PackageInfo::Actions['MOD_CLASS'],		(does not exist in qbXML API)
			PackageInfo::Actions['ADD_CLASS'],

			PackageInfo::Actions['ADD_PAYMENTMETHOD'],
			PackageInfo::Actions['ADD_SHIPMETHOD'],

			// Queries
			PackageInfo::Actions['QUERY_PURCHASEORDER'],
			PackageInfo::Actions['QUERY_ITEMRECEIPT'],
			PackageInfo::Actions['QUERY_SALESORDER'],
			PackageInfo::Actions['QUERY_SALESRECEIPT'],
			PackageInfo::Actions['QUERY_INVOICE'],
			PackageInfo::Actions['QUERY_ESTIMATE'],
			PackageInfo::Actions['QUERY_RECEIVEPAYMENT'],
			PackageInfo::Actions['QUERY_CREDITMEMO'],

			PackageInfo::Actions['QUERY_BILLPAYMENTCHECK'],
			PackageInfo::Actions['QUERY_BILLPAYMENTCREDITCARD'],
			PackageInfo::Actions['QUERY_BILLTOPAY'],
			PackageInfo::Actions['QUERY_BILL'],

			PackageInfo::Actions['QUERY_CREDITCARDCHARGE'],
			PackageInfo::Actions['QUERY_CREDITCARDCREDIT'],
			PackageInfo::Actions['QUERY_CHECK'],
			PackageInfo::Actions['QUERY_CHARGE'],

			PackageInfo::Actions['QUERY_DELETEDLISTS'],		// This gets all items deleted in the last 90 days
			PackageInfo::Actions['QUERY_DELETEDTXNS'],		// This gets all transactions deleted in the last 90 days

			PackageInfo::Actions['QUERY_TIMETRACKING'],
			PackageInfo::Actions['QUERY_VENDORCREDIT'],

			PackageInfo::Actions['QUERY_INVENTORYADJUSTMENT'],

			PackageInfo::Actions['QUERY_ITEM'],
			PackageInfo::Actions['QUERY_DISCOUNTITEM'],
			PackageInfo::Actions['QUERY_SALESTAXITEM'],
			PackageInfo::Actions['QUERY_SERVICEITEM'],
			PackageInfo::Actions['QUERY_NONINVENTORYITEM'],
			PackageInfo::Actions['QUERY_INVENTORYITEM'],

			PackageInfo::Actions['QUERY_SALESREP'],

			PackageInfo::Actions['QUERY_VEHICLEMILEAGE'],
			PackageInfo::Actions['QUERY_VEHICLE'],

			PackageInfo::Actions['QUERY_CUSTOMER'],
			PackageInfo::Actions['QUERY_VENDOR'],
			PackageInfo::Actions['QUERY_EMPLOYEE'],
			PackageInfo::Actions['QUERY_JOB'],

			PackageInfo::Actions['QUERY_WORKERSCOMPCODE'],

			PackageInfo::Actions['QUERY_UNITOFMEASURESET'],

			PackageInfo::Actions['QUERY_JOURNALENTRY'],
			PackageInfo::Actions['QUERY_DEPOSIT'],

			PackageInfo::Actions['QUERY_SHIPMETHOD'],
			PackageInfo::Actions['QUERY_PAYMENTMETHOD'],
			PackageInfo::Actions['QUERY_PRICELEVEL'],
			PackageInfo::Actions['QUERY_DATEDRIVENTERMS'],
			PackageInfo::Actions['QUERY_BILLINGRATE'],
			PackageInfo::Actions['QUERY_CUSTOMERTYPE'],
			PackageInfo::Actions['QUERY_CUSTOMERMSG'],
			PackageInfo::Actions['QUERY_TERMS'],
			PackageInfo::Actions['QUERY_SALESTAXCODE'],
			PackageInfo::Actions['QUERY_ACCOUNT'],
			PackageInfo::Actions['QUERY_CLASS'],
			PackageInfo::Actions['QUERY_JOBTYPE'],
			PackageInfo::Actions['QUERY_VENDORTYPE'],

			PackageInfo::Actions['QUERY_COMPANY'],


			PackageInfo::Actions['IMPORT_RECEIVEPAYMENT'],


			PackageInfo::Actions['IMPORT_PURCHASEORDER'],
			PackageInfo::Actions['IMPORT_ITEMRECEIPT'],
			PackageInfo::Actions['IMPORT_SALESRECEIPT'],

			// The ESTIMATE, then INVOICE, then SALES ORDER order is important,
			//	because we might have events which depend on the estimate being present
			//	when the invoice is imported, or the sales order being present when
			//	then invoice is imported, etc.
			PackageInfo::Actions['IMPORT_INVOICE'],
			PackageInfo::Actions['IMPORT_SALESORDER'],
			PackageInfo::Actions['IMPORT_ESTIMATE'],

			PackageInfo::Actions['IMPORT_BILLPAYMENTCHECK'],
			PackageInfo::Actions['IMPORT_BILLPAYMENTCREDITCARD'],
			PackageInfo::Actions['IMPORT_BILLTOPAY'],
			PackageInfo::Actions['IMPORT_BILL'],

			PackageInfo::Actions['IMPORT_CREDITCARDCHARGE'],
			PackageInfo::Actions['IMPORT_CREDITCARDCREDIT'],
			PackageInfo::Actions['IMPORT_CHECK'],
			PackageInfo::Actions['IMPORT_CHARGE'],

			PackageInfo::Actions['IMPORT_DELETEDLISTS'],    // This gets all items deleted in the last 90 days.
			PackageInfo::Actions['IMPORT_DELETEDTXN'],      // This gets all transactions deleted in the last 90 days.

			PackageInfo::Actions['IMPORT_TIMETRACKING'],
			PackageInfo::Actions['IMPORT_VENDORCREDIT'],

			PackageInfo::Actions['IMPORT_INVENTORYADJUSTMENT'],

			PackageInfo::Actions['IMPORT_ITEM'],
			PackageInfo::Actions['IMPORT_DISCOUNTITEM'],
			PackageInfo::Actions['IMPORT_SALESTAXITEM'],
			PackageInfo::Actions['IMPORT_SERVICEITEM'],
			PackageInfo::Actions['IMPORT_NONINVENTORYITEM'],
			PackageInfo::Actions['IMPORT_INVENTORYITEM'],
			PackageInfo::Actions['IMPORT_INVENTORYASSEMBLYITEM'],

			PackageInfo::Actions['IMPORT_SALESREP'],

			PackageInfo::Actions['IMPORT_VEHICLEMILEAGE'],
			PackageInfo::Actions['IMPORT_VEHICLE'],

			PackageInfo::Actions['IMPORT_CUSTOMER'],
			PackageInfo::Actions['IMPORT_VENDOR'],
			PackageInfo::Actions['IMPORT_EMPLOYEE'],
			PackageInfo::Actions['IMPORT_JOB'],

			PackageInfo::Actions['IMPORT_WORKERSCOMPCODE'],

			PackageInfo::Actions['IMPORT_UNITOFMEASURESET'],

			PackageInfo::Actions['IMPORT_JOURNALENTRY'],
			PackageInfo::Actions['IMPORT_DEPOSIT'],

			PackageInfo::Actions['IMPORT_SHIPMETHOD'],
			PackageInfo::Actions['IMPORT_PAYMENTMETHOD'],
			PackageInfo::Actions['IMPORT_PRICELEVEL'],
			PackageInfo::Actions['IMPORT_DATEDRIVENTERMS'],
			PackageInfo::Actions['IMPORT_BILLINGRATE'],
			PackageInfo::Actions['IMPORT_CUSTOMERTYPE'],
			PackageInfo::Actions['IMPORT_CUSTOMERMSG'],
			PackageInfo::Actions['IMPORT_TERMS'],
			PackageInfo::Actions['IMPORT_SALESTAXCODE'],
			PackageInfo::Actions['IMPORT_ACCOUNT'],
			PackageInfo::Actions['IMPORT_CLASS'],
			PackageInfo::Actions['IMPORT_JOBTYPE'],
			PackageInfo::Actions['IMPORT_VENDORTYPE'],

			PackageInfo::Actions['IMPORT_COMPANY'],
		];
		// high priorities down here (no dependencies OR queries)

		// Now, let's space those priorities out a little bit, it gives us some
		//	wiggle room in case we need to add things inbetween the default
		//	priority values
		static $wiggled = false;
		$wiggle = 6;

		if (!$wiggled)
		{
			$count = is_countable($priorities) ? count($priorities) : 0;
			for ($i = $count - 1; $i >= 0; $i--)
			{
				$priorities[$i * $wiggle] = $priorities[$i];
				unset($priorities[$i]);

				// with a wiggle multiplier of 2...
				// 	priority 25 goes to 50
				// 	priority 24 goes to 48
				// 	priority 23 goes to 46
				// 	etc. etc. etc.
			}

			$wiggled = true;

			//print_r($priorities);
		}

		if ($dependency)
		{
			//
			// This is a list of dependency modifications
			//	For instance, normally, you'd want to send just any  old DataExtAdd
			//	with a really low priority, because whatever record it applies to
			//	must be in QuickBooks before you send the DataExtAdd/Mod request.
			//
			//	However, if we pass in the $dependency of PackageInfo::Actions['ADD_CUSTOMER'],
			//	then we know that this DataExt applies to a CustomerAdd, and can
			//	therefore be sent with a priority *just barely lower than* than a
			//	CustomerAdd request, which will ensure this gets run as soon as
			//	possible, but not sooner than the CustomerAdd.
			//
			//	This is important because in some cases, this data will be
			//	automatically used by QuickBooks. For instance, a custom field that
			//	is placed on an Invoice *must already be populated for the
			//	Customer* before the invoice is created.
			//
			// This is an example of a priority list without dependencies, and it's bad:
			//	CustomerAdd, InvoiceAdd, DataExtAdd
			//	(the custom field for the customer doesn't get populated in the invoice)
			//
			// This is an example of a priority list with dependencies, and it's good:
			// 	CustomerAdd, DataExtAdd, InvoiceAdd
			//
			$dependencies = [
				PackageInfo::Actions['ADD_DATAEXT'] => [
					PackageInfo::Actions['ADD_CUSTOMER'] => self::priorityForAction(PackageInfo::Actions['ADD_CUSTOMER']) - 1,
					PackageInfo::Actions['MOD_CUSTOMER'] => self::priorityForAction(PackageInfo::Actions['MOD_CUSTOMER']) - 1,
				],
				PackageInfo::Actions['MOD_DATAEXT'] => [
					PackageInfo::Actions['ADD_CUSTOMER'] => self::priorityForAction(PackageInfo::Actions['ADD_CUSTOMER']) - 1,
					PackageInfo::Actions['MOD_CUSTOMER'] => self::priorityForAction(PackageInfo::Actions['MOD_CUSTOMER']) - 1,
				],

				// A *Bill VOID* has a slightly higher priority than a PurchaseOrderMod so that we can IsManuallyClosed POs (we'll get an error if we try to close it and a bill is dependent on it)
				PackageInfo::Actions['VOID_TRANSACTION'] => [
					PackageInfo::Actions['MOD_PURCHASEORDER'] => self::priorityForAction(PackageInfo::Actions['MOD_PURCHASEORDER']) + 1,
				],
			];
		}

		// Check for dependency priorities
		if ($dependency && isset($dependencies[$action]) && isset($dependencies[$action][$dependency]))
		{
			// Dependency modified priority
			return $dependencies[$action][$dependency];
		}
		else if ($key = array_search($action, $priorities))
		{
			// Regular priority
			return $key;
		}

		// Default priority
		return 999;
	}

	/**
	 * List all of the QuickBooks actions the framework supports
	 */
	static public function listActions(?string $filter = null, bool $return_keys = false): array
	{
		$startswith = [
			'IMPORT_',
			'QUERY_',
			'ADD_',
			'MOD_',
			'DEL_',
			'VOID_',
		];

		$constants = [];

		foreach (PackageInfo::Actions as $constant => $value)
		{
			foreach ($startswith as $start)
			{
				if (substr($constant, 0, strlen($start)) == $start)
				{
					if (!$return_keys)
					{
						// Return the value instead of the key
						$constant = $value;
					}

					if (!is_null($filter))
					{
						if (self::fnmatch($filter, $constant))
						{
							$constants[] = $constant;
						}
					}
					else
					{
						$constants[] = $constant;
					}
				}
			}
		}

		sort($constants);

		return $constants;
	}

	/**
	 * Get the primary key within QuickBooks for this type of object (or this type of action)
	 *
	 * <code>
	 * 	// This prints "ListID"
	 * 	print(self::keyForObject(PackageInfo::Actions['OBJECT_CUSTOMER']));
	 *
	 * 	// This prints "TxnID"  (this method also works for actions)
	 * 	print(self::keyForObject(PackageInfo::Actions['ADD_INVOICE']));
	 * </code>
	 *
	 * @param string $object		An object or action type
	 */
	static public function keyForObject(string $object): string
	{
		// Make sure it's an object
		$object = self::actionToObject($object);

		switch ($object)
		{
			case PackageInfo::Actions['OBJECT_BILLPAYMENTCREDITCARD']:
			case PackageInfo::Actions['OBJECT_INVENTORYADJUSTMENT']:
			case PackageInfo::Actions['OBJECT_BILLPAYMENTCHECK']:
			case PackageInfo::Actions['OBJECT_CREDITCARDCREDIT']:
			case PackageInfo::Actions['OBJECT_CREDITCARDCHARGE']:
			case PackageInfo::Actions['OBJECT_VEHICLEMILEAGE']:
			case PackageInfo::Actions['OBJECT_RECEIVEPAYMENT']:
			case PackageInfo::Actions['OBJECT_PURCHASEORDER']:
			case PackageInfo::Actions['OBJECT_TIMETRACKING']:
			case PackageInfo::Actions['OBJECT_SALESRECEIPT']:
			case PackageInfo::Actions['OBJECT_VENDORCREDIT']:
			case PackageInfo::Actions['OBJECT_JOURNALENTRY']:
			case PackageInfo::Actions['OBJECT_TRANSACTION']:
			case PackageInfo::Actions['OBJECT_ITEMRECEIPT']:
			case PackageInfo::Actions['OBJECT_CREDITMEMO']:
			case PackageInfo::Actions['OBJECT_SALESORDER']:
			case PackageInfo::Actions['OBJECT_BILLTOPAY']:
			case PackageInfo::Actions['OBJECT_ESTIMATE']:
			case PackageInfo::Actions['OBJECT_DEPOSIT']:
			case PackageInfo::Actions['OBJECT_INVOICE']:
			case PackageInfo::Actions['OBJECT_CHARGE']:
			case PackageInfo::Actions['OBJECT_CHECK']:
			case PackageInfo::Actions['OBJECT_BILL']:
				return 'TxnID';

			case PackageInfo::Actions['OBJECT_COMPANY']:
				return 'CompanyName';

			default:
				return 'ListID';
		}
	}

	/**
	 * Alias of self::keyForObject()
	 */
	static public function keyForAction(string $action): string
	{
		return self::keyForObject($action);
	}

	/**
	 * Converts an action to a request (example: "CustomerAdd" to "CustomerAddRq")
	 */
	static public function actionToRequest(string $action): string
	{

		return (substr(strtolower($action), -2) == 'rq') ? $action : $action . 'Rq';
	}

	/**
	 * Converts an action to a response (example: "CustomerAdd" to "CustomerAddRs")
	 */
	static public function actionToResponse(string $action): string
	{
		return (substr(strtolower($action), -2) == 'rs') ? $action : $action . 'Rs';
	}

	/**
	 * Converts a request to an action (example: "CustomerAddRq" to "CustomerAdd")
	 */
	static public function requestToAction(string $request): string
	{
		return (substr(strtolower($request), -2) == 'rq') ? substr($request, 0, -2) : $request;
	}

	/**
	 * Converts an object to an XML Element (example: "Customer" to "CustomerRet")
	 */
	static public function objectToXMLElement(string $object): string
	{
		return $object . 'Ret';
	}

	/**
	 * Converts an action to an XML Element (example: "CustomerAdd" to "CustomerRet")
	 */
	static public function actionToXMLElement(string $action): string
	{
		return self::actionToObject($action) . 'Ret';
	}

	/**
	 * Converts an object type to the corresponding Query Action (example: "Customer" to "CustomerQuery")
	 */
	static public function objectToQuery(string $type): string
	{
		return self::actionToObject($type) . 'Query';
	}

	/**
	 * Converts an object type to the corresponding Mod Action (example: "Customer" to "CustomerMod")
	 */
	static public function objectToMod(string $type): string
	{
		return self::actionToObject($type) . 'Mod';
	}

	/**
	 * Converts an object type to the corresponding Add Action (example: "Customer" to "CustomerAdd")
	 */
	static public function objectToAdd(string $type): string
	{
		return self::actionToObject($type) . 'Add';
	}


	/**
	 * Converts an action to the corresponding Query Action (example: "Customer" to "CustomerQuery")
	 */
	static public function convertActionToQuery(string $action): string
	{
		return self::objectToQuery(self::actionToObject($action));
	}

	/**
	 * Converts an action to the corresponding Mod Action (example: "Customer" to "CustomerMod")
	 */
	static public function convertActionToMod(string $action): string
	{
		return self::objectToMod(self::actionToObject($action));
	}







	/**
	 * Tell what type of callback this is (a function, an object instance method, a static method, etc.)
	 */
	static public function callbackType(&$callback, ?string &$errmsg): ?string
	{
		$errmsg = null;

		// This first section turns things like this:   ['MyClassName', 'myStaticMethod']    into this:   'MyClassName::myStaticMethod'
		if (is_array($callback))
		{
			if (count($callback) !== 2)
			{
				$errmsg = 'Invalid array callback format (must have exactly 2 elements but has ' . count($callback) .')'; //' : ' . print_r($callback, true);

				return null;
			}
			else if (isset($callback[0]) && is_string($callback[0]) && class_exists($callback[0]) &&
				isset($callback[1]) && is_string($callback[1]))
			{
				// This is a static-method callback
				$callback = $callback[0] . '::' . $callback[1];
			}
			else if (isset($callback[0]) && is_object($callback[0]) &&
					 isset($callback[1]) && is_string($callback[1]))
			{
				// This is an object-method callback.  No changes to $callback required.
			}
			else
			{
				// This is not a valid callable function in array format [object, method]
				$errmsg = 'Invalid array callback format: ';// . print_r($callback, true);

				return null;
			}
		}

		// This section determines the callback type now that static methods callbacks are in class::method format
		if (!$callback)
		{
			return Callbacks::TYPE_NONE;
		}
		else if (is_array($callback))
		{
			return Callbacks::TYPE_OBJECT_METHOD;
		}
		else if (is_string($callback) && false === strpos($callback, '::'))
		{
			return Callbacks::TYPE_FUNCTION;
		}
		else if (is_string($callback) && false !== strpos($callback, '::'))
		{
			return Callbacks::TYPE_STATIC_METHOD;
		}
		else if (is_object($callback) && method_exists($callback, 'hook') &&
			($callback instanceof \Hook || substr(get_class($callback),-4) == 'Hook' || (false != get_parent_class($callback) && substr(get_parent_class($callback),-4) == 'Hook')))
		{
			$callback = [$callback, 'hook'];
			return Callbacks::TYPE_HOOK_INSTANCE;
		}

		$errmsg = 'Could not determine callback type: ' . gettype($callback);

		return null;
	}
}
