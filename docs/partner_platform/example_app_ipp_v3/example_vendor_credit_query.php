<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\VendorCredit;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$VendorCreditService = new VendorCredit();

$vcs = $VendorCreditService->query($Context, $realm, "SELECT * FROM VendorCredit");

print('Found '. count($vcs) . ' Vendor Credits<br>');
print_r($vcs);

foreach ($vcs as $Vc)
{
	print_r($Vc);
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
