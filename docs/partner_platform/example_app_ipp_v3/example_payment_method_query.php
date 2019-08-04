<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\PaymentMethod;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$PaymentMethodService = new PaymentMethod();

$paymentmethods = $PaymentMethodService->query($Context, $realm, "SELECT * FROM PaymentMethod");

//print_r($terms);

foreach ($paymentmethods as $PaymentMethod)
{
	//print_r($Term);

	print('PaymentMethod Id=' . $PaymentMethod->getId() . ' is named: ' . $PaymentMethod->getName() . '<br>');
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
