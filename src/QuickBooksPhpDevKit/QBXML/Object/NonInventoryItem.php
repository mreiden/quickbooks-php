<?php declare(strict_types=1);

/**
 * QuickBooks NonInventoryItem (ItemNonInventory) object container
 *
 * NOTE: By default, NonInventoryItems are created as SalesOrPurchase items, and are
 * thus *NOT* created as SalesAndPurchase items. If you want to create an item
 * that is sold *and* purchased, you'll need to set the type with the method:
 * 	-> {@link QuickBooks_Object_NonInventoryItem::isSalesAndPurchase()}
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemNonInventory;

/**
 * QuickBooks NonInventoryItem (ItemNonInventory) Object
 *
 * NOTE!!!  You should use the ItemNonInventory class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemNonInventory instead.
 */
class NonInventoryItem extends ItemNonInventory
{
	public function __construct(array $arr = [], bool $is_sales_and_purchase = false)
	{
                // Trigger a deprecation warning
                trigger_error('Deprecated class ' . self::class . '. Use ' . ItemNonInventory::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr, $is_sales_and_purchase);
	}
}
