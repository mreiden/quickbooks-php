<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\BillPayment;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$BillPaymentService = new BillPayment();

$billpayments = $BillPaymentService->query($Context, $realm, "SELECT * FROM BillPayment ");

//print_r($customers);

foreach ($billpayments as $BillPayment)
{
	print('Bill Payment # ' . $BillPayment->getDocNumber() . ' has a total of $' . $BillPayment->getTotalAmt() . "\n");

	print_r($BillPayment);
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
