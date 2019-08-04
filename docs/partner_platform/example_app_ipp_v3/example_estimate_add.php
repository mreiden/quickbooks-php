<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Estimate as ObjEstimate;
use QuickBooksPhpDevKit\IPP\Object\Line as ObjLine;
use QuickBooksPhpDevKit\IPP\Object\SalesItemLineDetail as ObjSalesItemLineDetail;
use QuickBooksPhpDevKit\IPP\Service\Estimate;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$EstimateService = new Estimate();

$Estimate = new ObjEstimate();

$Estimate->setDocNumber('WEB123');
$Estimate->setTxnDate('2013-10-11');

$Line = new ObjLine();
$Line->setDetailType('SalesItemLineDetail');
$Line->setAmount(12.95 * 2);

$SalesItemLineDetail = new ObjSalesItemLineDetail();
$SalesItemLineDetail->setItemRef('8');
$SalesItemLineDetail->setUnitPrice(12.95);
$SalesItemLineDetail->setQty(2);

$Line->addSalesItemLineDetail($SalesItemLineDetail);

$Estimate->addLine($Line);

$Estimate->setCustomerRef('12');


if ($resp = $EstimateService->add($Context, $realm, $Estimate))
{
	print('Our new Estimate ID is: [' . $resp . ']');
}
else
{
	print($EstimateService->lastError());
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
