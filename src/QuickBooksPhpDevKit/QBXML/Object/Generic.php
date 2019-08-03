<?php declare(strict_types=1);

/**
 * QuickBooks Customer object container
 *
 * Not used, might be used in future versions
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;

/**
 *
 */
class Generic extends AbstractQbxmlObject
{
	protected $_override;

	public function __construct(array $arr = [], string $override = '')
	{
		$this->setOverride($override);

		parent::__construct($arr);
	}

	public function getOverride(): string
	{
		return $this->_override;
	}

	public function setOverride(string $override): bool
	{
		$this->_override = $override;

		return true;
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return $this->getOverride();
	}
}
