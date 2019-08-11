<?php declare(strict_types=1);

/**
 * QuickBooks ServiceItem (ItemService) object container
 *
 * NOTE: By default, ServiceItems are created as SalesOrPurchase items, and are
 * thus *NOT* created as SalesAndPurchase items. If you want to create an item
 * that is sold *and* purchased, you'll need to set the type with the method:
 * 	-> {@link QBXML\Object\ItemService::isSalesAndPurchase()}
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemService;

/**
 * QuickBooks ServiceItem (ItemService) object
 *
 * NOTE!!!  You should use the ItemService class.
 *          This is deprecated but remains for legacy usage.
 *
 */
class ServiceItem extends ItemService
{
	/**
	 * Create a new QuickBooks_Object_ServiceItem object (ServiceItem)
	 */
	public function __construct(array $arr = [], bool $is_sales_and_purchase = false)
	{
		// Trigger a deprecation warning
		trigger_error('Deprecated class ' . self::class . '. Use ' . ItemService::class . ' instead.', E_USER_DEPRECATED);

		// Call the ItemService constructor
		parent::__construct($arr, $is_sales_and_purchase);
	}
}
