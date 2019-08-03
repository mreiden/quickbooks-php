<?php declare(strict_types=1);

/**
 * Adapter class for the PHP SOAP ext/soap SOAP server
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

use \SoapServer as Server;
use QuickBooksPhpDevKit\Adapter\SOAP\Server\AbstractAdapter;
use QuickBooksPhpDevKit\Adapter\SOAP\Server\AdapterInterface;
use QuickBooksPhpDevKit\PackageInfo;

/**
 *
 */
class PhpExtensionAdapter extends AbstractAdapter implements AdapterInterface
{
	protected $SoapServerClass = Server::class;

	/**
	 * Create a new adapter for PHP's SOAP Extension SoapServer
	 *
	 * @param string $wsdl				The path to the WSDL file
	 * @param array $soap_options		An array of SOAP server options
	 */
	public function __construct(?string $wsdl = null, array $soap_options = [])
	{
		$this->configure($wsdl, $soap_options);
	}
}
