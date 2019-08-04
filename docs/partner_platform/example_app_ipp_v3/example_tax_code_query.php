<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\TaxCode;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$TaxCodeService = new TaxCode();

$taxcodes = $TaxCodeService->query($Context, $realm, "SELECT * FROM TaxCode");

foreach ($taxcodes as $TaxCode)
{
	//print_r($Item);

	print('TaxCode Id=' . $TaxCode->getId() . ' is named: ' . $TaxCode->getName() . '<br>');
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
