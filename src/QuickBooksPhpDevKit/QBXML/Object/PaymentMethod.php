<?php declare(strict_types=1);

/**
 * QuickBooks PaymentMethod object container
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
class PaymentMethod extends AbstractQbxmlObject
{
	/**
	 * Create a new QuickBooks_Object_PaymentMethod object
	 */
	public function __construct(array $arr = [])
	{
		parent::__construct($arr);
	}

	/**
	 * Set the ListID
	 */
	public function setListID(string $ListID): bool
	{
		return $this->set('ListID', $ListID);
	}

	/**
	 * Get the ListID
	 */
	public function getListID(): string
	{
		return $this->get('ListID');
	}

	/**
	 * Set the name
	 */
	public function setName(string $name): bool
	{
		return $this->set('Name', $name);
	}

	/**
	 * Get the name
	 */
	public function getName(): string
	{
		return $this->get('Name');
	}

	/**
	 * Set this as active or not
	 */
	public function setIsActive(bool $value): bool
	{
		return $this->setBooleanType('IsActive', $value);
	}

	/**
	 * Tell whether or not this is active
	 */
	public function getIsActive(): bool
	{
		return $this->getBooleanType('IsActive');
	}

	/* // This is a PaymentMethodType
	public function getPaymentMethodType()
	{
		return $this->get('PaymentMethodType');
	}
	*/

	/**
	 * Tell what type of object this is
	 */
	public function object(): string
	{
		return PackageInfo::Actions['OBJECT_PAYMENTMETHOD'];
	}
}
