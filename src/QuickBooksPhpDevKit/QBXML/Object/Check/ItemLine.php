<?php declare(strict_types=1);

/**
 * Check class for QuickBooks
 *
 * @author Keith Palmer Jr. <keith@ConsoliBYTE.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object\Check;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Check;
use QuickBooksPhpDevKit\XML\Node;

/**
 *
 */
class ItemLine extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\Check\ItemLine object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	// Path: ItemRef ListID, datatype:

	/**
	 * Set the ItemRef ListID for the Check
	 */
	public function setItemListID(string $ListID): bool
	{
		return $this->set('ItemRef ListID', $ListID);
	}

	/**
	 * Get the ItemRef ListID for the Check
	 */
	public function getItemListID(): ?string
	{
		return $this->get('ItemRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setItemApplicationID($value): bool
	{
		return $this->set('ItemRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ITEM'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: ItemRef FullName, datatype:

	/**
	 * Set the ItemRef FullName for the Check
	 */
	public function setItemFullName(string $FullName): bool
	{
		return $this->set('ItemRef FullName', $FullName);
	}

	/**
	 * Get the ItemRef FullName for the Check
	 */
	public function getItemFullName(): ?string
	{
		return $this->get('ItemRef FullName');
	}

	// Path: Desc, datatype:

	/**
	 * Set the Desc for the Check
	 */
	public function setDesc(string $value): bool
	{
		return $this->set('Desc', $value);
	}

	/**
	 * Get the Desc for the Check
	 */
	public function getDesc(): ?string
	{
		return $this->get('Desc');
	}

	/**
	 * @see self::setDesc()
	 */
	public function setDescription(string $value): bool
	{
		return $this->setDesc($value);
	}

	/**
	 * @see self::getDesc()
	 */
	public function getDescription(): ?string
	{
		return $this->getDesc();
	}
	// Path: Quantity, datatype:

	/**
	 * Set the Quantity for the Check
	 */
	public function setQuantity($value): bool
	{
		return $this->set('Quantity', (float) $value);
	}

	/**
	 * Get the Quantity for the Check
	 */
	public function getQuantity()
	{
		return $this->get('Quantity');
	}

	// Path: UnitOfMeasure, datatype:

	/**
	 * Set the UnitOfMeasure for the Check
	 */
	public function setUnitOfMeasure(string $value): bool
	{
		return $this->set('UnitOfMeasure', $value);
	}

	/**
	 * Get the UnitOfMeasure for the Check
	 */
	public function getUnitOfMeasure(): ?string
	{
		return $this->get('UnitOfMeasure');
	}

	// Path: Cost, datatype:

	/**
	 * Set the Cost for the Check
	 */
	public function setCost($value): bool
	{
		return $this->setAmountType('Cost', $value);
	}

	/**
	 * Get the Cost for the Check
	 */
	public function getCost(): ?string
	{
		return $this->getAmountType('Cost');
	}

	// Path: Amount, datatype:

	/**
	 * Set the Amount for the Check
	 */
	public function setAmount($value): bool
	{
		return $this->setAmountType('Amount', $value);
	}

	/**
	 * Get the Amount for the Check
	 */
	public function getAmount(): ?string
	{
		return $this->getAmountType('Amount');
	}

	// Path: TaxAmount, datatype:

	/**
	 * Set the TaxAmount for the Check
	 */
	public function setTaxAmount($value): bool
	{
		return $this->setAmountType('TaxAmount', $value);
	}

	/**
	 * Get the TaxAmount for the Check
	 */
	public function getTaxAmount(): ?string
	{
		return $this->get('TaxAmount');
	}

	// Path: CustomerRef ListID, datatype:

	/**
	 * Set the CustomerRef ListID for the Check
	 */
	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID', $ListID);
	}

	/**
	 * Get the CustomerRef ListID for the Check
	 */
	public function getCustomerListID(): ?string
	{
		return $this->get('CustomerRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setCustomerApplicationID($value): bool
	{
		return $this->set('CustomerRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CUSTOMER'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: CustomerRef FullName, datatype:

	/**
	 * Set the CustomerRef FullName for the Check
	 */
	public function setCustomerName(string $FullName): bool
	{
		return $this->set('CustomerRef FullName', $FullName);
	}

	/**
	 * Get the CustomerRef FullName for the Check
	 */
	public function getCustomerName(): ?string
	{
		return $this->get('CustomerRef FullName');
	}

	// Path: ClassRef ListID, datatype:

	/**
	 * Set the ClassRef ListID for the Check
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	/**
	 * Get the ClassRef ListID for the Check
	 */
	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setClassApplicationID($value): bool
	{
		return $this->set('ClassRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CLASS'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: ClassRef FullName, datatype:

	/**
	 * Set the ClassRef FullName for the Check
	 */
	public function setClassName(string $FullName): bool
	{
		return $this->set('ClassRef FullName', $FullName);
	}

	/**
	 * Get the ClassRef FullName for the Check
	 */
	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	// Path: SalesTaxCodeRef ListID, datatype:

	/**
	 * Set the SalesTaxCodeRef ListID for the Check
	 */
	public function setSalesTaxCodeListID(string $ListID): bool
	{
		return $this->set('SalesTaxCodeRef ListID', $ListID);
	}

	/**
	 * Get the SalesTaxCodeRef ListID for the Check
	 */
	public function getSalesTaxCodeListID(): ?string
	{
		return $this->get('SalesTaxCodeRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 */
	public function setSalesTaxCodeApplicationID($value): bool
	{
		return $this->set('SalesTaxCodeRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_SALESTAXCODE'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: SalesTaxCodeRef FullName, datatype:

	/**
	 * Set the SalesTaxCodeRef FullName for the Check
	 */
	public function setSalesTaxCodeName(string $FullName): bool
	{
		return $this->set('SalesTaxCodeRef FullName', $FullName);
	}

	/**
	 * Get the SalesTaxCodeRef FullName for the Check
	 */
	public function getSalesTaxCodeName(): ?string
	{
		return $this->get('SalesTaxCodeRef FullName');
	}

	// Path: BillableStatus, datatype:

	/**
	 * Set the BillableStatus for the Check
	 */
	public function setBillableStatus(string $value): bool
	{
		return $this->set('BillableStatus', $value);
	}

	/**
	 * Get the BillableStatus for the Check
	 */
	public function getBillableStatus(): ?string
	{
		return $this->get('BillableStatus');
	}

	// Path: OverrideItemAccountRef ListID, datatype:

	/**
	 * Set the OverrideItemAccountRef ListID for the Check
	 */
	public function setOverrideItemAccountListID(string $ListID): bool
	{
		return $this->set('OverrideItemAccountRef ListID', $ListID);
	}

	/**
	 * Get the OverrideItemAccountRef ListID for the Check
	 */
	public function getOverrideItemAccountListID(): ?string
	{
		return $this->get('OverrideItemAccountRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setOverrideItemAccountApplicationID($value): bool
	{
		return $this->set('OverrideItemAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_OVERRIDEITEMACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	// Path: OverrideItemAccountRef FullName, datatype:

	/**
	 * Set the OverrideItemAccountRef FullName for the Check
	 */
	public function setOverrideItemAccountName(string $FullName): bool
	{
		return $this->set('OverrideItemAccountRef FullName', $FullName);
	}

	/**
	 * Get the OverrideItemAccountRef FullName for the Check
	 */
	public function getOverrideItemAccountName(): ?string
	{
		return $this->get('OverrideItemAccountRef FullName');
	}

	public function asXML(?string $root = null, ?string $parent = null, $object = null): Node
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($parent)
		{
			case PackageInfo::Actions['ADD_CHECK']:
				$root = 'ItemLineAdd';
				$parent = null;
				break;

			case PackageInfo::Actions['MOD_CHECK']:
				$root = 'ItemLineMod';
				$parent = null;
				break;
		}

		return parent::asXML($root, $parent, $object);
	}

	public function object(): string
	{
		return 'ItemLine';
	}
}
