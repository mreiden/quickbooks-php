<?php declare(strict_types=1);

/**
 * QuickBooks driver singleton
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
 * @subpackage Driver
 */

namespace QuickBooksPhpDevKit\Driver;

use QuickBooksPhpDevKit\{
	Driver,
	Driver\Factory,
};

/**
 *
 */
class Singleton
{
	/**
	 *
	 */
	public static function getInstance($dsn_or_conn = null, array $options = [], array $hooks = [], ?int $log_level = null): Driver
	{
		static $instance = null;

		if (null === $instance)
		{
			$instance = Factory::create($dsn_or_conn, $options, $hooks, $log_level);
		}

		return $instance;
	}

	/**
	 *
	 *
	 */
	public static function initialize($dsn_or_conn = null, array $options = [], array $hooks = [], ?int $log_level = null): Driver
	{
		return self::getInstance($dsn_or_conn, $options, $hooks, $log_level);
	}
}
