<?php declare(strict_types=1);

/**
 * QuickBooks SalesTaxGroupItem (ItemSalesTaxGroup) object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemSalesTaxGroup;

/**
 * QuickBooks SalesTaxGroupItem (ItemSalesTaxGroup) Object
 *
 * NOTE!!!  You should use the ItemSalesTaxGroup class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemSalesTaxGroup instead.
 */
class SalesTaxGroupItem extends ItemSalesTaxGroup
{
	public function __construct(array $arr = [])
	{
                // Trigger a deprecation warning
                trigger_error('Deprecated class ' . self::class . '. Use ' . ItemSalesTaxGroup::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr);
	}
}
