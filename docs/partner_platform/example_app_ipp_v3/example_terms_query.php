<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Term;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$TermService = new Term();

$terms = $TermService->query($Context, $realm, "SELECT * FROM Term");

//print_r($terms);

foreach ($terms as $Term)
{
	//print_r($Term);

	print('Term Id=' . $Term->getId() . ' is named: ' . $Term->getName() . '<br>');
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
