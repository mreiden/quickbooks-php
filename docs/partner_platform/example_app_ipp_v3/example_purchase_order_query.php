<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\PurchaseOrder;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$PurchaseOrderService = new PurchaseOrder();

$pos = $PurchaseOrderService->query($Context, $realm, "SELECT * FROM PurchaseOrder");

//print_r($terms);

foreach ($pos as $PurchaseOrder)
{
	//print_r($Term);

	print('PurchaseOrder Id=' . $PurchaseOrder->getId() . ' is named: ' . $PurchaseOrder->getDocNumber() . '<br>');
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
