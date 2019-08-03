<?php declare(strict_types=1);

/**
 * Bill class for QuickBooks
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
use QuickBooksPhpDevKit\QBXML\Object\Bill\ItemLine;
use QuickBooksPhpDevKit\QBXML\Object\Bill\ExpenseLine;

/**
 *
 */
class Bill extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks Bill object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the transaction ID for this Bill
	 */
	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}
	/**
	 * Get the transaction ID for this Bill
	 */
	public function getTxnID(): string
	{
		return $this->get('TxnID');
	}


	/**
	 * Set the vendor ListID
	 */
	public function setVendorListID(string $ListID): bool
	{
		return $this->set('VendorRef ListID', $ListID);
	}

	/**
	 * Set the vendor ApplicationID (auto-replaced by the API with a ListID)
	 */
	public function setVendorApplicationID($value): bool
	{
		return $this->set('VendorRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_VENDOR'], PackageInfo::QbId['LISTID'], $value));
	}

	/**
	 * Set the vendor name
	 */
	public function setVendorFullname(string $name): bool
	{
		return $this->set('VendorRef FullName', $name);
	}

	/**
	 * Get the vendor ListID
	 */
	public function getVendorListID(): string
	{
		return $this->get('VendorRef ListID');
	}

	/**
	 * Get the vendor application ID
	 *
	 * @return mixed
	 */
	public function getVendorApplicationID()
	{
		return $this->extractApplicationID($this->get('VendorRef ' . PackageInfo::$API_APPLICATIONID));
	}

	// Path: TxnDate, datatype: DATETYPE

	/**
	 * Set the TxnDate for the Bill
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Get the TxnDate for the Bill
	 */
	public function getTxnDate(string $format = null): string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * @see Bill::setTxnDate()
	 */
	public function setTransactionDate($date): bool
	{
		return $this->setTxnDate($date);
	}

	/**
	 * @see Bill::getTxnDate()
	 */
	public function getTransactionDate(string $format = null): string
	{
		$this->getTxnDate($format);
	}
	// Path: RefNumber, datatype: STRTYPE

	public function setDueDate($date): bool
	{
		return $this->setDateType('DueDate', $date);
	}

	public function getDueDate(string $format = 'Y-m-d'): string
	{
		return $this->getDateType('DueDate', $format);
	}

	/**
	 * Set the RefNumber for the Bill
	 */
	public function setRefNumber($value): bool
	{
		return $this->set('RefNumber', $value);
	}

	/**
	 * Get the RefNumber for the Bill
	 */
	public function getRefNumber(): string
	{
		return $this->get('RefNumber');
	}

	// Path: Memo, datatype: STRTYPE

	/**
	 * Set the Memo for the Bill
	 */
	public function setMemo(string $value): bool
	{
		return $this->set('Memo', $value);
	}

	/**
	 * Get the Memo for the Bill
	 */
	public function getMemo(): string
	{
		return $this->get('Memo');
	}

	public function addExpenseLine(ExpenseLine $obj): bool
	{
		return $this->addListItem('ExpenseLine', $obj);
	}

	public function addItemLine(ItemLine $obj): bool
	{
		return $this->addListItem('ItemLine', $obj);
	}


	public function asList(string $request)
	{
		switch ($request)
		{
			case 'BillAddRq':
				if (isset($this->_object['ItemLine']))
				{
					$this->_object['ItemLineAdd'] = $this->_object['ItemLine'];
				}

				if (isset($this->_object['ExpenseLine']))
				{
					$this->_object['ExpenseLineAdd'] = $this->_object['ExpenseLine'];
				}
				break;

			case 'BillModRq':
				if (isset($this->_object['ItemLine']))
				{
					$this->_object['ItemLineMod'] = $this->_object['ItemLine'];
				}

				if (isset($this->_object['ExpenseLine']))
				{
					$this->_object['ExpenseLineMod'] = $this->_object['ExpenseLine'];
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
			case PackageInfo::Actions['ADD_BILL']:
				if (!empty($object['ItemLineAdd']))
				{
					foreach ($object['ItemLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ItemLineAdd');
					}
				}

				if (!empty($object['ExpenseLineAdd']))
				{
					foreach ($object['ExpenseLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ExpenseLineAdd');
					}
				}
				break;

			case PackageInfo::Actions['MOD_BILL']:
				if (!empty($object['ItemLineMod']))
				{
					foreach ($object['ItemLineMod'] as $key => $obj)
					{
						$obj->setOverride('ItemLineMod');
					}
				}

				if (!empty($object['ExpenseLineMod']))
				{
					foreach ($object['ExpenseLineMod'] as $key => $obj)
					{
						$obj->setOverride('ExpenseLineMod');
					}
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
		return PackageInfo::Actions['OBJECT_BILL'];
	}
}
