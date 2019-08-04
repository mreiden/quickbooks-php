<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Account;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$AccountService = new Account();

$accounts = $AccountService->query($Context, $realm, "SELECT * FROM Account");

//print_r($customers);

foreach ($accounts as $Account)
{
	print('Account Id=' . $Account->getId() . ' is named: ' . $Account->getFullyQualifiedName() . '<br>');
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
