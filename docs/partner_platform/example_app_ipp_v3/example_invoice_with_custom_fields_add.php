<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Invoice as ObjInvoice;
use QuickBooksPhpDevKit\IPP\Object\Line as ObjLine;
use QuickBooksPhpDevKit\IPP\Object\SalesItemLineDetail as ObjSalesItemLineDetail;
use QuickBooksPhpDevKit\IPP\Object\CustomField as ObjCustomField;
use QuickBooksPhpDevKit\IPP\Service\Invoice;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$InvoiceService = new Invoice();

$Invoice = new ObjInvoice();

$Invoice->setDocNumber('WEB456');
$Invoice->setTxnDate('2013-12-05');

$Line = new ObjLine();
$Line->setDetailType('SalesItemLineDetail');
$Line->setAmount(12.95 * 2);

$SalesItemLineDetail = new ObjSalesItemLineDetail();
$SalesItemLineDetail->setItemRef('8');
$SalesItemLineDetail->setUnitPrice(12.95);
$SalesItemLineDetail->setQty(2);

$Line->addSalesItemLineDetail($SalesItemLineDetail);

$Invoice->addLine($Line);

$Invoice->setCustomerRef('67');

// Add a custom field to the invoice (YOU NEED TO DEFINE THIS IN THE QBO PREFERENCES FIRST!!!)
$CustomField = new ObjCustomField();
$CustomField->setName('Test Field');
$CustomField->setType('StringType');
$CustomField->setStringValue('Test value here');
$Invoice->addCustomField($CustomField);

if ($resp = $InvoiceService->add($Context, $realm, $Invoice))
{
	print('Our new Invoice ID is: [' . $resp . ']');
}
else
{
	print($InvoiceService->lastError());
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
