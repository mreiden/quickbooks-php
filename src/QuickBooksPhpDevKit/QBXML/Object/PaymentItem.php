<?php declare(strict_types=1);

/**
 * QuickBooks PaymentItem (ItemPayment) object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemPayment;

/**
 * QuickBooks PaymentItem (ItemPayment) Object
 *
 * NOTE!!!  You should use the ItemPayment class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemPayment instead.
 */
class PaymentItem extends ItemPayment
{
	public function __construct(array $arr = [], bool $is_sales_and_purchase = false)
	{
                // Trigger a deprecation warning
                trigger_error('Deprecated class ' . self::class . '. Use ' . ItemPayment::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr, $is_sales_and_purchase);
	}
}
