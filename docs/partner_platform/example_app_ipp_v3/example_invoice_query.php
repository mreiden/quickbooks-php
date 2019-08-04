<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Invoice;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$InvoiceService = new Invoice();

$invoices = $InvoiceService->query($Context, $realm, "SELECT * FROM Invoice STARTPOSITION 1 MAXRESULTS 25");
//$invoices = $InvoiceService->query($Context, $realm, "SELECT * FROM Invoice WHERE DocNumber = '1002' ");

//print_r($customers);

foreach ($invoices as $Invoice)
{
	print('Invoice # ' . $Invoice->getDocNumber() . ' of Customer #' . $Invoice->getCustomerRef() . ' has a total of $' . $Invoice->getTotalAmt() . "\n");
	print('    First line item: ' . $Invoice->getLine(0)->getDescription() . "\n");
	print('    Internal Id value: ' . $Invoice->getId() . "\n");
	print("\n");

	//print_r($Invoice);
	//$Line = $Invoice->getLine(0);
	//print_r($Line);
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
