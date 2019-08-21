<?php declare(strict_types=1);

/**
 * QuickBooks Estimate object container
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
	XML\Node,
};
use QuickBooksPhpDevKit\QBXML\Object\Estimate\EstimateLine;

/**
 *
 *
 */
class Estimate extends AbstractQbxmlObject
{
	/**
	 * Create a QBXML\Object\Estimate object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Alias of {@link QBXML\Object\Estimate::setTxnID()}
	 */
	public function setTransactionID(string $TxnID): bool
	{
		return $this->setTxnID($TxnID);
	}

	/**
	 * Set the transaction ID of the object
	 */
	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}

	/**
	 * Alias of {@link QBXML\Object\Estimate::getTxnID()}
	 */
	public function getTransactionID(): ?string
	{
		return $this->getTxnID();
	}

	/**
	 * Get the transaction ID for this invoice
	 */
	public function getTxnID(): ?string
	{
		return $this->get('TxnID');
	}

	/**
	 * Set the customer ListID
	 */
	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID' , $ListID);
	}

	/**
	 * Set the customer ApplicationID (auto-replaced by the API with a ListID)
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function setCustomerApplicationID($value): bool
	{
		return $this->set('CustomerRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(QUICKBOOKS_OBJECT_CUSTOMER, PackageInfo::QbId['LISTID'], $value));
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
	public function getCustomerListID(): ?string
	{
		return $this->get('CustomerRef ListID');
	}

	/**
	 * Get the customer application ID
	 */
	public function getCustomerApplicationID()
	{
		return $this->extractApplicationID($this->get('CustomerRef ' . PackageInfo::$API_APPLICATIONID));
	}

	/**
	 * Get the customer name
	 */
	public function getCustomerName(): ?string
	{
		return $this->get('CustomerRef FullName');
	}

	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	public function setClassApplicationID($value): bool
	{
		return $this->set('ClassRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(QUICKBOOKS_OBJECT_CLASS, PackageInfo::QbId['LISTID'], $value));
	}

	public function getClassApplicationID()
	{
		return $this->get('ClassRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setClassName(string $name): bool
	{
		return $this->set('ClassRef FullName', $name);
	}

	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	public function setTemplateApplicationID($value)
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

	public function getTemplateName(): ?string
	{
		return $this->get('TemplateRef FullName');
	}

	public function getTemplateListID(): ?string
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
	 * Alias of {@link QBXML\Object\Estimate::setTxnDate()}
	 */
	public function setTransactionDate($date): bool
	{
		return $this->setTxnDate($date);
	}

	/**
	 * Get the transaction date
	 */
	public function getTxnDate(?string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * Alias of {@link QBXML\Object\Estimate::getTxnDate()}
	 */
	public function getTransactionDate(?string $format = 'Y-m-d'): ?string
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
	public function getRefNumber(): ?string
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
	/*public function getShipAddress($part = null, $defaults = [])
	{
		if (!is_null($part))
		{
			return $this->get('ShipAddress ' . $part);
		}

		return $this->getArray('ShipAddress *', $defaults);
	}*/

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
	 * @return void
	 */
	/*public function setShipAddress($addr1, $addr2 = '', $addr3 = '', $addr4 = '', $addr5 = '', $city = '', $state = '', $province = '', $postalcode = '', $country = '', $note = '')
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set('ShipAddress Addr' . $i, ${'addr' . $i});
		}

		$this->set('ShipAddress City', $city);
		$this->set('ShipAddress State', $state);
		$this->set('ShipAddress Province', $province);
		$this->set('ShipAddress PostalCode', $postalcode);
		$this->set('ShipAddress Country', $country);
		$this->set('ShipAddress Note', $note);
	}*/

	/**
	 *
	 *
	 * @param string $part
	 * @param array $defaults
	 * @return array
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
	 */
	public function setBillAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $province = '', string $postalcode = '', string $country = '', string $note = ''): bool
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set('BillAddress Addr' . $i, ${'addr' . $i});
		}

		$this->set('BillAddress City', $city);
		$this->set('BillAddress State', $state);
		$this->set('BillAddress Province', $province);
		$this->set('BillAddress PostalCode', $postalcode);
		$this->set('BillAddress Country', $country);
		$this->set('BillAddress Note', $note);

		return true;
	}

	public function setPONumber(string $num): bool
	{
		return $this->set('PONumber', $num);
	}

	public function getPONumber(): ?string
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

	public function getTermsApplicationID(): ?string
	{
		return $this->get('TermsRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setTermsName(string $name): bool
	{
		return $this->set('TermsRef FullName', $name);
	}

	public function getTermsName(): ?string
	{
		return $this->get('TermsRef FullName');
	}

	public function getTermsListID(): ?string
	{
		return $this->get('TermsRef ListID');
	}

	public function setDueDate($date): bool
	{
		return $this->setDateType('DueDate', $date);
	}

	public function getDueDate(?string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('DueDate', $format);
	}

	public function setSalesRepName(string $name): bool
	{
		return $this->set('SalesRepRef FullName', $name);
	}

	public function setSalesRepListID(string $ListID): bool
	{
		return $this->set('SalesRepRef ListID', $ListID);
	}

	public function setSalesRepApplicationID($value): bool
	{
		return $this->set('SalesRepRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_SALESREP'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getSalesRepApplicationID()
	{
		return $this->get('SalesRepRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function getSalesRepName(): ?string
	{
		return $this->get('SalesRepRef FullName');
	}

	public function getSalesRepListID(): ?string
	{
		return $this->get('SalesRepRef ListID');
	}

	public function getFOB(): ?string
	{
		return $this->get('FOB');
	}

	public function setFOB(string $fob): bool
	{
		return $this->set('FOB', $fob);
	}

	public function setSalesTaxItemListID(string $ListID): bool
	{
		return $this->set('ItemSalesTaxRef ListID', $ListID);
	}

	public function setSalesTaxItemApplicationID($value): bool
	{
		return $this->set('ItemSalesTaxRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_SALESTAXITEM'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getSalesTaxItemApplicationID()
	{
		return $this->get('ItemSalesTaxRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setSalesTaxItemName(string $name): bool
	{
		return $this->set('ItemSalesTaxRef FullName', $name);
	}

	public function getSalesTaxItemName(): ?string
	{
		return $this->get('ItemSalesTaxRef FullName');
	}

	public function getSalesTaxItemListID(): ?string
	{
		return $this->get('ItemSalesTaxRef ListID');
	}

	public function setMemo(string $memo): bool
	{
		return $this->set('Memo', $memo);
	}

	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	public function setIsToBeEmailed(bool $emailed): bool
	{
		return $this->setBooleanType('IsToBeEmailed', $emailed);
	}

	public function getIsToBeEmailed(): ?bool
	{
		return $this->getBooleanType('IsToBeEmailed');
	}

	public function setCustomerSalesTaxCodeListID(string $ListID): bool
	{
		return $this->set('CustomerSalesTaxCodeRef ListID', $ListID);
	}

	public function setCustomerSalesTaxCodeName(string $name): bool
	{
		return $this->set('CustomerSalesTaxCodeRef FullName', $name);
	}

	public function getCustomerSalesTaxCodeListID(): ?string
	{
		return $this->get('CustomerSalesTaxCodeRef ListID');
	}

	public function getCustomerSalesTaxCodeName(): ?string
	{
		return $this->get('CustomerSalesTaxCodeRef FullName');
	}

	/**
	 *
	 */
	public function addEstimateLine(EstimateLine $obj): bool
	{
		return $this->addListItem('EstimateLine', $obj);
	}
	/*
	public function setEstimateLine(int $i, EstimateLine $obj): void
	{

	}

	public function setEstimateLineData(int $i, string $key, $value): bool
	{
		$lines = $this->getEstimateLines();
		if (isset($lines[$i]))
		{

		}

		return $this->set('EstimateLine', $lines);
	}
	*/

	public function getEstimateLineData()
	{
		return $this->get('EstimateLine');
	}

	public function getEstimateLine($i): ?EstimateLine
	{
		return $this->getListItem('EstimateLine', $i);
	}

	public function getEstimateLines(): array
	{
		return $this->listEstimateLines();
	}

	public function listEstimateLines(): array
	{
		return $this->getList('EstimateLine');
	}

	public function setOther(string $other): bool
	{
		return $this->set('Other', $other);
	}

	public function getOther(): ?string
	{
		return $this->get('Other');
	}

	public function asList(string $request): array
	{
		switch ($request)
		{
			case 'EstimateAddRq':
				if (isset($this->_object['EstimateLine']))
				{
					$this->_object['EstimateLineAdd'] = $this->_object['EstimateLine'];
				}
				break;

			case 'EstimateModRq':
				if (isset($this->_object['EstimateLine']))
				{
					$this->_object['EstimateLineMod'] = $this->_object['EstimateLine'];
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
			case PackageInfo::Actions['ADD_ESTIMATE']:
				foreach ($object['EstimateLineAdd'] as $key => $obj)
				{
					$obj->setOverride('EstimateLineAdd');
				}

				break;

			case PackageInfo::Actions['MOD_ESTIMATE']:
				if (isset($object['EstimateLine']))
				{
					$object['EstimateLineMod'] = $object['EstimateLine'];
				}
				break;
		}

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell the type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_ESTIMATE'];
	}
}
