<?php declare(strict_types=1);

/**
 * QuickBooks Server-Adapter interface
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * SOAP servers and clients within the QuickBooks class are not accessed
 * directly, but instead via Adapter classes so that we can support more than
 * one PHP SOAP server and client type (nuSOAP, PEAR SOAP, PHP ext/soap, etc.)
 *
 * This is the interface for the server adapters. All supported SOAP servers
 * must implement this interface to function with the QuickBooks framework.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Adapter
 */

namespace QuickBooksPhpDevKit\Adapter\SOAP\Server;

/**
 * SOAP Server Adapter Interface
 */
interface AdapterInterface
{
	/**
	 * Create a new instance of the server adapter
	 *
	 * @param string $wsdl			The path to the WSDL file
	 * @param array $soap_options	Any SOAP configuration options to pass to the server class
	 */
	public function __construct(string $wsdl, array $soap_options);

	/**
	 * Handle a SOAP request
	 */
	public function handle(string $raw_http_input): void;

	/**
	 * Return a list of implemented SOAP methods/functions
	 */
	public function getFunctions(): array;

	/**
	 * Set a class whose methods will handle various SOAP methods/functions
	 */
	public function setClass(string $class, $dsn_or_conn, array $map, array $onerror, array $hooks, int $log_level, string $raw_http_input, array $handler_options, array $driver_options, array $callback_options): void;
}
