<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Qbclass;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<br>
<br>
<br>
<br>

<h1>
    This will return nothing until you add a Class in QuickBooks Online.
</h1>

<br>
<br>
<br>
<br>
<br>

<pre>

<?php

$ClassService = new Qbclass();

$classes = $ClassService->query($Context, $realm, "SELECT * FROM Class");

print_r($classes);

foreach ($classes as $Class)
{
	print('Class Id=' . $Class->getId() . ' is named: ' . $Class->getName() . '<br>');
}

/*
print("\n\n\n\n");
print('Request [' . $ClassService->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $ClassService->lastResponse() . ']');
print("\n\n\n\n");
*/

?>

</pre>


<?php
require_once __DIR__ . '/views/footer.tpl.php';
