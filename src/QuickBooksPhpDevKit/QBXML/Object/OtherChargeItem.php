<?php declare(strict_types=1);

/**
 * QuickBooks OtherChargeItem (ItemOtherCharge) object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemOtherCharge;

/**
 * QuickBooks OtherChargeItem (ItemOtherCharge) Object
 *
 * NOTE!!!  You should use the ItemOtherCharge class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemOtherCharge instead.
 */
class OtherChargeItem extends ItemOtherCharge
{
	public function __construct(array $arr = [], bool $is_sales_and_purchase = false)
	{
                // Trigger a deprecation warning
                trigger_error('Deprecated class ' . self::class . '. Use ' . ItemOtherCharge::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr, $is_sales_and_purchase);
	}
}
