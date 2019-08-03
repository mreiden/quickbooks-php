<?php declare(strict_types=1);

/**
 * QuickBooks encryption library: Blowfish
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
 * @subpackage Encryption
 */

namespace QuickBooksPhpDevKit\Encryption;

use QuickBooks\Encryption;

class Blowfish extends Encryption
{
	public const CRYPT_ENGINE_BUILTIN = 'builtin';
	public const CRYPT_ENGINE_MCRYPT = 'mcrypt';
	public const CRYPT_ENGINE_GUESS = 'guess';
	public const CRYPT_ENGINE__ = 'guess';

	public function __construct()
	{

	}

	public function setIV()
	{

	}

	public function getIV()
	{

	}

	public function setMode()
	{

	}

	public function getMode()
	{

	}

	public function encrypt()
	{

	}

	public function decrypt()
	{

	}
}
