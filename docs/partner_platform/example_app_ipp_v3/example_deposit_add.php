<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Deposit as ObjDeposit;
use QuickBooksPhpDevKit\IPP\Object\DepositLineDetail as ObjDepositLineDetail;
use QuickBooksPhpDevKit\IPP\Object\Line as ObjLine;
use QuickBooksPhpDevKit\IPP\Service\Deposit;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php
$DepositService = new Deposit();

// Create deposit object
$Deposit = new ObjDeposit();

// Create line for deposit (this details what it's applied to)
$Line = new ObjLine();
$Line->setAmount(100);
$Line->setDetailType('DepositLineDetail');
$DepositLineDetail = new ObjDepositLineDetail();
$DepositLineDetail->setEntity(10);
$DepositLineDetail->setAccountRef(87);
$Line->setDepositLineDetail($DepositLineDetail);

$Deposit->addLine($Line);
$Deposit->setGlobalTaxCalculation('NotApplicable');
$Deposit->setDepositToAccountRef('{-35}');


// Send Deposit to QBO

if ($resp = $DepositService->add($Context, $realm, $Deposit))
{
    print('Our new Deposit ID is: [' . $resp . ']');
}
else
{
    print($DepositService->lastError());
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
