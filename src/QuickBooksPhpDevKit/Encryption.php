<?php declare(strict_types=1);

/**
 * QuickBooks encryption library base class
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
 */

namespace QuickBooksPhpDevKit;

/**
 *
 *
 */
abstract class Encryption
{
	/**
	 *
	 *
	 *
	 */
	public function prefix(string $str): string
	{
		return '{' . strlen(get_class($this)) . ':' . strtolower(get_class($this)) . '}' . $str;
	}

	/**
	 *
	 */
	static function salt(): string
	{
		$tmp = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
		shuffle($tmp);

		$salt = substr(implode('', $tmp), 0, 32);

		return $salt;
	}
}
