<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Account as ObjAccount;
use QuickBooksPhpDevKit\IPP\Service\Account;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$AccountService = new Account();

$Account = new ObjAccount();

$Account->setName('My Test Name');
$Account->setDescription('Here is my description');
$Account->setAccountType('Income');

if ($resp = $AccountService->add($Context, $realm, $Account))
{
	print('Our new Account ID is: [' . $resp . ']');
}
else
{
	print($AccountService->lastError());
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
