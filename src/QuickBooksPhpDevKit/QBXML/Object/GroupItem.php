<?php declare(strict_types=1);

/**
 * QuickBooks GroupItem (ItemGroup) object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\QBXML\Object\ItemGroup;

/**
 * QuickBooks GroupItem (ItemGroup) Object
 *
 * NOTE!!!  You should use the ItemGroup class.
 *          This is deprecated but remains for legacy usage.
 *
 * @deprecated Use QBXML\Object\ItemGroup instead.
 */
class GroupItem extends ItemGroup
{
	public function __construct(array $arr = [])
	{
                // Trigger a deprecation warning
                trigger_error('Deprecated class ' . self::class . '. Use ' . ItemGroup::class . ' instead.', E_USER_DEPRECATED);

		parent::__construct($arr);
	}
}
