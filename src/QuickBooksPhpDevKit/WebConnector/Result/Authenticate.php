<?php declare(strict_types=1);

/**
 * Result container object for the SOAP ->authenticate() method call
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
 * Result container object for the SOAP ->authenticate() method call
 */
class Authenticate extends Result
{
	/**
	 * A two element array indicating the result of the call to ->authenticate()
	 *
	 * @var array
	 */
	public $authenticateResult;

	/**
	 * Create a new result object
	 *
	 * @param string $ticket	The ticket of the new login session
	 * @param string $status	The status of the new login session (blank, a company file path, or "nvu" for an invalid login)
	 */
	public function __construct(string $ticket, string $status, $wait_before_next_update = null, $min_run_every_n_seconds = null)
	{
		$this->authenticateResult = [ $ticket, $status ];

		if ((int) $wait_before_next_update)
		{
			$this->authenticateResult[] = (int) $wait_before_next_update;
		}

		if ((int) $min_run_every_n_seconds)
		{
			$this->authenticateResult[] = (int) $min_run_every_n_seconds;
		}
	}
}
