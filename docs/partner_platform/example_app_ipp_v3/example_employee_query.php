<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Employee;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$EmployeeService = new Employee();

$employees = $EmployeeService->query($Context, $realm, "SELECT * FROM Employee ");

//print_r($customers);

foreach ($employees as $Employee)
{
	print('Employee id=' . $Employee->getId() . ' has a name of ' . $Employee->getGivenName() . ' ' . $Employee->getFamilyName() . "\n");

}


print("\n\n\n\n");
print('Request [' . $IPP->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $IPP->lastResponse() . ']');
print("\n\n\n\n");

?>

</pre>


<?php
require_once __DIR__ . '/views/footer.tpl.php';
