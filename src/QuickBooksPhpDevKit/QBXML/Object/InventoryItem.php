<?php declare(strict_types=1);

/**
 * QuickBooks InventoryItem (ItemInventory) object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemInventory;

/**
 * QuickBooks InventoryItem (ItemInventory) Object
 *
 * NOTE!!!  You should use the ItemInventory class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemInventory instead.
 */
class InventoryItem extends ItemInventory
{
	public function __construct(array $arr = [])
	{
                // Trigger a deprecation warning
                trigger_error('Deprecated class ' . self::class . '. Use ' . ItemInventory::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr);
	}
}
