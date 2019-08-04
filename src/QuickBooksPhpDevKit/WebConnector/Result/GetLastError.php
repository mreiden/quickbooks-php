<?php declare(strict_types=1);

/**
 * Result container object for the SOAP ->getLastError() method call
 *
 * Copyright (c) {2010-04-16} {Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Server
 */

namespace QuickBooksPhpDevKit\WebConnector\Result;

use QuickBooksPhpDevKit\WebConnector\Result;

/**
 * Result container object for the SOAP ->getLastError() method call
 */
class GetLastError extends Result
{
	/**
	 * An error message
	 *
	 * @param string $resp
	 */
	public $getLastErrorResult;

	/**
	 * Create a new result object
	 *
	 * @param string $result 		A message describing the last error that occurred
	 */
	public function __construct(string $result)
	{
		$this->getLastErrorResult = $result;
	}
}
