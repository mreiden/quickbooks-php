<?php declare(strict_types=1);

/**
 * Deposit class for QuickBooks
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
use QuickBooksPhpDevKit\QBXML\Object\Deposit\DepositLine;
use QuickBooksPhpDevKit\XML\Node;

/**
 *
 */
class Deposit extends AbstractQbxmlObject
{
	public function __construct($arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the AccountRef ListID for the Deposit
	 */
	public function setDepositToAccountListID(string $ListID): bool
	{
		return $this->set('DepositToAccountRef ListID', $ListID);
	}

	/**
	 * Get the AccountRef ListID for the Deposit
	 */
	public function getDepositToAccountListID(): ?string
	{
		return $this->get('DepositToAccountRef ListID');
	}

	/**
	 * Set the primary key for the related record within your own application for the Deposit
	 *
	 * @param mixed $value			The primary key within your own application
	 */
	public function setDepositToAccountApplicationID($value): bool
	{
		return $this->set('DepositToAccountRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_ACCOUNT'], PackageInfo::QbId['LISTID'], $value));
	}

	public function getDepositToAccountApplicationID(): ?string
	{
		return $this->get('DepositToAccountRef ' . PackageInfo::$API_APPLICATIONID);
	}

	// Path: DepositToAccountRef FullName, datatype:

	/**
	 * Set the DepositToAccountRef FullName for the Deposit
	 */
	public function setDepositToAccountFullName(string $FullName): bool
	{
		return $this->set('DepositToAccountRef FullName', $FullName);
	}

	/**
	 * Get the DepositToAccountRef FullName for the Deposit
	 */
	public function getDepositToAccountFullName(): ?string
	{
		return $this->get('DepositToAccountRef FullName');
	}

	// Path: TxnDate, datatype: DATETYPE

	/**
	 * Set the TxnDate for the Deposit
	 *
	 * @param string $date
	 * @return boolean
	 */
	public function setTxnDate($date): bool
	{
		return $this->setDateType('TxnDate', $date);
	}

	/**
	 * Get the TxnDate for the Deposit
	 */
	public function getTxnDate(?string $format = null): ?string
	{
		return $this->getDateType('TxnDate', $format);
	}

	/**
	 * @see QBXML\Object\Deposit::setTxnDate()
	 */
	public function setTransactionDate($date): bool
	{
		return $this->setTxnDate($date);
	}

	/**
	 * @see QBXML\Object\Deposit::getTxnDate()
	 */
	public function getTransactionDate(?string $format = null): ?string
	{
		return $this->getTxnDate($format = null);
	}
	// Path: Memo, datatype: STRTYPE

	/**
	 * Set the Memo for the Deposit
	 */
	public function setMemo(string $value): bool
	{
		return $this->set('Memo', $value);
	}

	/**
	 * Get the Memo for the Deposit
	 */
	public function getMemo(): ?string
	{
		return $this->get('Memo');
	}

	public function setAmount($amount): bool
	{
		return $this->setAmountType('Amount', $amount);
	}

	public function getAmount()
	{
		return $this->getAmountType('Amount');
	}

	public function addDepositLine(DepositLine $obj): bool
	{
		return $this->addListItem('DepositLine', $obj);
	}

	public function asList(string $request): array
	{
		switch ($request)
		{
			case 'DepositAddRq':
				if (isset($this->_object['DepositLine']))
				{
					$this->_object['DepositLineAdd'] = $this->_object['DepositLine'];
				}
				break;

			case 'DepositModRq':
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
			case PackageInfo::Actions['ADD_DEPOSIT']:
				foreach ($object['DepositLineAdd'] as $key => $obj)
				{
					$obj->setOverride('DepositLineAdd');
				}
				break;

			case PackageInfo::Actions['MOD_DEPOSIT']:
				foreach ($object['DepositLineMod'] as $key => $obj)
				{
					$obj->setOverride('DepositLineMod');
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
		return PackageInfo::Actions['OBJECT_DEPOSIT'];
	}
}
