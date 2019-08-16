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

use QuickBooksPhpDevKit\Adapter\SOAP\Server\AbstractAdapter;
use QuickBooksPhpDevKit\Adapter\SOAP\Server\AdapterInterface;

/**
 * Adapter for the \SoapServer class provided by PHP's SOAP extension.
 */
class PhpExtensionAdapter extends AbstractAdapter implements AdapterInterface
{
	protected $SoapServerClass = \SoapServer::class;
}
