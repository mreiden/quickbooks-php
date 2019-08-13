<?php declare(strict_types=1);

/**
 * QuickBooks driver base class
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * The Driver classes act as back-end to the Queue class and SOAP server.
 * Driver classes should extend this base class and implement all abstract
 * methods.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Driver
 */

namespace QuickBooksPhpDevKit;

use QuickBooksPhpDevKit\Encryption\Factory as EncryptionFactory;
use QuickBooksPhpDevKit\PackageInfo;

/**
 * QuickBooks driver base class
 */
abstract class Driver
{
	public const HOOK_NOOP = 'Driver::noop';  // Hook called by the ->noop() method
	public const HOOK_CONNECTIONLOAD = 'Driver::connectionLoad';

	//// Error related hooks
	public const HOOK_ERRORLOG  = 'Driver::errorLog';	// Hook called by the ->errorLog() method
	public const HOOK_ERRORLAST = 'Driver::errorLast';  // Hook called by the ->errorLast() method

	//// Log related hooks
	public const HOOK_LOG = 'Driver::log';				// Hook called by the ->log() method
	//public const HOOK_LOGSIZE = 'Driver::logSize';	// Hook called by the ->logSize() method
	//public const HOOK_LOGVIEW = 'Driver::logView';	// Hook called by the ->logView() method

	//// Ident related hooks
	//public const HOOK_IDENTFETCH = 'Driver::identFetch';
//	public const HOOK_IDENTTOAPPLICATION = 'Driver::identToApplication';  // Hook called by the ->identToApplication() method
//	public const HOOK_IDENTTOQUICKBOOKS = 'Driver::identToQuickBooks';	// Hook called by the ->identToQuickBooks() method
//	public const HOOK_IDENTMAP = 'Driver::identMap';
	//public const HOOK_IDENTVIEW = 'Driver::identView';
	//public const HOOK_IDENTSIZE = 'Driver::identSize';

	//// Authentication related hooks
	public const HOOK_AUTHCHECK   = 'Driver::authCheck';		// Hook called by the ->authCheck() method
	public const HOOK_AUTHCREATE  = 'Driver::authCreate';		// Hook called by the ->authCreate() method
	public const HOOK_AUTHDEFAULT = 'Driver::authDefault';
	public const HOOK_AUTHLOGIN   = 'Driver::authLogin';
	public const HOOK_AUTHLOGOUT  = 'Driver::authLogout';
	public const HOOK_AUTHRESOLVE = 'Driver::authResolve';
	public const HOOK_AUTHDISABLE = 'Driver::authDisable';		// Hook called by the ->authDisable() method
	public const HOOK_AUTHENABLE  = 'Driver::authEnable';		// Hook called by the ->authEnable() method
	public const HOOK_AUTHLAST	  = 'Driver::authLast';
	//public const HOOK_AUTHVIEW	= 'Driver::authView';		// Hook called by the ->authView() method
	//public const HOOK_AUTHSIZE	= 'Driver::authSize';		// Hook called by the ->authSize() method

	//// Queue related hooks
	public const HOOK_QUEUELEFT            = 'Driver::queueLeft';
	//public const HOOK_QUEUESIZE            = 'Driver::queueSize';
	public const HOOK_QUEUEREPORT          = 'Driver::queueReport';
	//public const HOOK_QUEUEVIEW            = 'Driver::queueView';
	public const HOOK_QUEUEACTIONLAST      = 'Driver::queueActionLast';
	public const HOOK_QUEUEACTIONIDENTLAST = 'Driver::queueActionIdentLast';
	public const HOOK_QUEUEEXISTS          = 'Driver::queueExists';
	public const HOOK_QUEUESTATUS          = 'Driver::queueStatus';
	public const HOOK_QUEUEGET             = 'Driver::queueGet';
	//public const HOOK_QUEUEFETCH           = 'Driver::queueFetch';
	public const HOOK_QUEUEENQUEUE         = 'Driver::queueEnqueue';
	public const HOOK_QUEUEDEQUEUE         = 'Driver::queueDequeue';
	public const HOOK_QUEUEREMOVE          = 'Driver::queueRemove';
	public const HOOK_QUEUEPROCESSING      = 'Driver::queueProcessing';
	public const HOOK_QUEUEPROCESSED       = 'Driver::queueProcessed';

	//// Recurring Action Hooks
	public const HOOK_RECURENQUEUE = 'Driver::recurEnqueue';
	public const HOOK_RECURDEQUEUE = 'Driver::recurDequeue';
	//public const HOOK_RECURVIEW    = 'Driver::recurView';

	//// Database Initialization Hooks
	public const HOOK_INITIALIZE  = 'Driver::initialize';
	public const HOOK_INITIALIZED = 'Driver::initialized';

	//// Config Table Hooks
	public const HOOK_CONFIGREAD  = 'Driver::configRead';
	public const HOOK_CONFIGWRITE = 'Driver::configWrite';


	/**
	 * An array of hooks (map of hook-types => ['userdef1', 'userdef2', ... ]
	 * @var array
	 */
	// Hooks only get set when WebConnector\Server is constructed and causes Driver initialization to fail because it is not an array
	protected $_hooks = [];

	/**
	 *
	 * @var integer
	 */
	protected $_loglevel = PackageInfo::LogLevel['NORMAL'];

	/**
	 * Constructor
	 *
	 * @param string|array|resource $dsn	A DSN-style connection string, configuration array, or a database connection
	 * @param array $config					An array of configuration information
	 */
	abstract public function __construct($dsn, array $config);

	/**
	 * Register the set of user-defined hook functions
	 */
	final public function registerHooks(array $hooks): void
	{
		foreach ($hooks as $hook => $funcs)
		{
			if (!is_array($funcs))
			{
				$funcs = [$funcs];
			}

			$this->_hooks[$hook] = $funcs;
		}
	}

	/*
	final public function connectionLoad(string $user)
	{
		$hookdata = [
			'username' => $user,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_CONNECTIONLOAD, null, $hookerr, $hookdata);

		$arr = $this->_connectionLoad($user);

		if (!empty($arr['connection_ticket']))
		{
			$crypt = EncryptionFactory::determine($arr['connection_ticket']);

			if ($crypt)
			{
				// Do the decryption...
			}
		}

		return $arr;
	}
	*/

	/**
	 * Set the logging level for the driver class and return the old log level.
	 */
	final public function setLogLevel(int $lvl): int
	{
		$old_lvl = $this->getLogLevel();

		if (!in_array($lvl, PackageInfo::LogLevel, true))
		{
			throw new \Exception('Log Level ' . $lvl . ' is not valid.  See PackageInfo::LogLevel for valid options.');
		}
		$this->_loglevel = $lvl;

		return $old_lvl;
	}
	/**
	 * Get the logging level for the driver class
	 */
	final public function getLogLevel(): int
	{
		return $this->_loglevel;
	}

	final public function noop(): bool
	{
		$hookdata = [];
		$hookerr = '';

		$this->_callHook(static::HOOK_NOOP, null, $hookerr, $hookdata);

		return $this->_noop();
	}

	abstract protected function _noop(): bool;

	/**
	 * Place an action into the queue, along with a unique identifier (if neccessary)
	 *
	 * Example:
	 * <code>
	 * 	$driver->queueEnqueue('CustomerAdd', 1234); // Push customer #1234 over to QuickBooks
	 * </code>
	 *
	 */
	final public function queueEnqueue(string $user, string $action, $ident, bool $replace = true, ?int $priority = null, ?array $extra = null, ?string $qbxml = null): bool
	{
		// Make strict typing happy and convert numbers to string
		if (is_int($ident) || is_float($ident))
		{
			$ident = (string) $ident;
		}

		if (!strlen($ident))
		{
			// If they didn't provide an $ident, generate a random, unique one
			$ident = Utilities::GUID();
		}

		// Use a reasonable queue priority if none was provided
		$priority = $priority ?? Utilities::priorityForAction($action);

		// Set $extra to an empty array if no array was provided
		$extra = $extra ?? [];

		$hookdata = [
			'username' => $user,
			'action' => $action,
			'ident' => $ident,
			'replace' => $replace,
			'priority' => $priority,
			'extra' => $extra,
			'qbxml' => $qbxml,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_QUEUEENQUEUE, null, $hookerr, $hookdata);

		return $this->_queueEnqueue($user, $action, $ident, $replace, $priority, $extra, $qbxml);
	}

	/**
	 * @see Driver::queueEnqueue()
	 */
	abstract protected function _queueEnqueue(string $user, string $action, string $ident, bool $replace, int $priority, array $extra, ?string $qbxml);

	/**
	 * Remove an item from the queue
	 *
	 * @param string $user			The user who we want to dequeue an item for
	 * @param boolean $by_priority	If TRUE, remove the item with the highest priority next
	 * @return array|null
	 */
	final public function queueDequeue(string $user, bool $by_priority = false): ?array
	{
		$hookdata = [
			'username' => $user,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_QUEUEDEQUEUE, null, $hookerr, $hookdata);

		return $this->_queueDequeue($user, $by_priority);
	}

	/**
	 * @see Driver::queueDequeue()
	 */
	abstract protected function _queueDequeue(string $user, bool $by_priority = false): ?array;

	/**
	 * Fetch the item (database row) currently being processed by QuickBooks
	 */
	final public function queueProcessing(string $user): ?array
	{
		$hookdata = [
			'username' => $user,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_QUEUEPROCESSING, null, $hookerr, $hookdata);

		return $this->_queueProcessing($user);
	}

	/**
	 * @see Driver::queueProcessing()
	 */
	abstract protected function _queueProcessing(string $user): ?array;

	/**
	 * Create a recurring event which will be queued up every so often...
	 */
	final public function recurEnqueue(string $user, $run_every, string $action, ?string $ident, bool $replace = true, ?int $priority = null, ?array $extra = null, ?string $qbxml = null): bool
	{
		// Change string time intervals into seconds (e.g. '15 minutes')
		$run_every_original = $run_every;
		$run_every = Utilities::intervalToSeconds($run_every);
		if (null === $run_every)
		{
			throw new \Exception('Cannot convert $run_every interval "' . $run_every_original .'" into seconds in ' . __METHOD__);
		}

		// Use a reasonable queue priority if none was provided
		$priority = $priority ?? Utilities::priorityForAction($action);

		// Set $extra to an empty array if no array was provided
		$extra = $extra ?? [];

		$hookdata = [
			'username' => $user,
			'interval' => $run_every,
			'action' => $action,
			'ident' => $ident,
			'replace' => $replace,
			'priority' => $priority,
			'extra' => $extra,
			'qbxml' => $qbxml,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_RECURENQUEUE, null, $hookerr, $hookdata);

		return $this->_recurEnqueue($user, $run_every, $action, $ident, $replace, $priority, $extra, $qbxml);
	}

	/**
	 * @see Driver::recurEnqueue()
	 */
	abstract protected function _recurEnqueue(string $user, int $run_every, string $action, ?string $ident, bool $replace, int $priority, array $extra, ?string $qbxml): bool;

	/**
	 * Fetch the next recurring event from the recurring event queue
	 */
	final public function recurDequeue(string $user, bool $by_priority = false)
	{
		$hookdata = [
			'username' => $user,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_RECURDEQUEUE, null, $hookerr, $hookdata);

		return $this->_recurDequeue($user, $by_priority);
	}

	/**
	 * @see Driver::recurDequeue()
	 */
	abstract protected function _recurDequeue(string $user, bool $by_priority = false);

	/**
	 *
	 *
	 *
	 */
	final public function configWrite(string $user, string $module, string $key, $value, ?string $type = null, ?array $opts = null): bool
	{
		//$module = strtolower($module);

		$hookdata = [
			'username' => $user,
			'module' => $module,
			'key' => $key,
			'value' => $value,
			'type' => $type,
			'opts' => $opts,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_CONFIGWRITE, null, $hookerr, $hookdata);

		return $this->_configWrite($user, $module, $key, $value, $type, $opts);
	}

	/**
	 * @see Driver::configWrite()
	 */
	abstract protected function _configWrite(string $user, string $module, string $key, $value, ?string $type, ?array $opts): bool;

	/**
	 *
	 *
	 *
	 */
	final public function configRead(string $user, string $module, string $key, ?string &$type, ?array &$opts)
	{
		//$module = strtolower($module);

		$hookdata = [
			'username' => $user,
			'module' => $module,
			'key' => $key,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_CONFIGREAD, null, $hookerr, $hookdata);

		return $this->_configRead($user, $module, $key, $type, $opts);
	}

	/**
	 * @see Driver::configRead()
	 */
	abstract protected function _configRead(string $user, string $module, string $key, ?string &$type, ?array &$opts);

	/**
	 * Forcibly remove an item from the queue
	 *
	 * @param string $user
	 * @param string $action
	 * @param mixed $ident
	 * @return boolean
	 */
	final public function queueRemove(string $user, string $action, string $ident)
	{
		$hookdata = [
			'username' => $user,
			'action' => $action,
			'ident' => $ident
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_QUEUEREMOVE, null, $hookerr, $hookdata);

		return $this->_queueRemove($user, $action, $ident);
	}

	/**
	 * @see Driver::queueRemove()
	 */
	abstract protected function _queueRemove(string $user, string $action, string $ident);

	/**
	 * Update the status of a particular item in the queue
	 *
	 * @param string $ticket		The ticket of the process which is updating the status
	 * @param int $requestID		The requestID (QUEUE.quickbooks_queue_id)
	 * @param string $new_status		The new status code (PackageInfo::Status['SUCCESS'], PackageInfo::Status['ERROR'], etc.)
	 * @param string $msg			An error message (if an error message occurred)
	 * @return boolean
	 */
	final public function queueStatus(string $ticket, int $requestID, string $new_status, string $msg = ''): bool
	{
		$user = $this->_authResolve($ticket);

		$hookdata = [
			'username' => $user,
			//'action' => $action,
			//'ident' => $ident,
			'requestID' => $requestID,
			'status' => $new_status,
			'message' => $msg,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_QUEUESTATUS, $ticket, $hookerr, $hookdata);

		//return $this->_queueStatus($ticket, $action, $ident, $new_status, $msg);
		return $this->_queueStatus($ticket, $requestID, $new_status, $msg);
	}

	/**
	 * @see Driver::queueStatus()
	 */
	abstract protected function _queueStatus(string $ticket, int $requestID, string $new_status, string $msg = ''): bool;

	final public function queueGet(string $user, int $requestID, ?string $status = PackageInfo::Status['QUEUED']): ?array
	{
		//$user = $this->_authResolve($ticket);

		$hookdata = [
			'username' => $user,
			//'action' => $action,
			//'ident' => $ident,
			'requestID' => $requestID,
			'status' => $status,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_QUEUEGET, null, $hookerr, $hookdata);

		//return $this->_queueStatus($ticket, $action, $ident, $new_status, $msg);
		return $this->_queueGet($user, $requestID, $status);
	}

	/**
	 * @see Driver::queueStatus()
	 */
	abstract protected function _queueGet(string $user, int $requestID, ?string $status = PackageInfo::Status['QUEUED']): ?array;

	/**
	 * Tell the number of queued items left in the queue for a given user
	 */
	final public function queueLeft(string $user, bool $queued = true): int
	{
		$hookdata = [
			'username' => $user,
			'queued' => $queued,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_QUEUELEFT, null, $hookerr, $hookdata);

		return $this->_queueLeft($user, $queued);
	}

	/**
	 * @see Driver::queueLeft()
	 */
	abstract protected function _queueLeft(string $user, bool $queued = true): int;

	/**
	 * Get a list of records from the queue for use in a report
	 */
	final public function queueReport(string $user, ?string $date_from, ?string $date_to, int $offset = 0, int $limit = 0): array
	{
		$offset = max(0, $offset);
		$limit = min(999999999, $limit);

		$hookdata = [
			'offset' => $offset,
			'limit' => $limit,
			'from' => $date_from,
			'to' => $date_to,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_QUEUEREPORT, null, $hookerr, $hookdata);

		return $this->_queueReport(string, $date_from, $date_to, $offset, $limit);
	}

	/**
	 * @see Driver::queueReport()
	 */
	abstract protected function _queueReport(string $user, ?string $date_from, ?string $date_to, int $offset, int $limit): array;

	/**
	 * Tell how many commands have been processed during this login session
	 */
	final public function queueProcessed(string $ticket): ?int
	{
		$hookdata = [];
		$hookerr = '';
		$this->_callHook(static::HOOK_QUEUEPROCESSED, $ticket, $hookerr, $hookdata);

		return $this->_queueProcessed($ticket);
	}

	/**
	 * @see Driver::queueProcessed()
	 */
	abstract protected function _queueProcessed(string $ticket): ?int;

	/**
	 * Tell whether or not an item exists in the queue
	 */
	final public function queueExists(string $user, string $action, $ident): ?array
	{
		$ident = (string) $ident;

		$hookdata = [
			'username' => $user,
			'action' => $action,
			'ident' => $ident,
		];
		$hookerr = '';

		$this->_callHook(static::HOOK_QUEUEEXISTS, null, $hookerr, $hookdata);

		return $this->_queueExists($user, $action, $ident);
	}

	/**
	 * @see Driver::queueExists()
	 */
	abstract protected function _queueExists(string $user, string $action, string $ident): ?array;


	/**
	 * Log an error that occurred for a specific ticket
	 */
	final public function errorLog(string $ticket, ?int $errno, ?string $errstr): bool
	{
		$hookdata = [
			'errno' => $errno,
			'errstr' => $errstr,
		];
		$hookerr = '';
		$this->_callHook(static::HOOK_ERRORLOG, $ticket, $hookerr, $hookdata);

		return $this->_errorLog($ticket, $errno, $errstr);
	}

	/**
	 * @see Driver::errorLog()
	 */
	abstract protected function _errorLog(string $ticket, int $errno, string $errstr): bool;

	/**
	 * Get the last error that occurred
	 */
	final public function errorLast(string $ticket): string
	{
		$hookerr = '';
		$this->_callHook(static::HOOK_ERRORLAST, $ticket, $hookerr, []);

		return $this->_errorLast($ticket);
	}

	/**
	 * @see Driver::errorLast()
	 */
	abstract protected function _errorLast(string $ticket): string;

	/**
	 * Establish a session for a user (log that user in)
	 *
	 * The QuickBooks Web Connector will pass a username and password to the
	 * SOAP server. There is a SOAP ->authenticate() method which logs the user
	 * in.
	 *
	 * @param string $username		The username for the QuickBooks Web Connector user
	 * @param string $password		The password for the QuickBooks Web Connector user
	 * @param boolean $override		If set to TRUE, a correct password will not be required
	 * @return string				The ticket for the login session
	 */
	final public function authLogin(string $username, string $password, ?string &$company_file, ?int &$wait_before_next_update, ?int &$min_run_every_n_seconds, bool $override = false): ?string
	{
		$hookdata = [
			'username' => $username,
			'password' => $password,
			'override' => $override,
		];
		$err = '';
		$this->_callHook(static::HOOK_AUTHLOGIN, null, $err, $hookdata);

		return $this->_authLogin($username, $password, $company_file, $wait_before_next_update, $min_run_every_n_seconds, $override);
	}

	/**
	 * @see Driver::authLogin()
	 */
	abstract protected function _authLogin(string $username, string $password, ?string &$company_file, ?int &$wait_before_next_update, ?int &$min_run_every_n_seconds, bool $override = false): ?string;

	/**
	 * Return the default QuickBooks user's username
	 */
	final public function authDefault(): string
	{
		$err = '';
		$this->_callHook(static::HOOK_AUTHDEFAULT, null, $err, []);

		return $this->_authDefault();
	}

	/**
	 * @see Driver::authDefault()
	 */
	abstract protected function _authDefault(): string;

	/**
	 * Resolve a ticket string to a QuickBooks username
	 */
	final public function authResolve(string $ticket): ?string
	{
		$err = '';
		$this->_callHook(static::HOOK_AUTHRESOLVE, $ticket, $err, []);

		return $this->_authResolve($ticket);
	}

	/**
	 * @see Driver::authResolve()
	 */
	abstract protected function _authResolve(string $ticket): ?string;

	/**
	 * Get the last date/time stamp the user logged in
	 *
	 * @param string $username		The username of the user
	 * @return array				An array containing two date/time stamps in YYYY-MM-DD HH:II:SS format, one to indicate login time, and one to indicate log out time
	 */
	final public function authLast(string $username)
	{
		$ticket = null;
		$err = '';
		$this->_callHook(static::HOOK_AUTHLAST, $ticket, $err, []);

		return $this->_authLast($username);
	}

	/**
	 * @see Driver::authLast()
	 */
	abstract protected function _authLast(string $username);

	/**
	 * Check to see whether or not a ticket is for a valid, unexpired login session
	 *
	 * @param string $ticket	The login session ticket to check
	 * @return boolean 			Whether or not the ticket is valid
	 */
	final public function authCheck(string $ticket): bool
	{
		$err = '';
		$this->_callHook(static::HOOK_AUTHCHECK, $ticket, $err, []);

		return $this->_authCheck($ticket);
	}

	/**
	 * @see Driver::authCheck()
	 */
	abstract protected function _authCheck(string $ticket): bool;

	/**
	 * End a log-in session
	 */
	final public function authLogout(string $ticket): bool
	{
		$err = '';
		$this->_callHook(static::HOOK_AUTHLOGOUT, $ticket, $err, []);

		return $this->_authLogout($ticket);
	}

	/**
	 * @see Driver::authLogout()
	 */
	abstract protected function _authLogout(string $ticket): bool;

	/**
	 * Check if a user exists for the SOAP server.
	 *
	 * @return array|null	Returns null if the user does not exist and the USER table record as an array if it does
	 */
	public function authExists(string $username, array $restrict = []): ?array
	{
		return $this->_authExists($username, $restrict);
	}

	/**
	 * @see Driver::authExists()
	 */
	abstract protected function _authExists(string $username, array $restrict = []): ?array;

	/**
	 * Create a user account with the given username and password
	 */
	final public function authCreate(string $username, string $password, ?string $company_file = '', ?int $wait_before_next_update = 0, ?int $min_run_every_n_seconds = 0): bool
	{
		// Use default '' and 0 for null arguments to appease declare(strict_types=1)
		$company_file = $company_file ?? '';
		$wait_before_next_update = $wait_before_next_update ?? 0;
		$min_run_every_n_seconds = $min_run_every_n_seconds ?? 0;

		$hookdata = [
			'username' => $username,
			'password' => $password,
			'qb_company_file' => $company_file,
			'qbwc_wait_before_next_update' => $wait_before_next_update,
			'qbwc_min_run_every_n_seconds' => $min_run_every_n_seconds,
		];
		$err = '';
		$this->_callHook(static::HOOK_AUTHCREATE, null, $err, $hookdata);

		return $this->_authCreate($username, $password, $company_file, $wait_before_next_update, $min_run_every_n_seconds);
	}

	/**
	 * @see Driver::authCreate()
	 */
	abstract protected function _authCreate(string $username, string $password, string $company_file, int $wait_before_next_update, int $min_run_every_n_seconds): bool;

	/**
	 * Enable a username
	 */
	final public function authEnable(string $username): bool
	{
		$hookdata = [
			'username' => $username,
		];
		$err = '';
		$this->_callHook(static::HOOK_AUTHENABLE, null, $err, $hookdata);

		return $this->_authEnable($username);
	}

	/**
	 * @see Driver::authEnable()
	 */
	abstract protected function _authEnable(string $username): bool;

	/**
	 * Disable a username
	 */
	final public function authDisable(string $username): bool
	{
		$hookdata = [
			'username' => $username,
		];
		$err = '';
		$this->_callHook(static::HOOK_AUTHDISABLE, null, $err, $hookdata);

		return $this->_authDisable($username);
	}

	/**
	 * @see Driver::authDisable()
	 */
	abstract protected function _authDisable(string $username);

	/**
	 * Initialize the driver class
	 */
	public function initialize(array $options): bool
	{
		$hookdata = [
			'options' => $options,
		];
		$err = '';
		$this->_callHook(static::HOOK_INITIALIZE, null, $err, $hookdata);

		return $this->_initialize($options);
	}

	/**
	 * @see Driver::initialize()
	 */
	abstract protected function _initialize(array $options = []): bool;

	/**
	 * Tell whether or not the driver class has been initialized
	 */
	public function initialized(): bool
	{
		$hookdata = [];
		$err = '';
		$this->_callHook(static::HOOK_INITIALIZED, null, $err, $hookdata);

		return $this->_initialized();
	}

	/**
	 * @see Driver::initialized()
	 */
	abstract protected function _initialized(): bool;

/*
	public function oauthLoadV1($key, $app_tenant)
	{
		if ($data = $this->_oauthLoadV1($app_tenant))
		{
			if (strlen($data['oauth_access_token']) > 0)
			{
				$AES = EncryptionFactory::create('aes');

				$data['oauth_access_token'] = $AES->decrypt($key, $data['oauth_access_token']);
				$data['oauth_access_token_secret'] = $AES->decrypt($key, $data['oauth_access_token_secret']);
			}

			return $data;
		}

		return false;
	}

	abstract protected function _oauthLoadV1($app_tenant);
*/
	public function oauthLoadV2(string $key, string $app_tenant)
	{
		$data = $this->_oauthLoadV2($app_tenant);
		if ($data)
		{
			if (!empty($data['oauth_access_token']))
			{
				$Encryption = EncryptionFactory::create('sodium');

				$data['oauth_access_token'] = $Encryption->decrypt($key, $data['oauth_access_token']);
				$data['oauth_refresh_token'] = $Encryption->decrypt($key, $data['oauth_refresh_token']);
			}

			return $data;
		}

		return false;
	}

	abstract protected function _oauthLoadV2(string $app_tenant);
/*
	public function oauthAccessWriteV1($key, $request_token, $token, $token_secret, $realm, $flavor)
	{
		$AES = EncryptionFactory::create('aes');

		$encrypted_token = $AES->encrypt($key, $token);
		$encrypted_token_secret = $AES->encrypt($key, $token_secret);

		return $this->_oauthAccessWriteV1($request_token, $encrypted_token, $encrypted_token_secret, $realm, $flavor);
	}

	abstract protected function _oauthAccessWriteV1($request_token, $token, $token_secret, $realm, $flavor);
*/
	public function oauthAccessWriteV2(string $encryption_key, string $state, string $access_token, string $refresh_token, $access_expiry, $refresh_expiry, string $qb_realm)
	{
		$Encryption = EncryptionFactory::create('sodium');

		$encrypted_access_token = $Encryption->encrypt($encryption_key, $access_token);
		$encrypted_refresh_token = $Encryption->encrypt($encryption_key, $refresh_token);

		return $this->_oauthAccessWriteV2($state, $encrypted_access_token, $encrypted_refresh_token, $access_expiry, $refresh_expiry, $qb_realm);
	}

	abstract protected function _oauthAccessWriteV2(string $state, string $access_token, string $refresh_token, $access_expiry, $refresh_expiry, string $qb_realm);


	public function oauthAccessRefreshV2(string $encryption_key, $oauthv2_id, string $access_token, string $refresh_token, $access_expiry, $refresh_expiry)
	{
		$Encryption = EncryptionFactory::create('sodium');

		$encrypted_access_token = $Encryption->encrypt($encryption_key, $access_token);
		$encrypted_refresh_token = $Encryption->encrypt($encryption_key, $refresh_token);

		return $this->_oauthAccessRefreshV2($oauthv2_id, $encrypted_access_token, $encrypted_refresh_token, $access_expiry, $refresh_expiry);
	}

	abstract protected function _oauthAccessRefreshV2($oauthv2_id, string $access_token, string $refresh_token, $access_expiry, $refresh_expiry);

	public function oauthAccessDelete(string $app_username, $app_tenant)
	{
		return $this->_oauthAccessDelete($app_username, $app_tenant);
	}

	abstract protected function _oauthAccessDelete(string $app_username, string $app_tenant);

	public function oauthRequestWriteV2(string $app_tenant, string $state)
	{
		return $this->_oauthRequestWriteV2($app_tenant, $state);
	}

	abstract protected function _oauthRequestWriteV2(string $app_tenant, string $state);
/*
	public function oauthRequestWriteV1($app_tenant, $token, $token_secret)
	{
		return $this->_oauthRequestWriteV1($app_tenant, $token, $token_secret);
	}

	abstract protected function _oauthRequestWriteV1($app_tenant, $token, $token_secret);

	public function oauthRequestResolveV1($token)
	{
		return $this->_oauthRequestResolveV1($token);
	}

	abstract protected function _oauthRequestResolveV1($token);
*/
	public function oauthRequestResolveV2(string $state)
	{
		return $this->_oauthRequestResolveV2($state);
	}

	abstract protected function _oauthRequestResolveV2(string $state);

	/**
	 * Log a message to the QuickBooks log
	 *
	 * @param string $msg		The message to place in the log
	 * @param string $ticket	The ticket for the login session
	 * @param integer $lvl
	 * @return boolean 			Whether or not the message was logged successfully
	 */
	final public function log(string $msg, ?string $ticket = null, int $lvl = PackageInfo::LogLevel['NORMAL']): bool
	{
		/*
		$hookdata = [
			'message' => $msg,
			'level' => $lvl,
		];
		$err = '';
		$this->_callHook(static::HOOK_LOG, $ticket, $err, $hookdata);
		*/
		if (is_null($lvl) || $this->_loglevel >= $lvl)
		{
			return $this->_log($msg, $ticket, $lvl);
		}

		return true;
	}

	/**
	 * @see Driver::log()
	 */
	abstract protected function _log(string $msg, ?string $ticket = null, int $lvl = PackageInfo::LogLevel['NORMAL']);

	/**
	 *
	 *
	 * @param integer $offset
	 * @param integer $limit
	 * @param string $match
	 * @return QuickBooks_Iterator
	 */
	/*final public function logView($offset, $limit, $match = '')
	{
		$offset = max(0, (int) $offset);
		$limit = max(1, (int) $limit);

		$hookdata = [
			'offset' => $offset,
			'limit' => $limit,
			'match' => $match,
		];
		$err = '';
		$this->_callHook(static::HOOK_LOGVIEW, null, $err, $hookdata);

		return $this->_logView($offset, $limit, $match);
	}*/

	/**
	 * @see Utilities::logView()
	 */
	/*abstract protected function _logView($offset, $limit, $match);*/

	/*final public function logSize($match = '')
	{
		$hookdata = [
			'match' => $match,
		];
		$err = '';
		$this->_callHook(static::HOOK_LOGSIZE, null, $err, $hookdata);

		return $this->_logSize($match);
	}*/

	/*abstract protected function _logSize($match);*/

	/**
	 * One-way hash a password for storage in the database
	 */
	final protected function _hash(string $password): string
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}

	/**
	 * Call any user-defined hooks hooked into a particular type of event
	 *
	 * Hooks will be executed in the order they were added in. If any hook
	 * returns FALSE, then execution for that type of hook will be stopped and
	 * no other hooks will run. Errors reported via the $err parameter will be
	 * logged using the driver logging mechanism.
	 *
	 * @param string $hook			The type of hook we're to execute
	 * @param string $ticket		The Web Connector ticket
	 * @param string $err			Any error messages that should be reported
	 * @param array $hook_data		An array of hook data
	 * @return boolean
	 */
	final protected function _callHook(string $hook, ?string $ticket, string &$err, array $hook_data): bool
	{
		$user = '';
		$ticket = $ticket ?? '';
		if (strlen($ticket) > 0)
		{
			$user = (string) $this->_authResolve($ticket);
		}

		// Use a null requestID since this was a server generated request and not due to a request from WebConnector
		$requestID = null;

		// Call the hook
		$called = Callbacks::callHook($this, $this->_hooks, $hook, $requestID, $user, $ticket, $err, $hook_data);

		if ($err)
		{
			// Log errors reported by hook
			$this->errorLog($ticket, PackageInfo::Error['HOOK'], $err);

			return false;
		}

		return true;
	}
}
