<?php declare(strict_types=1);

/**
 * QuickBooks SalesTaxItem (ItemSalesTax) object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemSalesTax;

/**
 * QuickBooks SalesTaxItem (ItemSalesTax) Object
 *
 * NOTE!!!  You should use the ItemSalesTax class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemSalesTax instead.
 */
class SalesTaxItem extends ItemSalesTax
{
	public function __construct(array $arr = [])
	{
                // Trigger a deprecation warning
                trigger_error('Deprecated class ' . self::class . '. Use ' . ItemSalesTax::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr);
	}
}
