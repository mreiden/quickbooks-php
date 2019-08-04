<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Bill as ObjBill;
use QuickBooksPhpDevKit\IPP\Object\Line as ObjLine;
use QuickBooksPhpDevKit\IPP\Object\AccountBasedExpenseLineDetail as ObjAccountBasedExpenseLineDetail;
use QuickBooksPhpDevKit\IPP\Service\Bill;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$BillService = new Bill();

$Bill = new ObjBill();

$Bill->setDocNumber('abc123');
$Bill->setTxnDate('2014-07-12');
$Bill->setVendorRef('{-39}');

$Line = new ObjLine();
$Line->setAmount(650);
$Line->setDetailType('AccountBasedExpenseLineDetail');

$AccountBasedExpenseLineDetail = new ObjAccountBasedExpenseLineDetail();
$AccountBasedExpenseLineDetail->setAccountRef('{-17}');

$Line->setAccountBasedExpenseLineDetail($AccountBasedExpenseLineDetail);

$Bill->addLine($Line);

if ($id = $BillService->add($Context, $realm, $Bill))
{
	print('New bill id is: ' . $id);
}
else
{
	print('Bill add failed...? ' . $BillService->lastError());
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
