<?php declare(strict_types=1);

/**
 * QuickBooks SalesOrder object container
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\SalesOrder\SalesOrderLine;

/**
 *
 *
 */
class SalesOrder extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks SalesOrder object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Alias of {@link QBXML\Object\SalesOrder::setTxnID()}
	 */
	public function setTransactionID(string $TxnID): bool
	{
		return $this->setTxnID($TxnID);
	}

	public function getTransactionID(): string
	{
		return $this->getTxnID();
	}

	/**
	 * Set the transaction ID of the Invoice object
	 */
	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}

	public function getTxnId(): string
	{
		return $this->get('TxnID');
	}

	/**
	 * Set the customer ListID
	 */
	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID', $ListID);
	}

	/**
	 * Set the customer ApplicationID (auto-replaced by the API with a ListID)
	 */
	public function setCustomerApplicationID($value): bool
	{
		return $this->set('CustomerRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CUSTOMER'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getCustomerApplicationID()
	{
		return $this->get('CustomerRef ' . PackageInfo::$API_APPLICATIONID);
	}

	/**
	 * Set the customer name
	 */
	public function setCustomerName(string $name): bool
	{
		return $this->set('CustomerRef FullName', $name);
	}

	/**
	 * Get the customer ListID
	 */
	public function getCustomerListID(): string
	{
		return $this->get('CustomerRef ListID');
	}

	/**
	 * Get the customer name
	 */
	public function getCustomerName(): string
	{
		return $this->get('CustomerRef FullName');
	}

	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	public function setClassApplicationID($value): bool
	{
		return $this->set('ClassRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_CLASS'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getClassApplicationID()
	{
		return $this->get('ClassRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setClassName(string $name): bool
	{
		return $this->set('ClassRef FullName', $name);
	}

	public function getClassName(): string
	{
		return $this->get('ClassRef FullName');
	}

	public function getClassListID(): string
	{
		return $this->get('ClassRef ListID');
	}

	public function setARAccountApplicationID($value): bool
	{
		return $this->set('ARAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getARAccountApplicationID()
	{
		return $this->get('ARAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setARAccountListID(string $ListID): bool
	{
		return $this->set('ARAccountRef ListID', $ListID);
	}

	public function setARAccountName(string $name): bool
	{
		return $this->set('ARAccountRef FullName', $name);
	}

	public function getARAccountListID(): string
	{
		return $this->get('ARAccountRef ListID');
	}

	public function getARAccountName(): string
	{
		return $this->get('ARAccountRef FullName');
	}

	public function setTemplateApplicationID($value): bool
	{
		return $this->set('TemplateRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_TEMPLATE'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getTemplateApplicationID()
	{
		return $this->get('TemplateRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setTemplateName(string $name): bool
	{
		return $this->set('TemplateRef FullName', $name);
	}

	public function setTemplateListID(string $ListID): bool
	{
		return $this->set('TemplateRef ListID', $ListID);
	}

	public function getTemplateName(): string
	{
		return $this->get('TemplateRef FullName');
	}

	public function getTemplateListID(): string
	{
		return $this->get('TemplateRef ListID');
	}

	/**
	 * Set the transaction date
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Alias of {@link QuickBooks_Object_Invoice::setTxnDate()}
	 */
	public function setTransactionDate($date)
	{
		return $this->setTxnDate($date);
	}

	/**
	 * Get the transaction date
	 */
	public function getTxnDate(string $format = 'Y-m-d'): string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * Alias of {@link QuickBooks_Object_Invoice::getTxnDate()}
	 */
	public function getTransactionDate(): string
	{
		return $this->getTxnDate();
	}

	/**
	 * Set the reference number
	 */
	public function setRefNumber(string $str): bool
	{
		return $this->set('RefNumber', $str);
	}

	/**
	 * Get the reference number
	 */
	public function getRefNumber(): string
	{
		return $this->get('RefNumber');
	}

	/**
	 *
	 *
	 * @param string $part
	 * @param array $defaults
	 * @return array
	 */
	public function getShipAddress(string $part = null, array $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get('ShipAddress ' . $part);
		}

		return $this->getArray('ShipAddress *', $defaults);
	}

	/**
	 * Set the shipping address for the invoice
	 *
	 * @param string $addr1
	 * @param string $addr2
	 * @param string $addr3
	 * @param string $addr4
	 * @param string $addr5
	 * @param string $city
	 * @param string $state
	 * @param string $postalcode
	 * @param string $country
	 * @param string $note
	 * @return boolean
	 */
	public function setShipAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $postalcode = '', string $country = '', string $note = ''): bool
	{
		$b = false;
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set('ShipAddress Addr' . $i, ${'addr' . $i});
		}

		$this->set('ShipAddress City', $city);
		$this->set('ShipAddress State', $state);
		$this->set('ShipAddress PostalCode', $postalcode);
		$this->set('ShipAddress Country', $country);
		$this->set('ShipAddress Note', $note);

		return true;
	}

	/**
	 *
	 */
	public function getBillAddress(string $part = null, array $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get('BillAddress ' . $part);
		}

		return $this->getArray('BillAddress *', $defaults);
	}

	/**
	 * Set the billing address for the invoice
	 *
	 * @param string $addr1
	 * @param string $addr2
	 * @param string $addr3
	 * @param string $addr4
	 * @param string $addr5
	 * @param string $city
	 * @param string $state
	 * @param string $postalcode
	 * @param string $country
	 * @param string $note
	 * @return void
	 */
	public function setBillAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $postalcode = '', string $country = '', string $note = ''): bool
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set('BillAddress Addr' . $i, ${'addr' . $i});
		}

		$this->set('BillAddress City', $city);
		$this->set('BillAddress State', $state);
		$this->set('BillAddress PostalCode', $postalcode);
		$this->set('BillAddress Country', $country);
		$this->set('BillAddress Note', $note);

		return true;
	}

	public function setIsPending(?bool $pending)
	{
		return $this->setBooleanType('IsPending', $pending);
	}

	public function getIsPending(): ?bool
	{
		return $this->getBooleanType('IsPending');
	}

	public function setPONumber(string $num): bool
	{
		return $this->set('PONumber', $num);
	}

	public function getPONumber(): string
	{
		return $this->get('PONumber');
	}

	public function setTermsListID(string $ListID): bool
	{
		return $this->set('TermsRef ListID', $ListID);
	}

	public function setTermsApplicationID($value): bool
	{
		return $this->set('TermsRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_TERMS'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getTermsApplicationID()
	{
		return $this->get('TermsRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setTermsName(string $name): bool
	{
		return $this->set('TermsRef FullName', $name);
	}

	public function getTermsName(): string
	{
		return $this->get('TermsRef FullName');
	}

	public function getTermsListID(): string
	{
		return $this->get('TermsRef ListID');
	}

	public function setDueDate($date): bool
	{
		return $this->setDateType('DueDate', $date);
	}

	public function getDueDate(string $format = 'Y-m-d'): string
	{
		return $this->getDateType('DueDate', $format);
	}
/*
	public function setSalesRepName(string $name): bool
	{

	}

	public function setSalesRepListID(string $ListID): bool
	{

	}

	public function setSalesRepApplicationID($value): bool
	{

	}

	public function getSalesRepApplicationID()
	{

	}

	public function getSalesRepName(): string
	{

	}

	public function getSalesRepListID(): string
	{

	}
*/
	public function getFOB(): string
	{
		return $this->get('FOB');
	}

	public function setFOB(string $fob): bool
	{
		return $this->set('FOB', $fob);
	}

	public function setShipDate($date): bool
	{
		return $this->setDateType('ShipDate', $date);
	}

	public function getShipDate(string $format = 'Y-m-d'): string
	{
		return $this->getDateType('ShipDate', $format);
	}
/*
	public function setShipMethodApplicationID(): bool
	{

	}

	public function getShipMethodApplicationID()
	{

	}

	public function setShipMethodName(string $name): bool
	{

	}

	public function setShipMethodListID(string $ListID): bool
	{

	}

	public function getShipMethodName(): string
	{

	}

	public function getShipMethodListID(): string
	{

	}

	public function setSalesTaxItemListID(string $ListID): bool
	{

	}

	public function setSalesTaxItemApplicationID($value): bool
	{

	}

	public function getSalesTaxItemApplicationID(): string
	{

	}

	public function setSalesTaxItemName(string $name): bool
	{

	}

	public function getSalesTaxItemName(): string
	{

	}

	public function getSalesTaxItemListID(): string
	{

	}
*/
	public function setMemo(string $memo): bool
	{
		return $this->set('Memo', $memo);
	}

	public function getMemo(): string
	{
		return $this->get('Memo');
	}
/*
	public function setIsToBePrinted(bool $print): bool
	{

	}

	public function getIsToBePrinted(): bool
	{

	}

	public function setIsToBeEmailed(bool $email): bool
	{

	}

	public function getIsToBeEmailed(): bool
	{

	}

	public function setCustomerSalesTaxCodeListID(string $ListID): bool
	{

	}

	public function setCustomerSalesTaxCodeName(string $name): bool
	{

	}

	public function getCustomerSalesTaxCodeListID(string $ListID)
	{

	}

	public function getCustomerSalesTaxCodeName(): string
	{

	}
*/
	public function setLinkToTxnID(string $TxnID): bool
	{
		return $this->set('LinkToTxnID', $TxnID);
	}

	public function getLinkToTxnID(): string
	{
		return $this->get('LinkToTxnID');
	}



	/**
	 * Get an array of all Sales Order Line
	 */
	public function listSalesOrderLines(): array
	{
		return $this->getList('SalesOrderLine');
	}

	/**
	 * Get a specific Sales Order Line
	 */
	public function getSalesOrderLine(int $i): ?SalesOrderLine
	{
		return $this->getListItem('SalesOrderLine', $i);
	}

	/**
	 * Add a Sales Order Line
	 */
	public function addSalesOrderLine(SalesOrderLine $obj): bool
	{
		return $this->addListItem('SalesOrderLine', $obj);
	}


	public function setOther(string $other): bool
	{
		return $this->set('Other', $other);
	}

	public function getOther(): string
	{
		return $this->get('Other');
	}

	public function asList(string $request)
	{
		switch ($request)
		{
			case 'SalesOrderAddRq':
				if (isset($this->_object['SalesOrderLine']))
				{
					$this->_object['SalesOrderLineAdd'] = $this->_object['SalesOrderLine'];
				}

				break;

			case 'SalesOrderModRq':
				if (isset($this->_object['SalesOrderLine']))
				{
					$this->_object['SalesOrderLineMod'] = $this->_object['SalesOrderLine'];
				}
				break;
		}

		return parent::asList($request);
	}

	public function asXML(string $root = null, string $parent = null, $object = null)
	{
		if (is_null($object))
		{
			$object = $this->_object;
		}

		switch ($root)
		{
			case PackageInfo::Actions['ADD_SALESORDER']:

				foreach ($object['SalesOrderLineAdd'] as $key => $obj)
				{
					$obj->setOverride('SalesOrderLineAdd');
				}

				break;

			case PackageInfo::Actions['MOD_SALESORDER']:
				if (isset($object['SalesOrderLine']))
				{
					$object['SalesOrderLineMod'] = $object['SalesOrderLine'];
				}
				break;
		}

		//print_r($this->_object);

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell the type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_SALESORDER'];
	}
}
