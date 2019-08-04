<?php declare(strict_types=1);

/**
 * Handlers for each of the QBWC SOAP server required methods
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * The QuickBooks Web Connector requires that your SOAP server be able to
 * handle six basic methods. Each of the six methods are implemented in this
 * class and called by the WebConnector\Server class instance.
 *
 * These methods in turn will call the action handlers you register with the
 * SOAP server, and also log quite a bit of debugging information to the
 * database so that you can see what's happening during the QBWC exchange with
 * your SOAP server.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Server
 */

namespace QuickBooksPhpDevKit\WebConnector;

use \stdClass;														// PHP's standard class object (holds the data sent by the client's Web Connector)
use QuickBooksPhpDevKit\Callbacks;									// Functions for calling callbacks (functions, static methods, object methods, etc.)
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\WebConnector\Request;
use QuickBooksPhpDevKit\WebConnector\Result;
use QuickBooksPhpDevKit\WebConnector\Result\Authenticate;			// Response container for calls to ->authenticate()
use QuickBooksPhpDevKit\WebConnector\Result\ClientVersion;			// Response container for calls to ->clientVersion()
use QuickBooksPhpDevKit\WebConnector\Result\CloseConnection;		// Response container for calls to ->closeConnection()
use QuickBooksPhpDevKit\WebConnector\Result\ConnectionError;		// Response container for calls to ->connectionError()
use QuickBooksPhpDevKit\WebConnector\Result\GetInteractiveURL;		// Response container for calls to ->getInteractiveURL()
use QuickBooksPhpDevKit\WebConnector\Result\GetLastError;			// Response container for calls to ->getLastError()
use QuickBooksPhpDevKit\WebConnector\Result\InteractiveDone;		// Response container for calls to ->interactiveDone()
use QuickBooksPhpDevKit\WebConnector\Result\InteractiveRejected;	// Response container for calls to ->interactiveRejected()
use QuickBooksPhpDevKit\WebConnector\Result\ReceiveResponseXML;		// Response container for calls to ->receiveResponseXML()
use QuickBooksPhpDevKit\WebConnector\Result\SendRequestXML;			// Response container for calls to ->sendRequestXML()
use QuickBooksPhpDevKit\WebConnector\Result\ServerVersion;			// Response container for calls to ->serverVersion()
use QuickBooksPhpDevKit\Utilities;

/**
 * Handlers for each of the QBWC SOAP server required methods
 */
class Handlers
{
	/**
	 * Hook which is called when a login succeeds
	 */
	public const HOOK_LOGINSUCCESS = 'Handlers::login-success';
	/**
	 * Hook which is called when a login fails
	 */
	public const HOOK_LOGINFAILURE = 'Handlers::login-fail';
	public const HOOK_LOGINFAIL    = 'Handlers::login-fail';


	/**
	 * Hook which gets called when the ->authenticate() method gets called
	 */
	public const HOOK_AUTHENTICATE = 'Handlers::authenticate';
	/**
	 * Hook which gets called when the ->clientVersion() method gets called
	 */
	public const HOOK_CLIENTVERSION = 'Handlers::clientVersion';
	/**
	 * Hook which gets called when the ->serverVersion() method gets called
	 */
	public const HOOK_SERVERVERSION = 'Handlers::serverVersion';
	/**
	 * Hook which gets called when the ->closeConnection() method gets called
	 */
	public const HOOK_CLOSECONNECTION = 'Handlers::closeConnection';
	/**
	 * Hook which gets called when the ->connectionError() method gets called
	 */
	public const HOOK_CONNECTIONERROR = 'Handlers::connectionError';
	/**
	 * Hook which gets called when the ->getLastError() method gets called
	 */
	public const HOOK_GETLASTERROR = 'Handlers::getLastError';

	/**
	 * Hook which gets called when the ->receiveResponseXML() method gets called
	 */
	public const HOOK_RECEIVERESPONSEXML = 'Handlers::receiveResponseXML';
	/**
	 * Hook which gets called when the ->sendRequestXML() method gets called
	 */
	public const HOOK_SENDREQUESTXML = 'Handlers::sendRequestXML';

	/**
	 * Hook which is called when recurring events are registered
	 */
	public const HOOK_RECURRING = 'Handlers::recurring';

	/**
	 * Hooks called for Interactive mode (not implemented)
	 */
	public const HOOK_GETINTERACTIVEURL = 'Handlers::getInteractiveURL';
	public const HOOK_INTERACTIVEDONE = 'Handlers::interactiveDone';
	public const HOOK_INTERACTIVEREJECTED = 'Handlers::interactiveRejected';

	/**
	 * Hook which is called to report a percentage completed
	 */
	public const HOOK_PERCENT = 'Handlers::percent';




	/**
	 * Driver object instance for backend of SOAP server
	 * @var Driver
	 */
	protected $_driver;

	/**
	 * Map of queued actions to function handlers
	 * @var array
	 */
	protected $_map;

	/**
	 *
	 *
	 */
	protected $_instance_map;

	/**
	 * Map of error codes to function handler
	 * @var array
	 */
	protected $_onerror;

	/**
	 *
	 *
	 */
	protected $_instance_onerror;

	/**
	 * Map of hook names to function handlers
	 * @var array
	 */
	protected $_hooks;

	/**
	 *
	 *
	 */
	protected $_instance_hooks;

	/**
	 * Configuration parameters
	 * @var array
	 */
	protected $_config;

	/**
	 * Callback configuration parameters
	 * @var array
	 */
	protected $_callback_config;

	/**
	 * Create the server handler instance
	 *
	 * Optional configuration items should be passed as an associative array with any of these keys:
	 * 	- qb_company_file				The full filesystem path to a specific QuickBooks company file (by default, it will use the currently open company file)
	 * 	- qbwc_min_version				Minimum version of the Web Connector that must be used to connect (by default, any version may connect)
	 * 	- qbwc_wait_before_next_update 	Tell the Web Connector to wait this number of seconds before doign another update
	 * 	- qbwc_min_run_every_n_seconds	Tell the Web Connector to run every n seconds (overrides whatever was in the .QWC web connector configuration file)
	 * 	- qbwc_interactive_url			The URL to use for Interactive QuickBooks Web Connector sessions
	 * 	- server_version				Server version string
	 * 	- authenticate_handler			If you want to use some custom authentication method, put the function name of your custom authentication function here
	 * 	- autoadd_missing_requestid		This defaults to TRUE, if TRUE and you forget to embed a requestID="..." attribute, it will try to automatically add that attribute for you
	 *
	 * @param mixed $dsn_or_conn		DSN connection string for QuickBooks queue
	 * @param array $map				A map of QuickBooks API calls to callback functions/methods
	 * @param array $onerror			A map of QuickBooks error codes to callback functions/methods
	 * @param array $hooks				A map of hook names to callback functions/methods
	 * @param string $input				Raw XML input from QuickBooks API call
	 * @param array $handler_config		An array of configuration options
	 * @param array $driver_config		An array of driver configuration options
	 */
	public function __construct($dsn_or_conn, array $map, array $onerror, array $hooks, int $log_level, string $UNUSED_input, array $handler_config = [], array $driver_config = [], array $callback_config = [])
	{
		$this->_driver = Utilities::driverFactory($dsn_or_conn, $driver_config, $hooks, $log_level);
		$this->_map = $map;
		$this->_onerror = $onerror;

		$this->_hooks = [];
		foreach ($hooks as $hook => $funcs)
		{
			if (!is_array($funcs))
			{
				$funcs = [$funcs];
			}

			$this->_hooks[$hook] = $funcs;
		}

		$this->_config = $this->_defaults($handler_config);

		$this->_callback_config = $callback_config;

		$this->_log('Handler is starting up NOW...: ' . print_r($this->_config, true), '', PackageInfo::LogLevel['DEBUG']);
	}

	/**
	 * Massage any optional configuration flags
	 */
	protected function _defaults(array $config)
	{
		$defaults = [
			'qb_company_file' => null,					// To force a specific company file to be used
			'qbwc_min_version' => null, 				// Minimum version of the QBWC that must be used to connect
			'qbwc_wait_before_next_update' => null, 	// Tell the QBWC to wait this number of seconds before doing another update
			'qbwc_min_run_every_n_seconds' => null,		// Tell the QBWC to run every n seconds (overrides whatever was in the .QWC web connector configuration file)
			'qbwc_version_warning_message' => null,		// Not implemented...
			'qbwc_version_error_message' => null,  		// Not implemented...
			'qbwc_interactive_url' => null, 			// Provide the URL for an interactive session to the QuickBooks Web Connector
			'autoadd_missing_requestid' => true,
			'check_valid_requestid' => true,
			'server_version' => '',						// Server version string
			'authenticate' => null, 					// If you want to use some custom authentication scheme (and not the quickbooks_user MySQL table) you can specify your own function here
			'authenticate_dsn' => null, 				//		(backward compat. for 'authenticate')
			'map_application_identifiers' => true, 		// Try to map web application IDs to QuickBooks ListIDs/TxnIDs
			'allow_remote_addr' => [],
			'deny_remote_addr' => [],
			'convert_unix_newlines' => true,

			'deny_concurrent_logins' => true,
			'deny_concurrent_timeout' => 60,

			'deny_reallyfast_logins' => true,
			'deny_reallyfast_timeout' => 600,

			'masking' => true,
		];

		$config = array_merge($defaults, $config);

		// Make sure this is an *array* of addresses to allow
		if (!is_array($config['allow_remote_addr']))
		{
			$config['allow_remote_addr'] = array( $config['allow_remote_addr'] );
		}

		// Make sure this is an *array* of addresses to deny
		if (!is_array($config['deny_remote_addr']))
		{
			$config['deny_remote_addr'] = array( $config['deny_remote_addr'] );
		}

		$config['autoadd_missing_requestid'] = (boolean) $config['autoadd_missing_requestid'];
		$config['check_valid_requestid'] = (boolean) $config['check_valid_requestid'];
		$config['map_application_identifiers'] = (boolean) $config['map_application_identifiers'];
		$config['convert_unix_newlines'] = (boolean) $config['convert_unix_newlines'];

		$config['deny_concurrent_logins'] = (boolean) $config['deny_concurrent_logins'];
		$config['deny_concurrent_timeout'] = (int) max(1, $config['deny_concurrent_timeout']);

		$config['deny_reallyfast_logins'] = (boolean) $config['deny_reallyfast_logins'];
		$config['deny_reallyfast_timeout'] = (int) max(1, $config['deny_reallyfast_timeout']);

		return $config;
	}

	/**
	 * Check if a given remote address (IP address) is allowed based on allow and deny arrays
	 */
	protected function _checkRemote(string $remoteaddr, array $arr_allow, array $arr_deny): bool
	{
		return Utilities::checkRemoteAddress($remoteaddr, $arr_allow, $arr_deny);
	}

	/**
	 * Log a message to the error/debug log
	 */
	protected function _log(string $msg, string $ticket, int $level = PackageInfo::LogLevel['NORMAL']): bool
	{
		if ($this->_config['masking'])
		{
			$msg = Utilities::mask($msg);
		}

		if ($this->_driver)
		{
			return $this->_driver->log($msg, $ticket, $level);
		}

		return false;
	}

	/**
	 * Queue up recurring events that are overdue to be run
	 */
	protected function _handleRecurringEvents(string $ticket): bool
	{
		$user = $this->_driver->authResolve($ticket);
		if ($user)
		{
			while ($next = $this->_driver->recurDequeue($user, true))
			{
				$this->_log('Dequeued a recurring event, enqueuing!', $ticket, PackageInfo::LogLevel['VERBOSE']);

				//print_r($next);

				$hookerr = '';
				$this->_callHook($ticket,
					static::HOOK_RECURRING,
					null,
					$next['qb_action'],
					$next['ident'],
					$next['extra'],
					$hookerr);
				// $ticket, $hook, $requestID, $action, $ident, $extra, &$err, $xml = '', $qb_identifiers = []

				//print_r($next);
				//exit;

				// (boolean) $next['replace']
				// 							$user, $action, $ident, $replace = true, $priority = 0, $extra = null, $qbxml = null
				$this->_driver->queueEnqueue($user, $next['qb_action'], $next['ident'], true, $next['priority'], $next['extra'], $next['qbxml']);
			}

			return true;
		}

		return false;
	}

	/**
	 * QuickBooks Web Connector ->serverVersion() SOAP method
	 *
	 * This is the 1st method the client's Web Connector will call each time it runs.
	 *
	 * The following user-defined hooks are invoked:
	 * 	- static::HOOK_SERVERVERSION
	 */
	public function serverVersion($obj): ServerVersion
	{
		$this->_log('serverVersion()', '', PackageInfo::LogLevel['VERBOSE']);

		$hookdata = [];
		$hookerr = '';
		$this->_callHook(null, static::HOOK_SERVERVERSION, null, null, null, null, $hookerr, null, [], $hookdata);

		return new ServerVersion($this->_config['server_version']);
	}

	/**
	 * QuickBooks Web Connector ->clientVersion() SOAP method - Receive the QuickBooks Web Connector client version (and, if neccessary, act on it)
	 *
	 * This is the 2nd method the client's Web Connector will call each time it runs.
	 *
	 * This is an *optional* method, and not all versions of the QuickBooks Web
	 * Connector will support this method. It doesn't really even *need* to be
	 * implemented, but PHP will dump notices in our error log if we don't
	 * implement it and then the web connector tries to call it.
	 *
	 * The stdClass object passed as a parameter will have the following members:
	 * 	- strVersion	A string version code indicating the version of the QuickBooks Web Connector that's being used
	 *
	 * The one member of the returned object should be:
	 * 	- The empty string to tell the web connector to continue with the update
	 * 	- A string that begins with "W:" to display a warning message to the end-user, specify the warning message after the "W:"
	 * 	- A string that begins with "E:" to display an error message to the end-user, specify the error message after the "E:"
	 * 	- A string that begins with "O:" (as in OKAY) to tell the end-user that the server expects a newer version of the web connector, specify the minimum required version after the "O:"
	 *
	 * The following user-defined hooks are invoked:
	 * 	- static::HOOK_CLIENTVERSION
	 */
	public function clientVersion($obj): ClientVersion
	{
		$this->_log('clientVersion()', '', PackageInfo::LogLevel['VERBOSE']);

		$hookdata = [];
		$hookerr = '';
		$this->_callHook(null, static::HOOK_CLIENTVERSION, null, null, null, null, $hookerr, null, [], $hookdata);

		if (!is_null($this->_config['qbwc_min_version']))
		{
			if (version_compare($obj->strVersion, $this->_config['qbwc_min_version'], '<'))
			{
				$this->_log('Version Requirement, current: ' . $obj->strVersion . ', required: ' . $this->_config['qbwc_min_version'], '', PackageInfo::LogLevel['NORMAL']);

				return new ClientVersion('O:' . $this->_config['qbwc_min_version']);
			}
		}

		return new ClientVersion('');
	}

	/**
	 * Authenticate method for the QuickBooks Web Connector SOAP service
	 *
	 * This is the 3rd method the client's Web Connector will call each time it runs.
	 *
	 * The authenticate method is called when the Web Connector establishes a
	 * connection with the SOAP server in order to ensure that there is work to
	 * do and that the Web Connector is allowed to connect/that it actually is
	 * the Web Connector that is connecting and sending us messages.
	 *
	 * The stdClass object that is received as a parameter will have two
	 * members:
	 * 	- strUserName	The username provided in the QWC file to the Web Connector
	 * 	- strPassword 	The password the end-user enters into the QuickBooks Web Connector application
	 *
	 * The return object should be an array with two elements. The first
	 * element is a generated login ticket (or an empty string if the login
	 * failed) and the second string is either "none" (for successful log-ins
	 * with nothing to do in the queue) or "nvu" if the login failed.
	 *
	 * The following user-defined hooks are invoked:
	 * 	- static::HOOK_AUTHENTICATE
	 * 	- static::HOOK_LOGINSUCCESS
	 * 	- static::HOOK_LOGINFAILURE
	 */
	public function authenticate($obj): Authenticate
	{
		$this->_log('authenticate()', '', PackageInfo::LogLevel['VERBOSE']);

		$ticket = '';
		$status = '';

		// Authenticate login hook
		$hookdata = [
			'username' => $obj->strUserName,
			'password' => $obj->strPassword,
		];
		$hookerr = '';
		$this->_callHook($ticket, static::HOOK_AUTHENTICATE, null, null, null, null, $hookerr, null, [], $hookdata);

		// Remote address allow/deny
		if (false == $this->_checkRemote($_SERVER['REMOTE_ADDR'], $this->_config['allow_remote_addr'], $this->_config['deny_remote_addr']))
		{
			$this->_log('Connection from remote address rejected: ' . $_SERVER['REMOTE_ADDR'], null, PackageInfo::LogLevel['VERBOSE']);

			return new Authenticate('', 'nvu', null, null);
		}

		// If we do either concurrent login checks, or rate-limiting, we need to grab the date/time
		//	of the last connection.
		$authLast = null;
		if ($this->_config['deny_concurrent_logins'] || $this->_config['deny_reallyfast_logins'])
		{
			$authlast = $this->_driver->authLast($obj->strUserName);
		}

		// Check for concurrent logins
		if ($this->_config['deny_concurrent_logins'])
		{
			if ($authlast &&
				time() - strtotime($authlast[1]) < $this->_config['deny_concurrent_timeout'])
			{
				$this->_log('Denied concurrent login from: ' . $obj->strUserName, null, PackageInfo::LogLevel['VERBOSE']);

				return new Authenticate('CONC1234', 'none', null, null);
			}
		}

		// Rate-limiting
		if ($this->_config['deny_reallyfast_logins'])
		{
			if ($authlast &&
				time() - strtotime($authlast[1]) < $this->_config['deny_reallyfast_timeout'])
			{
				$this->_log('Denied really fast login from: ' . $obj->strUserName . ' (last login: ' . $authlast[1] . ')', null, PackageInfo::LogLevel['VERBOSE']);

				return new Authenticate('FAST1234', 'none', null, null);
			}
		}

		// Custom authentication backends
		$override_dsn = $this->_config['authenticate'];

		if (!empty($this->_config['authenticate_dsn']))
		{
			// Backwards compat.
			$override_dsn = $this->_config['authenticate_dsn'];
		}

		$company_file = null;
		$wait_before_next_update = null;
		$min_run_every_n_seconds = null;

		$customauth_company_file = null;
		$customauth_wait_before_next_update = null;
		$customauth_min_run_every_n_seconds = null;

		if (!empty($override_dsn) && (is_array($override_dsn) || is_string($override_dsn))) 	// Custom auth
		{
			if (Callbacks::callAuthenticate($this->_driver, $override_dsn, $obj->strUserName, $obj->strPassword, $customauth_company_file, $customauth_wait_before_next_update, $customauth_min_run_every_n_seconds) &&
				($ticket = $this->_driver->authLogin($obj->strUserName, $obj->strPassword, $company_file, $wait_before_next_update, $min_run_every_n_seconds, true)))
			{
				$this->_log('Login via ' . print_r($override_dsn, true) . ': ' . $obj->strUserName, $ticket, PackageInfo::LogLevel['DEBUG']);

				if ($customauth_company_file)
				{
					$status = $customauth_company_file;
				}
				else if ($company_file)
				{
					$status = $company_file;
				}
				else if ($this->_config['qb_company_file'])
				{
					$status = $this->_config['qb_company_file'];
				}

				if ((int) $customauth_wait_before_next_update)
				{
					$wait_before_next_update = (int) $customauth_wait_before_next_update;
				}
				else if ((int) $wait_before_next_update)
				{
					// Do Nothing
				}
				else if ((int) $this->_config['qbwc_wait_before_next_update'])
				{
					$wait_before_next_update = (int) $this->_config['qbwc_wait_before_next_update'];
				}

				if ((int) $customauth_min_run_every_n_seconds)
				{
					$min_run_every_n_seconds = (int) $customauth_min_run_every_n_seconds;
				}
				else if ((int) $min_run_every_n_seconds)
				{
					;
				}
				else if ((int) $this->_config['qbwc_min_run_every_n_seconds'])
				{
					$min_run_every_n_seconds = (int) $this->_config['qbwc_min_run_every_n_seconds'];
				}

				// Call login hook
				$hookdata = [
					'authenticate_dsn' => $override_dsn,
					'username' => $obj->strUserName,
					'password' => $obj->strPassword,
					'ticket' => $ticket,
					'qb_company_file' => $status,
					'qbwc_wait_before_next_update' => $wait_before_next_update,
					'qbwc_min_run_every_n_seconds' => $min_run_every_n_seconds,
				];
				$hookerr = '';
				$this->_callHook($ticket, static::HOOK_LOGINSUCCESS, null, null, null, null, $hookerr, null, [], $hookdata);

				// Move any recurring events that are due to the queue table
				$this->_handleRecurringEvents($ticket);

				if (!$this->_driver->queueDequeue($obj->strUserName))
				{
					$status = 'none';
				}

				// Login success (with a custom login handler)!
			}
			else
			{
				$this->_log('Login failed: ' . $obj->strUserName, '', PackageInfo::LogLevel['DEBUG']);

				$hookdata = [
					'authenticate_dsn' => $override_dsn,
					'username' => $obj->strUserName,
					'password' => $obj->strPassword,
				];
				$hookerr = '';
				$this->_callHook(null, static::HOOK_LOGINFAILURE, null, null, null, null, $hookerr, null, [], $hookdata);

				$ticket = '';
				$status = 'nvu'; // Invalid username/password
			}

			return new Authenticate($ticket, $status, $wait_before_next_update, $min_run_every_n_seconds);
		}
		else	// Standard authentication
		{
			$ticket = $this->_driver->authLogin($obj->strUserName, $obj->strPassword, $company_file, $wait_before_next_update, $min_run_every_n_seconds);
			if ($ticket)
			{
				$this->_log('Login: ' . $obj->strUserName, $ticket, PackageInfo::LogLevel['DEBUG']);

				if (!strlen($company_file) && $this->_config['qb_company_file'])
				{
					$status = $this->_config['qb_company_file'];
				}
				else if (strlen($company_file))
				{
					$status = $company_file;
				}

				if (! (int) $wait_before_next_update && (int) $this->_config['qbwc_wait_before_next_update'])
				{
					$wait_before_next_update = (int) $this->_config['qbwc_wait_before_next_update'];
				}

				if (! (int) $min_run_every_n_seconds && (int) $this->_config['qbwc_min_run_every_n_seconds'])
				{
					$min_run_every_n_seconds = (int) $this->_config['qbwc_min_run_every_n_seconds'];
				}

				$hookdata = [
					'username' => $obj->strUserName,
					'password' => $obj->strPassword,
					'ticket' => $ticket,
					'qb_company_file' => $status,
					'qbwc_wait_before_next_update' => $wait_before_next_update,
					'qbwc_min_run_every_n_seconds' => $min_run_every_n_seconds,
				];
				$hookerr = '';
				$this->_callHook($ticket, static::HOOK_LOGINSUCCESS, null, null, null, null, $hookerr, null, [], $hookdata);

				$this->_handleRecurringEvents($ticket);

				if (!$this->_driver->queueDequeue($obj->strUserName))
				{
					$status = 'none'; // Good login, but there isn't anything in the queue
				}

				// Login success!
			}
			else
			{
				$this->_log('Login failed: ' . $obj->strUserName, '', PackageInfo::LogLevel['DEBUG']);

				$hookdata = [
					'username' => $obj->strUserName,
					'password' => $obj->strPassword,
				];
				$hookerr = '';
				$this->_callHook(null, static::HOOK_LOGINFAILURE, null, null, null, null, $hookerr, null, [], $hookdata);

				$ticket = '';
				$status = 'nvu'; // Invalid username/password
			}

			return new Authenticate($ticket, $status, $wait_before_next_update, $min_run_every_n_seconds);
		}
	}

	/**
	 * SendRequestXML method for the QuickBooks Web Connector SOAP server - Generate and send a request to QuickBooks
	 *
	 * This is the 4th method the client's Web Connector will call each time it runs.
	 *
	 * The QuickBooks Web Connector calls this method to ask for things to do.
	 * So, calling this method is the Web Connectors way of saying: "Please
	 * send me a command so that I can pass that command on to QuickBooks."
	 * After it passes the command to QuickBooks, it will pass the response
	 * back via a call to receiveResponseXML().
	 *
	 * The stdClass object sent from the client's webconnector will contain these members:
	 *  - ticket				The login session ticket
	 *  - strHCPResponse		Only sent with the first sendRequestXML request.  The XML responses from the QuickBooks
	 *							file for a HostQuery, a CompanyQuery, and a PreferencesQuery.  You are not required
	 *							(nor recommended) to do anything with this.
	 *							with this, but you can if you want to.
	 *  - strCompanyFileName	The file name of the QuickBooks file.
	 *  - qbXMLCountry			The country code for whatever version of QuickBooks is sitting behind the Web Connector
	 *  - qbXMLMajorVers		The major version code of the QuickBooks web connector
	 *  - qbXMLMinorVers 		The minor version code of the QuickBooks web connector
	 *
	 * You should return one of:
	 *  - A valid qbXML or qbposXML request string
	 *  - An empty string '' to signal an error state to the client's WebConnector so it will call ->getLastError() to retrieve the error
	 *  - An empty string '' if there is nothing in the queue to send.
	 *
	 * The following user-defined hooks are invoked by this method:
	 * 	- static::HOOK_SENDREQUESTXML
	 */
	public function sendRequestXML($obj): SendRequestXML
	{
		$this->_log('sendRequestXML()', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

		// Make sure this is a valid ticket
		if (true !== $this->_driver->authCheck($obj->ticket))
		{
			// Ticket was not found, so return '' to indicate an error state to the client's Web Connector
			// Sending a '' will cause the client's Web Connector call ->getLastError()
			return new SendRequestXML('');
		}

		// Get the Web Connect user from the ticket
		$user = $this->_driver->authResolve($obj->ticket);

		// Call any configured hooks
		$hookdata = [
			'username' => 			$user,
			'ticket' => 			$obj->ticket,
			'strHCPResponse' => 	$obj->strHCPResponse,
			'strCompanyFileName' => $obj->strCompanyFileName,
			'qbXMLCountry' => 		$obj->qbXMLCountry,
			'qbXMLMajorVers' => 	$obj->qbXMLMajorVers,
			'qbXMLMinorVers' => 	$obj->qbXMLMinorVers,
		];
		$hookerr = '';
		$this->_callHook($obj->ticket, static::HOOK_SENDREQUESTXML, null, null, null, null, $hookerr, null, [], $hookdata);
		// _callHook($ticket, $hook, $requestID, $action, $ident, $extra, &$err, $xml = '', $qb_identifiers = [], $hook_data = [])

		// Move recurring events which are due to run to the queue table
		// 	We *CAN'T* re-register recurring events here, otherwise, we run
		//	the risk of re-adding an event which has occurred, *before* the
		//	entire session has finishing running. Thus, we'd create an
		//	infinite loop of web connector that would never end.
		//$this->_handleRecurringEvents($obj->ticket);


		// Find the next action/command in the queue with the highest priority
		$next = $this->_driver->queueDequeue($user, true);
		if (!$next)
		{
			// This should never happen since receiveResponseXML should send a '100' (percentage complete) when there is nothing left in the queue.
			// Sending a '' will cause the client's Web Connector call ->getLastError()
			return new SendRequestXML('');
		}
		$extra = $next['extra'];


		// Log the request Action and Ident
		$this->_log('Dequeued: ( ' . $next['qb_action'] . ', ' . $next['ident'] . ' ) ', $obj->ticket, PackageInfo::LogLevel['DEBUG']);

		// Update the status of the action/command in the QUEUE table
		//$this->_driver->queueStatus($obj->ticket, $next['qb_action'], $next['ident'], PackageInfo::Status['PROCESSING']);
		$this->_driver->queueStatus($obj->ticket, $next['quickbooks_queue_id'], PackageInfo::Status['PROCESSING']);

		/*
		// Here's a strange case, interactive mode handler
		if ($next['qb_action'] == QUICKBOOKS_INTERACTIVE_MODE)
		{
			// Set the error to "Interactive mode"
			$this->_driver->errorLog($obj->ticket, PackageInfo::Error['OK'], QUICKBOOKS_INTERACTIVE_MODE);

			// This will cause ->getLastError() to be called, and ->getLastError() will then return the string "Interactive mode" which will cause QuickBooks to call ->getInteractiveURL() and start an interactive session... I think...?
			return new SendRequestXML('');
		}
		*/

		$last_action_time = null;
		$last_actionident_time = null;

		// Call the mapped function that should generate an appropriate qbXML request
		$err = '';
		$xml = $this->_callMappedFunction(0, $user, $next['quickbooks_queue_id'], $next['qb_action'], $next['ident'], $extra, $err, $last_action_time, $last_actionident_time, $obj->qbXMLMajorVers . '.' . $obj->qbXMLMinorVers, $obj->qbXMLCountry, $next['qbxml']);
		if (!empty($err))
		{
			$errmsg = "Error calling mapped function for \"{$next['qb_action']}\" with ID {$next['ident']} (Queue ID {$next['quickbooks_queue_id']}):\n$err";
			if ($this->_driver->getLogLevel() >= PackageInfo::LogLevel['DEBUG'])
			{
				$this->_driver->log($errmsg, $obj->ticket);
			}
			return new SendRequestXML('');
		}

		// Make sure there's no whitespace around it
		$xml = trim($xml);

		// NoOp can be returned to skip this current operation. This will cause getLastError
		//	to be called, at which point NoOp should be returned to tell the Web
		//	Connector to then pause for 5 seconds before asking for another request.
		if ($xml == PackageInfo::Actions['NOOP'])
		{
			$this->_driver->errorLog($obj->ticket, 0, PackageInfo::Actions['NOOP']);

			// Mark it as a NoOp to remove it from the queue
			//$this->_driver->queueStatus($obj->ticket, $next['qb_action'], $next['ident'], PackageInfo::Status['NOOP'], 'Handler function returned: ' . PackageInfo::Actions['NOOP']);
			$this->_driver->queueStatus($obj->ticket, $next['quickbooks_queue_id'], PackageInfo::Status['NOOP'], 'Handler function returned: ' . PackageInfo::Actions['NOOP']);

			return new SendRequestXML('');
		}

		// If the requestID="..." attribute was not specified, we can try to automatically add it to the request
		$requestID = null;
		if (!($requestID = $this->_extractRequestID($xml)) &&
			$this->_config['autoadd_missing_requestid'])
		{
			// Find the <DoSomethingRq tag

			foreach (Utilities::listActions() as $action)
			{
				$request = Utilities::actionToRequest($action);
				if (false !== strpos($xml, '<' . $request . ' '))
				{
					$xml = str_replace('<' . $request . ' ', '<' . $request . ' requestID="' . $next['quickbooks_queue_id'] . '" ', $xml);
					break;
				}
				else if (false !== strpos($xml, '<' . $request . '>'))
				{
					$xml = str_replace('<' . $request . '>', '<' . $request . ' requestID="' . $next['quickbooks_queue_id'] . '">', $xml);
					break;
				}
			}
		}
		else if ($this->_config['check_valid_requestid'])
		{
			// They embedded a requestID="..." attribute, let's make sure it's valid
			if ($next['quickbooks_queue_id'] != $requestID)
			{
				// They are sending this request with an INVALID requestID! Error this out and warn them!
				$err = 'This request contains an invalid embedded requestID="..." attribute; either embed the $requestID parameter, or leave out the requestID="..." attribute entirely, found [' . $requestID . ' vs. expected ' . $next['quickbooks_queue_id'] . ']!';
			}
		}

		/*
		if ($this->_config['convert_unix_newlines'] &&
			false === strpos($xml, "\r") && 				// there are currently no Windows newlines...
			false !== strpos($xml, "\n"))					// ... but there *are* Unix newlines!
		{
			; // (this is currently broken/unimplemented)
		}
		*/

		if ($err)
		{
			// The function encountered an error when generating the qbXML request

			//$this->_driver->errorLog($obj->ticket, PackageInfo::Error['HANDLER'], $err);
			//$this->_driver->log('ERROR: ' . $err, $obj->ticket, PackageInfo::LogLevel['NORMAL']);
			//$this->_driver->queueStatus($obj->ticket, $next['qb_action'], $next['ident'], PackageInfo::Status['ERROR'], 'Registered handler returned error: ' . $err);

			$errerr = '';
			$this->_handleError($obj->ticket, PackageInfo::Error['HANDLER'], $err, $next['quickbooks_queue_id'], $next['qb_action'], $next['ident'], $extra, $errerr, $xml);

			return new SendRequestXML('');
		}

		// Log the outgoing request
		$this->_log('Outgoing XML request: ' . $xml, $obj->ticket, PackageInfo::LogLevel['DEBUG']);

		if (strlen($xml) &&  // Returned XML AND
			!$this->_extractRequestID($xml)) // Does not have a requestID in the request
		{
			// Mark it as successful right now
			//$this->_driver->queueStatus($obj->ticket, $next['qb_action'], $next['ident'], PackageInfo::Status['SUCCESS'], 'Unverified... no requestID attribute in XML stream.');
			$this->_driver->queueStatus($obj->ticket, $next['quickbooks_queue_id'], PackageInfo::Status['SUCCESS'], 'Unverified... no requestID attribute in XML stream.');
		}

		return new SendRequestXML($xml);
	}

	/**
	 * ReceiveResponseXML() method for the QuickBooks Web Connector - Receive and handle a resonse form QuickBooks
	 *
	 * The stdClass object passed as a parameter will have the following members:
	 * 	- ->ticket 		The QuickBooks Web Connector ticket
	 * 	- ->response	An XML response message
	 * 	- ->hresult		Error code
	 * 	- ->message		Error message
	 *
	 * The sole data member of the returned object should be an integer.
	 * 	- The data member should be -1 if an error occurred and QBWC should call ->getLastError()
	 * 	- Should be an integer 0 <= x < 100 to indicate success *and* that the application should continue to call ->sendRequestXML() at least one more time (more queued items still in the queue, the integer represents the percentage complete the total batch job is)
	 * 	- Should be 100 to indicate success *and* that the queue has been exhausted
	 *
	 * The following user-defined hooks are invoked:
	 * 	- static::HOOK_RECEIVERESPONSEXML
	 *
	 */
	public function receiveResponseXML($obj): ReceiveResponseXML
	{
		$this->_log('receiveResponseXML()', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

		if ($this->_driver->authCheck($obj->ticket)) // Check the ticket
		{
			$user = $this->_driver->authResolve($obj->ticket);

			$hookdata = [
				'username' => $user,
				'ticket' => $obj->ticket,
			];
			$hookerr = '';
			$this->_callHook($obj->ticket, static::HOOK_RECEIVERESPONSEXML, null, null, null, null, $hookerr, null, [], $hookdata);

			$this->_log('Incoming XML response: ' . $obj->response, $obj->ticket, PackageInfo::LogLevel['DEBUG']);

			// Check if we got a error message...
			if ((null !== $obj->message && strlen($obj->message)) ||
				$this->_extractStatusCode($obj->response)) // or an error code
			{
				// We got an error message or an error code.


				//$this->_log('Extracted code[' . $this->_extractStatusCode($obj->response) . ']', $obj->ticket, PackageInfo::LogLevel['DEBUG']);

				$action = null;
				$ident = null;
				$current = null;		// The current item we're receiving a response for

				$errnum = null;
				$requestID = $this->_extractRequestID($obj->response);
				if ($requestID)
				{
					// This happens if a data validation error occurs
					//	(string too long, vendor name already taken, etc.)

					$errnum = $this->_extractStatusCode($obj->response);

					// Retrieve the action/command details from the queue by its requestID
					$current = $this->_driver->queueGet($user, $requestID, PackageInfo::Status['PROCESSING']);
					if ($current)
					{
						// This is the particular item that experienced an error
						$action = $current['qb_action'];
						$ident = $current['ident'];
					}
					else
					{
						$requestID = null;
					}
				}
				else
				{
					// This happens if a protocol error occurs
					//	Poorly formed XML documents, missing XML node, missing line items, etc.)

					$errnum = $obj->hresult;

					// Try to guess at the request that caused an error (the last request that went out)
					$current = $this->_driver->queueProcessing($user);
					if ($current)
					{
						$requestID = $current['quickbooks_queue_id'];
						$action = $current['qb_action'];
						$ident = $current['ident'];
					}
				}

				if ($current)
				{
					$extra = $current['extra'];

					$errmsg = null;
					if ($obj->message)
					{
						$errmsg = $obj->message;
					}
					else if ($status = $this->_extractStatusMessage($obj->response))
					{
						$errmsg = $status;
					}

					$errerr = '';
					$continue = $this->_handleError($obj->ticket, $errnum, $errmsg, $requestID, $action, $ident, $extra, $errerr, $obj->response, []);
					//					$errnum, $errmsg, $requestID, $action, $ident, $extra, &$err, $xml, $qb_identifiers = []

					if ($errerr)
					{
						// The error handler returned an error too...
						$this->_log('An error occurred while handling quickbooks error ' . $errnum . ': ' . $errmsg . ': ' . $errerr, $obj->ticket, PackageInfo::LogLevel['NORMAL']);
					}
				}
				else	// Generic error (poorly encoded XML, XML syntax error, etc. -- Something that caused parsing the XML to fail)
				{
					$errerr = '';
					$continue = $this->_handleError($obj->ticket, $obj->hresult, $obj->message, null, null, null, null, $errerr, $obj->response, []);

					if ($errerr)
					{
						// The error handler returned an error too...
						$this->_log('An error occurred while handling generic error ' . $obj->hresult . ': ' . $obj->message . ': ' . $errerr, $obj->ticket, PackageInfo::LogLevel['NORMAL']);
					}
				}

				// If the error was handled (i.e. $this->_handleError() returned true), return the percentage done.
				// Otherwise, return -1 to notify the client Web Connector there was an error.
				$progress = $continue ? $this->_calculateProgress($obj->ticket) : -1;

				$this->_log('Transaction error at ' . $progress . '% complete... ', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

				return new ReceiveResponseXML($progress);
			}


			// No error occurred, so proceed with handling the response
			$extra = null;
			$action = null;
			$ident = null;

			$requestID = $this->_extractRequestID($obj->response);
			if ($requestID &&
				($current = $this->_driver->queueGet($user, $requestID, PackageInfo::Status['PROCESSING'])))
			{
				$extra = $current['extra'];
				$action = $current['qb_action'];
				$ident = $current['ident'];

				// Update the status to success
				$this->_driver->queueStatus($obj->ticket, $requestID, PackageInfo::Status['SUCCESS']);
			}
			else
			{
				// It's a good response... but we failed to get the action/command details from the QUEUE table for some reason?
				$this->_log('This appears to be a correct response, but the requestID could not be validated... ', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

				$progress = -1;

				return new ReceiveResponseXML($progress);
			}

			// Extract ListID, TxnID, etc. from the response
			$identifiers = $this->_extractIdentifiers($obj->response);
			//$this->_driver->log(var_export($identifiers, true), $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

			$err = null;
			//$last_action_time = $this->_driver->queueActionLast($user, $action);
			$last_action_time = null;
			$last_actionident_time = null;

			$this->_callMappedFunction(1, $user, $requestID, $action, $ident, $extra, $err, $last_action_time, $last_actionident_time, $obj->response, $identifiers);

			// Calculate the percentage done
			$progress = $this->_calculateProgress($obj->ticket);

			if ($err)
			{
				$errerr = '';
				$continue = $this->_handleError($obj->ticket, PackageInfo::Error['HANDLER'], $err, $requestID, $action, $ident, $extra, $errerr, $obj->response, $identifiers);

				if (!$continue)
				{
					$progress = -1;
				}
			}

			$this->_log($progress . '% complete... ', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

			return new ReceiveResponseXML($progress);
		}

		return new ReceiveResponseXML(-1);
	}

	/**
	 * QuickBooks Web Connector ->connectionError() SOAP method
	 *
	 * The stdClass object passed in as a parameter has these members:
	 * 	- ->ticket 		The ticket string
	 * 	- ->hresult		An error code
	 * 	- ->message 	An error message from QuickBooks/QBWC
	 *
	 * The following user-defined hooks are invoked:
	 * 	- static::HOOK_CONNECTIONERROR
	 */
	public function connectionError($obj): ConnectionError
	{
		$this->_log('connectionErrorXML()', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

		if ($this->_driver->authCheck($obj->ticket))
		{
			$user = $this->_driver->authResolve($obj->ticket);

			$hookdata = [
				'username' => $user,
				'ticket' => $obj->ticket,
				'hresult' => $obj->hresult,
				'message' => $obj->message,
			];
			$hookerr = '';
			$this->_callHook($obj->ticket, static::HOOK_CONNECTIONERROR, null, null, null, null, $hookerr, null, [], $hookdata);

			$err = '';
			$this->_handleError($obj->ticket, $obj->hresult, $obj->message, null, null, null, null, $err, null);

			return new ConnectionError('done');
		}

		return new ConnectionError('done');
	}

	/**
	 * QuickBooks Web Connector ->getLastError() SOAP method
	 *
	 * The stdClass object passed in as a parameter has these members:
	 * 	- ->ticket		The ticket string
	 *
	 * The returned object should have just one member, an error message
	 * describing the last error that occurred.
	 *
	 * The following user-defined hooks are invoked:
	 * 	- static::HOOK_GETLASTERROR
	 */
	public function getLastError($obj): GetLastError
	{
		$this->_log('getLastError()', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

		if ($this->_driver->authCheck($obj->ticket))
		{
			$user = $this->_driver->authResolve($obj->ticket);

			$hookdata = [
				'username' => $user,
				'ticket' => $obj->ticket,
			];
			$hookerr = '';
			$this->_callHook($obj->ticket, static::HOOK_GETLASTERROR, null, null, null, null, $hookerr, null, [], $hookdata);

			$lasterr = $this->_driver->errorLast($obj->ticket);

			return new GetLastError($lasterr);
		}

		return new GetLastError('Bad ticket.');
	}

	/**
	 * QuickBooks Web Connector ->closeConnection() SOAP method
	 *
	 * The stdClass object passed in as a parameter has these members:
	 * 	- ->ticket 		The ticket string
	 *
	 * The sole member of the returned object should be a string describing the reason for closing the connection
	 *
	 * @todo The "Complete!" message should probably be based on a configuration variable, user configurable
	 *
	 * The following user-defined hooks are invoked:
	 * 	- static::HOOK_CLOSECONNECTION
	 */
	public function closeConnection($obj): CloseConnection
	{
		$this->_log('closeConnection()', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

		if ($this->_driver->authCheck($obj->ticket))
		{
			$user = $this->_driver->authResolve($obj->ticket);

			$hookdata = [
				'username' => $user,
				'ticket' => $obj->ticket,
			];
			$hookerr = '';
			$this->_callHook($obj->ticket, static::HOOK_CLOSECONNECTION, null, null, null, null, $hookerr, null, [], $hookdata);

			//
			return new CloseConnection('Complete!');
		}

		// Bad ticket
		return new CloseConnection('Bad ticket.');
	}

	/**
	 * QuickBooks Web Connector ->getInteractiveURL() SOAP method - Get the URL to use for an interactive session
	 *
	 * The stdClass object passed as a parameter will have the following members:
	 * 	- ticket		The ticket string
	 * 	- sessionID		??? (undocumented in QBWC documentation...?)
	 *
	 * The following user-defined hooks are invoked:
	 * 	- static::HOOK_GETINTERACTIVEURL
	 *
	 * @param stdClass $obj
	 * @return GetInteractiveURL
	 */
	/*
	public function getInteractiveURL($obj): GetInteractiveURL
	{
		$this->_log('getInteractiveURL()', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

		if ($this->_driver->authCheck($obj->ticket))
		{
			$user = $this->_driver->authResolve($obj->ticket);

			$hookdata = [
				'username' => $user,
				'ticket' => $obj->ticket,
			];
			$hookerr = '';
			$this->_callHook($obj->ticket, static::HOOK_GETINTERACTIVEURL, null, null, null, null, $hookerr, null, [], $hookdata);

			return new GetInteractiveURL($this->_config['qbwc_interactive_url']);
		}

		return new GetInteractiveURL('');
	}
	*/

	/**
	 *
	 * @todo Implement this... returned object is null!
	 *
	 * @param stdClass $obj
	 * @return void
	 */
	/*
	public function interactiveRejected($obj)
	{
		$this->_log('interactiveRejected()', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

		if ($this->_driver->authCheck($obj->ticket))
		{
			$user = $this->_driver->authResolve($obj->ticket);

			$hookdata = [
				'username' => $user,
				'ticket' => $obj->ticket,
			];
			$hookerr = '';
			$this->_callHook($obj->ticket, static::HOOK_GETINTERACTIVEURL, null, null, null, null, $hookerr, null, [], $hookdata);

			return null;
		}

		return null;
	}
	*/

	/**
	 *
	 *
	 * The stdClass object passed as a parameter will have the following members:
	 * 	- ticket
	 *
	 * @param stdClass $obj
	 * @return InteractiveDone
	 */
	/*
	public function interactiveDone($obj): InteractiveDone
	{
		$this->_log('interactiveDone()', $obj->ticket, PackageInfo::LogLevel['VERBOSE']);

		if ($this->_driver->authCheck($obj->ticket))
		{
			$user = $this->_driver->authResolve($obj->ticket);

			$hookdata = [
				'username' => $user,
				'ticket' => $obj->ticket,
			];
			$hookerr = '';
			$this->_callHook($obj->ticket, static::HOOK_INTERACTIVEDONE, null, null, null, null, $hookerr, null, [], $hookdata);

			return new InteractiveDone('Done');
		}

		return new InteractiveDone('');
	}
	*/



	/**
	 * Extract the requestID attribute from an XML stream
	 *
	 * @param string $xml	The XML stream to look for a requestID attribute in
	 * @return mixed		The request ID
	 */
	protected function _extractRequestID(string $xml)
	{
		return Utilities::extractRequestID($xml);
	}

	/**
	 * Extract a unique record identifier from an XML response
	 *
	 * Some (most?) records within QuickBooks have unique identifiers which are
	 * returned with the qbXML responses. This method will try to extract all
	 * identifiers it can find from a qbXML response and return them in an
	 * associative array.
	 *
	 * For example, Customers have unique ListIDs, Invoices have unique TxnIDs,
	 * etc. For an AddCustomer request, you'll get an array that looks like
	 * this:
	 * <code>
	 * [
	 * 	'ListID' => '2C0000-1039887390'
	 * ]
	 * </code>
	 *
	 * Other transactions might have more than one identifier. For instance, a
	 * call to AddInvoice returns both a ListID and a TxnID:
	 * <code>
	 * [
	 * 	'ListID' => '200000-1036881887', // This is actually part of the 'CustomerRef' entity in the Invoice XML response
	 * 	'TxnID' => '11C26-1196256987', // This is the actual transaction ID for the Invoice XML response
	 * ]
	 * </code>
	 *
	 * *** IMPORTANT *** If there are duplicate fields (i.e.: 3 different
	 * ListIDs returned) then only the first value encountered will appear in
	 * the associative array.
	 *
	 * The following elements/attributes are supported:
	 * 	- ListID
	 * 	- TxnID
	 * 	- iteratorID
	 * 	- OwnerID
	 * 	- TxnLineID
	 *
	 * @param string $xml	The XML stream to look for an identifier in
	 * @return array		An associative array mapping identifier fields to identifier values
	 */
	protected function _extractIdentifiers(string $xml): array
	{
		$fetch_tagdata = [
			'ListID',
			'TxnID',
			'OwnerID',
			'TxnLineID',
			'EditSequence',
			'FullName',
			'Name',
			'RefNumber',
		];

		$fetch_attributes = [
			'requestID',
			'iteratorID',
			'iteratorRemainingCount',
			'metaData',
			'retCount',
			'statusCode',
			'statusSeverity',
			'statusMessage',
			'newMessageSetID',
			'messageSetStatusCode',
		];

		$list = [];

		foreach ($fetch_tagdata as $tag)
		{
			if (false !== ($start = strpos($xml, '<' . $tag . '>')) &&
				false !== ($end = strpos($xml, '</' . $tag . '>')))
			{
				$list[$tag] = substr($xml, $start + 2 + strlen($tag), $end - $start - 2 - strlen($tag));
			}
		}

		foreach ($fetch_attributes as $attribute)
		{
			if (false !== ($start = strpos($xml, ' ' . $attribute . '="')) &&
				false !== ($end = strpos($xml, '"', $start + strlen($attribute) + 3)))
			{
				$list[$attribute] = substr($xml, $start + strlen($attribute) + 3, $end - $start - strlen($attribute) - 3);
			}
		}

		return $list;
	}

	/**
	 * Extract the status code from an XML response
	 *
	 * Each qbXML response should return a status code and a status message
	 * indicating whether or not an error occurred.
	 *
	 * @param string $xml	The XML stream to look for a response status code in
	 * @return integer		The response status code (0 if OK, another positive integer if an error occurred)
	 */
	protected function _extractStatusCode(string $xml): int
	{
		if (false !== ($start = strpos($xml, ' statusCode="')) &&
			false !== ($end = strpos($xml, '"', $start + 13)))
		{
			return filter_var(substr($xml, $start + 13, $end - $start - 13), FILTER_VALIDATE_INT);
		}

		return PackageInfo::Error['OK'];
	}

	/**
	 * Extract the status message from an XML response
	 *
	 * Each qbXML response should return a status code and a status message
	 * indicating whether or not an error occurred.
	 *
	 * @param string $xml	The XML stream to look for a response status message in
	 * @return string		The response status message
	 */
	protected function _extractStatusMessage($xml)
	{
		if (false !== ($start = strpos($xml, ' statusMessage="')) &&
			false !== ($end = strpos($xml, '"', $start + 16)))
		{
			return substr($xml, $start + 16, $end - $start - 16);
		}

		return '';
	}

	/**
	 * Call the mapped function for a given action
	 *
	 * @param integer $which			Whether or call the request action handler (pass a 0) or the response action handler (pass a 1)
	 * @param string $user				QuickBooks username of the user the request/response is for
	 * @param string $action
	 * @param mixed $ident
	 * @param mixed $extra
	 * @param string $err				If the function returns an error message, the error message will be stored here
	 * @param integer $last_action_time
	 * @param integer $last_actionident_time
	 * @param string $xml				A qbXML response (if you're calling the response handler)
	 * @param array $qb_identifier
	 * @return string
	 */
	protected function _callMappedFunction($which, $user, $requestID, $action, $ident, $extra, &$err, $last_action_time, $last_actionident_time, $xml_or_version = '', $qb_identifier_or_locale = [], $qbxml = null)
	{
		if ($which == 0)
		{
			return Callbacks::callRequestHandler($this->_driver, $this->_map, $requestID, $action, $user, $ident, $extra, $err, $last_action_time, $last_actionident_time, $xml_or_version, $qb_identifier_or_locale, $this->_callback_config, $qbxml);
		}
		else if ($which == 1)
		{
			return Callbacks::callResponseHandler($this->_driver, $this->_map, $requestID, $action, $user, $ident, $extra, $err, $last_action_time, $last_actionident_time, $xml_or_version, $qb_identifier_or_locale, $this->_callback_config, $qbxml);
		}

		$err = 'Request for a mapped function could not be fulfilled, invalid $which parameter.';
		return false;
	}

	/**
	 * Call a user-defined hook
	 *
	 * @param string $ticket
	 * @param string $hook
	 * @param string $requestID
	 * @param string $action
	 * @param mixed $ident
	 * @param mixed $extra
	 * @param string $err
	 * @param string $xml
	 * @param array $qb_identifiers
	 * @param array $hook_data
	 * @return boolean
	 */
	protected function _callHook(?string $ticket, string $hook, $requestID, ?string $action, $ident, ?array $extra, ?string &$err, ?string $xml = '', array $qb_identifiers = [], array $hook_data = [])
	{
		$user = '';
		if ($ticket)
		{
			$user = $this->_driver->authResolve($ticket);
		}

		// Call the hook
		$ret = Callbacks::callHook($this->_driver, $this->_hooks, $hook, $requestID, $user, $ticket, $err, $hook_data, $this->_callback_config, __FILE__, __LINE__);

		// If the hook reported an error, log the error
		if ($err)
		{
			$errerr = '';
			$this->_handleError($ticket, PackageInfo::Error['HOOK'], $err, $requestID, $action, $ident, $extra, $errerr, $xml, $qb_identifiers);
		}

		return true;
	}

	/**
	 * Call an error-handler function and update the status of a request to ERROR
	 *
	 * @param string $ticket
	 * @param integer $errnum		The error number from QuickBooks (see the QuickBooks SDK/IDN for a list of error codes)
	 * @param string $errmsg		The error message from QuickBooks
	 * @param string $requestID
	 * @param string $action
	 * @param mixed $ident
	 * @param array $extra
	 * @param string $err
	 * @param string $xml
	 * @param array $qb_identifiers
	 */
	protected function _handleError(?string $ticket, int $errnum, string $errmsg, $requestID, ?string $action, $ident, ?array $extra, ?string &$err, ?string $xml = '', array $qb_identifiers = [])
	{
		// Call the error handler (if one is set)

		$errmsg = html_entity_decode($errmsg);

		// First, set the status of the item to error
		if ($requestID)
		{
			$this->_driver->queueStatus($ticket, $requestID, PackageInfo::Status['ERROR'], $errnum . ': ' . $errmsg);
		}

		// Log the last error (for the ticket)
		$this->_driver->errorLog($ticket, $errnum, $errmsg);
		$this->_log('Attempting to handle error: ' . $errnum . ', ' . $errmsg, $ticket, PackageInfo::LogLevel['NORMAL']);

		// By default, we don't want to continue if the error is not handled
		$continue = false;

		// Get username of user which experienced the error
		$user = $this->_driver->authResolve($ticket);

		// CALL THE ERROR HANDLER
		$err = '';
		$continue = Callbacks::callErrorHandler($this->_driver, $this->_onerror, $errnum, $errmsg, $user, $requestID, $action, $ident, $extra, $err, $xml, $this->_callback_config);
		//													$Driver, $errmap, $errnum, $errmsg, $user, $action, $ident, $extra, &$errerr, $xml, $callback_config

		if ($err)
		{
			// Log error messages returned by the error handler
			$this->_log('An error occurred while handling error: ' . $errnum . ': ' . $errmsg . ': ' . $err, $ticket, PackageInfo::LogLevel['NORMAL']);
			$this->_driver->errorLog($ticket, PackageInfo::Error['HANDLER'], $err);
		}

		// Log the last error (for the log)
		$this->_log('Handled error: ' . $errnum . ': ' . $errmsg . ' (handler returned: ' . $continue . ')', $ticket, PackageInfo::LogLevel['NORMAL']);

		// Update the queue status
		if ($requestID && $continue)
		{
			//$this->_driver->queueStatus($ticket, $action, $ident, PackageInfo::Status['HANDLED'], $errnum . ': ' . $errmsg);
			$this->_driver->queueStatus($ticket, $requestID, PackageInfo::Status['HANDLED'], $errnum . ': ' . $errmsg);
		}

		return $continue;
	}

	/**
	 * Calculate the current progress (what percentage done are we with this session?)
	 */
	protected function _calculateProgress(string $ticket): int
	{
		if ($this->_driver->authCheck($ticket)) // Check the ticket
		{
			$user = $this->_driver->authResolve($ticket);

			$current = $this->_driver->queueLeft($user);				// Number of items currently in the queue
			$processed = $this->_driver->queueProcessed($ticket);	// Number of items we've processed during this session
			$percentage = ($current === 0) ? 100 : intval(min(99, floor(100 * ($processed / ($processed + $current)))));

			// Call the percentage done hook
			$hookerr = '';
			$hookdata = [
				'user' => $user,
				'percentage' => $percentage,
				'items_left' => $current,
				'items_processed' => $processed,
			];
			$this->_callHook($ticket, static::HOOK_PERCENT, null, null, null, null, $hookerr, null, [], $hookdata);

			return $percentage;
		}

		return -1;
	}
}
