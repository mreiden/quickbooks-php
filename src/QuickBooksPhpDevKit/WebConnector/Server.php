<?php declare(strict_types=1);

/**
 * QuickBooks SOAP server for interacting with the QuickBooks Web Connector
 *
 * Copyright (c) 2010-04-16 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * This class provides a framework for generating and handling messages to send
 * to and from QuickBooks via the Web Connector. For every operation you wish
 * to perform on QuickBooks, you should be expecting to implement two functions:
 * 	- A function responsible for generating the qbXML request (telling QuickBooks what you want to do)
 * 	- A function responsible for parsing and handling the qbXML response (handling the response form QuickBooks)
 *
 * You should see the docs/example_server.php file for detailed examples of
 * using this!
 *
 * Add items to the QuickBooks queue using the {@see QuickBooksPhpDevKit\WebConnector\Queue} class.
 * The next time the QuickBooks Web Connector connects to the SOAP server, it
 * will be instructed to perform commands based on what has been placed in the
 * queue. So if you queued up "CustomerAdd" "1234", #1234,
 * customer_add_request() will generate a qbXML request telling QuickBooks to
 * add that customer, the SOAP server will send that request out, and
 * QuickBooks will send back a qbXML response indicating whether or not that
 * customer was added successfully.
 *
 * The QuickBooks Web Connector (QBWC) works like this:
 * 	- You create a SOAP server that response to a set of SOAP methods
 * 	- You install and run the QBWC alongside your existing QuickBooks installation
 * 	- You register your SOAP server with the QBWC
 * 	- The QBWC calls the ->authenticate() method via a SOAP request
 * 	- You create and assign a 'ticket' (essentially a session ID value) to the QBWC session, this ticket gets sent to your SOAP server for authentication purposes with every request thereafter
 * 	- The QBWC calls the ->sendRequestXML() method via a SOAP request, if there is work to do, you send back qbXML commands encapsulated in an object
 * 	- The QBWC passes these qbXML commands to QuickBooks, QuickBooks processes them and passes them back
 * 	- The QBWC passes back the response from QuickBooks to your SOAP server via a SOAP call to ->receiveResponseXML()
 * 	- If you return an integer between 0 and 99 (inclusive) from ->receiveResponseXML(), the QBWC will call ->sendRequestXML() again, to get the next qbXML command
 * 	- Once you return a 100 from ->receiveResponseXML(), the QBWC calls ->closeConnection() and closes the socket connection shortly thereafter
 *
 * Troubleshooting:
 * 	- Errors which QuickBooks reports will be logged (check the quickbooks_queue and quickbooks_log tables in your database)
 * 	- If actions get stuck in the queue and never seem to be pulled out, it is most likely that you're generating badly-formed XML for that request. Check the XML document you're creating for well-formedness, *and especially* character set issues
 * 	- The quickbooks_log database table shows everything that is happening
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Server
 */

namespace QuickBooksPhpDevKit\WebConnector;

use QuickBooksPhpDevKit\Adapter\SOAP\Server\AdapterInterface;
use QuickBooksPhpDevKit\Callbacks;
//use QuickBooksPhpDevKit\ErrorHandler;
use QuickBooksPhpDevKit\Driver\Factory;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;
//use QuickBooksPhpDevKit\WebConnector\Handlers;	// Base handlers for each of the methods required by the QuickBooks Web Connector

/**
 * QuickBooks SOAP Server
 */
class Server
{
	/**
	 * Hook which gets called when a request is received
	 * @var string
	 */
	public const HOOK_PREHANDLE = 'Server::handle (pre)';

	/**
	 * Hook which gets called after a request gets handled
	 * @var string
	 */
	public const HOOK_POSTHANDLE = 'Server::handle (post)';


	/**
	 * The logging level
	 * @var integer
	 */
	protected $_loglevel;

	/**
	 * Driver instance object
	 * @var object
	 */
	protected $_driver;

	/**
	 * Server instance object
	 * @var object
	 */
	protected $_server;

	/**
	 * Server version string
	 * @var string
	 */
	protected $_server_version_string;

	/**
	 * Registered hook functions for the server
	 * @var array
	 */
	protected $_hooks;

	/**
	 * An array of data to pass to every callback function
	 * @var array
	 */
	protected $_callback_config;

	/**
	 * The raw input to the script
	 * @var string
	 */
	protected $_input;

	/**
	 * The timestamp when the server was created
	 * @var float
	 */
	protected $_timestamp;

	/**
	 * Create a new QuickBooks SOAP server
	 *
	 * @param mixed 			$dsn_or_conn		Either a DSN-style connection string *or* a database resource (if reusing an existing connection)
	 * @param AdapterInterface	$soapAdapter		SOAP server adapter interface (Built-In or PHP Extension)
	 * @param array 			$map				An associative array mapping queued commands to function/method calls
	 * @param array 			$onerror			An associative array mapping error codes to function/method calls
	 * @param array 			$hooks				An associative array mapping events to hook function/method calls
	 * @param int   			$log_level			PackageInfo::LogLevel['NORMAL'] | NONE, NORMAL, VERBOSE, DEBUG, DEVELOP
	 * @param array 			$handler_options	Options to pass to the handler class
	 * @param array 			$driver_options		Options to pass to the driver class (i.e.: MySQLi, etc.)
	 */
	public function __construct($dsn_or_conn, AdapterInterface $soapAdapter, array $map, array $onerror = [], array $hooks = [], int $log_level = PackageInfo::LogLevel['NORMAL'], array $handler_options = [], array $driver_options = [], array $callback_options = [])
	{
		// Not sure what this is actually for
		$this->_timestamp = microtime(true);

		// Set the default time zone.  Should be set to the WebConnect client's time zone if possible.
		$this->setDefaultTimeZone();

		// If safe mode is turned on, this causes a NOTICE/WARNING to be issued...
		//if (!ini_get('safe_mode'))
		//{
		//	set_time_limit($soap_options['time_limit']);
		//}

		/*
		if ($soap_options['error_handler'])
		{
			set_error_handler($soap_options['error_handler']);
		}
		else if ($soap_options['use_builtin_error_handler'])
		{
			set_error_handler( ['ErrorHandler', 'handle'] );
		}

		if ($soap_options['log_to_syslog'])
		{

		}

		if ($soap_options['log_to_file'])
		{

		}
		*/

		// Save the server version string so it's not here in the handle method and in WebConnector\Handlers.
		$http_scheme = !empty($_SERVER['REQUEST_SCHEME']) ? "{$_SERVER['REQUEST_SCHEME']}://" : (empty($_SERVER['HTTPS']) || true !== filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN) ? 'http://' : 'https://');
		$this->_server_version_string = 'PHP QuickBooks SOAP Server v' . PackageInfo::Package['VERSION'] . ' at ' . $http_scheme . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '?');

		$handler_options = array_merge(
			['server_version' => $this->_server_version_string],
			$handler_options
		);

		// Logging level
		$this->_loglevel = $log_level;

		if ($this->_loglevel >= PackageInfo::LogLevel['DEVELOP'])
		{
			// Driver must be created for logging to work
			$this->_driver = Factory::create($dsn_or_conn, $driver_options, $hooks, $log_level);
		}

		// SOAP server adapter class
		$this->_server = $soapAdapter;


		// Check the user configured callbacks, error-handlers, and hooks to make sure they're able to be called
		$uncallable_errors = array_filter([
			'QueueMap' => static::validateMappedFunctions($map),           // $map: An associative array containing an array [RequestFunction, ResponseFunction] mapping queued commands to function/method calls
			'ErrorMap' => static::validateErrorHandlerFunctions($onerror), // $onerror: An associative array mapping error codes to function/method calls
			'HookMap' => static::validateHookFunctions($hooks),            // $hooks: An associative array mapping events to hook function/method calls
		]);
		if (!empty($uncallable_errors)) {
			http_response_code(503);
			throw new \Exception('Detected uncallable callbacks: ' . print_r($uncallable_errors, true));
		}

		// Assign hooks
		$this->_hooks = $hooks;

		// Assign callback configuration info
		$this->_callback_config = $callback_options;

		// Base handlers
		// $dsn_or_conn, $map, $onerror, $hooks, $log_level, $this->_input, $handler_config = [], $driver_config = []
		$this->_server->setClass(__NAMESPACE__ . "\\Handlers", $dsn_or_conn, $map, $onerror, $hooks, $log_level, 'UNUSED_$this->_input', $handler_options, $driver_options, $callback_options);
	}

	public function setDefaultTimeZone(?string $tz = null): bool
	{
		PackageInfo::$TIMEZONE_AUTOSET = false;

		if (null === $tz)
		{
			if (!empty(PackageInfo::$TIMEZONE) && is_string(PackageInfo::$TIMEZONE))
			{
				$tz = PackageInfo::$TIMEZONE;
			}
			else
			{
				PackageInfo::$TIMEZONE_AUTOSET = true;
				$tz = @date_default_timezone_get();
			}
		}

		$success = @date_default_timezone_set($tz);
		if ($success == true)
		{
			PackageInfo::$TIMEZONE = $tz;
		}

		return $success;
	}

	/**
	 * Merge two arrays, allowing $arr2 to be merged over matching keys in $arr1
	 *
	 * If $arr1 or $arr2 are arrays of arrays, and $array_of_arrays is set to
	 * true, then the arrays of arrays will be merged, allowing $arr2 to
	 * override $arr1 entries. If the arrays of arrays are numerically indexed,
	 * $arr2 entries will be appended to $arr1 entries.
	 */
	protected function _merge(array $arr1, array $arr2, bool $array_of_arrays = false): array
	{
		if ($array_of_arrays)
		{
			foreach ($arr2 as $key => $funcs)
			{
				if (!is_array($funcs))
				{
					$funcs = [$funcs];
				}

				if (isset($arr1[$key]))
				{
					if (!is_array($arr1[$key]))
					{
						$arr1[$key] = [$arr1[$key]];
					}

					$arr1[$key] = array_merge($arr1[$key], $funcs);
				}
				else
				{
					$arr1[$key] = $funcs;
				}
			}
		}
		else
		{
			// *DO NOT* use array_merge() here, it screws things up!!!
			//return array_merge($arr1, $arr2);

			foreach ($arr2 as $key => $value)
			{
				$arr1[$key] = $value;
			}
		}

		return $arr1;
	}

	/**
	 * Send the correct HTTP headers for this request
	 */
	protected function _headers(): bool
	{
		$content_type = ($_SERVER['REQUEST_METHOD'] == 'POST' ? 'text/xml' : 'text/plain');
		@header('Content-Type: ' . $content_type . '; charset=UTF-8');

		return true;
	}

	/**
	 * Log a message to the error/debug log
	 */
	protected function _log(string $msg, ?string $ticket, int $level = PackageInfo::LogLevel['NORMAL']): bool
	{
		$Driver = $this->_driver;

		$msg = Utilities::mask($msg);

		if ($Driver)
		{
			return $Driver->log($msg, $ticket, $level);
		}

		return false;
	}


	/**
	 * Set the raw request input (Let's us write tests)
	 */
	public function setRawRequestInput(?string $input): void
	{
		$this->_input = $input;
	}

	/**
	 * Handle the SOAP request
	 */
	public function handle(bool $return = false, bool $debug = false): ?string
	{
		if (null === $this->_input)
		{
			// Raw input
			$this->setRawRequestInput(file_get_contents('php://input'));
		}

		// raw input
		$input = &$this->_input;

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// This is a SOAP Request

			$this->_headers();

			$output_buffering = false;

			$hook_data = [
				'input' => $input,
			];

			$err = '';
			$this->_callHooks(static::HOOK_PREHANDLE, null, null, null, $err, $hook_data);

			if ($this->_loglevel >= PackageInfo::LogLevel['DEVELOP'])
			{
				if (function_exists('apache_request_headers'))
				{
					$headers = '';
					foreach (apache_request_headers() as $header => $value)
					{
						$headers .= $header . ': ' . $value . "\n";
					}

					$this->_log('Incoming HTTP Headers: ' . $headers, null, PackageInfo::LogLevel['DEVELOP']);
				}

				$this->_log('Incoming SOAP Request: ' . $input, null, PackageInfo::LogLevel['DEVELOP']);
			}

			$output_buffering = ($return || isset($this->_hooks[static::HOOK_POSTHANDLE]) || $this->_loglevel >= PackageInfo::LogLevel['DEVELOP']);
			if ($output_buffering)
			{
				ob_start();
			}

			// Handle to SOAP request
			$this->_server->handle($input);

			if ($output_buffering)
			{
				// Get the buffered output
				$output = ob_get_flush();

				$hook_data = [
					'input' => $input,
					'output' => $output,
				];

				$err = '';
				$this->_callHooks(static::HOOK_POSTHANDLE, null, null, null, $err, $hook_data);
				//QuickBooks_Callbacks::callHook($this->_driver, $this->_hooks, QUICKBOOKS_SERVER_HOOK_POSTHANDLE, null, null, null, $err, $hook_data, $this->_callback_config);

				if ($this->_loglevel >= PackageInfo::LogLevel['DEVELOP'])
				{
					$this->_log("Outgoing SOAP Response: \n" . $output, null, PackageInfo::LogLevel['DEVELOP']);
				}

				if ($return)
				{
					return $output;
				}
			}
		}
		else if (array_key_exists('wsdl', array_change_key_case($_GET, CASE_LOWER)))
		{
			// Output the WSDL file

			$contents = file_get_contents($this->_server->getWsdlPath());
			if (false !== $contents)
			{
				@header('Content-Type: text/xml; charset=UTF-8');
				echo $contents;
			}
		}
		else
		{
			// Output the QuickBooksPhpDevKit package information
			$this->_headers();

			print('*************************************************************************************************************************' . "\n");
			print('***  Use QuickBooks Web Connector to access this SOAP server.                                                         ***' . "\n");
			print('***  https://developer.intuit.com/app/developer/qbdesktop/docs/get-started/get-started-with-quickbooks-web-connector  ***' . "\n");
			print('*************************************************************************************************************************' . "\n");
			print("\n");
			print($this->_server_version_string . "\n");
			print('   (c) ' . PackageInfo::Package['AUTHOR'] . "\n");
			print('   Visit us at: ' . PackageInfo::Package['WEBSITE'] . "\n");
			print("\n\n");

			if ($debug)
			{
				print(__METHOD__ . '() Parameters: ' . "\n");
				print(' - $return = ' . $return . "\n");
				print(' - $debug  = ' . $debug . "\n");
				print("\n");
				print('Miscellaneous Information: ' . "\n");
				print(' - Logging: ' . $this->_loglevel . "\n");

				if (function_exists('date_default_timezone_get'))
				{
					print(' - Timezone: ' . date_default_timezone_get() . ' (Auto-set: ');
					print (PackageInfo::$TIMEZONE_AUTOSET === true ? 'Yes' : 'No') . ')' . "\n";
				}
				print(' - Current Date/Time: ' . date('Y-m-d H:i:s') . "\n");
				print(' - Error Reporting: ' . error_reporting() . "\n");

				print("\n");
				print('SOAP Adapter: ' . "\n");
				print(' - ' . get_class($this->_server) . "\n");

				print("\n");
				print('Registered SOAP Handler Functions: ' . "\n");
				$handler_functions = array_filter($this->_server->getFunctions(), function($f){return $f != '__construct';});
				print_r($handler_functions);

				/*
				print("\n");
				print('Registered Hooks: ' . "\n");
				//print_r($this->_hooks);		// This is bad because it prints passwords
				foreach ($this->_hooks as $hook => $arr)
				{
					if (!is_array($arr))
					{
						continue;
					}

					print(' - ' . $hook . PackageInfo::$CRLF);
					foreach ($arr as $x)
					{
						$y = current(explode("\n", print_r($x, true)));

						print('    ' . $y . PackageInfo::$CRLF);
					}
				}
				*/

				print("\n");
				print('Detected input: ' . "\n");
				print($input);
				print("\n");
				print("\n");
				print('Extra Bits: ' . "\n");
				print(' - Timestamp: ' . date('Y-m-d H:i:s') . ' -- process took ' . round(microtime(true) - $this->_timestamp, 5) . " seconds\n");
				print(' - Peak Memory Usage: ' . number_format(memory_get_peak_usage(), 0) . " bytes\n");
			}
		}

		return null;
	}

	/**
	 *
	 */
	protected function _callHooks(string $hook, ?string $requestID, ?string $user, ?string $ticket, string &$err, array $hook_data): bool
	{
		$err = '';

		return Callbacks::callHook(
			$this->_driver,
			$this->_hooks,
			$hook,
			$requestID,
			$user,
			$ticket,
			$err,
			$hook_data,
			$this->_callback_config);
	}

	/**
	 * Get debugging information from the SOAP server
	 */
	public function debug(): string
	{
		return print_r($this, true);
	}








	protected static function validateCallbackFunction($callback): ?string
	{
		$errmsg = null;

		// Find the callback type
		$type = Utilities::callbackType($callback, $errmsg);

		//$isValid = true;
		if ($type == Callbacks::TYPE_FUNCTION && !function_exists($callback))
		{
			//$isValid = false;
			$errmsg = 'Callback does not exist: [function] ' . $callback . '(...)';
		}
		else if ($type == Callbacks::TYPE_OBJECT_METHOD || $type == Callbacks::TYPE_HOOK_INSTANCE)			// Object instance method hook
		{
			if (!is_callable($callback))
			{
				//$isValid = false;
				if ($type == Callbacks::TYPE_OBJECT_METHOD)
				{
					$errmsg = 'Object method does not exist: instance of ' . get_class($callback[0]) . '->' . $callback[1] . '(...)';
				}
				else
				{
					$errmsg = 'Hook instance uncallable: instance of ' . get_class($callback[0]) . '->' . $callback[1] . '(...)';
				}
			}
		}
		else if ($type == Callbacks::TYPE_STATIC_METHOD)
		{
			if (!is_callable($callback))
			{
				$errmsg = 'Static method does not exist: ' . $callback . '(...)';

				// is_callable returns true even if the method is not static, so find out if it is a static function
				try
				{
					$method = new \ReflectionMethod($callback);
					if ($method->isPublic() === false)
					{
						$visibility = $method->isProtected() ? 'Protected' : 'Private';
						$errmsg = 'Static method exists but is ' . $visibility . ' instead of Public: ' . $callback . '(...)';
					}
				}
				catch (\Exception $e)
				{
					// Attempt to use reflection to determine if this is really a static method failed... just let it go through.
				}
			}
			else
			{
				// is_callable returns true even if the method is not static, so find out if it is a static function
				try
				{
					$method = new \ReflectionMethod($callback);
					if ($method->isStatic() === false)
					{
						$errmsg = 'Method exists but is not static.  Create an instance and use [$instance, "methodName"] instead: ' . $callback . '(...)';
					}
					else if ($method->isPublic() === false)
					{
						$visibility = $method->isProtected() ? 'Protected' : 'Private';
						$errmsg = 'Static method exists but is ' . $visibility . ' instead of Public: ' . $callback . '(...)';
					}
				}
				catch (\Exception $e)
				{
					// Attempt to use reflection to determine if this is really a static method failed... just let it go through.
				}

			}
		}

		return $errmsg;
	}




	public static function validateHookFunctions(array &$map): ?array
	{
		$invalid = [];

		foreach ($map as $hook => &$callbacks)
		{
			if (!is_array($callbacks))
			{
				$callbacks = [$callbacks];
			}
			else if (count($callbacks) == 2 && in_array(Utilities::callbackType($callbacks, $errmsg), [Callbacks::TYPE_OBJECT_METHOD, Callbacks::TYPE_STATIC_METHOD, Callbacks::TYPE_HOOK_INSTANCE]))
			{
				$callbacks = [$callbacks];
			}

			for ($i = 0; $i < count($callbacks); $i++)
			{
				$callback = &$callbacks[$i];

				$errmsg = static::validateCallbackFunction($callback);
				if ($errmsg !== null)
				{
					$invalid[$hook][$i] = $errmsg;
				}
			}
		}

		return count($invalid) > 0 ? $invalid : null;
	}


	public static function validateErrorHandlerFunctions(array &$map): ?array
	{
		$invalid = [];

		foreach ($map as $errnum => &$callback)
		{
			$errmsg = static::validateCallbackFunction($callback);
			if ($errmsg !== null)
			{
				$invalid[$errnum] = $errmsg;
			}
		}

		return count($invalid) > 0 ? $invalid : null;
	}

	public static function validateMappedFunctions(array &$map): ?array
	{
		$invalid = [];

		foreach ($map as $action => &$config)
		{
			$identity = $action;

			if (!is_array($config))
			{
				$invalid[$identity] = "The Action to Request/Response mapping elements be an associatiive array containing arrays with exactly 2 callback elements (1 Request Function and 1 Response Function) but is a variable of type ". gettype($config);
			}
			else
			{
				$arraySize = count($config);
				if ($arraySize !== 2)
				{
					$invalid[$identity] = "The Action to Request/Response mapping element for $action must be an array containing exactly 2 callbacks (1 Request Function and 1 Response Function) but has $arraySize elements.";
				}
				else if (in_array(Utilities::callbackType($config, $errmsg), [Callbacks::TYPE_OBJECT_METHOD, Callbacks::TYPE_STATIC_METHOD, Callbacks::TYPE_HOOK_INSTANCE]))
				{
					$errmsg = "The Action to Request/Response mapping element must contain exactly 2 callbacks but contains 1 static or instance callback [object, method].";
				}
				else
				{
					$columnToRequestResponse = [
						0 => 'Request',
						1 => 'Response',
					];

					foreach ($columnToRequestResponse as $column => $requestOrResponse)
					{
						if (!isset($config[$column]))
						{
							$errmsg = '';
						}
						$identity = "$action - $requestOrResponse";
						$errmsg = static::validateCallbackFunction($config[$column]);
						if ($errmsg !== null)
						{
							$invalid[$identity] = $errmsg;
						}
					}
				}
			}
		}

		return count($invalid) > 0 ? $invalid : null;
	}
}
