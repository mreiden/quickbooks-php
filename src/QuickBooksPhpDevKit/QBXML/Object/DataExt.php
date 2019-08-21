<?php declare(strict_types=1);

/**
 * QuickBooks DataExt object container
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
class DataExt extends AbstractQbxmlObject
{
	/**
	 * Create a new QBXML\Object\DataExt object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the OwnerID
	 */
	public function setOwnerID(string $OwnerID): bool
	{
		return $this->set('OwnerID', $OwnerID);
	}

	/**
	 * Get the OwnerID
	 */
	public function getOwnerID(): string
	{
		return $this->get('OwnerID');
	}

	public function setDataExtName(string $name): bool
	{
		return $this->set('DataExtName', $name);
	}

	public function getDataExtName(): string
	{
		return $this->get('DataExtName');
	}

	public function setListDataExtType(string $type): bool
	{
		return $this->set('ListDataExtType', $type);
	}

	public function getListDataExtType(): string
	{
		return $this->get('ListDataExtType');
	}

	public function setListObjListID(string $ListID): bool
	{
		return $this->set('ListObjRef ListID', $ListID);
	}

	public function getListObjListID(): string
	{
		return $this->get('ListObjRef ListID');
	}

	public function setListObjName(string $name): bool
	{
		return $this->set('ListObjRef FullName', $name);
	}

	public function getListObjName(): string
	{
		return $this->get('ListObjRef FullName');
	}

	public function setListObjApplicationID($value, $type): bool
	{
		return $this->set('ListObjRef ' . PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID($type, PackageInfo::QbId['LISTID'], $value));
	}

	public function getListObjApplicationID(): string
	{
		return $this->get('ListObjRef ' . PackageInfo::$API_APPLICATIONID);
	}

	public function setTxnDataExtType(string $type): bool
	{
		return $this->set('TxnDataExtType', $type);
	}

	public function getTxnDataExtType(): string
	{
		return $this->get('TxnDataExtType');
	}

	public function setTxnID(string $TxnID): bool
	{
		return $this->set('TxnID', $TxnID);
	}

	public function getTxnID(): string
	{
		return $this->get('TxnID');
	}

	public function setTxnApplicationID($value): bool
	{
		return $this->set(PackageInfo::$API_APPLICATIONID, $this->encodeApplicationID(PackageInfo::Actions['OBJECT_DATAEXT'], PackageInfo::QbId['TXNID'], $value));
	}

	public function getTxnApplicationID(): string
	{
		return $this->get(PackageInfo::$API_APPLICATIONID);
	}

	public function setTxnLineID(string $value): bool
	{
		return $this->set('TxnLineID', $value);
	}

	public function getTxnLineID(): string
	{
		return $this->get('TxnLineID');
	}

	public function setOtherDataExtType(string $type): bool
	{
		return $this->set('OtherDataExtType', $type);
	}

	public function getOtherDataExtType(): string
	{
		return $this->get('OtherDataExtType');
	}

	public function setDataExtValue(string $value): bool
	{
		return $this->set('DataExtValue', $value);
	}

	public function getDataExtValue(): string
	{
		return $this->get('DataExtValue');
	}

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_DATAEXT'];
	}
}
