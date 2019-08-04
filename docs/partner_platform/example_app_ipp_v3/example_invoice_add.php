<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Invoice as ObjInvoice;
use QuickBooksPhpDevKit\IPP\Object\Line as ObjLine;
use QuickBooksPhpDevKit\IPP\Object\SalesItemLineDetail as ObjSalesItemLineDetail;
use QuickBooksPhpDevKit\IPP\Service\Invoice;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$InvoiceService = new Invoice();

$Invoice = new ObjInvoice();

$Invoice->setDocNumber('Example-100');
$Invoice->setTxnDate('2013-10-11');

$Line = new ObjLine();
$Line->setDetailType('SalesItemLineDetail');
$Line->setAmount(20.0000 * 1.0000 * 0.516129);
$Line->setDescription('Test description goes here.');

$SalesItemLineDetail = new ObjSalesItemLineDetail();
$SalesItemLineDetail->setItemRef('8');
$SalesItemLineDetail->setUnitPrice(20 * 0.516129);
$SalesItemLineDetail->setQty(1.00000);

$Line->addSalesItemLineDetail($SalesItemLineDetail);

$Invoice->addLine($Line);

$Invoice->setCustomerRef('12');


if ($resp = $InvoiceService->add($Context, $realm, $Invoice))
{
	print('Our new Invoice ID is: [' . $resp . ']');
}
else
{
	print($InvoiceService->lastError());
}

print('<br><br><br><br>');
print("\n\n\n\n\n\n\n\n");
print('Request [' . $IPP->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $IPP->lastResponse() . ']');
print("\n\n\n\n\n\n\n\n\n");

?>

</pre>


<?php
require_once __DIR__ . '/views/footer.tpl.php';
