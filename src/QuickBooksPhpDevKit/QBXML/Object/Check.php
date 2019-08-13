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

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Check\ExpenseLine;
use QuickBooksPhpDevKit\QBXML\Check\ItemLine;
use QuickBooksPhpDevKit\QBXML\Check\ItemGroupLine;
use QuickBooksPhpDevKit\QBXML\Check\ApplyCheckToTxn;
use QuickBooksPhpDevKit\XML\Node;

/**
 *
 */
class Check extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\Check object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	// Path: AccountRef ListID, datatype:

	/**
	 * Set the AccountRef ListID for the Check
	 */
	public function setAccountListID(string $ListID): bool
	{
		return $this->set('AccountRef ListID', $ListID);
	}

	/**
	 * Get the AccountRef ListID for the Check
	 */
	public function getAccountListID(): ?string
	{
		return $this->get('AccountRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setAccountApplicationID($value): bool
	{
		return $this->set('AccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getAccountApplicationID(): ?string
	{
		return $this->get('AccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	// Path: AccountRef FullName, datatype:

	/**
	 * Set the AccountRef FullName for the Check
	 */
	public function setAccountName(string $FullName): bool
	{
		return $this->set('AccountRef FullName', $FullName);
	}

	/**
	 * Get the AccountRef FullName for the Check
	 */
	public function getAccountName(): ?string
	{
		return $this->get('AccountRef FullName');
	}

	// Path: PayeeEntityRef ListID, datatype:

	/**
	 * Set the PayeeEntityRef ListID for the Check
	 */
	public function setPayeeEntityListID(string $ListID): bool
	{
		return $this->set('PayeeEntityRef ListID', $ListID);
	}

	/**
	 * Get the PayeeEntityRef ListID for the Check
	 */
	public function getPayeeEntityListID(): ?string
	{
		return $this->get('PayeeEntityRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Check
	 *
	 * @param mixed $value			The primary key within your own application
	 */
	public function setPayeeEntityApplicationID($value): bool
	{
		return $this->set('PayeeEntityRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_PAYEEENTITY'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getPayeeEntityApplicationID(): ?string
	{
		return $this->get('PayeeEntityRef ' . PackageInfo::$API_APPLICATIONID);
	}

	// Path: PayeeEntityRef FullName, datatype:

	/**
	 * Set the PayeeEntityRef FullName for the Check
	 */
	public function setPayeeEntityFullName(string $FullName): bool
	{
		return $this->set('PayeeEntityRef FullName', $FullName);
	}

	/**
	 * Get the PayeeEntityRef FullName for the Check
	 */
	public function getPayeeEntityFullName(): ?string
	{
		return $this->get('PayeeEntityRef FullName');
	}

	// Path: RefNumber, datatype: STRTYPE

	/**
	 * Set the RefNumber for the Check
	 */
	public function setRefNumber(string $value): bool
	{
		return $this->set('RefNumber', $value);
	}

	/**
	 * Get the RefNumber for the Check
	 */
	public function getRefNumber(): ?string
	{
		return $this->get('RefNumber');
	}

	// Path: TxnDate, datatype: DATETYPE

	/**
	 * Set the TxnDate for the Check
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Get the TxnDate for the Check
	 */
	public function getTxnDate(?string $format = null): ?string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * @see QBXML\Object\Check::setTxnDate()
	 */
	public function setTransactionDate($date): bool
	{
		return $this->setTxnDate($date);
	}

	/**
	 * @see QBXML\Object\Check::getTxnDate()
	 */
	public function getTransactionDate(?string $format = null): ?string
	{
		return $this->getTxnDate($format);
	}
	// Path: Memo, datatype: STRTYPE

	/**
	 * Set the Memo for the Check
	 */
	public function setMemo(string $value): bool
	{
		return $this->set('Memo', $value);
	}

	/**
	 * Get the Memo for the Check
	 */
	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	// Path: IsToBePrinted, datatype: BOOLTYPE

	/**
	 * Set the IsToBePrinted for the Check
	 */
	public function setIsToBePrinted(bool $bool): bool
	{
		return $this->setBooleanType('IsToBePrinted', $bool);
	}

	/**
	 * Get the IsToBePrinted for the Check
	 */
	public function getIsToBePrinted(): ?bool
	{
		return $this->getBooleanType('IsToBePrinted');
	}

	// Path: IsTaxIncluded, datatype: BOOLTYPE

	/**
	 * Set the IsTaxIncluded for the Check
	 */
	public function setIsTaxIncluded(bool $bool): bool
	{
		return $this->setBooleanType('IsTaxIncluded', $bool);
	}

	/**
	 * Get the IsTaxIncluded for the Check
	 */
	public function getIsTaxIncluded(): ?bool
	{
		return $this->getBooleanType('IsTaxIncluded');
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
	 *
	 * @param mixed $value			The primary key within your own application
	 * @return string
	 */
	public function setSalesTaxCodeApplicationID($value): bool
	{
		return $this->set('SalesTaxCodeRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(QUICKBOOKS_OBJECT_SALESTAXCODE, PackageInfo::QbId['LISTID'], $value));
	}

	public function getSalesTaxCodeApplicationID(): ?string
	{
		return $this->get('SalesTaxCodeRef ' . PackageInfo::$API_APPLICATIONID);
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

	public function addItemLine(ItemLine $obj): bool
	{
		return $this->addListItem('ItemLine', $obj);
	}

	public function addItemGroupLine(ItemGroupLine $obj): bool
	{
		return $this->addListItem('ItemGroupLine', $obj);
	}

	public function addExpenseLine(ExpenseLine $obj): bool
	{
		return $this->addListItem('ExpenseLine', $obj);
	}

	public function addAddCheckToTxn(ApplyCheckToTxn $obj): bool
	{
		return $this->addListItem('AddCheckToTxn', $obj);
	}

	public function setAddress(string $addr1, string $addr2 = '', string $addr3 = '', string $addr4 = '', string $addr5 = '', string $city = '', string $state = '', string $postalcode = '', string $country = '', string $note = ''): bool
	{
		return $this->_setAddress('', $addr1, $addr2, $addr3, $addr4, $addr5, $city, $state, $postalcode, $country, $note);
	}

	protected function _setAddress(string $post, string $addr1, string $addr2, string $addr3, string $addr4, string $addr5, string $city, string $state, string $postalcode, string $country, string $note): bool
	{
		for ($i = 1; $i <= 5; $i++)
		{
			$this->set('Address' . $post . ' Addr' . $i, ${'addr' . $i});
		}

		$this->set('Address' . $post . ' City', $city);
		$this->set('Address' . $post . ' State', $state);
		$this->set('Address' . $post . ' PostalCode', $postalcode);
		$this->set('Address' . $post . ' Country', $country);
		$this->set('Address' . $post . ' Note', $note);

		return true;
	}
	public function asList(string $request): array
	{
		switch ($request)
		{
			case 'CheckAddRq':
				if (isset($this->_object['ItemLine']))
				{
					$this->_object['ItemLineAdd'] = $this->_object['ItemLine'];
				}

				if (isset($this->_object['ItemGroupLine']))
				{
					$this->_object['ItemGroupLineAdd'] = $this->_object['ItemGroupLine'];
				}

				if (isset($this->_object['ExpenseLine']))
				{
					$this->_object['ExpenseLineAdd'] = $this->_object['ExpenseLine'];
				}

				if (isset($this->_object['AddCheckToTxn']))
				{
					$this->_object['AddCheckToTxnAdd'] = $this->_object['AddCheckToTxn'];
				}
				break;

			case 'CheckModRq':
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
			case PackageInfo::Actions['ADD_CHECK']:
				if (!empty($object['ItemLineAdd']))
				{
					foreach ($object['ItemLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ItemLineAdd');
					}
				}

				if (!empty($object['ItemGroupLineAdd']))
				{
					foreach ($object['ItemGroupLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ItemGroupLineAdd');
					}
				}

				if (!empty($object['ExpenseLineAdd']))
				{
					foreach ($object['ExpenseLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ExpenseLineAdd');
					}
				}

				if (!empty($object['ApplyCheckToTxnAdd']))
				{
					foreach ($object['ApplyCheckToTxnAdd'] as $key => $obj)
					{
						$obj->setOverride('ApplyCheckToTxnAdd');
					}
				}
				break;

			case PackageInfo::Actions['MOD_CHECK']:
				if (isset($object['ItemLine']))
				{
					$object['ItemLineMod'] = $object['ItemLine'];
				}
				break;
		}

		return parent::asXML($root, $parent, $object);
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_CHECK'];
	}
}
