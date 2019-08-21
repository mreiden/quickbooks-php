<?php declare(strict_types=1);

/**
 * QuickBooks ListDeleted object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\{
	PackageInfo,
	QBXML\AbstractQbxmlObject,
};

/**
 *
 */
class ListDeleted extends AbstractQbxmlObject
{
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListDelType for this item
	 */
	public function setListDelType(string $type): bool
	{
		$valid = [
			'account' => 'Account',
			'billingrate' => 'BillingRate',
			'class' => 'Class',
			'currency' => 'Currency',
			'customer' => 'Customer',
			'customermsg' => 'CustomerMsg',
			'customertype' => 'CustomerType',
			'datedriventerms' => 'DateDrivenTerms',
			'employee' => 'Employee',
			'inventorysite' => 'InventorySite',
			'itemdiscount' => 'ItemDiscount',
			'itemfixedasset' => 'ItemFixedAsset',
			'itemgroup' => 'ItemGroup',
			'iteminventory' => 'ItemInventory',
			'iteminventoryassembly' => 'ItemInventoryAssembly',
			'itemnoninventory' => 'ItemNonInventory',
			'itemothercharge' => 'ItemOtherCharge',
			'itempayment' => 'ItemPayment',
			'itemsalestax' => 'ItemSalesTax',
			'itemsalestaxgroup' => 'ItemSalesTaxGroup',
			'itemservice' => 'ItemService',
			'itemsubtotal' => 'ItemSubtotal',
			'jobtype' => 'JobType',
			'othername' => 'OtherName',
			'paymentmethod' => 'PaymentMethod',
			'payrollitemnonwage' => 'PayrollItemNonWage',
			'payrollitemwage' => 'PayrollItemWage',
			'pricelevel' => 'PriceLevel',
			'salesrep' => 'SalesRep',
			'salestaxcode' => 'SalesTaxCode',
			'shipmethod' => 'ShipMethod',
			'standardterms' => 'StandardTerms',
			'todo' => 'ToDo',
			'unitofmeasureset' => 'UnitOfMeasureSet',
			'vehicle' => 'Vehicle',
			'vendor' => 'Vendor',
			'vendortype' => 'VendorType',
			'workerscompcode' => 'WorkersCompCode',
		];

		$type = strtolower(trim($type));
		if (!isset($valid[$type]))
		{
			throw new \Exception('ListDelType is invalid. See valid options in ' . __METHOD__);
		}

		return $this->set('ListDelType', $valid[$type]);
	}

	/**
	 * Get the ListDelType for this item
	 */
	public function getListDelType(): string
	{
		return $this->get('ListDelType');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_LISTDELETED'];
	}
}
