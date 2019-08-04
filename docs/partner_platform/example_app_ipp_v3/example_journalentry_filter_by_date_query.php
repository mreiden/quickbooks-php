<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\JournalEntry;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$JournalEntryService = new JournalEntry();

$list = $JournalEntryService->query($Context, $realm, "SELECT * FROM JournalEntry WHERE TxnDate > '2014-01-16' ");

//print_r($salesreceipts);

foreach ($list as $JournalEntry)
{
	print_r($JournalEntry);
}

/*
print($IPP->lastError($Context));
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
