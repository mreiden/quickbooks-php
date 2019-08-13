<?php declare(strict_types=1);

/**
 *
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage SQL
 */

namespace QuickBooksPhpDevKit;

/**
 *
 */
class SQL
{
	/**
	 * Hook which occurs every time a new record is INSERTed into the SQL mirror
	 */
	public const HOOK_SQL_INSERT = 'QuickBooks_SQL sql-insert';

	/**
	 * Hook which occurs every time a record is UPDATEd in the SQL mirror
	 */
	public const HOOK_SQL_UPDATE = 'QuickBooks_SQL sql-update';

	/**
	 *
	 */
	public const HOOK_SQL_DELETE = 'QuickBooks_SQL sql-delete';

	/**
	 *
	 */
	public const HOOK_SQL_INVENTORY = 'QuickBooks_SQL sql-inventory';

	public const HOOK_SQL_INVENTORYASSEMBLY = 'QuickBooks_SQL sql-inventoryassembly';

	/**
	 *
	 */
	public const HOOK_QUICKBOOKS_INSERT = 'QuickBooks_SQL quickbooks-insert';

	/**
	 *
	 */
	public const HOOK_QUICKBOOKS_UPDATE = 'QuickBooks_SQL quickbooks-update';

	/**
	 *
	 */
	public const HOOK_QUICKBOOKS_DELETE = 'QuickBooks_SQL quickbooks-delete';

	/**
	 *
	 */
	protected $_config;

	/**
	 *
	 */
	public function __construct(string $dsn, array $sql_options = [], array $driver_options = [])
	{
		$this->_config = $this->_defaults($sql_options);
	}

	protected function _defaults(array $options): array
	{
		$defaults = [];

		return array_merge($defaults, $options);
	}

	/**
	 * Tell whether or not a string starts with another string
	 */
	protected function _startsWith(string $str, string $startswith): bool
	{
		return (substr($str, 0, strlen($startswith)) === $startswith);
	}

	/**
	 * Execute an SQL query and return the result resource
	 *
	 * @param string $sql		The SQL query to execute
	 * @param boolean $look		Whether or not to examine the query and see if it's an INSERT/UPDATE/DELETE query
	 * @return resource
	 */
	public function query(string $sql, bool $look = true)
	{
		if ($this->_driver)
		{
			/*
			if ($look)
			{
				$tmp = trim(strtoupper($sql));

				if ($this->_startsWith($sql, 'UPDATE '))
				{

				}
				else if ($this->_startsWith($sql, 'INSERT INTO '))
				{

				}
				else if ($this->_startsWith($sql, 'DELETE FROM '))
				{

				}
			}
			*/

			return $this->_driver->query($sql);
		}

		return false;
	}
}
