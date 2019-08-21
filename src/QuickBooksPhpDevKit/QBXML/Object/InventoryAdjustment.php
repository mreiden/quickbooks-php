<?php declare(strict_types=1);

/**
 * QuickBooks InventoryAdjustment object container
 *
 * @todo Verify the get/set methods on this one... it was copied from InventoryItem
 *
 * @author Harley Laue <harley.laue@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\{
	PackageInfo,
	QBXML\AbstractQbxmlObject,
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\Object\InventoryAdjustment\InventoryAdjustmentLine;

/**
 * Quickbooks InventoryAdjustment definition
 */
class InventoryAdjustment extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\InventoryAdjustment object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Gets the Account ListID
	 */
	public function getAccountListID(): ?string
	{
		return $this->get('AccountRef ListID');
	}

	/**
	 * Set the Account ListID
	 */
	public function setAccountListID(string $ListID): bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	/**
	 * Gets the Account Name
	 */
	public function getAccountName(): ?string
	{
		return $this->get('AccountRef FullName');
	}

	/**
	 * Set the account Name
	 */
	public function setAccountName(string $value): bool
	{
		return $this->set('AccountRef FullName', $value);
	}

	/**
	 * Gets the transaction date
	 */
	public function getTxnDate(?string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('TxnDate');
	}

	/**
	 * Set the transaction date
	 */
	public function setTxnDate($value): bool
	{
		return $this->setDateType('TxnDate', $value);
	}

	/**
	 * Gets the RefNumber
	 */
	public function getRefNumber(): ?string
	{
		return $this->get('RefNumber');
	}

	/**
	 * Set the RefNumber
	 */
	public function setRefNumber($value): bool
	{
		return $this->set('RefNumber', $value);
	}

	/**
	 * Gets the Memo
	 */
	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	/**
	 * Set the Memo
	 */
	public function setMemo(string $value): bool
	{
		return $this->set('Memo', $value);
	}

	/**
	 * Gets the Customer ListID
	 */
	public function getCustomerListID(): ?string
	{
		return $this->get('CustomerRef ListID');
	}

	/**
	 * Set the Customer ListID
	 */
	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID', $ListID);
	}

	/**
	 * Gets the Customer Name
	 */
	public function getCustomerName(): ?string
	{
		return $this->get('CustomerRef FullName');
	}

	/**
	 * Set the Customer Name
	 */
	public function setCustomerName(string $name): bool
	{
			return $this->set('CustomerRef FullName', $name);
	}

	/**
	 * Gets the Class ListID
	 */
	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	/**
	 * Set the class ListID
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	/**
	 * Gets the Class Name
	 */
	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	/**
	 * Set the class name
	 */
	public function setClassName(string $name): bool
	{
		return $this->set('ClassRef Name', $name);
	}

	public function addInventoryAdjustmentLine(InventoryAdjustmentLine $obj): bool
	{
		return $this->addListItem('InventoryAdjustmentLine', $obj);
	}

	/**
	 * Gets the InventoryAdjustmentLine at @param $i
	 *
	 * @param $i a number between 0 and added Lines-1
	 */
	public function getInventoryAdjustmentLine(int $i): ?InventoryAdjustmentLine
	{
		return $this->getListItem('InventoryAdjustmentLine', $i);
	}

	/**
	 * Gets a list of all InventoryAdjustment Lines
	 */
	public function getInventoryAdjustmentLines(): array
	{
		return $this->getList('InventoryAdjustmentLine');
	}

	/********* Query ************/
/*
	public function getTxnID()
	{
		return $this->get('TxnID');
	}

	public function setTxnID($TxnID)
	{
		return $this->set('TxnID', $TxnID);
	}

	public function getRefNumberCaseSensitive()
	{
		return $this->get('RefNumberCaseSensitive');
	}

	public function setRefNumberCaseSensitive($value)
	{
		return $this->set('RefNumberCaseSensitive', $value);
	}

	public function getMaxReturned()
	{
		return $this->get('MaxReturned');
	}

	public function setMaxReturned($max)
	{
		return $this->set('MaxReturned', $max);
	}

	public function getFromModifiedDate()
	{
		return $this->get('ModifiedDateRangeFilter FromModifiedDate');
	}

	public function setFromModifiedDate($date)
	{
		return $this->set('ModifiedDateRangeFilter FromModifiedDate', $date);
	}

	public function getToModifiedDate()
	{
		return $this->get('ModifiedDateRangeFilter ToModifiedDate');
	}

	public function setToModifiedDate($date)
	{
		return $this->set('ModifiedDateRangeFilter ToModifiedDate', $date);
	}

	public function getFromTxnDate()
	{
		return $this->get('TxnDateRangeFilter FromTxnDate');
	}

	public function setFromTxnDate($date)
	{
		return $this->set('TxnDateRangeFilter FromTxnDate', $date);
	}

	public function getToTxnDate()
	{
		return $this->get('TxnDateRangeFilter ToTxnDate');
	}

	public function setToTxnDate($date)
	{
		return $this->set('TxnDateRangeFilter ToTxnDate', $date);
	}

	public function getDateMacro()
	{
		return $this->get('TxnDateRangeFilter DateMacro');
	}

	public function setDateMacro($macro)
	{
		return $this->set('TxnDateRangeFilter DateMacro', $macro);
	}

	public function getEntityListID()
	{
		return $this->get('EntityFilter ListID');
	}

	public function setEntityListID($listid)
	{
		return $this->set('EntityFilter ListID', $listid);
	}

	public function getEntityFullName()
	{
		return $this->get('EntityFilter FullName');
	}

	public function setEntityFullName($name)
	{
		return $this->set('EntityFilter FullName', $name);
	}

	public function getEntityListIDWithChildren()
	{
		return $this->get('EntityFilter ListIDWithChildren');
	}

	public function setEntityListIDWithChildren($listid)
	{
		return $this->set('EntityFilter ListIDWithChildren', $listid);
	}

	public function getEntityFullNameWithChildren()
	{
		return $this->get('EntityFilter FullNameWithChildren');
	}

	public function setEntityFullNameWithChildren($name)
	{
		return $this->set('EntityFilter FullNameWithChildren', $name);
	}

	public function getFilterAccountListID()
	{
		return $this->get('AccountFilter ListID');
	}

	public function setFilterAccountListID($listid)
	{
		return $this->set('AccountFilter ListID', $listid);
	}

	public function getFilterAccountFullName()
	{
		return $this->get('AccountFilter FullName');
	}

	public function setFilterAccountFullName($name)
	{
		return $this->set('AccountFilter FullName', $name);
	}

	public function getFilterAccountListIDWithChildren()
	{
		return $this->get('AccountFilter ListIDWithChildren');
	}

	public function setFilterAccountListIDWithChildren($listid)
	{
		return $this->set('AccountFilter ListIDWithChildren', $listid);
	}

	public function getFilterAccountFullNameWithChildren()
	{
		return $this->get('AccountFilter FullNameWithChildren');
	}

	public function setFilterAccountFullNameWithChildren($name)
	{
		return $this->set('AccountFilter FullNameWithChildren', $name);
	}

	public function getFilterItemListID()
	{
		return $this->get('ItemFilter ListID');
	}

	public function setFilterItemListID($listid)
	{
		return $this->set('ItemFilter ListID', $listid);
	}

	public function getFilterItemName()
	{
		return $this->get('ItemFilter FullName');
	}

	public function setFilterItemName($name)
	{
		return $this->set('ItemFilter FullName', $name);
	}

	public function getFilterItemListIDWithChildren()
	{
		return $this->get('ItemFilter ListIDWithChildren');
	}

	public function setFilterItemListIDWithChildren($listid)
	{
		return $this->set('ItemFilter ListIDWithChildren', $listid);
	}

	public function getFilterItemFullNameWithChildren()
	{
		return $this->get('ItemFilter FullNameWithChildren');
	}

	public function setFilterItemFullNameWithChildren($name)
	{
		return $this->set('ItemFilter FullNameWithChildren', $name);
	}

	public function getFilterRefNumberMatchCriterion()
	{
		return $this->get('RefNumberFilter MatchCriterion');
	}

	public function setFilterRefNumberMatchCriterion($refnumber)
	{
		return $this->set('RefNumberFilter MatchCriterion', $refnumber);
	}

	public function getFilterRefNumberRefNumber()
	{
		return $this->get('RefNumberFilter RefNumber');
	}

	public function setFilterRefNumberRefNumber($refnumber)
	{
		return $this->set('RefNumberFilter RefNumber', $refnumber);
	}

	public function getFilterRefNumberRangeFromRefNumber()
	{
		return $this->get('RefNumberRangeFilter FromRefNumber');
	}

	public function setFilterRefNumberRangeFromRefNumber($refnumber)
	{
		return $this->set('RefNumberRangeFilter FromRefNumber', $refnumber);
	}

	public function getFilterRefNumberRangeToRefNumber()
	{
		return $this->get('RefNumberRangeFilter ToRefNumber');
	}

	public function setFilterRefNumberRangeToRefNumber($refnumber)
	{
		return $this->set('RefNumberRangeFilter ToRefNumber', $refnumber);
	}
*/
// Are these needed or useful to include?
/*
	public function getIncludeLineItems()
	{
		return $this->get('IncludeLineItems');
	}

	public function setIncludeLineItems($)
	{
		return $this->set('IncludeLineItems', $);
	}

	public function getIncludeRetElement()
	{
		return $this->get('IncludeRetElement');
	}

	public function setIncludeRetElement($)
	{
		return $this->set('IncludeRetElement', $);
	}

	public function getOwnerID()
	{
		return $this->get('OwnerID');
	}

	public function setOwnerID($)
	{
		return $this->set('OwnerID', $);
	}
*/

	public function asList(string $request): array
	{
		switch ($request)
		{
			case PackageInfo::Actions['ADD_INVENTORYADJUSTMENT'] . 'Rq':

				if (isset($this->_object['InventoryAdjustmentLine']))
				{
					$this->_object['InventoryAdjustmentLineAdd'] = $this->_object['InventoryAdjustmentLine'];
				}
				break;
		}

		return parent::asList($request);
	}

	public function asXML(?string $root = null, ?string $parent = null, ?array $object = null): Node
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($root)
		{
			case PackageInfo::Actions['ADD_INVENTORYADJUSTMENT']:
				foreach ($object['InventoryAdjustmentLineAdd'] as $key => $obj)
				{
					$obj->setOverride('InventoryAdjustmentLineAdd');
				}
				$parent = null;
				break;
		}

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_INVENTORYADJUSTMENT'];
	}
}
