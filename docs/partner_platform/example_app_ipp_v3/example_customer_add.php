<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Customer as ObjCustomer;
use QuickBooksPhpDevKit\IPP\Object\BillAddr as ObjBillAddr;
use QuickBooksPhpDevKit\IPP\Object\Fax as ObjFax;
use QuickBooksPhpDevKit\IPP\Object\Mobile as ObjMobile;
use QuickBooksPhpDevKit\IPP\Object\PrimaryEmailAddr as ObjPrimaryEmailAddr;
use QuickBooksPhpDevKit\IPP\Object\PrimaryPhone as ObjPrimaryPhone;
use QuickBooksPhpDevKit\IPP\Service\Customer;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$CustomerService = new Customer();

$Customer = new ObjCustomer();
$Customer->setTitle('Ms');
$Customer->setGivenName('Shannon');
$Customer->setMiddleName('B');
$Customer->setFamilyName('Palmer');
$Customer->setDisplayName('Shannon B Palmer ' . mt_rand(0, 1000));

// Terms (e.g. Net 30, etc.)
$Customer->setSalesTermRef(4);

// Phone #
$PrimaryPhone = new ObjPrimaryPhone();
$PrimaryPhone->setFreeFormNumber('860-532-0089');
$Customer->setPrimaryPhone($PrimaryPhone);

// Mobile #
$Mobile = new ObjMobile();
$Mobile->setFreeFormNumber('860-532-0089');
$Customer->setMobile($Mobile);

// Fax #
$Fax = new ObjFax();
$Fax->setFreeFormNumber('860-532-0089');
$Customer->setFax($Fax);

// Bill address
$BillAddr = new ObjBillAddr();
$BillAddr->setLine1('72 E Blue Grass Road');
$BillAddr->setLine2('Suite D');
$BillAddr->setCity('Mt Pleasant');
$BillAddr->setCountrySubDivisionCode('MI');
$BillAddr->setPostalCode('48858');
$Customer->setBillAddr($BillAddr);

// Email
$PrimaryEmailAddr = new ObjPrimaryEmailAddr();
$PrimaryEmailAddr->setAddress('support@consolibyte.com');
$Customer->setPrimaryEmailAddr($PrimaryEmailAddr);

if ($resp = $CustomerService->add($Context, $realm, $Customer))
{
	print('Our new customer ID is: [' . $resp . '] (name "' . $Customer->getDisplayName() . '")');
}
else
{
	print($CustomerService->lastError($Context));
}

/*
print('<br><br><br><br>');
print("\n\n\n\n\n\n\n\n");
print('Request [' . $IPP->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $IPP->lastResponse() . ']');
print("\n\n\n\n\n\n\n\n\n");
*/

?>

</pre>


<?php
require_once __DIR__ . '/views/footer.tpl.php';
