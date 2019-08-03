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

namespace QuickBooksPhpDevKit\SQL;

/**
 *
 */
class SqlObject
{
	/**
	 * @var string
	 */
	protected $_table;

	/**
	 * @var string
	 */
	protected $_path;

	/**
	 * @var array
	 */
	protected $_arr;

	/**
	 *
	 */
	public function __construct(?string $table, ?string $path, array $arr = [])
	{
		$this->_table = $table;
		$this->_path = $path;
		$this->_arr = $arr;
	}

	/**
	 * Return the type of SQL object this is
	 *
	 * @deprecated
	 */
	public function type(): string
	{
		return $this->_table;
	}
	public function table(): string
	{
		return $this->_table;
	}

	public function path(): string
	{
		return $this->_path;
	}

	/**
	 * Set an attribute of the SQL object
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set(string $key, $value): void
	{
		$this->_arr[$key] = $value;
	}

	/**
	 * Set an attribute of the SQL object
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function remove(string $key): void
	{
		if ($this->exists($key))
		{
			unset($this->_arr[$key]);
		}
	}

	/**
	 * Change the path (i.e. "InvoiceRet InvoiceLineRet" of this SQL object to something else
	 *
	 * @param string $path
	 * @return void
	 */
	public function change(string $path): void
	{
		$this->_path = $path;
	}

	public function get(string $key, $default = null)
	{
		if ($this->exists($key))
		{
			return $this->_arr[$key];
		}

		return $default;
	}

	public function exists(string $key): bool
	{
		return isset($this->_arr[$key]);
	}

	public function asArray(): array
	{
		return $this->_arr;
	}

	public function asXML(): void
	{

	}
}
