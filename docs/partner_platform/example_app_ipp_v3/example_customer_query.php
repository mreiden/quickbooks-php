<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Customer;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$CustomerService = new Customer();

$customers = $CustomerService->query($Context, $realm, "SELECT * FROM Customer OrderBy Id MAXRESULTS 60");

foreach ($customers as $Customer)
{
	print('Customer Id=' . $Customer->getId() . ' is named: ' . $Customer->getFullyQualifiedName() . '<br>');
}

/*
print("\n\n\n\n");
print('Request [' . $CustomerService->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $CustomerService->lastResponse() . ']');
print("\n\n\n\n");
print('Error [' . $CustomerService->lastError() . ']');
print("\n\n\n\n");
*/

?>

</pre>


<?php
require_once __DIR__ . '/views/footer.tpl.php';
