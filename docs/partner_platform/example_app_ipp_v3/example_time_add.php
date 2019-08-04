<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\TimeActivity as ObjTimeActivity;
use QuickBooksPhpDevKit\IPP\Service\TimeActivity;
use QuickBooksPhpDevKit\Utilities;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$TimeActivityService = new TimeActivity();

$TimeActivity = new ObjTimeActivity();
$TimeActivity->setTxnDate('2013-10-10');
$TimeActivity->setNameOf('Vendor');
$TimeActivity->setVendorRef('89');
$TimeActivity->setItemRef('8');
$TimeActivity->setHourlyRate('250');
$TimeActivity->setStartTime(Utilities::datetime('-5 hours'));
$TimeActivity->setEndTime(Utilities::datetime('-1 hour'));
$TimeActivity->setDescription('Test entry.');

if ($resp = $TimeActivityService->add($Context, $realm, $TimeActivity))
{
	print('Our new TimeActivity ID is: [' . $resp . ']');
}
else
{
	print($TimeActivityService->lastError($Context));
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
