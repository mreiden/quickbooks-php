<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Item as ObjItem;
use QuickBooksPhpDevKit\IPP\Service\Item;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$ItemService = new Item();

$Item = new ObjItem();

$Item->setName('My Item');
$Item->setType('Service');
$Item->setIncomeAccountRef('53');

if ($resp = $ItemService->add($Context, $realm, $Item))
{
	print('Our new Item ID is: [' . $resp . ']');
}
else
{
	print($ItemService->lastError($Context));
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
