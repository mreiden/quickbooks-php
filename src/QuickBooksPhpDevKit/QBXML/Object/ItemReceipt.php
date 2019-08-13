<?php declare(strict_types=1);

/**
 * QuickBooks ItemReceipt object container
 *
 * @todo Add and verify the Mod schema
 * @todo Documentation
 * @todo Explain in documentation about how to use LinkToTxnID
 *
 * @author Harley Laue <harley.laue@gmail.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML\Object;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\ItemReceipt\ExpenseLine;
use QuickBooksPhpDevKit\QBXML\Object\ItemReceipt\ItemGroupLine;
use QuickBooksPhpDevKit\QBXML\Object\ItemReceipt\ItemLine;
use QuickBooksPhpDevKit\XML\Node;

/**
 * Quickbooks ItemReceipt definition
 */
class ItemReceipt extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks ItemReceipt object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Gets the Vendor ListID
	 */
	public function getVendorListID(): ?string
	{
		return $this->get('VendorRef ListID');
	}

	/**
	 * Set the Vendor ListID
	 */
	public function setVendorListID(string $value): bool
	{
		return $this->set('VendorRef ListID', $value);
	}

	/**
	 * Gets the Vendor Name
	 */
	public function getVendorName(): ?string
	{
		return $this->get('VendorRef FullName');
	}

	/**
	 * Set the Vendor Name
	 */
	public function setVendorName(string $value): bool
	{
		return $this->set('VendorRef FullName', $value);
	}

	/**
	 * Gets the Accounts Payable Account ListID
	 */
	public function getAPAccountListID(): ?string
	{
		return $this->get('APAccountRef ListID');
	}

	/**
	 * Set the Accounts Payable Account ListID
	 */
	public function setAPAccountListID(string $value): bool
	{
		return $this->set('APAccountRef ListID', $value);
	}

	/**
	 * Gets the Accounts Payable Account Name
	 */
	public function getAPAccountName(): ?string
	{
		return $this->get('APAccountRef FullName');
	}

	/**
	 * Set the Accounts Payable Account Name
	 */
	public function setAPAccountName(string $value): bool
	{
		return $this->set('APAccountRef FullName', $value);
	}

	/**
	 * Gets the transaction date
	 */
	public function getTxnDate(?string $format='Y-m-d'): ?string
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
	public function setRefNumber(string $value): bool
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
	 * Gets the Exchange Rate
	 */
	public function getExchangeRate(): ?float
	{
		return $this->get('ExchangeRate');
	}

	/**
	 * Set the Exchange Rate
	 */
	public function setExchangeRate(float $rate): bool
	{
		return $this->set('ExchangeRate', $rate);
	}

	/**
	 * Gets the LinkToTxnID
	 */
	public function getLinkToTxnIDList(): array
	{
		return $this->get('LinkToTxnIDList');
	}

	/**
	 * Sets the LinksToTxnID
	 */
	public function setLinkToTxnIDList(array $TxnIDs)
	{
		return $this->set('LinkToTxnIDList', $TxnIDs);
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

	/**
	 * Gets the ExpenseLine at @param $i
	 *
	 * @param $i a number between 0 and added Lines-1
	 */
	public function getExpenseLine(int $i): ?ExpenseLine
	{
		return $this->getListItem('ExpenseLine', $i);
	}

	/**
	 * Gets a list of all Expense Lines
	 */
	public function getExpenseLines(): array
	{
		return $this->getList('ExpenseLine');
	}

	/**
	 * Gets the ItemLine at @param $i
	 *
	 * @param $i a number between 0 and added Lines-1
	 */
	public function getItemLine(int $i): ?ItemLine
	{
		return $this->getListItem('ItemLine', $i);
	}

	/**
	 * Gets a list of all Item Lines
	 */
	public function getItemLines(): array
	{
		return $this->getList('ItemLine');
	}

	/**
	 * Gets the ItemGroupLine at @param $i
	 *
	 * @param $i a number between 0 and added Lines-1
	 */
	public function getItemGroupLine(int $i): ?ItemGroupLine
	{
		return $this->getListItem('ItemGroupLine', $i);
	}

	/**
	 * Gets a list of all ItemGroup Lines
	 */
	public function getItemGroupLines(): array
	{
		return $this->getList('ItemGroupLine');
	}

	public function asList(string $request): array
	{
		switch ($request)
		{
			case PackageInfo::Actions['ADD_ITEMRECEIPT'] . 'Rq':
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
				break;

			case PackageInfo::Actions['MOD_ITEMRECEIPT'] . 'Rq':
				if (isset($this->_object['ItemLine']))
				{
					$this->_object['ItemLineMod'] = $this->_object['ItemLine'];
				}
				if (isset($this->_object['ItemGroupLine']))
				{
					$this->_object['ItemGroupLineMod'] = $this->_object['ItemGroupLine'];
				}
				if (isset($this->_object['ExpenseLine']))
				{
					$this->_object['ExpenseLineMod'] = $this->_object['ExpenseLine'];
				}
				break;

			case PackageInfo::Actions['QUERY_ITEMRECEIPT'] . 'Rq':
				if (isset($this->_object['ItemLine']))
				{
					$this->_object['ItemLineQuery'] = $this->_object['ItemLine'];
				}
				if (isset($this->_object['ItemGroupLine']))
				{
					$this->_object['ItemGroupLineQuery'] = $this->_object['ItemGroupLine'];
				}
				if (isset($this->_object['ExpenseLIne']))
				{
					$this->_object['ExpenseLineQuery'] = $this->_object['ExpenseLine'];
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
			case PackageInfo::Actions['ADD_ITEMRECEIPT']:
				if (isset($object['ItemLineAdd']))
				{
					foreach ($object['ItemLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ItemLineAdd');
					}
				}
				if (isset($object['ItemGroupLineAdd']))
				{
					foreach ($object['ItemGroupLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ItemGroupLineAdd');
					}
				}
				if (isset($object['ExpenseLineAdd']))
				{
					foreach ($object['ExpenseLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ExpenseLineAdd');
					}
				}
				break;

			// For possible future use...
			/*
			case PackageInfo::Actions['QUERY_ITEMRECEIPT']:
				if (isset($this->_object['ItemLineAdd']))
				{
					foreach ($this->_object['ItemLineQuery'] as $key => $obj)
					{
						$obj->setOverride('ItemLineQuery');
					}
				}
				if (isset($this->_object['ItemGroupLineAdd']))
				{
					foreach ($this->_object['ItemGroupQuery'] as $key => $obj)
					{
						$obj->setOverride('ItemGroupQuery');
					}
				}
				if (isset($this->_object['ExpenseLineAdd']))
				{
					foreach ($this->_object['ExpenseLineQuery'] as $key => $obj)
					{
						$obj->setOverride('ExpenseLineQuery');
					}
				}
				break;
			*/
			case PackageInfo::Actions['MOD_ITEMRECEIPT']:
				if (isset($object['ItemLineAdd']))
				{
					foreach ($object['ItemLineMod'] as $key => $obj)
					{
						$obj->setOverride('ItemLineMod');
					}
				}
				if (isset($object['ItemGroupLineAdd']))
				{
					foreach ($object['ItemGroupMod'] as $key => $obj)
					{
						$obj->setOverride('ItemGroupMod');
					}
				}
				if (isset($object['ExpenseLineAdd']))
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
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_ITEMRECEIPT'];
	}
}
