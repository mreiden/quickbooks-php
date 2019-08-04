<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Purchase;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$PurchaseService = new Purchase();

$purchases = $PurchaseService->query($Context, $realm, "SELECT * FROM Purchase");
print_r($purchases);

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
