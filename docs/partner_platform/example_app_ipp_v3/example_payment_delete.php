<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Payment;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$PaymentService = new Payment();

$the_payment_to_delete = '{-31}';

$retr = $PaymentService->delete($Context, $realm, $the_payment_to_delete);
if ($retr)
{
	print('The payment was deleted!');
}
else
{
	print('Could not delete payment: ' . $PaymentService->lastError());
}

/*
// For debugging

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
