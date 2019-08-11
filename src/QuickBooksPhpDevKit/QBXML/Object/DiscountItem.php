<?php declare(strict_types=1);

/**
 * QuickBooks DiscountItem (ItemDiscount) object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemDiscount;

/**
 * QuickBooks DiscountItem (ItemDiscount) class
 *
 * NOTE!!!  You should use the ItemDiscount class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemDiscount instead.
 */
class DiscountItem extends ItemDiscount
{
	public function __construct(array $arr = [])
	{
		// Trigger a deprecation warning
		trigger_error('Deprecated class ' . self::class . '. Use ' . ItemDiscount::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr);
	}
}
