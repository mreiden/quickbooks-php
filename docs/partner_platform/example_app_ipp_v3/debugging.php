<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Bill as ObjBill;
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
$Bill->setVendorRef('{-9}');

$id = $BillService->add($Context, $realm, $Bill);
if ($id)
{
	print('This will never happen... (this script is DESIGNED TO TRIGGER ERRORS to teach you how to troubleshoot!');
}
else
{
	// The below lines are helpful for debugging and troubleshooting.
	//  They will show you the raw XML request that was sent to Intuit, and the
	//  raw XML response that Intuit returned.
	//
	// IF YOU ASK FOR HELP ONLINE, PLEASE PROVIDE THE OUTPUT FROM THESE LINES.
	// WE WILL NOT BE ABLE TO ASSIST YOU WITHOUT THIS INFORMATION!!!

	print('The bill add failed, and here is why:');

	print('ERROR: ' . $BillService->lastError());
	print('<br><br>');
	print('REQUEST: <code>' . htmlspecialchars($BillService->lastRequest()) . '</code><br><br><br>');
	print('RESPONSE: <code>' . htmlspecialchars($BillService->lastResponse()) . '</code><br><br><br>');
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
