<?php declare(strict_types=1);

/**
 *
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
 * @subpackage Client
 */

namespace QuickBooksPhpDevKit\WebConnector\Request;

use QuickBooksPhpDevKit\WebConnector\Request;

/**
 *
 *
 *
 */
class Receiveresponsexml extends Request
{
	public $ticket;

	public $hresult;

	public $message;

	public $response;

	public function __construct(?string $ticket = null, ?string $response = null, $hresult = null, ?string $message = null)
	{
		$this->ticket = $ticket;
		$this->response = $response;
		$this->hresult = $hresult;
		$this->message = $message;
	}
}
