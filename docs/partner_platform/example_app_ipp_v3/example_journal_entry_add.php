<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\JournalEntry as ObjJournalEntry;
use QuickBooksPhpDevKit\IPP\Object\Line as ObjLine;
use QuickBooksPhpDevKit\IPP\Object\JournalEntryLineDetail as ObjJournalEntryLineDetail;
use QuickBooksPhpDevKit\IPP\Service\JournalEntry;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$JournalEntryService = new JournalEntry();

// Main journal entry object
$JournalEntry = new ObjJournalEntry();
$JournalEntry->setDocNumber('1234');
$JournalEntry->setTxnDate(date('Y-m-d'));

// Debit line
$Line1 = new ObjLine();
$Line1->setDescription('Line 1 description');
$Line1->setAmount(100);
$Line1->setDetailType('JournalEntryLineDetail');

$Detail1 = new ObjJournalEntryLineDetail();
$Detail1->setPostingType('Debit');
$Detail1->setAccountRef(3);

$Line1->addJournalEntryLineDetail($Detail1);
$JournalEntry->addLine($Line1);

// Credit line
$Line2 = new ObjLine();
$Line2->setDescription('Line 2 description');
$Line2->setAmount(100);
$Line2->setDetailType('JournalEntryLineDetail');

$Detail2 = new ObjJournalEntryLineDetail();
$Detail2->setPostingType('Credit');
$Detail2->setAccountRef(56);

$Line2->addJournalEntryLineDetail($Detail2);
$JournalEntry->addLine($Line2);

if ($resp = $JournalEntryService->add($Context, $realm, $JournalEntry))
{
	print('Our new journal entry ID is: [' . $resp . ']');
}
else
{
	print($JournalEntryService->lastError($Context));
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
