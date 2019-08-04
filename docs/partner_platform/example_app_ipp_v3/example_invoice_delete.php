<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Invoice;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$InvoiceService = new Invoice();


// Find the invoice added with example_invoice_add
$invoices = $InvoiceService->query($Context, $realm, "SELECT * FROM Invoice WHERE DocNumber = 'Example-100' ");


$the_invoice_to_delete = ($invoices) ? $invoices[0]->getId() : '{-10}';
print('Trying to delete invoice ID '. $the_invoice_to_delete .'<br>');


$retr = $InvoiceService->delete($Context, $realm, $the_invoice_to_delete);
if ($retr)
{
	print('The invoice was deleted!');
}
else
{
	print('Could not delete invoice: ' . $InvoiceService->lastError());
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
