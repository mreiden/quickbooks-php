<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Payment as ObjPayment;
use QuickBooksPhpDevKit\IPP\Object\Line as ObjLine;
use QuickBooksPhpDevKit\IPP\Object\LinkedTxn as ObjLinkedTxn;
use QuickBooksPhpDevKit\IPP\Service\Payment;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$PaymentService = new Payment();

// Create payment object
$Payment = new ObjPayment();

$Payment->setPaymentRefNum('WEB123');
$Payment->setTxnDate('2014-02-11');
$Payment->setTotalAmt(10);

// Create line for payment (this details what it's applied to)
$Line = new ObjLine();
$Line->setAmount(10);

// The line has a LinkedTxn node which links to the actual invoice
$LinkedTxn = new ObjLinkedTxn();
$LinkedTxn->setTxnId('{-42}');
$LinkedTxn->setTxnType('Invoice');

$Line->setLinkedTxn($LinkedTxn);

$Payment->addLine($Line);

$Payment->setCustomerRef('{-26}');

// Send payment to QBO
if ($resp = $PaymentService->add($Context, $realm, $Payment))
{
	print('Our new Payment ID is: [' . $resp . ']');
}
else
{
	print($PaymentService->lastError());
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
