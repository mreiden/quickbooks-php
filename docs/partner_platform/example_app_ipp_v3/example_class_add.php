<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Qbclass as ObjQbclass;
use QuickBooksPhpDevKit\IPP\Service\Qbclass;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$ClassService = new Qbclass();

$Class = new ObjQbclass();

$Class->setName('My Class');

if ($resp = $ClassService->add($Context, $realm, $Class))
{
	print('Our new class ID is: [' . $resp . ']');
}
else
{
	print($ClassService->lastError());
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
