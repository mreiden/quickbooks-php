<?php declare(strict_types=1);

/**
 * QuickBooks InventoryAssemblyItem (ItemInventoryAssembly) object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemInventoryAssembly;

/**
 * QuickBooks InventoryAssemblyItem (ItemInventoryAssembly) Object
 *
 * NOTE!!!  You should use the ItemInventoryAssembly class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemInventoryAssembly instead.
 */
class InventoryAssemblyItem extends ItemInventoryAssembly
{
	public function __construct(array $arr = [])
	{
                // Trigger a deprecation warning
                trigger_error('Deprecated class ' . self::class . '. Use ' . ItemInventoryAssembly::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr);
	}
}
