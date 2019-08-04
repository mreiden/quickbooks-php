<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Invoice;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$InvoiceService = new Invoice();

$invoice_to_void = '{-34}';
//$invoice_to_void = 34;    // just the integer will work too

if ($resp = $InvoiceService->void($Context, $realm, $invoice_to_void))
{
	print('&nbsp; Invoice Voided!<br>');
}
else
{
	print('&nbsp; ' . $InvoiceService->lastError() . '<br>');
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
