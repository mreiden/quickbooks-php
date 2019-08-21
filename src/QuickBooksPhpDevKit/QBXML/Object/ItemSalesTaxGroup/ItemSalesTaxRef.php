<?php declare(strict_types=1);

/**
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\ItemSalesTaxGroup;

use QuickBooksPhpDevKit\{
	PackageInfo,
	QBXML\AbstractQbxmlObject,
};

/**
 *
 *
 */
class ItemSalesTaxRef extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks SalesTaxGroupItem ItemSalesTaxRef object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	public function setFullName(string $FullName): bool
	{
		return $this->setFullNameType('FullName', null, null, $FullName);
	}

	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	public function getListID(): string
	{
		return $this->get('ListID');
	}

	public function getFullName(): string
	{
		return $this->get('FullName');
	}

	/**
	 * Tell the type of object this is
	 */
	public function object(): string
	{
		return 'ItemSalesTaxRef';
	}
}
