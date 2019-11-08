<?php declare(strict_types=1);

/**
 * QuickBooks SQL-database driver base-class
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * All SQL back-end drivers should extend this class. This class provides some
 * database abstraction and scheme generating functions for SQL back-ends.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @author Garrett Griffin <grgisme@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Driver
 */

namespace QuickBooksPhpDevKit\Driver;

use QuickBooksPhpDevKit\{
	Driver,
	PackageInfo,
	Utilities,
	SQL\Schema,
};

/**
 * SQL driver back-end for QuickBooks queues
 */
abstract class Sql extends Driver
{
	public static $PASSWORD_HASH = null;
	public static $PASSWORD_SALT = null;
	// Defaults before switching to password_hash
	public const LEGACY_QUICKBOOKS_SALT = 'andB@++3ry';
	public const LEGACY_QUICKBOOKS_DRIVER_SQL_SALT = '@ndP3pp@';

	public static $TIMEOUT = 1800;

	public static $TablePrefix = [
		// This is the prefix for the base SQL tables
		'BASE' => 'quickbooks_',
		// This is the prefix for the SQL mirror tables
		// This needs to be short for PostgreSql to be under the maximum 63 character identifier name length
		'SQL_MIRROR' => 'qb_',
		// This is the prefix used for any extra SQL tables needed by the integrators
		'INTEGRATOR' => 'qb_',
	];

	public static $Table = [
		'CONFIG' => 'config',
		//'CONNECTION' => 'connection',
		//'IDENT' => 'ident',
		'LOG' => 'log',
		//'NOTIFY' => 'notify',
		//'OAUTHV1' => 'oauthv1',
		'OAUTHV2' => 'oauthv2',
		'QUEUE' => 'queue',
		'RECUR' => 'recur',
		'TICKET' => 'ticket',
		'USER' => 'user',
	];


	public const USER_ENABLED = 'e';
	public const USER_DISABLED = 'd';

	public const DataType = [
		'CHAR' => 'char',
		'VARCHAR' => 'varchar',
		'BOOLEAN' => 'boolean',
		'TEXT' => 'text',
		'INTEGER' => 'integer',
		'DECIMAL' => 'decimal',
		'FLOAT' => 'float',
		'SERIAL' => 'serial',
		'DATE' => 'date',
		'TIME' => 'time',
		'DATETIME' => 'datetime',
		'TIMESTAMP' => 'timestamp',
		'TIMESTAMP_ON_INSERT_OR_UPDATE' => 'timestamp-on-update-or-insert',
		'TIMESTAMP_ON_UPDATE' => 'timestamp-on-update',
		'TIMESTAMP_ON_INSERT' => 'timestamp-on-insert',
	];

	public const Field = [
		'ID' => 'qbsql_id',
		'USERNAME_ID' => 'qbsql_username_id',
		'EXTERNAL_ID' => 'qbsql_external_id',

		// Default SQL field to keep track of when records were first pushed into the database
		'DISCOVER' => 'qbsql_discov_datetime',
		// Default SQL field to keep track of when records were last synced from QuickBooks
		'RESYNC' => 'qbsql_resync_datetime',
		// Default SQL field to keep track of records that have been modified (update-on-modify if SQL driver supports it)
		'MODIFY' => 'qbsql_modify_timestamp',
		// Default SQL field to keep track of the last record hash for this record
		'HASH' => 'qbsql_last_hash',
		'QBXML' => 'qbsql_last_qbxml',
		'AUDIT_AMOUNT' => 'qbsql_audit_amount',
		'AUDIT_MODIFIED' => 'qbsql_audit_modified',

		// Default SQL field to keep track of records that should be synced
		'TO_SYNC' => 'qbsql_to_sync',
		// Default SQL field to indicate a record should be voided
		'TO_VOID' => 'qbsql_to_void',
		// Default SQL field to keep track of records that should be deleted
		'TO_DELETE' => 'qbsql_to_delete',
		// Default SQL field to keep track of records that should be skipped
		'TO_SKIP' => 'qbsql_to_skip',

		'FLAG_SKIPPED' => 'qbsql_flag_skipped',
		'FLAG_DELETED' => 'qbsql_flag_deleted',
		'FLAG_VOIDED' => 'qbsql_flag_voided',

		// Default SQL field to keep track of add/mods that failed
		'ERROR_NUMBER' => 'qbsql_last_errnum',
		// Default SQL field to keep track of why add/mods failed
		'ERROR_MESSAGE' => 'qbsql_last_errmsg',

		// Default SQL field to keep track of when records were queued to be updated
		'ENQUEUE_TIME' => 'qbsql_enqueue_datetime',
		// Default SQL field to keep track of when records were last dequeued
		'DEQUEUE_TIME' => 'qbsql_dequeue_datetime',
	];


	/**
	 * The maximum number of entries we should keep in the log table
	 * @var integer
	 */
	protected $_max_log_history;

	/**
	 * The maximum number of (successfully processed) entries we should keep in the queue table
	 * @var integer
	 */
	protected $_max_queue_history;

	/**
	 * The maximum number of entries we should keep in the ticket table
	 * @var integer
	 */
	protected $_max_ticket_history;

	/**
	 *
	 */
	public function __construct($dsn, array $config)
	{
		$config = $this->__defaults($config);

		$this->_max_log_history = (int) $config['max_log_history'];
		$this->_max_queue_history = (int) $config['max_queue_history'];
		$this->_max_ticket_history = (int) $config['max_ticket_history'];

		$this->_loglevel = $config['log_level'];
	}

	/**
	 * Merge an array of configuration options with the defaults
	 */
	private function __defaults(array $config): array
	{
		$defaults = [
			'max_log_history' => -1, 		// -1 means no limit
			'max_queue_history' => -1,
			'max_ticket_history' => -1,
			'log_level' => PackageInfo::LogLevel['NORMAL'],
		];

		return array_merge($defaults, $config);
	}

	/**
	 * Resolve a ticket string back to a ticket ID number
	 */
	protected function _ticketResolve(?string $ticket): int
	{
		static $cache = [];

		if (null === $ticket)
		{
			return 0;
		}

		$errnum = 0;
		$errmsg = '';

		if (isset($cache[$ticket]))
		{
			return $cache[$ticket];
		}

		$arr = $this->_fetch($this->_query("
			SELECT
				quickbooks_ticket_id
			FROM
				" . $this->_mapTableName(static::$Table['TICKET']) . "
			WHERE
				ticket = '" . $this->_escape($ticket) . "' ", $errnum, $errmsg, 0, 1));

		if ($arr)
		{
			if (true === ctype_digit($arr['quickbooks_ticket_id']))
			{
				$arr['quickbooks_ticket_id'] = intval($arr['quickbooks_ticket_id']);
			}
			$cache[$ticket] = $arr['quickbooks_ticket_id'];

			return $arr['quickbooks_ticket_id'];
		}

		return 0;
	}

	/**
	 * Write a configuration variable to the database
	 *
	 * @param string $user		The QuickBooks user this is stored for
	 * @param string $module	The module name this is stored for (free-form text, you make it up, but make it unique! a good habit is to use the __CLASS__ constant)
	 * @param string $key		A key to fetch and store this value by
	 * @param mixed $value		The value
	 * @param string $type
	 * @param array $opts
	 * @return boolean			Success or failure
	 */
	protected function _configWrite(string $user, string $module, string $key, $value, ?string $type, ?array $opts): bool
	{
		$errnum = 0;
		$errmsg = '';

		$value = $value ?? '';

		if ($arr = $this->_fetch($this->_query("
			SELECT
				quickbooks_config_id
			FROM
				" . $this->_mapTableName(static::$Table['CONFIG']) . "
			WHERE
				qb_username = '" . $this->_escape($user) . "' AND
				module = '" . $this->_escape($module) . "' AND
				cfgkey = '" . $this->_escape($key) . "' ", $errnum, $errmsg, 0, 1)))
		{
			// Config key exists, so update it
			return false !== $this->_query("
				UPDATE
					" . $this->_mapTableName(static::$Table['CONFIG']) . "
				SET
					cfgval = '" . $this->_escape($value) . "',
					mod_datetime = '" . date('Y-m-d H:i:s') . "'
				WHERE
					quickbooks_config_id = " . $arr['quickbooks_config_id'], $errnum, $errmsg);
		}

		// Key does not exist, so insert it
		$type = $type ?? '';
		$opts = $opts ?? [];
		return false !== $this->_query("
			INSERT INTO
				" . $this->_mapTableName(static::$Table['CONFIG']) . "
			(
				qb_username,
				module,
				cfgkey,
				cfgval,
				cfgtype,
				cfgopts,
				write_datetime,
				mod_datetime
			) VALUES (
				'" . $this->_escape($user) . "',
				'" . $this->_escape($module) . "',
				'" . $this->_escape($key) . "',
				'" . $this->_escape($value) . "',
				'" . $this->_escape($type) . "',
				'" . $this->_escape(json_encode($opts)) . "',
				'" . date('Y-m-d H:i:s') . "',
				'" . date('Y-m-d H:i:s') . "'
			) ", $errnum, $errmsg);
	}

	/**
	 * Read configuration information
	 *
	 * @param string $user		The username to store this for
	 * @param string $module	The module to store this for
	 * @param string $key		The key to store this by
	 * @param string $type
	 * @param array $opts
	 * @return mixed			The value read from the SQL database
	 */
	protected function _configRead(string $user, string $module, string $key, ?string &$type, ?array &$opts)
	{
		$errnum = 0;
		$errmsg = '';

		$sql = "
			SELECT
				cfgval,
				cfgtype,
				cfgopts
			FROM
				" . $this->_mapTableName(static::$Table['CONFIG']) . "
			WHERE
				qb_username = '" . $this->_escape($user) . "' AND
				module = '" . $this->_escape($module) . "' AND
				cfgkey = '" . $this->_escape($key) . "' ";

		//print($sql);

		if ($arr = $this->_fetch($this->_query($sql, $errnum, $errmsg, 0, 1)))
		{
			$type = $arr['cfgtype'];
			$opts = $arr['cfgopts'];

			//print_r($arr);

			return $arr['cfgval'];
		}

		$type = null;
		$opts = null;

		return null;
	}

	/**
	 * Convert a ticket into a username
	 *
	 * @param string $ticket
	 * @return string			The username of the user who belongs to this ticket
	 */
	protected function _authResolve(string $ticket): ?string
	{
		static $cache = [];

		if ($ticket === '')
		{
			// Empty ticket cannot resolve to a user
			return null;
		}

		if (isset($cache[$ticket]))
		{
			// Return cached ticket
			return $cache[$ticket];
		}

		// See if the ticket is a valid ticket
		$ticket_id = $this->_ticketResolve($ticket);
		if ($ticket_id === 0)
		{
			// Ticket not found
			return null;
		}

		// Try to resolve the ticket_id to an enabled username
		$errnum = 0;
		$errmsg = '';
		if ($arr = $this->_fetch($this->_query("
			SELECT
				qb_username
			FROM
				" . $this->_mapTableName(static::$Table['TICKET']) . "
			WHERE
				quickbooks_ticket_id = " . $ticket_id, $errnum, $errmsg, 0, 1)))
		{
			// Ticket resolves to user.

			// Confirm the user is enabled
			if (null !== $this->_authExists($arr['qb_username'], ['status' => static::USER_ENABLED]))
			{
				//Cache the result and return the username.
				$cache[$ticket] = $arr['qb_username'];

				return $arr['qb_username'];
			}
		}

		// Ticket does not resolve to a user
		return null;
	}



	/**
	 * Check if a user exists for the SOAP server.
	 *
	 * @return array|null	Returns null if the user does not exist and the USER table record as an array if it does
	 */
	protected function _authExists(string $username, array $restrict = []): ?array
	{
		$restrict = array_merge($restrict, [
			'qb_username' => trim(strtolower($username)),
		]);

		$result = $this->select($this->_mapTableName(static::$Table['USER']), $restrict);
		if (is_array($result) && count($result) == 1) {
			return array_pop($result);
		}

		return null;
	}

	/**
	 * Create a new user for the SOAP server
	 */
	protected function _authCreate(string $username, string $password, string $company_file, int $wait_before_next_update, int $min_run_every_n_seconds): bool
	{
		$errnum = 0;
		$errmsg = '';

		if (!$this->_count($this->_query("SELECT qb_username FROM " . $this->_mapTableName(static::$Table['USER']) . " WHERE qb_username = '" . $this->_escape($username) . "' ", $errnum, $errmsg, 0, 1)))
		{
			return false !== $this->_query("
				INSERT INTO
					" . $this->_mapTableName(static::$Table['USER']) . "
				(
					qb_username,
					qb_password,
					qb_company_file,
					qbwc_wait_before_next_update,
					qbwc_min_run_every_n_seconds,
					status,
					write_datetime,
					touch_datetime
				) VALUES (
					'" . $this->_escape($username) . "',
					'" . $this->_escape($this->_hash($password)) . "',
					'" . $this->_escape($company_file) . "',
					" . $wait_before_next_update . ",
					" . $min_run_every_n_seconds . ",
					'" . static::USER_ENABLED . "',
					'" . date('Y-m-d H:i:s') . "',
					'" . date('Y-m-d H:i:s') . "'
				) ", $errnum, $errmsg);
		}

		return false;
	}

	/**
	 * Enable a username
	 */
	protected function _authEnable(string $username): bool
	{
		$errnum = 0;
		$errmsg = '';

		return false !== $this->_query("
			UPDATE
				" . $this->_mapTableName(static::$Table['USER']) . "
			SET
				status = '" . static::USER_ENABLED . "',
				touch_datetime = '" . date('Y-m-d H:i:s') . "'
			WHERE
				qb_username = '" . $this->_escape($username) . "' ", $errnum, $errmsg);
	}

	/**
	 * Disable a username
	 */
	protected function _authDisable(string $username): bool
	{
		$errnum = 0;
		$errmsg = '';

		return false !== $this->_query("
			UPDATE
				" . $this->_mapTableName(static::$Table['USER']) . "
			SET
				status = '" . static::USER_DISABLED . "',
				touch_datetime = '" . date('Y-m-d H:i:s') . "'
			WHERE
				qb_username = '" . $this->_escape($username) . "' ", $errnum, $errmsg);
	}

	/**
	 * Get the default user
	 */
	protected function _authDefault(): string
	{
		$errnum = 0;
		$errmsg = '';

		if ($arr = $this->_fetch($this->_query("
			SELECT
				qb_username
			FROM
				" . $this->_mapTableName(static::$Table['USER']) . "
			WHERE
				status = '" . static::USER_ENABLED . "'
			ORDER BY
				 touch_datetime DESC ", $errnum, $errmsg, 0, 1)))
		{
			return $arr['qb_username'];
		}

		return '';
	}

	/**
	 * Get the last date/time a particular user logged in
	 */
	protected function _authLast(string $username): ?array
	{
		$errnum = 0;
		$errmsg = '';

		if ($arr = $this->_fetch($this->_query("
			SELECT
				write_datetime,
				touch_datetime
			FROM
				" . $this->_mapTableName(static::$Table['TICKET']) . "
			WHERE
				qb_username = '" . $this->_escape($username) . "'
			ORDER BY
				quickbooks_ticket_id DESC ", $errnum, $errmsg, 0, 1)))
		{
			return [
				$arr['write_datetime'],
				$arr['touch_datetime'],
			];
		}

		return null;
	}

	/**
	 * Log a user in
	 *
	 * @param boolean $override		If this is set to TRUE, a correct password *is not* required
	 * @return string				A session ticket, or null if the login failed
	 */
	protected function _authLogin(string $username, string $password, ?string &$company_file, ?int &$wait_before_next_update, ?int &$min_run_every_n_seconds, bool $override = false): ?string
	{
		$errnum = 0;
		$errmsg = '';

		$valid_user = false;
		$rehash_password = false;

		$username = trim($username);
		$password = trim($password);

		if (!$override)
		{
			// General checks unless override is set

			if (empty($password))
			{
				// Blank passwords *always fail*
				return null;
			}
		}

		// Retrieve the user's information
		$user = $this->authExists($username);
		if ($override)
		{
			// Always valid if $override was set
			$valid_user = true;

			if (null === $user)
			{
				// Create the non-existing user
				$this->authCreate($username, $password);
				// Fetch the user so the other fields are available
				$user = $this->authExists($username);
			}
		}
		else
		{
			$correct_password = $user['qb_password'] ?? '';
			$user_status = $user['status'] ?? false;

			if ($user_status != static::USER_ENABLED)
			{
				// User is not enabled
				return null;
			}

			if (strlen($correct_password) == 0)
			{
				// User has a blank password stored in the database, so fail
				return null;
			}

			// Attempt to verify the password using password_hash
			$hash_info = password_get_info($correct_password);
			if (isset($hash_info['algo']) && 0 !== $hash_info['algo'])
			{
				// This is a password hash
				if (password_verify($password, $correct_password)) {
					$valid_user = true;
					$rehash_password = password_needs_rehash($correct_password, PASSWORD_DEFAULT);
				}
			}
			else if (PackageInfo::$PASSWORD_ALLOW_LEGACY === true)
			{
				// This is not a password hash, so do the old password checking and upgrade the password if possible

				if (strlen(trim($password)) == 32 || strlen(trim($password)) == 40)
				{
					// Possible *hack* attempt (they're sending us a random hash hoping it will match one of the hashed passwords)
					return null;
				}

				$possible_passwords = [
					$password, // plain-text password
					md5($password), // md5 password without salt
					sha1($password), // sha1 password without salt

					// Try old defaults
					md5($password . static::LEGACY_QUICKBOOKS_SALT),
					md5($password . static::LEGACY_QUICKBOOKS_DRIVER_SQL_SALT),
					sha1($password . static::LEGACY_QUICKBOOKS_SALT),
					sha1($password . static::LEGACY_QUICKBOOKS_DRIVER_SQL_SALT),
				];

				// Add additional possibilities if a custom password hash and/or custom password salt was configured
				$hash_function = static::$PASSWORD_HASH ?? null;
				$hash_custom_algorithm = is_string($hash_function) && !in_array(strtolower($hash_function), ['md5', 'sha1']) && is_callable($hash_function);
				if ($hash_custom_algorithm)
				{
					// Try the custom hash function with no salt and the two legacy default salts
					$possible_passwords[] = $hash_function($password);
					$possible_passwords[] = $hash_function($password . static::LEGACY_QUICKBOOKS_SALT);
					$possible_passwords[] = $hash_function($password . static::LEGACY_QUICKBOOKS_DRIVER_SQL_SALT);
					if (is_string(static::$PASSWORD_SALT))
					{
						// Try the custom hash function with custom password salt
						$possible_passwords[] = $hash_function($password . static::$PASSWORD_SALT);
					}
				}
				if (is_string(static::$PASSWORD_SALT))
				{
					// Try md5 and sha1 hashes with the custom password salt
					$possible_passwords[] = md5($password . static::$PASSWORD_SALT);
					$possible_passwords[] = sha1($password . static::$PASSWORD_SALT);
				}

				if (in_array($password, $possible_passwords))
				{
					// Successful login. One of the above salted password hashes worked.
					// Rehash the password to use PHP's password_hash
					$valid_user = true;
					$rehash_password = true;
				}
			}
		}

		if ($valid_user === true) {
			$ticket = Utilities::GUID(false);
			$this->_query("
				INSERT INTO
					" . $this->_mapTableName(static::$Table['TICKET']) . "
				(
					qb_username,
					ticket,
					ipaddr,
					write_datetime,
					touch_datetime
				) VALUES (
					'" . $this->_escape($username) . "',
					'" . $this->_escape($ticket) . "',
					'" . $this->_escape($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "',
					'" . $this->_escape(date('Y-m-d H:i:s')) . "',
					'" . $this->_escape(date('Y-m-d H:i:s')) . "'
				) ", $errnum, $errmsg);

			// Update the pass-by-ref variables with this user's data
			if (isset($user) && is_array($user))
			{
				// Probably do not have these if it's an authentication override
				$company_file = $user['qb_company_file'];
				$wait_before_next_update = $user['qbwc_wait_before_next_update'];
				$min_run_every_n_seconds = $user['qbwc_min_run_every_n_seconds'];
			}

			// Update the password to a password hash if we can
			$update_password_sql = '';
			if (!$override && (true === PackageInfo::$PASSWORD_UPGRADE) && $rehash_password) {
				// The user authenticated themselves and we have their password, so let's update their password to something secure
				$password_hashed = password_hash($password, PASSWORD_DEFAULT);
				$update_password_sql = ", qb_password = '" . $this->_escape($password_hashed) ."' ";
			}

			$this->_query("
				UPDATE
					" . $this->_mapTableName(static::$Table['USER']) . "
				SET
					touch_datetime = '" . date('Y-m-d H:i:s') . "'
					$update_password_sql
				WHERE
					qb_username = '" . $this->_escape($username) . "' ", $errnum, $errmsg);

			return $ticket;
		}

		return null;
	}

	/**
	 * Check to see if a log in session is valid
	 */
	protected function _authCheck(string $ticket): bool
	{
		$errnum = 0;
		$errmsg = '';

		if ($arr = $this->_fetch($this->_query("
			SELECT
				quickbooks_ticket_id
			FROM
				" . $this->_mapTableName(static::$Table['TICKET']) . "
			WHERE
				ticket = '" . $this->_escape($ticket) . "' AND
				touch_datetime > '" . date('Y-m-d H:i:s', time() - static::$TIMEOUT) . "' ", $errnum, $errmsg, 0, 1)))
		{
			$this->_query("
				UPDATE
					" . $this->_mapTableName(static::$Table['TICKET']) . "
				SET
					touch_datetime = '" . date('Y-m-d H:i:s') . "'
				WHERE
					quickbooks_ticket_id = " . $arr['quickbooks_ticket_id'], $errnum, $errmsg);

			return true;
		}

		return false;
	}

	/**
	 * Log a user out
	 */
	protected function _authLogout(string $ticket): bool
	{
		return true;
	}

	/**
	 * Store the last error which occurred
	 *
	 * @param string $ticket		The session ticket for the session this error occurred within
	 * @param string $errnum		The error number
	 * @param string $errmsg		The error message
	 * @return boolean
	 */
	protected function _errorLog(string $ticket, int $errnum, string $errmsg): bool
	{
		$ticket_id = $this->_ticketResolve($ticket);
		if ($ticket_id)
		{
			$db_errnum = 0;
			$db_errmsg = '';

			return false !== $this->_query("
				UPDATE
					" . $this->_mapTableName(static::$Table['TICKET']) . "
				 SET
					lasterror_num = '" . $errnum . "',
					lasterror_msg = '" . $this->_escape(substr($errmsg, 0, 255)) . "'
				WHERE
					quickbooks_ticket_id = " . (int) $ticket_id, $db_errnum, $db_errmsg);
		}

		return false;
	}

	/**
	 * Retreive the last error message which occurred for a given ticket (session)
	 */
	protected function _errorLast(string $ticket): string
	{
		$errnum = 0;
		$errmsg = '';

		if ($ticket_id = $this->_ticketResolve($ticket))
		{
			if ($arr = $this->_fetch($this->_query("
				SELECT lasterror_num, lasterror_msg
				FROM " . $this->_mapTableName(static::$Table['TICKET']) . "
				WHERE quickbooks_ticket_id = '" . $ticket_id . "' ", $errnum, $errmsg, 0, 1)))
			{
				if ($arr['lasterror_msg'] == PackageInfo::Actions['NOOP'])
				{
					return PackageInfo::Actions['NOOP'];
				}

				return $arr['lasterror_num'] . ': ' . $arr['lasterror_msg'];
			}
		}

		return 'Error fetching last error.';
	}

	/**
	 * Register a recurring event for a particular user
	 */
	protected function _recurEnqueue(string $user, int $run_every, string $action, ?string $ident, bool $replace, int $priority, array $extra, ?string $qbxml): bool
	{
		$errnum = 0;
		$errmsg = '';

		$ident = $ident ?? '';
		$extra = ($extra === []) ? '' : json_encode($extra);
		$qbxml = $qbxml ?? '';

		// By default, it has *never* occurred
		$recur_lasttime = (time() - $run_every - 60);

		if ($replace)
		{
			if ($existing = $this->_fetch($this->_query("
					SELECT
						recur_lasttime
					FROM
						" . $this->_mapTableName(static::$Table['RECUR']) . "
					WHERE
						qb_username = '" . $this->_escape($user) . "' AND
						qb_action = '" . $this->_escape($action) . "' AND
						ident = '" . $this->_escape($ident) . "' ", $errnum, $errmsg)))
			{
				$this->_query("
					DELETE FROM
						" . $this->_mapTableName(static::$Table['RECUR']) . "
					WHERE
						qb_username = '" . $this->_escape($user) . "' AND
						qb_action = '" . $this->_escape($action) . "' AND
						ident = '" . $this->_escape($ident) . "' ", $errnum, $errmsg);

				$recur_lasttime = $existing['recur_lasttime'];
			}
		}

		return false !== $this->_query("
			INSERT INTO
				" . $this->_mapTableName(static::$Table['RECUR']) . "
			(
				qb_username,
				qb_action,
				ident,
				extra,
				qbxml,
				priority,
				run_every,
				recur_lasttime,
				enqueue_datetime
			) VALUES (
				'" . $this->_escape($user) . "',
				'" . $this->_escape($action) . "',
				'" . $this->_escape($ident) . "',
				'" . $this->_escape($extra) . "',
				'" . $this->_escape($qbxml) . "',
				" . $priority . ",
				" . $run_every . ",
				" . $recur_lasttime . ",
				'" . date('Y-m-d H:i:s') . "'
			) ", $errnum, $errmsg);
	}

	/**
	 * Dequeue a recurring even that is schedule to be run
	 */
	protected function _recurDequeue(string $user, bool $by_priority = false): ?array
	{
		$errnum = 0;
		$errmsg = '';

		$sql = "
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['RECUR']) . "
			WHERE
				qb_username = '" . $this->_escape($user) . "' AND
				recur_lasttime + run_every <= " . time();

		if ($by_priority)
		{
			$sql .= ' ORDER BY priority DESC ';
		}

		$arr = $this->queueArrayRecordHelper($this->_fetch($this->_query($sql . ' ', $errnum, $errmsg, 0, 1)));
		if ($arr)
		{
			// Update it, so it doesn't get fetched again until it's supposed to
			$errnum = 0;
			$errmsg = '';
			$this->_query("UPDATE " . $this->_mapTableName(static::$Table['RECUR']) . " SET recur_lasttime = " . time() . " WHERE quickbooks_recur_id = " . $arr['quickbooks_recur_id'], $errnum, $errmsg);

			return $arr;
		}

		return null;
	}

	/**
	 * Forcibly remove an item from the queue
	 */
	protected function _queueRemove(string $user, string $action, string $ident): bool
	{
		$errnum = 0;
		$errmsg = '';

		return false !== $this->_query("
			UPDATE
				" . $this->_mapTableName(static::$Table['QUEUE']) . "
			SET
				qb_status = '" . PackageInfo::Status['REMOVED'] . "'
			WHERE
				qb_username = '" . $this->_escape($user) . "' AND
				qb_action = '" . $this->_escape($action) . "' AND
				ident = '" . $this->_escape($ident) . "' AND
				qb_status = '" . PackageInfo::Status['QUEUED'] . "' ", $errnum, $errmsg);
	}

	/**
	 * Add an item to the queue
	 */
	protected function _queueEnqueue(string $user, string $action, string $ident, bool $replace, int $priority, array $extra, ?string $qbxml): bool
	{
		$errnum = 0;
		$errmsg = '';

		$extra = ($extra === []) ? '' : json_encode($extra);
		$qbxml = $qbxml ?? '';

		if ($replace)
		{
			$this->_query("
				DELETE FROM
					" . $this->_mapTableName(static::$Table['QUEUE']) . "
				WHERE
					qb_username = '" . $this->_escape($user) . "' AND
					qb_action = '" . $this->_escape($action) . "' AND
					ident = '" . $this->_escape($ident) . "' AND
					qb_status = '" . PackageInfo::Status['QUEUED'] . "' ", $errnum, $errmsg);
		}

		return null !== $this->_query("
			INSERT INTO
				" . $this->_mapTableName(static::$Table['QUEUE']) . "
			(
				qb_username,
				qb_action,
				ident,
				extra,
				qbxml,
				priority,
				qb_status,
				enqueue_datetime
			) VALUES (
				'" . $this->_escape($user) . "',
				'" . $this->_escape($action) . "',
				'" . $this->_escape($ident) . "',
				'" . $this->_escape($extra) . "',
				'" . $this->_escape($qbxml) . "',
				" . $priority . ",
				'" . PackageInfo::Status['QUEUED'] . "',
				'" . date('Y-m-d H:i:s') . "'
			) ", $errnum, $errmsg);
	}

	protected function _queueGet(string $user, int $requestID, ?string $status = PackageInfo::Status['QUEUED']): ?array
	{
		$errnum = 0;
		$errmsg = '';

		$sql = "
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['QUEUE']) . "
			WHERE
				quickbooks_queue_id = " . $requestID . " AND
				qb_username = '" . $this->_escape($user) . "' ";

		if (!is_null($status))
		{
			$sql .= "AND
				qb_status = '" . $this->_escape($status) . "'  ";
		}

		return $this->queueArrayRecordHelper($this->_fetch($this->_query($sql, $errnum, $errmsg, 0, 1)));
	}

	/**
	 * Get the queue item (QUEUE database row) currently being processed by QuickBooks
	 */
	protected function _queueProcessing(string $user): ?array
	{
		$errnum = 0;
		$errmsg = '';

		// Fetch the latest record to be dequeued for this user, and check that it's set with a status of in processing
		$sql = "
			SELECT
				quickbooks_queue_id,
				qb_action,
				ident,
				extra,
				qb_status,
				dequeue_datetime
			FROM
				" . $this->_mapTableName(static::$Table['QUEUE']) . "
			WHERE
				dequeue_datetime IS NOT NULL
				AND qb_username = '" . $this->_escape($user) . "'
			ORDER BY
				dequeue_datetime DESC ";

		$arr = $this->queueArrayRecordHelper($this->_fetch($this->_query($sql, $errnum, $errmsg, 0, 1)));
		if ($arr &&
			$arr['qb_status'] == PackageInfo::Status['PROCESSING'] && 				// Make sure this was the last thing we tried to process...
			time() - strtotime($arr['dequeue_datetime']) < static::$TIMEOUT)			// ... and it occurred during a reasonably recent run
		{
			return $this->_queueGet($user, $arr['quickbooks_queue_id'], PackageInfo::Status['PROCESSING']);
		}

		return null;
	}

	/**
	 * Find the next item in the queue to be proccessed (optionally by highest priority)
	 */
	protected function _queueDequeue(string $user, bool $by_priority = false): ?array
	{
		$errnum = 0;
		$errmsg = '';

		$sql = "
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['QUEUE']) . "
			WHERE
				qb_username = '" . $this->_escape($user) . "' AND
				qb_status = '" . PackageInfo::Status['QUEUED'] . "' ";

		if ($by_priority)
		{
			$sql .= ' ORDER BY priority DESC, ident ASC ';
		}
		else
		{
			$sql .= ' ORDER BY quickbooks_queue_id ASC ';
		}

		return $this->queueArrayRecordHelper($this->_fetch($this->_query($sql, $errnum, $errmsg, 0, 1)));
	}

	/**
	 * Tell how many items are in the queue
	 */
	protected function _queueLeft(string $user, bool $queued = true): int
	{
		$errnum = 0;
		$errmsg = '';

		// SELECT * FROM quickbooks_queue WHERE qb_status = 'q'
		$sql = "
			SELECT
				COUNT(*) AS num_left
			FROM
				" . $this->_mapTableName(static::$Table['QUEUE']) . "
			WHERE
				qb_username = '" . $this->_escape($user) . "' ";

		if ($queued)
		{
			$sql .= " AND qb_status = '" . PackageInfo::Status['QUEUED'] . "' ";
		}

		$arr = $this->_fetch($this->_query($sql, $errnum, $errmsg));

		return (int) $arr['num_left'];
	}

	/**
	 *
	 *
	 *
	 */
	protected function _queueReport(string $user, ?string $date_from, ?string $date_to, int $offset, int $limit): array
	{
		$where = '';
		if ($date_from || $date_to)
		{
			if ($date_from && $date_to)
			{
				$where = "
					AND
						enqueue_datetime >= '" . date('Y-m-d H:i:s', strtotime($date_from)) . "' AND
						enqueue_datetime <= '" . date('Y-m-d H:i:s', strtotime($date_to)) . "' ";
			}
			else if ($date_from)
			{
				$where = "
					AND
						enqueue_datetime >= '" . date('Y-m-d H:i:s', strtotime($date_from)) . "' ";
			}
			else if ($date_to)
			{
				$where = "
					AND
						enqueue_datetime <= '" . date('Y-m-d H:i:s', strtotime($date_to)) . "' ";
			}
		}

		$sql = "
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['QUEUE']) . "
			WHERE
				qb_username = '" . $this->_escape($user) . "'
				" . $where . "
			ORDER BY
				enqueue_datetime DESC ";

		//print($sql);

		$res = $this->_query($sql, $errnum, $errmsg, $offset, $limit);

		$list = [];
		while ($arr = $this->queueArrayRecordHelper($this->_fetch($res)))
		{
			$list[] = $arr;
		}

		return $list;
	}

	/**
	 * Update the status of an item in the queue
	 */
	protected function _queueStatus(string $ticket, int $requestID, string $new_status, ?string $msg = null): bool
	{
		$errnum = 0;
		$errmsg = '';

		$ticket_id = $this->_ticketResolve($ticket);
		if ($ticket_id)
		{
			$user = $this->authResolve($ticket);

			//print('action: ' . $action . ', ident: ' . $ident . ', new status: ' . $new_status . ', ticket_id: ' . $ticket_id . ', user: ' . $user . ', msg: ' . $msg);

			if ($new_status == PackageInfo::Status['PROCESSING'])
			{
				$this->_query("
					UPDATE
						" . $this->_mapTableName(static::$Table['QUEUE']) . "
					SET
						qb_status = '" . $this->_escape($new_status) . "',
						msg = '" . $this->_escape($msg) . "',
						quickbooks_ticket_id = " . $ticket_id . ",
						dequeue_datetime = '" . date('Y-m-d H:i:s') . "'
					WHERE
						quickbooks_queue_id = " . $requestID . " AND
						qb_username = '" . $this->_escape($user) . "' AND
						qb_status = '" . $this->_escape(PackageInfo::Status['QUEUED']) . "' ", $errnum, $errmsg, null, null);

				//print('running processing status query! ' . $user . ', ' . $action . ', ' . $ident . ', new: ' . $new_status);

				// If we're currently processing, then no error is occuring...
				$errnum = null;
				$errmsg = null;
				$this->_query("
					UPDATE
						" . $this->_mapTableName(static::$Table['TICKET']) . "
					SET
						lasterror_num = NULL,
						lasterror_msg = NULL
					WHERE
						quickbooks_ticket_id = " . $ticket_id, $errnum, $errmsg, null, null);
			}
			else if ($new_status == PackageInfo::Status['SUCCESS'])
			{
				// Update the number of queue items processed for this ticket_id
				$this->_query("
					UPDATE
						" . $this->_mapTableName(static::$Table['TICKET']) . "
					SET
						processed = processed + 1,
						lasterror_num = NULL,
						lasterror_msg = NULL
					WHERE
						quickbooks_ticket_id = " . $ticket_id . " ", $errnum, $errmsg);

				// You can only update to a SUCCESS status if you're currently
				//	in a PROCESSING status
				$sql = "
					UPDATE
						" . $this->_mapTableName(static::$Table['QUEUE']) . "
					SET
						qb_status = '" . $this->_escape($new_status) . "',
						msg = '" . $this->_escape($msg) . "'
					WHERE
						quickbooks_ticket_id = " . $ticket_id . " AND
						qb_username = '" . $this->_escape($user) . "' AND
						quickbooks_queue_id = " . $requestID . " AND
						qb_status = '" . PackageInfo::Status['PROCESSING'] . "' ";

				$this->_query($sql, $errnum, $errmsg, null, null);
			}
			else
			{
				// There are some statuses which *can not be updated* because
				//	they're already removed from the queue. These are listed in
				//	the NOT IN section

				$sql = "
					UPDATE
						" . $this->_mapTableName(static::$Table['QUEUE']) . "
					SET
						qb_status = '" . $this->_escape($new_status) . "',
						msg = '" . $this->_escape($msg) . "'
					WHERE
						quickbooks_ticket_id = " . $ticket_id . " AND
						qb_username = '" . $this->_escape($user) . "' AND
						quickbooks_queue_id = " . $requestID . " AND
						qb_status NOT IN (
							'" . PackageInfo::Status['SUCCESS'] . "',
							'" . PackageInfo::Status['HANDLED'] . "',
							'" . PackageInfo::Status['CANCELLED'] . "',
							'" . PackageInfo::Status['REMOVED'] . "' ) ";

				$this->_query($sql, $errnum, $errmsg, null, null);

				// If that got marked as a NoOp, we should also remove the NoOp
				//	status from the quickbooks_ticket table, or we can get stuck
				//	in an infinite loop (we're all done, last request returns a
				//	no op, get last error is called, returns no op, send request
				//	is called and returns a no op because there's nothing to do,
				//	get last error is called and retuns a no op, etc. etc. etc.
				/*
				if ($new_status == PackageInfo::Status['NOOP'])
				{
					$errnum = null;
					$errmsg = null;
					$this->_query("
						UPDATE
							" . $this->_mapTableName(static::$Table['TICKET']) . "
						SET
							lasterror_num = NULL,
							lasterror_msg = NULL
						WHERE
							quickbooks_ticket_id = " . (int) $ticket_id, $errnum, $errmsg, 0, 1);
				}*/
			}

			return true;
		}

		return false;
	}

	/**
	 * Tell how many items have been processed during this session
	 */
	protected function _queueProcessed(string $ticket): ?int
	{
		$errnum = 0;
		$errmsg = '';

		if ($arr = $this->_fetch($this->_query("
			SELECT
				processed
			FROM
				" . $this->_mapTableName(static::$Table['TICKET']) . "
			WHERE
				ticket = '" . $this->_escape($ticket) . "' ", $errnum, $errmsg, 0, 1)))
		{
			return (int) $arr['processed'];
		}

		return null;
	}

	/**
	 * Tell whether or not an item exists in the queue
	 */
	protected function _queueExists(string $user, string $action, string $ident): ?array
	{
		$errnum = 0;
		$errmsg = '';

		return $this->queueArrayRecordHelper($this->_fetch($this->_query("
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['QUEUE']) . "
			WHERE
				qb_username = '" . $this->_escape($user) . "' AND
				qb_action = '" . $this->escape($action) . "' AND
				ident = '" . $this->escape($ident) . "' AND
				qb_status = '" . PackageInfo::Status['QUEUED'] . "' ", $errnum, $errmsg)));
	}

	/**
	 * Truncate (if necessary) the log, queue, and ticket tables if they grow too large
	 *
	 * @param string $table
	 * @param integer $max_history
	 * @return void
	 */
	protected function _truncate(string $table, int $max_history): void
	{
		// Don't do this all the time...
		if (mt_rand(0, 10) == 1)
		{
			return;
		}

		// We only need to run this once per table per HTTP session, so keep track of if we've alrealdy run or not
		static $runs = [];
		if (!empty($runs[$table]))
		{
			return;
		}
		$runs[$table] = true;

		// If max_history is set to -1, we *never* truncate
		if ($max_history <= 0)
		{
			return;
		}

		switch ($table)
		{
			case static::$Table['LOG']:
				$sql = "SELECT quickbooks_log_id FROM " . $this->_mapTableName($table) . " ORDER BY quickbooks_log_id ASC LIMIT ";
				$field = 'quickbooks_log_id';
				break;

			case static::$Table['QUEUE']:
				$sql = "
					SELECT
						quickbooks_queue_id
					FROM
						" . $this->_mapTableName($table) . "
					WHERE
						qb_status IN (
							'" . PackageInfo::Status['SUCCESS'] . "',
							'" . PackageInfo::Status['HANDLED'] . "',
							'" . PackageInfo::Status['CANCELLED'] . "',
							'" . PackageInfo::Status['NOOP'] . "' )
					ORDER BY
						quickbooks_queue_id ASC LIMIT ";
				$field = 'quickbooks_queue_id';
				break;

			case static::$Table['TICKET']:
				$sql = "SELECT quickbooks_ticket_id FROM " . $this->_mapTableName($table) . " ORDER BY quickbooks_ticket_id ASC LIMIT ";
				$field = 'quickbooks_ticket_id';
				break;

			default:
				// $table must be LOG, QUEUE, or TICKET
				throw new \Exception('Truncating table "' . $table . '" is unsupported.  Table must be LOG, QUEUE, or TICKET.');
		}

		// How big is the log file? Should we auto-truncate it?
		$errnum = 0;
		$errmsg = '';
		$arr = $this->_fetch($this->_query("SELECT COUNT(" . $field . ") AS counter FROM " . $this->_mapTableName($table), $errnum, $errmsg));
		if ($arr['counter'] > $max_history)
		{
			// Truncate the log to the size specified

			$start = time();
			$cutoff = 3; 		// 3 seconds max cutoff time to avoid timeouts

			$limit = 100;
			$list = [];

			$errnum = 0;
			$errmsg = '';
			$res = $this->_query($sql . floor($max_history / 2), $errnum, $errmsg);
			while (($arr = $this->_fetch($res)) && ((time() - $start) < $cutoff))
			{
				// Delete it batches of $limit, keep under $cutoff seconds
				$list[] = current($arr);

				if (count($list) > $limit)
				{
					$errnum = 0;
					$errmsg = '';

					$this->_query("DELETE FROM " . $this->_mapTableName($table) . " WHERE " . $field . " IN ( " . implode(', ', $list) . " )", $errnum, $errmsg);
					$list = [];
				}
			}
		}

		return;
	}
/*
	protected function _oauthRequestResolveV1($request_token)
	{
		$errnum = 0;
		$errmsg = '';

		return $this->fetch($this->query("
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['OAUTHV1']) . "
			WHERE
				oauth_request_token = '%s' ", $errnum, $errmsg, null, null, [ $request_token ]));
	}
*/
	protected function _oauthRequestResolveV2(string $state)
	{
		$errnum = 0;
		$errmsg = '';

		return $this->fetch($this->query("
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['OAUTHV2']) . "
			WHERE
				oauth_state = '%s' AND
				request_datetime >= '%s'
				", $errnum, $errmsg, null, null, [ $state, date('Y-m-d H:i:s', strtotime('-30 minutes')) ]));
	}

/*
	protected function _oauthLoadV1($app_tenant)
	{
		$errnum = 0;
		$errmsg = '';

		if ($arr = $this->fetch($this->query("
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['OAUTHV1']) . "
			WHERE
				app_tenant = '%s' ", $errnum, $errmsg, null, null, [$app_tenant])))
		{
			$this->query("
				UPDATE
					" . $this->_mapTableName(static::$Table['OAUTHV1']) . "
				SET
					touch_datetime = '%s'
				WHERE
					app_tenant = '%s' ", $errnum, $errmsg, null, null, [ date('Y-m-d H:i:s'), $app_tenant ]);

			return $arr;
		}

		return false;
	}
*/
	/**
	 * Load OAuth v2 tokens from Database
	 *
	 * @param string		$app_tenant   The tenant to load tokens for
	 */
	protected function _oauthLoadV2(string $app_tenant): ?array
	{
		$errnum = 0;
		$errmsg = '';

		$arr = $this->fetch($this->query("
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['OAUTHV2']) . "
			WHERE
				app_tenant = '%s' ", $errnum, $errmsg, null, null, [ $app_tenant ]));
		if ($arr)
		{
			$this->query("
				UPDATE
					" . $this->_mapTableName(static::$Table['OAUTHV2']) . "
				SET
					touch_datetime = '%s'
				WHERE
					app_tenant = '%s' ", $errnum, $errmsg, null, null, [ date('Y-m-d H:i:s'), $app_tenant ]);

			return $arr;
		}

		return null;
	}

	protected function _oauthRequestWriteV2(string $app_tenant, string $state)
	{
		$errnum = 0;
		$errmsg = '';

		// Check if it exists or not first
		$arr = $this->_oauthLoadV2($app_tenant);
		if ($arr)
		{
			// Exists... UPDATE!
			return $this->query("
				UPDATE
					" . $this->_mapTableName(static::$Table['OAUTHV2']) . "
				SET
					oauth_state = '%s',
					request_datetime = '%s'
				WHERE
					app_tenant = '%s' ", $errnum, $errmsg, null, null, [ $state, date('Y-m-d H:i:s'), $app_tenant ]);
		}
		else
		{
			// Insert it
			return $this->query("
				INSERT INTO
					" . $this->_mapTableName(static::$Table['OAUTHV2']) . "
				(
					app_tenant,
					oauth_state,
					request_datetime
				) VALUES (
					'%s',
					'%s',
					'%s'
				)", $errnum, $errmsg, null, null, [ $app_tenant, $state, date('Y-m-d H:i:s') ]);
		}
	}
/*
	protected function _oauthRequestWriteV1($app_tenant, $token, $token_secret)
	{
		$errnum = 0;
		$errmsg = '';

		// Check if it exists or not first
		if ($arr = $this->_oauthLoadV1($app_tenant))
		{
			// Exists... UPDATE!
			return $this->query("
				UPDATE
					" . $this->_mapTableName(static::$Table['OAUTHV1']) . "
				SET
					oauth_request_token = '%s',
					oauth_request_token_secret = '%s',
					request_datetime = '%s'
				WHERE
					app_tenant = '%s' ", $errnum, $errmsg, null, null, array( $token, $token_secret, date('Y-m-d H:i:s'), $app_tenant ));
		}
		else
		{
			// Insert it
			return $this->query("
				INSERT INTO
					" . $this->_mapTableName(static::$Table['OAUTHV1']) . "
				(
					app_tenant,
					oauth_request_token,
					oauth_request_token_secret,
					request_datetime
				) VALUES (
					'%s',
					'%s',
					'%s',
					'%s'
				)", $errnum, $errmsg, null, null, array( $app_tenant, $token, $token_secret, date('Y-m-d H:i:s') ));
		}
	}

	protected function _oauthAccessWriteV1($request_token, $token, $token_secret, $realm, $flavor)
	{
		$errnum = 0;
		$errmsg = '';

		// Check if it exists or not first
		if ($arr = $this->_oauthRequestResolveV1($request_token))
		{
			$vars = [$token, $token_secret, date('Y-m-d H:i:s')];

			$more = '';

			if ($realm)
			{
				$more .= ", qb_realm = '%s' ";
				$vars[] = $realm;
			}

			if ($flavor)
			{
				$more .= ", qb_flavor = '%s' ";
				$vars[] = $flavor;
			}

			$vars[] = $request_token;

			// Exists... UPDATE!
			return $this->query("
				UPDATE
					" . $this->_mapTableName(static::$Table['OAUTHV1']) . "
				SET
					oauth_access_token = '%s',
					oauth_access_token_secret = '%s',
					access_datetime = '%s'
					" . $more . "
				WHERE
					oauth_request_token = '%s' ", $errnum, $errmsg, null, null, $vars);
		}

		return false;
	}
*/
	protected function _oauthAccessRefreshV2($oauthv2_id, string $encrypted_access_token, string $encrypted_refresh_token, $access_expiry, $refresh_expiry)
	{
		$errnum = 0;
		$errmsg = '';

		$vars = [
			$encrypted_access_token,
			$encrypted_refresh_token,
			date('Y-m-d H:i:s', strtotime($access_expiry)),
			date('Y-m-d H:i:s', strtotime($refresh_expiry)),
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
		];

		$vars[] = (string) $oauthv2_id;

		// Exists... UPDATE!
		return $this->query("
			UPDATE
				" . $this->_mapTableName(static::$Table['OAUTHV2']) . "
			SET
				oauth_access_token = '%s',
				oauth_refresh_token = '%s',
				oauth_access_expiry = '%s',
				oauth_refresh_expiry = '%s',
				last_access_datetime = '%s',
				last_refresh_datetime = '%s'
			WHERE
				quickbooks_oauthv2_id = %d ", $errnum, $errmsg, null, null, $vars);
	}

	protected function _oauthAccessWriteV2(string $state, string $encrypted_access_token, string $encrypted_refresh_token, $access_expiry, $refresh_expiry, string $qb_realm)
	{
		$errnum = 0;
		$errmsg = '';

		// Check if it exists or not first
		$arr = $this->_oauthRequestResolveV2($state);
		if ($arr)
		{
			$vars = [
				$encrypted_access_token,
				$encrypted_refresh_token,
				date('Y-m-d H:i:s', strtotime($access_expiry)),
				date('Y-m-d H:i:s', strtotime($refresh_expiry)),
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
			];

			$more = '';

			if ($qb_realm)
			{
				$more .= ", qb_realm = '%s' ";
				$vars[] = $qb_realm;
			}

			$vars[] = $state;

			// Exists... UPDATE!
			return $this->query("
				UPDATE
					" . $this->_mapTableName(static::$Table['OAUTHV2']) . "
				SET
					oauth_access_token = '%s',
					oauth_refresh_token = '%s',
					oauth_access_expiry = '%s',
					oauth_refresh_expiry = '%s',
					access_datetime = '%s',
					last_access_datetime = '%s',
					last_refresh_datetime = '%s'
					" . $more . "
				WHERE
					oauth_state = '%s' ", $errnum, $errmsg, null, null, $vars);
		}

		return false;
	}

	protected function _oauthAccessDelete(string $UNUSED_IN_OAUTHV2_app_username, string $app_tenant): bool
	{
		return $this->_oauthAccessDeleteV2($app_tenant);
	}

	protected function _oauthAccessDeleteV2(string $app_tenant): bool
	{
		$errnum = 0;
		$errmsg = '';

		// Exists... DELETE!
		$this->query("
			DELETE FROM
				" . $this->_mapTableName(static::$Table['OAUTHV2']) . "
			WHERE
			app_tenant = '%s' ", $errnum, $errmsg, null, null, [$app_tenant]);

		return $this->affected() > 0;
	}

	/**
	 * Write a message to the log file
	 *
	 * @param string $msg
	 * @param string $ticket
	 * @param integer $log_level
	 * @return boolean
	 */
	protected function _log(string $msg, ?string $ticket = null, int $log_level = PackageInfo::LogLevel['NORMAL'], ?int $cur_log_level = null)
	{
		static $batch = 0;
		/*
		if ($batch == 0)		// Batching needs to be revised, *major* performance hit
		{
			// We store a batch ID so that we can tell which logged messages go with which actual separate HTTP request

			$errnum = 0;
			$errmsg = '';

			if ($arr = $this->_fetch($this->_query("SELECT MAX(batch) AS maxbatch FROM " . $this->_mapTableName(static::$Table['LOG']) . " ", $errnum, $errmsg)))
			{
				$batch = (int) $arr['maxbatch'];
			}

			$batch++;
		}
		*/

		// Truncate log and queue tables
		$this->_truncate(static::$Table['LOG'], $this->_max_log_history);
		$this->_truncate(static::$Table['QUEUE'], $this->_max_queue_history);
		$this->_truncate(static::$Table['TICKET'], $this->_max_ticket_history);

		// Actually insert the log message...
		$errnum = 0;
		$errmsg = '';

		// Make sure the message isn't too long
		$msg = substr($msg, 0, 65534);

		// Log level handling is handled by the QuickBooks_Driver base class (see public method {@link Driver::log()})
		$ticket_id = $this->_ticketResolve($ticket);
		if ($ticket_id)
		{
			return false !== $this->_query("
				INSERT INTO
					" . $this->_mapTableName(static::$Table['LOG']) . "
				(
					quickbooks_ticket_id,
					batch,
					msg,
					log_datetime
				) VALUES (
					" . $ticket_id . ",
					" . $batch . ",
					'" . $this->_escape($msg) . "',
					'" . date('Y-m-d H:i:s') . "' ) ", $errnum, $errmsg);
		}

		return false !== $this->_query("
			INSERT INTO
				" . $this->_mapTableName(static::$Table['LOG']) . "
			(
				batch,
				msg,
				log_datetime
			) VALUES (
				" . $batch . ",
				'" . $this->_escape($msg) . "',
				'" . date('Y-m-d H:i:s') . "' ) ", $errnum, $errmsg);
	}

	/**
	 *
	 *
	 * @param integer $offset
	 * @param integer $limit
	 * @param string $match
	 * @return QuickBooks_Iterator
	 */
	/*protected function _logView($offset, $limit, $match)
	{
		$errnum = 0;
		$errmsg = '';

		$match = trim($match);

		$list = [];

		if (strlen($match))
		{

		}
		else
		{
			$sql = "
				SELECT
					*
				FROM
					" . $this->_mapTableName(static::$Table['LOG']) . "
				ORDER BY
					quickbooks_log_id DESC ";
			$res = $this->_query($sql, $errnum, $errmsg, $offset, $limit);

			while ($arr = $this->_fetch($res))
			{
				$list[] = $arr;
			}
		}

		return new QuickBooks_Iterator($list);
	}*/

	/*protected function _logSize($match)
	{
		$errnum = 0;
		$errmsg = '';

		$match = trim($match);

		if (strlen($match))
		{
			return 0;
		}
		else
		{
			$arr = $this->_fetch($this->_query("
				SELECT
					COUNT(*) AS total
				FROM
					" . $this->_mapTableName(static::$Table['LOG']) . " ", $errnum, $errmsg));
			return $arr['total'];
		}
	}*/

	/**
	 *
	 *
	 *
	 */
	/*
	protected function _connectionLoad($user)
	{
		$errnum = 0;
		$errmsg = '';

		$this->_query("
			UPDATE
				" . $this->_mapTableName(static::$Table['CONNECTION']) . "
			SET
				touch_datetime = '" . date('Y-m-d H:i:s') . "'
			WHERE
				qb_username = '" . $this->_escape($user) . "' ", $errnum, $errmsg);

		return $this->_fetch($this->_query("
			SELECT
				*
			FROM
				" . $this->_mapTableName(static::$Table['CONNECTION']) . "
			WHERE
				qb_username = '" . $this->_escape($user) . "' ", $errnum, $errmsg));
	}
	*/

	/**
	 * Decode/Unserialize the "extra" data for the Queue action/command
	 *
	 * @param string|array|null $extra	The encoded/serialized "extra" data.
	 */
	public function decodeExtraData($extra): array
	{
		if (is_array($extra))
		{
			// Already an array
			return $extra;
		}
		else if (null === $extra || '' === $extra)
		{
			// Return an empty array for null or empty string
			return [];
		}
		else if (!is_string($extra))
		{
			throw new \TypeError('Argument 1 passed to ' . __METHOD__ . ' must be an array, string, or null but is a '. gettype($extra));
		}


		if (!is_null($value = json_decode($extra, true)) && is_array($value))
		{
			return $value;
		}
		else if (true === PackageInfo::$ALLOW_PHP_UNSERIALIZE_EXTRA_DATA && (false !== ($value = unserialize($extra)) && is_array($value)))
		{
			return $value;
		}

		throw new \Exception('Could not convert "extra" data into an array.');
	}

	/**
	 * Helper function that performs various repetitive tasks on a QUEUE record.
	 */
	public function queueArrayRecordHelper(?array $item): array
	{
		if (null === $item)
		{
			return null;
		}

		// Decode/Unserialize the extra data
		if (array_key_exists('extra', $item))
		{
			$item['extra'] = $this->decodeExtraData($item['extra']);
		}

		// Convert the queue id to an integer to appease type hinting for PgSQL since it returns strings for all data
		if (array_key_exists('quickbooks_queue_id', $item))
		{
			$item['quickbooks_queue_id'] = (int) $item['quickbooks_queue_id'];
		}

		// Convert the queue id to an integer to appease type hinting for PgSQL since it returns strings for all data
		if (array_key_exists('priority', $item))
		{
			$item['priority'] = (int) $item['priority'];
		}

		return $item;
	}


	/**
	 *
	 */
	protected function _noop(): bool
	{
		$errnum = 0;
		$errmsg = '';
		$tmp = $this->_fetch($this->_query("SELECT 42 + 42 AS thesum ", $errnum, $errmsg));

		return $tmp['thesum'] == 84;
	}

	/**
	 * Execute an SQL query against the database
	 *
	 * @param string $sql
	 * @param integer $errnum
	 * @param string $errmsg
	 * @return resource
	 */
	public function query(string $sql, ?int &$errnum, ?string &$errmsg, ?int $offset = 0, ?int $limit = null, ?array $vars = [])
	{
		if (is_array($vars) && count($vars))
		{
			foreach ($vars as $key => $value)
			{
				$vars[$key] = $this->escape($value);
			}

			$sql = vsprintf($sql, $vars);
		}

		//print($sql . '<br>');

		return $this->_query($sql, $errnum, $errmsg, $offset, $limit);
	}

	/**
	 * Fetch all rows of a result into an array
	 */
	public function fetchAll($res): array
	{
		$rows = [];

		while (!empty($row = $this->fetch($res)))
		{
			$rows[] = $row;
		}

		return $rows;
	}

	/**
	 * @see Driver\Sql::query()
	 */
	protected abstract function _query(string $sql, ?int &$errnum, ?string &$errmsg, ?int $offset = 0, ?int $limit = null);

	/**
	 * Escape a string
	 */
	public abstract function escape(string $str): string;

	/**
	 * Fetch a row from a result set
	 *
	 * @param resource $res
	 * @return array
	 */
	public abstract function fetch($res): array;

	protected abstract function _fetch($res);

	/**
	 * Get the number of rows the last query affected
	 */
	public abstract function affected(): int;

	/**
	 * Get the last sequence value from the last SQL insert
	 */
	public abstract function last(): int;

	/**
	 * Get a count of the number of results in an SQL result set
	 *
	 * @param resource $res
	 * @return integer
	 */
	public function count($res): int
	{
		return $this->_count($res);
	}

	/**
	 * Get a count of the number of results in an SQL result set
	 *
	 * @see Driver\Sql::count()
	 */
	abstract protected function _count($res): int;

	/**
	 * Rewind the result set
	 *
	 * @param resource $res
	 * @return boolean
	 */
	public abstract function rewind($res): bool;

	/**
	 * Get a list of the fields within an SQL table
	 */
	public function fields(string $table, bool $with_field_names_as_keys = false): array
	{
		static $cache = [];

		if (isset($cache[$table]))
		{
			return $cache[$table];
		}

		// *Careful* by default it's stored as array( 'field_name' => true, ... )
		$tmp = $this->_fields($table);
		$cache[$table] = array_combine($tmp, array_fill(0, count($tmp), true));

		if ($with_field_names_as_keys)
		{
			return $cache[$table];
		}

		return array_keys($cache[$table]);
	}

	/**
	 * Get a list of fields within a specific SQL table
	 */
	protected abstract function _fields(string $table): array;

	/**
	 * Map a default table name to a database-specific table name
	 */
	protected function _mapTableName(string $table): string
	{
		return static::$TablePrefix['BASE'] . $table;
	}

	/**
	 *
	 *
	 */
	/*
	protected function _generateCreatePrimaryKey(string $table, string $key, bool $serial = true)
	{
	}
	*/

	/**
	 *
	 *
	 */
	/*
	protected function _generateCreateForeignKey(string $table, string $this_field, $references_that_field)
	{
	}
	*/

	/**
	 *
	 */
	protected function _generateFieldSchema(string $name, array $def): string
	{
		$sql = '';

		switch ($def[0])
		{
			case self::DataType['INTEGER']:
				$sql .= $name . ' INTEGER ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else
					{
						$sql .= ' DEFAULT ' . (int) $def[2];
					}
				}
				break;

			case self::DataType['DECIMAL']:
				$sql .= $name . ' DECIMAL ';

				if (!empty($def[1]))
				{
					$tmp = explode(',', $def[1]);
					if (count($tmp) == 2)
					{
						$sql .= '(' . (int) $tmp[0] . ',' . (int) $tmp[1] . ') ';
					}
				}

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' NULL ';
					}
					else
					{
						if (isset($tmp) && count($tmp) == 2)
						{
							$sql .= ' DEFAULT ' . sprintf('%01.'. (int) $tmp[1] . 'f', (float) $def[2]);
						}
						else
						{
							$sql .= ' DEFAULT ' . sprintf('%01.2f', (float) $def[2]);
						}
					}
				}

				if (isset($tmp))
				{
					unset($tmp);
				}
				break;

			case self::DataType['FLOAT']:
				$sql .= $name . ' FLOAT ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' NULL ';
					}
					else
					{
						$sql .= ' DEFAULT ' . sprintf('%01.2f', (float) $def[2]);
					}
				}
				break;

			case self::DataType['BOOLEAN']:
				$sql .= $name . ' BOOLEAN ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else if ($def[2])
					{
						$sql .= ' DEFAULT TRUE ';
					}
					else
					{
						$sql .= ' DEFAULT FALSE ';
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case self::DataType['DATE']:
				$sql .= $name . ' DATE ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case self::DataType['TIME']:
				break;

			case self::DataType['DATETIME']:
				$sql .= $name . ' DATETIME ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case self::DataType['VARCHAR']:
				$sql .= $name . ' VARCHAR';

				/*if ($name == 'ListID')
				{
					print('LIST ID:');
					print_r($def);
				}*/

				if (!empty($def[1]))
				{
					$sql .= '(' . (int) $def[1] . ') ';
				}

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else if ($def[2] === false)
					{
						$sql .= ' NOT NULL ';
					}
					else
					{
						$sql .= " NOT NULL DEFAULT '" . $def[2] . "' ";
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case self::DataType['CHAR']:
				$sql .= $name . ' CHAR';

				if (!empty($def[1]))
				{
					$sql .= '(' . (int) $def[1] . ') ';
				}

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else
					{
						$sql .= " NOT NULL DEFAULT '" . $def[2] . "' ";
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;

			case self::DataType['TEXT']:
			default:
				$sql .= $name . ' TEXT ';

				if (isset($def[2]))
				{
					if (is_string($def[2]) && strtolower($def[2]) == 'null')
					{
						$sql .= ' DEFAULT NULL ';
					}
					else
					{
						$sql .= " NOT NULL DEFAULT '" . $def[2] . "' ";
					}
				}
				else
				{
					$sql .= ' NOT NULL ';
				}
				break;
		}

		return $sql;
	}

	protected function _generateCreateTable(string $name, array $arr, $primary = [], array $keys = [], array $uniques = [], bool $if_not_exists = true): array
	{
		$sql = '';

		foreach ($arr as $field => $def)
		{
			$sql .= "\t" . $this->_generateFieldSchema($field, $def) . ', ' . "\n";
		}

		return [
			'CREATE TABLE ' . ($if_not_exists?"IF NOT EXISTS ":"") . $name . ' ( ' . "\n" . substr($sql, 0, -3) . ' ); ',
		];
	}

	protected function _initialize(array $init_options = []): bool
	{
		$defaults = [
			//'quickbooks_api_enabled' => false,
			//'quickbooks_api_debug' => false,
			//'quickbooks_api_droptable' => false,
			//'quickbooks_api_print' => false,

			'quickbooks_sql_enabled' => false,
			'quickbooks_sql_schema' => realpath(dirname(__DIR__, 3) . '/data/schema'),
			'quickbooks_sql_debug' => false,
			'quickbooks_sql_droptable' => false,
			'quickbooks_sql_prefix' => static::$TablePrefix['SQL_MIRROR'],
			'quickbooks_sql_print' => false,

			'quickbooks_integrator_enabled' => false,
			'quickbooks_integrator_prefix' => static::$TablePrefix['INTEGRATOR'],
		];
		$config = array_merge($defaults, $init_options);


		// list of SQL statements to run
		$arr_sql = [];

		// Log Table
		$table = $this->_mapTableName(static::$Table['LOG']);
		$def = [
			'quickbooks_log_id' => [self::DataType['SERIAL']],
			'quickbooks_ticket_id' => [self::DataType['INTEGER'], null, 'null'],
			'batch' => [self::DataType['INTEGER']],
			'msg' => [self::DataType['TEXT']],
			'log_datetime' => [self::DataType['DATETIME']],
		];
		$primary = 'quickbooks_log_id';
		$keys = ['quickbooks_ticket_id', 'batch'];
		$uniques = [];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));

		// Queue table
		$table = $this->_mapTableName(static::$Table['QUEUE']);
		$def = [
			'quickbooks_queue_id' => [self::DataType['SERIAL']],
			'quickbooks_ticket_id' => [self::DataType['INTEGER'], null, 'null'],
			'qb_username' => [self::DataType['VARCHAR'], 40],
			'qb_action' => [self::DataType['VARCHAR'], 32],
			'ident' => [self::DataType['VARCHAR'], 40],
			'extra' => [self::DataType['TEXT'], null, 'null'],
			'qbxml' => [self::DataType['TEXT'], null, 'null'],
			'priority' => [self::DataType['INTEGER'], 3, 0],
			'qb_status' => [self::DataType['CHAR'], 1],
			'msg' => [self::DataType['TEXT'], null, 'null'],
			'enqueue_datetime' => [self::DataType['DATETIME']],
			'dequeue_datetime' => [self::DataType['DATETIME'], null, 'null'],
		];
		$primary = 'quickbooks_queue_id';
		$keys = ['quickbooks_ticket_id', 'priority', ['qb_username', 'qb_action', 'ident', 'qb_status'], 'qb_status'];
		$uniques = [];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));

		// Recurring queue table
		$table = $this->_mapTableName(static::$Table['RECUR']);
		$def = [
			'quickbooks_recur_id' => [self::DataType['SERIAL']],
			'qb_username' => [self::DataType['VARCHAR'], 40],
			'qb_action' => [self::DataType['VARCHAR'], 32],
			'ident' => [self::DataType['VARCHAR'], 40],
			'extra' => [self::DataType['TEXT'], null, 'null'],
			'qbxml' => [self::DataType['TEXT'], null, 'null'],
			'priority' => [self::DataType['INTEGER'], 3, 0],
			'run_every' => [self::DataType['INTEGER']],
			'recur_lasttime' => [self::DataType['INTEGER']],
			'enqueue_datetime' => [self::DataType['DATETIME']],
		];
		$primary = 'quickbooks_recur_id';
		$keys = [['qb_username', 'qb_action', 'ident'], 'priority'];
		$uniques = [];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));

		// Ticket table
		$table = $this->_mapTableName(static::$Table['TICKET']);
		$def = [
			'quickbooks_ticket_id' => [self::DataType['SERIAL']],
			'qb_username' => [self::DataType['VARCHAR'], 40],
			'ticket' => [self::DataType['CHAR'], 36],
			'processed' => [self::DataType['INTEGER'], null, 0],
			'lasterror_num' => [self::DataType['VARCHAR'], 32, 'null'],
			'lasterror_msg' => [self::DataType['VARCHAR'], 255, 'null'],
			'ipaddr' => [self::DataType['CHAR'], 15],
			'write_datetime' => [self::DataType['DATETIME']],
			'touch_datetime' => [self::DataType['DATETIME']],
		];
		$primary = 'quickbooks_ticket_id';
		$keys = ['ticket'];
		$uniques = [];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));

		// User table
		$table = $this->_mapTableName(static::$Table['USER']);
		$def = [
			'qb_username' => [self::DataType['VARCHAR'], 40],
			'qb_password' => [self::DataType['VARCHAR'], 255],
			'qb_company_file' => [self::DataType['VARCHAR'], 255, 'null'],
			'base_currency' => [self::DataType['VARCHAR'], 3, 'null'],
			'qbwc_wait_before_next_update' => [self::DataType['INTEGER'], null, 0],
			'qbwc_min_run_every_n_seconds' => [self::DataType['INTEGER'], null, 0],
			'status' => [self::DataType['CHAR'], 1],
			'write_datetime' => [self::DataType['DATETIME']],
			'touch_datetime' => [self::DataType['DATETIME']],
		];
		$primary = 'qb_username';
		$keys = [];
		$uniques = [];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));

		// Config table
		$table = $this->_mapTableName(static::$Table['CONFIG']);
		$def = [
			'quickbooks_config_id' => [ self::DataType['SERIAL'] ],
			'qb_username' => [ self::DataType['VARCHAR'], 40 ],
			'module' => [ self::DataType['VARCHAR'], 255 ],
			'cfgkey' => [ self::DataType['VARCHAR'], 40 ],
			'cfgval' => [ self::DataType['VARCHAR'], 40 ],
			'cfgtype' => [ self::DataType['VARCHAR'], 40 ],
			'cfgopts' => [ self::DataType['TEXT'], null ],
			'write_datetime' => [ self::DataType['DATETIME'] ],
			'mod_datetime' => [ self::DataType['DATETIME'] ],
		];
		$primary = 'quickbooks_config_id';
		$keys = [];
		$uniques = [['qb_username', 'module', 'cfgkey']];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));

		/*
		$table = $this->_mapTableName(static::$Table['NOTIFY']);
		$def = [
			'quickbooks_notify_id' => [self::DataType['SERIAL']],
			'qb_username' => [self::DataType['VARCHAR'], 40],
			'qb_object' => [self::DataType['VARCHAR'], 40],
			'unique_id' => [self::DataType['VARCHAR'], 40],
			'qb_ident' => [self::DataType['VARCHAR'], 40],
			'errnum' => [self::DataType['INTEGER'], null, 'null'],
			'errmsg' => [self::DataType['TEXT'], null],
			'note' => [self::DataType['TEXT'], null],
			'priority' => [self::DataType['INTEGER']],
			'write_datetime' => [self::DataType['DATETIME']],
			'mod_datetime' => [self::DataType['DATETIME']],
		];
		$primary = 'quickbooks_notify_id';
		$keys = [];
		$uniques = [];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));
		*/

		/*
		$table = $this->_mapTableName(static::$Table['CONNECTION']);
		$def = [
			'quickbooks_connection_id' => [self::DataType['SERIAL']],
			'qb_username' => [self::DataType['VARCHAR'], 40],
			'certificate' => [self::DataType['VARCHAR'], 255, 'null'],
			'application_id' => [self::DataType['INTEGER']],
			'application_login' => [self::DataType['VARCHAR'], 40, 'null'],
			'lasterror_num' => [self::DataType['VARCHAR'], 32, 'null'],
			'lasterror_msg' => [self::DataType['VARCHAR'], 255, 'null'],
			'connection_ticket' => [self::DataType['VARCHAR'], 255, 'null'],
			'connection_datetime' => [self::DataType['DATETIME']],
			'write_datetime' => [self::DataType['DATETIME']],
			'touch_datetime' => [self::DataType['DATETIME']],
		];
		$primary = 'quickbooks_connection_id';
		$keys = [];
		$uniques = [];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));
		*/
/*
		// OAuth1
		$table = $this->_mapTableName(static::$Table['OAUTHV1']);
		$def = [
			'quickbooks_oauthv1_id' => [self::DataType['SERIAL']],
			'app_username' => [self::DataType['VARCHAR'], 255],
			'app_tenant' => [self::DataType['VARCHAR'], 255],
			'oauth_request_token' => [self::DataType['VARCHAR'], 255, 'null'],
			'oauth_request_token_secret' => [self::DataType['VARCHAR'], 255, 'null'],
			'oauth_access_token' => [self::DataType['VARCHAR'], 255, 'null'],
			'oauth_access_token_secret' => [self::DataType['VARCHAR'], 255, 'null'],
			'qb_realm' => [self::DataType['VARCHAR'], 32, 'null'],
			'qb_flavor' => [self::DataType['VARCHAR'], 12, 'null'],
			'qb_user' => [self::DataType['VARCHAR'], 64, 'null'],
			'request_datetime' => [self::DataType['DATETIME']],
			'access_datetime' => [self::DataType['DATETIME'], null, 'null'],
			'touch_datetime' => [self::DataType['DATETIME'], null, 'null'],
		];
		$primary = 'quickbooks_oauthv1_id';
		$keys = [];
		$uniques = [['app_username', 'app_tenant']];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));
*/
		// OAuth2 table
		$table = $this->_mapTableName(static::$Table['OAUTHV2']);
		$def = [
			'quickbooks_oauthv2_id' => [self::DataType['SERIAL']],
			'app_tenant' => [self::DataType['VARCHAR'], 255],
			'oauth_state' => [self::DataType['VARCHAR'], 255],
			'oauth_access_token' => [self::DataType['TEXT'], null, 'null'],
			'oauth_refresh_token' => [self::DataType['TEXT'], null, 'null'],
			'oauth_access_expiry' => [self::DataType['DATETIME'], null, 'null'],
			'oauth_refresh_expiry' => [self::DataType['DATETIME'], null, 'null'],
			'qb_realm' => [self::DataType['VARCHAR'], 32, 'null'],
			'request_datetime' => [self::DataType['DATETIME']],
			'access_datetime' => [self::DataType['DATETIME'], null, 'null'],
			'last_access_datetime' => [self::DataType['DATETIME'], null, 'null'],
			'last_refresh_datetime' => [self::DataType['DATETIME'], null, 'null'],
			'touch_datetime' => [self::DataType['DATETIME'], null, 'null'],
		];
		$primary = 'quickbooks_oauthv2_id';
		$keys = [];
		$uniques = [['app_tenant']];
		$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($table, $def, $primary, $keys, $uniques));

		//header('Content-Type: text/plain');
		//print_r($arr_sql);
		//exit;

		// Support for mirroring the QuickBooks database in an SQL database
		if ($config['quickbooks_sql_enabled'])
		{
			$tables = [];

			$excluded_files = [
				'UIEventSubscriptionQuery.xml',
				'UIExtensionSubscriptionQuery.xml',
				'DataEventSubscriptionQuery.xml',
			];

			// Use the QuickBooks_SQL_Schema class
			$dh = opendir($defaults['quickbooks_sql_schema']);
			while (false !== ($file = readdir($dh)))
			{
				if ($file{0} == '.' || is_dir($defaults['quickbooks_sql_schema'] . '/' . $file))
				{
					// Skip directories including current (.) and parent directories (..)
					continue;
				}

				//var_dump($file);
				$extension = explode('.', $file);
				if (strtolower(array_pop($extension)) != 'xml')
				{
					// Skip any non-xml files
					continue;
				}

				if (in_array($file, $excluded_files))
				{
					//exit('Excluded File: '. $file . "\n");
					continue;
				}

				//echo "Processing schema file: $file\n";
				$xml = file_get_contents($defaults['quickbooks_sql_schema'] . '/' . $file);

				Schema::mapSchemaToSQLDefinition($xml, $tables);

				// This times out on some SQL connections because it takes so darn long to generate the
				//	schema. Thus, we're going to issue a few useless queries here, just so we don't lose
				//	the connection to the database.
				$this->noop();
			}

			// A table has to be created for each query type, and each table has to have some extra fields added to it
			foreach ($tables as $table)
			{
				// @TODO Support other transformations (table names to uppercase, field names to lowercase, etc.)
				$name = strtolower($config['quickbooks_sql_prefix'] . $table[0]);

				$idfield = [self::DataType['SERIAL'], null, 0];

				$username_field = [self::DataType['INTEGER'], null, 'null'];
				$external_field = [self::DataType['INTEGER'], null, 'null'];

				$ifield = [self::DataType['DATETIME'], null, 'null'];		// Date/time when first inserted
				$ufield = [self::DataType['DATETIME'], null, 'null'];		// Date/time when updated (re-sync from QuickBooks)
				$mfield = [self::DataType['TIMESTAMP'], null, 'null'];		// Date/time when modified by a user (needs to be pushed to QB)
				$hfield = [self::DataType['VARCHAR'], 40, 'null'];
				$qfield = [self::DataType['TEXT'], null, 'null'];
				//$dfield = [self::DataType['DATETIME'], null, 'null'];		// Date/time when deleted by a user (needs to be deleted from QB)
				//$cfield = [self::DataType['TIMESTAMP']_ON_INSERT, null, 'NOW()'];
				//$mfield = [self::DataType['TIMESTAMP']_ON_INSERT_OR_UPDATE, null, 'NOW()'];

				// This should be an VARCHAR, QuickBooks errors are sometimes in the format "0x12341234"
				$enfield = [self::DataType['VARCHAR'], 32, 'null'];			// Add/mod error number
				$emfield = [self::DataType['VARCHAR'], 255, 'null'];		// Add/mod error message
				$enqfield = [self::DataType['DATETIME'], null, 'null'];		// Add/mod enqueue date/time
				$deqfield = [self::DataType['DATETIME'], null, 'null'];		// Add/mod dequeue date/time

				$audit_modified_field = [self::DataType['DATETIME'], null, 'null'];
				$audit_amount_field = [self::DataType['DECIMAL'], null, 'null'];

				$to_delete_field = [self::DataType['BOOLEAN'], null, 0];	// Flag it for deletion
				$to_void_field = [self::DataType['BOOLEAN'], null, 0];
				$to_skip_field = [self::DataType['BOOLEAN'], null, 0];		// Flag it for skipping
				$to_sync_field = [self::DataType['BOOLEAN'], null, 0];

				$flag_deleted_field = [self::DataType['BOOLEAN'], null, 0];	// This has been deleted within QuickBooks
				$flag_voided_field = [self::DataType['BOOLEAN'], null, 0];
				$flag_skipped_field = [self::DataType['BOOLEAN'], null, 0];	// This has been skipped within the sync to QuickBooks

				$fields = $table[1];

				$prepend = [
					self::Field['ID'] => $idfield,
					self::Field['USERNAME_ID'] => $username_field,
					self::Field['EXTERNAL_ID'] => $external_field,
				];

				$fields = array_merge( $prepend, $fields );

				$fields[self::Field['DISCOVER']] = $ifield;
				$fields[self::Field['RESYNC']] = $ufield;
				$fields[self::Field['MODIFY']] = $mfield;
				$fields[self::Field['HASH']] = $hfield;
				$fields[self::Field['QBXML']] = $qfield;
				//$fields[QUICKBOOKS_DRIVER_SQL_FIELD_DELETE] = $dfield;

				$fields[self::Field['ERROR_NUMBER']] = $enfield;
				$fields[self::Field['ERROR_MESSAGE']] = $emfield;
				$fields[self::Field['ENQUEUE_TIME']] = $enqfield;
				$fields[self::Field['DEQUEUE_TIME']] = $deqfield;

				//$fields[QUICKBOOKS_DRIVER_SQL_FIELD_DELETED_FLAG] = $delflagfield;

				$fields[self::Field['AUDIT_AMOUNT']] = $audit_amount_field;
				$fields[self::Field['AUDIT_MODIFIED']] = $audit_modified_field;

				$fields[self::Field['TO_SYNC']] = $to_sync_field;
				$fields[self::Field['TO_DELETE']] = $to_delete_field;
				$fields[self::Field['TO_SKIP']] = $to_skip_field;
				$fields[self::Field['TO_VOID']] = $to_void_field;
				$fields[self::Field['FLAG_DELETED']] = $flag_deleted_field;
				$fields[self::Field['FLAG_SKIPPED']] = $flag_skipped_field;
				$fields[self::Field['FLAG_VOIDED']] = $flag_voided_field;

				$primary = self::Field['ID'];
				//$keys = [];
				//$uniques = [$table[2]];
				//$uniques = [];


				$keys = $table[3];
				$uniques = $table[4];

				// @TODO Fix this to support unique keys
				$keys = array_merge($keys, $uniques);

				/*
				print('keys: ');
				print_r($keys);
				print("\n\n");
				print('uniques: ');
				print_r($uniques);
				exit;
				*/

				$arr_sql = array_merge($arr_sql, $this->_generateCreateTable($name, $fields, $primary, $keys));
			}
		}

		// Run each CREATE TABLE statement...
		foreach ($arr_sql as $sql)
		{
			if ($config['quickbooks_sql_debug'] || $config['quickbooks_sql_print'])
			{
				print($sql . "\n\n");
			}
			else
			{
				$errnum = 0;
				$errmsg = '';

				//print($sql);

				$this->_query($sql, $errnum, $errmsg);
			}
		}

		return true;
	}

	/**
	 *
	 *
	 * @param string $table
	 * @param array $restrict
	 * @return object
	 */
	public function get(string $table, array $restrict)
	{
		if (count($restrict))
		{
			$where = [];
			foreach ($restrict as $field => $value)
			{
				if (is_null($value))
				{
					$where[] = $field . ' IS NULL ';
				}
				else
				{
					$where[] = $field . " = '" . $this->_escape($value) . "' ";
				}
			}

			$errnum = 0;
			$errmsg = '';
			$res = $this->_query("SELECT * FROM " . $this->_escape($table) . " WHERE " . implode(' AND ', $where) . " ", $errnum, $errmsg, 0, 1);
			if ($res &&
				($arr = $this->_fetch($res)))
			{
				return $this->_unfold($arr);
			}
		}

		return false;
	}

	protected function _unfoldKeys(array $arr, array $keys_map): array
	{
		$i = 0;
		foreach ($arr as $key => $value)
		{
			if (!empty($keys_map[$key]))
			{
				$firsthalf = array_slice($arr, 0, $i);
				$secondhalf = array_slice($arr, $i + 1);

				$firsthalf[$keys_map[$key]] = $value;

				$arr = array_merge($firsthalf, $secondhalf);
			}

			$i++;
		}

		return $arr;
	}

	protected function _unfold(array $arr): array
	{
		static $folding = [
			'txnid' => 'TxnID',
			'listid' => 'ListID',
			'txnlineid' => 'TxnLineID',
			'editsequence' => 'EditSequence',
			'customer_listid' => 'Customer_ListID',
			'vendor_listid' => 'Vendor_ListID',
			'prefvendor_listid' => 'PrefVendor_ListID',
			'account_listid' => 'Account_ListID',
			'araccount_listid' => 'ARAccount_ListID',
			'paymentmethod_listid' => 'PaymentMethod_ListID',
		];

		if ($this->foldsToLower())
		{
			/*
			foreach ($folding as $lower => $unfolded)
			{
				if (isset($arr[$lower]))
				{
					$arr[$unfolded] = $arr[$lower];
					unset($arr[$lower]);
				}
			}
			*/

			$arr = $this->_unfoldKeys($arr, $folding);
		}
		else if ($this->foldsToUpper())
		{
			foreach ($folding as $lower => $unfolded)
			{
				$upper = strtoupper($lower);
				if (isset($arr[$upper]))
				{
					$arr[$unfolded] = $arr[$upper];
					unset($arr[$upper]);
				}
			}
		}

		return $arr;
	}

	/**
	 * Get a list of objects back from the database
	 */
	public function select(string $table, array $restrict, ?array $order = [], ?int $offset = null, ?int $limit = null): array
	{
		$list = [];

		$where = '';
		if (count($restrict))
		{
			$where = [];
			foreach ($restrict as $field => $value)
			{
				$where[] = $field . " = '" . $this->_escape($value) . "' ";
			}

			$where = " WHERE " . implode(' AND ', $where) . " ";
		}

		$orderby = '';
		if (is_array($order) && count($order))
		{
			$orderby = [];
			foreach ($order as $field => $direction)
			{
				$orderby[] = " " . $field . " " . $direction;
			}

			$orderby = " ORDER BY " . implode(', ', $orderby);
		}

		$errnum = 0;
		$errmsg = '';
		if ($res = $this->_query("SELECT * FROM " . $this->_escape($table) . " " . $where . " " . $orderby, $errnum, $errmsg, $offset, $limit))
		{
			while ($arr = $this->_fetch($res))
			{
				$list[] = $arr;
			}
		}

		return $list;
	}

	/**
	 * Update a record in the SQL table
	 *
	 * @todo We should make this support only passing a single $where component, instead of an array of array where components
	 * @todo Support the $derive flag
	 *
	 * @param string $table			The table to update
	 * @param object $object		An object containing a record to update
	 * @param array $where			An array to use to build the WHERE clause
	 * @param boolean $resync		Update the timestamp field which indicates when we last re-synced with QuickBooks
	 * @param boolean $discov		Update the timestamp field which indicates when this record was discoved in QuickBooks
	 * @param boolean $derive		Update the timestamp field which indicates when we last updated derived fields from QuickBooks
	 * @return boolean
	 */
	public function update(string $table, $object, array $where = [], bool $resync = true, ?bool $discov = null, bool $derive = true)	// @todo Is that the correct default for $derive?
	{
		if (is_object($object))
		{
			$object = $object->asArray();
		}

		$avail = $this->fields($table, true);		// List of available fields

		// Case folding support
		if ($this->foldsToLower())
		{
			$object = array_change_key_case($object, CASE_LOWER);
		}
		else if ($this->foldsToUpper())
		{
			$object = array_change_key_case($object, CASE_UPPER);
		}

		// Merge by keys to make sure we don't INSERT any fields that don't exist in this schema
		$object = array_intersect_key($object, $avail);

		$set = [];
		foreach ($object as $field => $value)
		{
			// Commented out because doing this to very large integers (i.e. ItemRef/FullName is a large integer SKU) causes integer overflow
			/*if (strlen((int) $value) == strlen($value))
			{
				$set[] = $field . ' = ' . (int) $value;
			}
			else
			{*/
			//	$set[] = $field . " = '" . $this->_escape($value) . "' ";
			//}
			if (is_null($value))
			{
				$set[] = $field . ' = NULL ';
			}
			else if (false !== filter_var($value, FILTER_VALIDATE_INT) &&
				strlen((string) $value) < 16)
			{
				// Number, cast as float to avoid integer overflow
				$set[] = $field . ' = ' . (float) $value;
			}
			else if (false !== filter_var($value, FILTER_VALIDATE_FLOAT) &&
				strlen((string) $value) < 16)
			{
				// Number, cast as float to avoid integer overflow
				$set[] = $field . ' = ' . (float) $value;
			}
			else if ($value != '')
			{
				if (preg_match('/^\d\d\d\d-\d\d-\d\dT\d\d:\d\d:\d\d([+\-])\d\d:\d\d$/', $value))
				{
					// SOAP DateTime format (2019-06-30T22:04:37-06:00)
					$value = date('Y-m-d H:i:s', strtotime($value));
				}

				// String value, put it in quotes.
				$set[] = $field . " = '" . $this->_escape($value) . "' ";
			}
		}

		$wheres = [];
		foreach ($where as $part)
		{
			foreach ($part as $field => $value)
			{
				if (is_null($value))
				{
					$wheres[] = $field . ' IS NULL ';
				}
				else
				{
					$wheres[] = $field . " = '" . $this->_escape($value) . "' ";
				}
			}
		}

		$sql = "UPDATE " . $this->_escape($table) . " SET " . implode(', ', $set);

		if ($resync)
		{
			$sql .= ", " . self::Field['RESYNC'] . " = '" . date('Y-m-d H:i:s') . "' ";
		}

		$sql .= " WHERE " . implode(' AND ', $wheres);

		//print($sql);

		$errnum = 0;
		$errmsg = '';
		$return = $this->_query($sql, $errnum, $errmsg);

		if (is_null($discov))
		{
			$discov = $resync;
		}

		if ($discov)
		{
			// Update the discover datetime *IF THE DISCOVER DATETIME IS NULL*
			//	This happens when an AddResponse is received, and we need to
			//	update a record that has just been added to QuickBooks. If we
			//	don't mark it as discovered, then updates to the record will
			//	never be picked up and sent to QuickBooks

			$errnum = 0;
			$errmsg = '';

			$wheres[] = self::Field['DISCOVER'] . " IS NULL ";

			$this->_query("
				UPDATE
					" . $this->_escape($table) . "
				SET
					" . self::Field['DISCOVER'] . " = " . self::Field['RESYNC'] . "
				WHERE
					" . implode(' AND ', $wheres), $errnum, $errmsg);
		}

		return $return;
	}

	/**
	 * Insert a new record into an SQL table
	 *
	 * @param string $table
	 * @param object $object
	 * @return boolean
	 */
	public function insert(string $table, $object, bool $discov_and_resync = true): bool
	{
		$avail = $this->fields($table, true);		// List of available fields
		$fields = [];
		$values = [];

		if (is_object($object))
		{
			$object = $object->asArray();
		}

		// Case folding support
		if ($this->foldsToLower())
		{
			$object = array_change_key_case($object, CASE_LOWER);
		}
		else if ($this->foldsToUpper())
		{
			$object = array_change_key_case($object, CASE_UPPER);
		}

		if (!is_array($object) || !is_array($avail))
		{
			print('ERROR SAVING [[' . "\n");
			print('TABLE: ' . $table . "\n");
			print_r($object);
			print_r($avail);
			print(']]');
			exit;
		}

		// Merge by keys to make sure we don't INSERT any fields that don't exist in this schema
		$object = array_intersect_key($object, $avail);
		//print_r($object);

		foreach ($object as $field => $value)
		{
			$fields[] = $field;

			if (false !== filter_var($value, FILTER_VALIDATE_INT) &&
				strlen((string) $value) < 16)
			{
				// Number, cast as float to avoid integer overflow
				$values[] = (float) $value;
			}
			else if (false !== filter_var($value, FILTER_VALIDATE_FLOAT) &&
				strlen((string) $value) < 16)
			{
				// Number, cast as float to avoid integer overflow
				$values[] = (float) $value;
			}
			else if ($value != '')
			{
				if (preg_match('/^\d\d\d\d-\d\d-\d\dT\d\d:\d\d:\d\d([+\-])\d\d:\d\d$/', $value))
				{
					// SOAP DateTime format (2019-06-30T22:04:37-06:00)
					$value = date('Y-m-d H:i:s', strtotime($value));
				}

				// String value, put it in quotes.
				$values[] = " '" . $this->_escape($value) . "' ";
			}
			else
			{
				// Empty string value, don't insert
				array_pop($fields);
			}
		}

		$sql = "INSERT INTO " . $this->_escape($table) . " ( " . implode(', ', $fields) . " ";

		if ($discov_and_resync)
		{
			$sql .= ", " . self::Field['DISCOVER'] . ", " . self::Field['RESYNC'] . " ";
		}

		$sql .= " ) VALUES ( " . implode(', ', $values) . " ";

		if ($discov_and_resync)
		{
			$sql .= ", '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "' ";
		}

		$sql .= " ); ";

		//print_r($object);
		//die($sql);

		/*
		if ($table == 'pricemodel_tierset')
		{
			print_r($object);
			print($sql);
			exit;
		}
		*/

		$errnum = 0;
		$errmsg = '';
		return false !== $this->_query($sql, $errnum, $errmsg);
	}

	/**
	 *
	 *
	 * @param string $table
	 * @param array $where
	 * @return boolean
	 */
	public function delete(string $table, array $where): bool
	{
		$wheres = [];
		foreach ($where as $part)
		{
			foreach ($part as $field => $value)
			{
				$wheres[] = $field . " = '" . $this->_escape($value) . "' ";
			}
		}

		$sql = 'DELETE FROM ' . $this->_escape($table);

		$sql .= ' WHERE ' . implode(' AND ', $wheres);

		$errnum = 0;
		$errmsg = '';

		// _query throws an exception on a failed query
		$this->_query($sql, $errnum, $errmsg);

		return true;
	}

	/**
	 * Table and field names are folded to lowercase
	 */
	public function foldsToLower(): bool
	{
		return false;
	}

	/**
	 * Table and field names are folded to uppercase
	 */
	public function foldsToUpper(): bool
	{
		return false;
	}

	/**
	 * Boolean datatype is a true boolean (true/false) and not 1/0
	 */
	public function hasTrueBoolean(): bool
	{
		return false;
	}
}
