<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Vendor as ObjVendor;
use QuickBooksPhpDevKit\IPP\Service\Vendor;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

$VendorDisplayName = 'Keith R Palmer Jr ' . mt_rand(0, 1000);

?>

<pre>

<?php

$VendorService = new Vendor();

$Vendor = new ObjVendor();
$Vendor->setTitle('Mr');
$Vendor->setGivenName('Keith');
$Vendor->setMiddleName('R');
$Vendor->setFamilyName('Palmer');
$Vendor->setDisplayName($VendorDisplayName);

if ($resp = $VendorService->add($Context, $realm, $Vendor))
{
	print('Our new Vendor ID for "' . $VendorDisplayName .'" is: [' . $resp . ']');
}
else
{
	print($VendorService->lastError($Context));
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
