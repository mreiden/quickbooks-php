<?php declare(strict_types=1);

/**
 * QuickBooks FixedAssetItem (ItemFixedAsset) object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemFixedAsset;

/**
 * QuickBooks FixedAssetItem (ItemFixedAsset) Object
 *
 * NOTE!!!  You should use the ItemFixedAsset class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemFixedAsset instead.
 */
class FixedAssetItem extends ItemFixedAsset
{
	public function __construct(array $arr = [])
	{
                // Trigger a deprecation warning
                trigger_error('Deprecated class ' . self::class . '. Use ' . ItemFixedAsset::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr);
	}
}
