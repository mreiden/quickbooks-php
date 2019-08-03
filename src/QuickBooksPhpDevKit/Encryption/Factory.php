<?php declare(strict_types=1);

/**
 * QuickBooks encryption library factory method
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

/**
 *
 *
 *
 */
class Factory
{
	// , $iv = null, $mode = null
	static public function create(string $encrypt)
	{
		$class = __NAMESPACE__ . "\\". ucfirst(strtolower($encrypt));

		return new $class();
	}

	/**
	 *
	 */
	static public function determine(string &$encrypted): ?string
	{
		if ($encrypted[0] == '{' &&
			false !== ($end = strpos($encrypted, ':')))
		{
			$number = substr($encrypted, 1, $end);

			$method = substr($encrypted, 1 + strlen($number), $number);
			$encrypted = substr($encrypted, $number + 4);

			return $method;
		}

		return null;
	}
}
