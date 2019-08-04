<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Object\Purchase as ObjPurchase;
use QuickBooksPhpDevKit\IPP\Object\AccountBasedExpenseLineDetail as ObjAccountBasedExpenseLineDetail;
use QuickBooksPhpDevKit\IPP\Object\Line as ObjLine;
use QuickBooksPhpDevKit\IPP\Service\Purchase;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$PurchaseService = new Purchase();

// Create our Purchase
$Purchase = new Purchase();

$Line = new ObjLine();
$Line->setDescription('Test description');
$Line->setAmount(29.95);
$Line->setDetailType('AccountBasedExpenseLineDetail');

$AccountBasedExpenseLineDetail = new ObjAccountBasedExpenseLineDetail();
$AccountBasedExpenseLineDetail->setAccountRef('{-9}');
$AccountBasedExpenseLineDetail->setBillableStatus('NotBillable');

$Line->setAccountBasedExpenseLineDetail($AccountBasedExpenseLineDetail);

$Purchase->addLine($Line);

$Purchase->setAccountRef('{-58}');
$Purchase->setEntityRef('{-137}');
$Purchase->setPaymentType('Check');

/*
  <Line>
    <Id>1</Id>
    <Description>this is line 1</Description>
    <Amount>10.00</Amount>
    <DetailType>AccountBasedExpenseLineDetail</DetailType>
    <AccountBasedExpenseLineDetail>
      <AccountRef name="Cash Over and Short">65</AccountRef>
      <BillableStatus>NotBillable</BillableStatus>
      <TaxCodeRef>3</TaxCodeRef>
    </AccountBasedExpenseLineDetail>
  </Line>
  <AccountRef name="Test Purchase Bank Account">61</AccountRef>
  <PaymentType>Cash</PaymentType>
  <EntityRef name="Mr V3 Service Customer Jr2">1</EntityRef>
  <TotalAmt>11.00</TotalAmt>
  <GlobalTaxCalculation>TaxInclusive</GlobalTaxCalculation>
*/

if ($id = $PurchaseService->add($Context, $realm, $Purchase))
{
	print('New purchase check id is: ' . $id);
}
else
{
	print('Error creating check: ' . $IPP->lastError($Context) . '');
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
