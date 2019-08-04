<?php declare(strict_types=1);

/**
 * QuickBooks action queue - A queue of actions to be performed via the QBWC
 *
 * Copyright (c) {2010-04-16} {Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * The QuickBooks action queue can be used to queue up actions that need to be
 * pushed out to QuickBooks, or queue up items which need to be fetched from
 * QuickBooks.
 *
 * For instance, everytime someone creates an account on your website, you
 * would "push" their name, e-mail, etc. into a customer account within
 * QuickBooks. You could use the QuickBooks_Queue class to enqueue a request
 * for each new client like this:
 * <code>
 * require_once 'QuickBooks/Queue.php';
 * $queue = new QuickBooks_Queue('mysql://user:pass@localhost/database');
 * $queue->enqueue(QUICKBOOKS_ADD_CUSTOMER, $the_customer_ID_number_here);
 * </code>
 *
 * The next time the QuickBooks Web Connector calls your QuickBooks SOAP server,
 * the SOAP server calls your SOAP server handler function associated with
 * adding customer records, your handler function will generate the qbXML
 * request, and the request will be sent off to QuickBooks and (if everything
 * goes well) the customer will appear in QuickBooks.
 *
 * If you need to perform a particular action on a set schedule, say, for
 * example, pull customers from QuickBooks to your web application once per
 * day, then you can register a recurring event instead of queueing up a
 * request every single day.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 */

namespace QuickBooksPhpDevKit\WebConnector;

use QuickBooksPhpDevKit\Driver;
use QuickBooksPhpDevKit\Driver\Factory;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;

/**
 * QuickBooks queueing class - Queue up actions to be performed in QuickBooks
 */
class Queue
{
	/**
	 * The default username to use when queueing items
	 * @var string
	 */
	protected $_user;

	/**
	 * The database driver
	 * @var Driver
	 */
	protected $_driver;

	/**
	 * Create a new QuickBooks queue instance
	 *
	 * @param mixed $dsn_or_conn	A DSN-style connection string (i.e.: mysql://root:pass@locahost/database) or a database connection (if you wish to re-use an existing database connection)
	 * @param array $config			Configuration array for the driver
	 */
	public function __construct($dsn_or_conn, ?string $user = null, array $config = [])
	{
		$this->_driver = Factory::create($dsn_or_conn, $config);

		// No default username was provided, fetch the default from the driver
		$user = trim($user ?? '');
		if (empty($user))
		{
			$user = $this->_driver->authDefault();
		}

		// Set the default username
		$this->_user = $user;
	}

	/**
	 * Get or set the username the queue is operating on/for
	 */
	public function user(?string $user = null): ?string
	{
		if (!empty($user))
		{
			$this->_user = $user;
		}

		return $this->_user;
	}

	/**
	 * Request to enter "Interactive Mode" with the Web Connector
	 *
	 * * This function *does not* work. Please don't use it.
	 */
	public function interactive(int $priority = 0, ?string $user = null): bool
	{
		throw new \Exception('Interactive mode is not implemented.  Find another way');
		/*
		if ($this->_driver)
		{
			$tmp = array_merge(range('a', 'z'), range(0, 9));
			shuffle($tmp);
			$random = substr(implode('', $tmp), 0, 8);

			// Use the default user (provided in __construct) if none is given
			$user = trim($user ?? '');
			if (empty($user))
			{
				$user = $this->_user;
			}

			return $this->_driver->queueEnqueue(QUICKBOOKS_INTERACTIVE_MODE, $random, true, $priority, $user);
		}
		*/
		return false;
	}

	/**
	 * Register a recurring event
	 *
	 * Recurring events are actions that get queued up to run every so
	 * often. So, for instance, if you want to make sure you issue a
	 * CustomerQuery every day, you can register a recurring event that occurs
	 * every day, and then the SOAP server/Web Connector will try to make sure
	 * that it gets run every day.
	 *
	 * Note that the SOAP server can't guarantee that an action will occur
	 * every interval *unless* the Web Connector is set to connect at that same
	 * interval or (preferably) more often.
	 *
	 * @param mixed $run_every		This can be either an integer number of seconds, or a string like: "15 minutes", "2 days", "hour", etc.
	 * @param string $action		The QuickBooks action you want to execute
	 * @param mixed $ident			An optional ident string (say you wanted to do a CustomerQuery for customer #14 every 15 minutes, you'd pass CustomerQuery for the action, and 14 for the ident)
	 * @param integer $priority		The priority of the action (higher priorities run first)
	 * @param mixed $extra			Any extra data to include for the request handler
	 * @param string $user			The username of the QuickBooks Web Connector user this event should be registered for
	 * @param boolean $replace		Whether or not this should replace any other recurring events with this action/ident
	 * @return boolean
	 */
	public function recurring($run_every, string $action, $ident, ?int $priority = null, ?array $extra = null, ?string $user = null, ?string $qbxml = null, bool $replace = true): bool
	{
		$ident = $this->validIdent($ident ?? '');

		$priority = $priority ?? Utilities::priorityForAction($action);

		// Use the default user (provided in __construct) if none is given
		$user = trim($user ?? '');
		if (empty($user))
		{
			$user = $this->_user;
		}

		return $this->_driver->recurEnqueue($user, $run_every, $action, $ident, $replace, $priority, $extra, $qbxml);
	}

	/**
	 * Add a new item/action to the QuickBooks queue
	 *
	 * Queue up a request for an action/ident pair to be sent over to
	 * QuickBooks. The next time the Web Connector connects, your request
	 * handler for the given action will be called, generating a qbXML request
	 * which will then be passed to QuickBooks. QuickBooks will pass the
	 * response back to the SOAP server and your response handler will be
	 * called.
	 *
	 * @param string $action		An action to be performed within QuickBooks (see the qbXML and QuickBooks SDK documentation, i.e.: "CustomerAdd", "InvoiceAdd", "CustomerMod", etc.)
	 * @param mixed $ident			A unique identifier (if required) for a record being operated on (i.e. if you're doing a "CustomerAdd", you'd probaly put a unique customer ID number here, so you're SOAP handler function knows which customer it is supposed to add)
	 * @param integer $priorityForAction	The priority of the update (higher priority actions will be pushed to QuickBooks before lower priority actions)
	 * @param array $extra			If you need to make additional bits of data available to your request/response functions, you can pass an array of extra data here
	 * @param string $user			The username of the QuickBooks Web Connector user this item should be queued for
	 * @param boolean $replace		Whether or not to replace any other currently queued entries with the same action/ident
	 * @return boolean
	 */
	public function enqueue(string $action, $ident, ?int $priority = null, ?array $extra = null, ?string $user = null, $qbxml = null, bool $replace = true): bool
	{
		$ident = $this->validIdent($ident);

		// Use the default user (provided in __construct) if none is given
		$user = trim($user ?? '');
		if (empty($user))
		{
			$user = $this->_user;
		}

		return false !== $this->_driver->queueEnqueue($user, $action, $ident, $replace, $priority, $extra, $qbxml);
	}

	/**
	 * Tell whether or not an action/ident already exists in the queue
	 *
	 * @param string $action	An action to be performed within QuickBooks
	 * @param mixed $ident		A unique identifier (if required) for the record being operated on
	 * @param string $user		The username of the user to check if the queued item exists for
	 * @return boolean			Whether or not that action/ident tuple is already in the queue
	 */
	public function exists(string $action, $ident, ?string $user = null): ?array
	{
		$ident = $this->validIdent($ident);

		// Use the default user (provided in __construct) if none is given
		$user = trim($user ?? '');
		if (empty($user))
		{
			$user = $this->_user;
		}

		return $this->_driver->queueExists($user, $action, $ident);
	}

	/**
	 * Tell the number of items currently in the queue
	 *
	 * @param string $user		The username of the user to check the queue size for
	 * @return integer			The number of items in the queue
	 */
	public function size(?string $user = null): ?int
	{
		$queued = true;

		// Use the default user (provided in __construct) if none is given
		$user = trim($user ?? '');
		if (empty($user))
		{
			$user = $this->_user;
		}

		return $this->_driver->queueLeft($user, $queued);
	}

	/**
	 * Forcibly remove an item from the queue
	 */
	public function remove(string $action, $ident, ?string $user = null): ?bool
	{
		$ident = $this->validIdent($ident);

		// Use the default user (provided in __construct) if none is given
		$user = trim($user ?? '');
		if (empty($user))
		{
			$user = $this->_user;
		}

		return $this->_driver->queueRemove($user, $action, $ident);
	}

	/**
	 * Convert ident into a string.
	 */
	private function validIdent($ident): string
	{
		if (!is_string($ident) && !is_int($ident) && !is_float($ident))
		{
			throw new \Exception('Ident must be a string, an int, or a float but got (' . gettype($ident) .')');
		}
		$ident = (string) $ident;

		// Database is limited to 40 characters
		$ident = substr($ident, 0, 40);

		return $ident;
	}

	/**
	 * Get debugging information from the queue
	 */
	public function debug(): array
	{
		return [
			'driver' => var_export($this->_driver),
		];
	}
}
