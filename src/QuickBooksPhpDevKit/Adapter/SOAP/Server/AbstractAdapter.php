<?php declare(strict_types=1);

/**
 * Adapter class for the built-in QuickBooks SOAP server
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
 * @subpackage Adapter
 */

namespace QuickBooksPhpDevKit\Adapter\SOAP\Server;

use \stdClass as Server;
use QuickBooksPhpDevKit\Adapter\SOAP\Server\AdapterInterface;
use QuickBooksPhpDevKit\PackageInfo;

/**
 *
 */
abstract class AbstractAdapter implements AdapterInterface
{
	// \stdClass is just a placeholder in this abstract class.  The real adapters have the class that actually gets used.
	protected $SoapServerClass = Server::class;

	/**
	 * SoapServer Instance
	 */
	protected $_server;

	/**
	 * Path to WSDL file
	 */
	protected $wsdl;

	/**
	 * Create a new adapter for the built-in SOAP server
	 *
	 * @param string $wsdl				The path to the WSDL file
	 * @param array $soap_options		An array of SOAP server options
	 */
	public function __construct(?string $wsdl = null, array $soap_options = [])
	{
		$this->configure($wsdl, $soap_options);
	}

	protected function configure(?string $wsdl, array $soap_options)
	{
		$this->wsdl = is_null($wsdl) ? PackageInfo::$WSDL : $wsdl;

		$soap_options = $this->_defaults($soap_options);
		// If safe mode is turned on, this causes a NOTICE/WARNING to be issued...
		if (!ini_get('safe_mode'))
		{
			set_time_limit($soap_options['time_limit']);
		}

		$this->_server = new $this->SoapServerClass($this->wsdl, $soap_options);
	}

	/**
	 * Get WSDL file location
	 */
	public function getWsdlPath(): string
	{
		return $this->wsdl;
	}


	/**
	 * Handle an incoming SOAP request
	 */
	public function handle(string $raw_http_input): void
	{
		$this->_server->handle($raw_http_input);
	}

	/**
	 * Merge soap options with the defaults
	 */
	final protected function _defaults(array $arr): array
	{
		$defaults = [
			'error_handler' => '',
			'use_builtin_error_handler' => false,
			'time_limit' => 0,
			'log_to_file' => null,
			'log_to_syslog' => null,
			'masking' => true,
		];
		$arr = array_merge($defaults, $arr);

		return $arr;
	}

	/**
	 * Set the class that will handle all the soap requests.
	 * The class must have methods matching the WSDL functions (e.g. Authenticate, CloseConnection)
	 */
	public function setClass(string $class, $dsn_or_conn, array $map, array $onerror, array $hooks, int $log_level, string $raw_http_input, array $handler_options, array $driver_options, array $callback_options): void
	{
		$this->_server->setClass($class, $dsn_or_conn, $map, $onerror, $hooks, $log_level, $raw_http_input, $handler_options, $driver_options, $callback_options);
	}

	/**
	 * Returns a list of SOAP functions this server supports based on the WSDL file.
	 */
	public function getFunctions(): array
	{
		return $this->_server->getFunctions();
	}
}
