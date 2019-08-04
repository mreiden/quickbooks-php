<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Customer;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$CustomerService = new Customer();

$count = $CustomerService->query($Context, $realm, "SELECT COUNT(*) FROM Customer  ");

print('There are a total of ' . $count . ' customers!');

/*
print("\n\n\n\n");
print('Request [' . $CustomerService->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $CustomerService->lastResponse() . ']');
print("\n\n\n\n");
*/

?>

</pre>


<?php
require_once __DIR__ . '/views/footer.tpl.php';
