<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Customer;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

// Jobs are really just Customers, so we can use the CustomerService and Customer query methods to do this
$CustomerService = new Customer();

// Get all jobs that have a parent customer "Derrick Huckleberry"
$parentName = 'Derrick Huckleberry';
$jobs = $CustomerService->query($Context, $realm, "SELECT * FROM Customer WHERE FullyQualifiedName LIKE '$parentName:%' ");

print('Found '. count($jobs) . ' Customer Jobs under ' . $parentName .'<br>');
print_r($jobs);

foreach ($jobs as $Job)
{
	print('Job Id=' . $Job->getId() . ' is named: ' . $Job->getFullyQualifiedName() . '<br>');
}

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
