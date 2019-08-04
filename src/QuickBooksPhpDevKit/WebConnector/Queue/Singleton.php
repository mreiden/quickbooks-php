<?php declare(strict_types=1);

/**
 * QuickBooks singleton class for the queueing class
 *
 * Copyright (c) {2010-04-16} {Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * Web Connector applications will often use the queueing class within many
 * different functions within the applications. It is desireable then to always
 * use a single instance of the queueing class to avoid unneccessary database
 * connections and code cruft. This singleton class provides a way to use a
 * single instance of the queueing class without resorting to the use of
 * globals.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Queue
 */

namespace QuickBooksPhpDevKit\WebConnector;

use QuickBooksPhpDevKit\WebConnector\Queue;

/**
 * QuickBooks singleton class for the QuickBooks_Queue class
 */
class Singleton
{
	/**
	 * Initialize the queueing object
	 * @return Queue|bool
	 */
	static public function initialize(?string $dsn = null, ?string $user = null, array $config = [], bool $return_boolean = true)
	{
		static $instance;
		if (empty($instance))
		{
			if (empty($dsn))
			{
				return false;
			}

			$instance = new Queue($dsn, $user, $config);
		}

		if ($return_boolean && $instance)
		{
			return true;
		}

		return $instance;
	}

	/**
	 * Get the instance of the queueing class
	 *
	 * @return QuickBooks_WebConnector_Queue
	 */
	static public function getInstance()
	{
		return static::initialize(null, null, null, false);
	}
}
