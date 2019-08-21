<?php declare(strict_types=1);

/**
 * QuickBooks CreditCardMemo object container
 *
 * @author Jayson Lindsley <jay.lindsley@gmail.com>
 * @author Keith Palmer <keith@consolibyte.com>
 *
 * TODO: Add support for items as per the QBXML spec
 *
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
use QuickBooksPhpDevKit\QBXML\Object\CreditMemo\CreditMemoLine;

/**
 * QBXML\Ojbect\CreditMemo class
 */
class CreditMemo extends AbstractQbxmlObject
{
 	/**
	 * Create a new QBXML\Ojbect\CreditMemo object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the transaction ID of the CreditMemo
	 */
	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}
	/**
	 * Get the transaction ID for this CreditMemo
	 */
	public function getTxnID(): ?string
	{
		return $this->get('TxnID');
	}

	public function setCustomerListID(string $ListID): bool
	{
		return $this->set('CustomerRef ListID', $ListID);
	}

	public function setCustomerFullName(string $name): bool
	{
		return $this->set('CustomerRef FullName', $name);
	}

	public function getCustomerListID(): ?string
	{
		return $this->get('CustomerRef ListID');
	}

	public function getCustomerFullName(): ?string
	{
		return $this->get('CustomerRef FullName');
	}

	/**
	 * Set the QB Class ListID for this CreditMemo
	 */
	public function setClassListID(string $ListID): bool
	{
		return $this->set('ClassRef ListID', $ListID);
	}

	/**
	 * Set the QB Class Name for this CreditMemo
	 */
	public function setClassName(string $name): bool
	{
		return $this->set('ClassRef FullName', $name);
	}

	public function setClassFullName(string $name): bool
	{
		return $this->set('ClassRef FullName', $name);
	}

	public function getClassListID(): ?string
	{
		return $this->get('ClassRef ListID');
	}

	public function getClassName(): ?string
	{
		return $this->get('ClassRef FullName');
	}

	/**
	 * Set the reference number
	 */
	public function setRefNumber($str): bool
	{
		return $this->set('RefNumber', (string) $str);
	}

	/**
	 * Alias of {@link QBXML\Ojbect\CreditMemo::setRefNumber()}
	 */
	public function setReferenceNumber($str): bool
	{
		return $this->setRefNumber($str);
	}

	/**
	 * Get the reference number
	 */
	public function getRefNumber(): ?string
	{
		return $this->get('RefNumber');
	}

	/**
	 * Alias of {@link QBXML\Ojbect\CreditMemo::getRefNumber()}
	 */
	public function getReferenceNumber(): ?string
	{
		return $this->getRefNumber();
	}

	public function setPONumber($num): bool
	{
		return $this->set('PONumber', (string) $num);
	}
	public function getPONumber(): ?string
	{
		return $this->get('PONumber');
	}

	/**
	 * Set Sales Rep Full Name
	 *
	 * NOTE: It can only be 5 characters, so it's really the "Sales Rep Initials" field in QuickBooks
	 */
	public function setSalesRepName(string $name): bool
	{
		return $this->set('SalesRepRef FullName', $name);
	}

	public function getSalesRepName(): ?string
	{
		return $this->get('SalesRepRef FullName');
	}

	public function setSalesRepListID(string $ListID): bool
	{
		return $this->set('SalesRepRef ListID', $ListID);
	}

	public function getSalesRepListID(): ?string
	{
		return $this->get('SalesRepRef ListID');
	}

	/**
	 * Set the transaction date
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Get the transaction date
	 */
	public function getTxnDate(string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * Set the due date
	 */
	public function setDueDate($date): bool
	{
		return $this->setDateType('DueDate', $date);
	}

	/**
	 * Get the due date
	 */
	public function getDueDate(string $format = 'Y-m-d'): ?string
	{
		return $this->getDateType('DueDate', $format);
	}


	public function addCreditMemoLine(CreditMemoLine $obj): bool
	{
		return $this->addListItem('CreditMemoLine', $obj);
	}

	public function getCreditMemoLine(int $i): ?CreditMemoLine
	{
		return $this->getListItem('CreditMemoLine', $i);
	}

	public function listCreditMemoLine(): array
	{
		return $this->getList('CreditMemoLine');
	}


	public function setShipMethodName(string $name): bool
	{
		return $this->set('ShipMethodRef FullName', $name);
	}

	public function setShipMethodListID(string $ListID): bool
	{
		return $this->set('ShipMethodRef ListID', $ListID);
	}

	public function getShipMethodName(): ?string
	{
		return $this->get('ShipMethodRef FullName');
	}

	public function getShipMethodListID(): ?string
	{
		return $this->get('ShipMethodRef ListID');
	}

	public function setSalesTaxItemFullName(string $name): bool
	{
		return $this->set('ItemSalesTaxRef FullName', $name);
	}

	public function getSalesTaxItemName(): ?string
	{
		return $this->get('ItemSalesTaxRef FullName');
	}

	/**
	 * Set the tax code ListID for the customer
	 */
	public function setCustomerSalesTaxCodeListID(string $name): bool
	{
		return $this->set('CustomerSalesTaxCodeRef ListID', $name);
	}
	/**
	 * Get the tax code ListID for the customer
	 */
	public function getCustomerSalesTaxCodeListID(): ?string
	{
		return $this->get('CustomerSalesTaxCodeRef ListID');
	}

	/**
	 * Set the tax code FullName for the customer
	 */
	public function setCustomerSalesTaxCodeFullName(string $name): bool
	{
		return $this->set('CustomerSalesTaxCodeRef FullName', $name);
	}
	/**
	 * Get the tax code FullName for the customer
	 */
	public function getCustomerSalesTaxCodeFullName(): ?string
	{
		return $this->get('CustomerSalesTaxCodeRef FullName');
	}

	public function setIsToBePrinted(bool $printed): bool
	{
		return $this->setBooleanType('IsToBePrinted', $printed);
	}
	public function getIsToBePrinted(): ?bool
	{
		return $this->getBooleanType('IsToBePrinted');
	}

	public function setIsToBeEmailed(bool $emailed): bool
	{
		return $this->setBooleanType('IsToBeEmailed', $emailed);
	}
	public function getIsToBeEmailed(): ?bool
	{
		return $this->getBooleanType('IsToBeEmailed');
	}

	/**
	 * Set as pending
	 */
	public function setIsPending(bool $pending): bool
	{
		return $this->setBooleanType('IsPending', $pending);
	}

	public function getIsPending(): ?bool
	{
		return $this->getBooleanType('IsPending');
	}

	/**
	 * Get the shipping address as an array (or a specific portion of the address as a string)
	 *
	 * @param string $part			A specific portion of the address to get (i.e. "Addr1" or "State")
	 * @param array $defaults		Default values if a value isn't filled in
	 * @return array				The address
	 */
	public function getShipAddress(?string $part = null, array $defaults = [])
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
	 * @param string $addr1			Address line 1
	 * @param string $addr2			Address line 2
	 * @param string $addr3			Address line 3
	 * @param string $addr4			Address line 4
	 * @param string $addr5			Address line 5
	 * @param string $city			City
	 * @param string $state			State
	 * @param string $province		Province (Canadian editions of QuickBooks only!)
	 * @param string $postalcode	Postal code
	 * @param string $country		Country
	 * @param string $note			Notes
	 * @return void
	 */
	public function setShipAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $province = '', string $postalcode = '', string $country = '', string $note = ''): void
	{
		$this->set('ShipAddress Addr1', $addr1);
		$this->set('ShipAddress Addr2', $addr2);
		$this->set('ShipAddress Addr3', $addr3);
		$this->set('ShipAddress Addr4', $addr4);
		$this->set('ShipAddress Addr5', $addr5);

		$this->set('ShipAddress City', $city);
		$this->set('ShipAddress State', $state);
		$this->set('ShipAddress Province', $province);
		$this->set('ShipAddress PostalCode', $postalcode);
		$this->set('ShipAddress Country', $country);
		$this->set('ShipAddress Note', $note);
	}

	/**
	 * Get the billing address
	 *
	 * @param string $part			A specific portion of the address to get (i.e. "Addr1" or "State")
	 * @param array $defaults		Default values if a value isn't filled in
	 * @return array				The address
	 */
	public function getBillAddress(?string $part = null, array $defaults = [])
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
	 * @param string $addr1			Address line 1
	 * @param string $addr2			Address line 2
	 * @param string $addr3			Address line 3
	 * @param string $addr4			Address line 4
	 * @param string $addr5			Address line 5
	 * @param string $city			City
	 * @param string $state			State
	 * @param string $province		Province (Canadian editions of QuickBooks only!)
	 * @param string $postalcode	Postal code
	 * @param string $country		Country
	 * @param string $note			Notes
	 * @return void
	 */
	public function setBillAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $province = '', string $postalcode = '', string $country = '', string $note = ''): void
	{
		$this->set('BillAddress Addr1', $addr1);
		$this->set('BillAddress Addr2', $addr2);
		$this->set('BillAddress Addr3', $addr3);
		$this->set('BillAddress Addr4', $addr4);
		$this->set('BillAddress Addr5', $addr5);

		$this->set('BillAddress City', $city);
		$this->set('BillAddress State', $state);
		$this->set('BillAddress Province', $province);
		$this->set('BillAddress PostalCode', $postalcode);
		$this->set('BillAddress Country', $country);
		$this->set('BillAddress Note', $note);
	}

	public function asList(string $request): array
	{
		switch ($request)
		{
			case 'CreditMemoAddRq':
				if (isset($this->_object['CreditMemoLine']))
				{
					$this->_object['CreditMemoLineAdd'] = $this->_object['CreditMemoLine'];
				}
				break;

			case 'CreditMemoModRq':
				if (isset($this->_object['CreditMemoLine']))
				{
					$this->_object['CreditMemoLineMod'] = $this->_object['CreditMemoLine'];
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
			case PackageInfo::Actions['ADD_CREDITMEMO']:
				foreach ($object['CreditMemoLineAdd'] as $key => $obj)
				{
					$obj->setOverride('CreditMemoLineAdd');
				}
				break;

			case PackageInfo::Actions['MOD_CREDITMEMO']:
				foreach ($object['CreditMemoLineMod'] as $key => $obj)
				{
					$obj->setOverride('CreditMemoLineMod');
				}
				break;
		}
		return parent::asXML($root, $parent, $object);
	}

	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_CREDITMEMO'];
	}
}
