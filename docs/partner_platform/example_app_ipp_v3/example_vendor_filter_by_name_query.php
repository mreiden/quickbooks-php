<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Vendor;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

//$VendorDisplayName = 'Shannon Palmer';
$VendorDisplayName = '%Palmer%';

?>

<pre>

<?php

$VendorService = new Vendor();

$vendors = $VendorService->query($Context, $realm, "SELECT * FROM Vendor WHERE DisplayName LIKE '$VendorDisplayName' ");

print 'Found '. count($vendors) .' vendors with DisplayName LIKE "' . $VendorDisplayName .'"<br>';
//print_r($vendors);

foreach ($vendors as $Vendor)
{
	//print_r($Vendor);

	print('Vendor Id=' . $Vendor->getId() . ' is named: ' . $Vendor->getDisplayName() . '<br>');
}

/*
print("\n\n\n\n");
print('Request [' . $IPP->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $IPP->lastResponse() . ']');
print("\n\n\n\n");
*/

?>

</pre>


<?php
require_once __DIR__ . '/views/footer.tpl.php';
