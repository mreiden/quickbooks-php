<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\Invoice;

require_once __DIR__ . '/config_oauthv2.php';

$InvoiceService = new Invoice();

$invoices = $InvoiceService->query($Context, $realm, "SELECT * FROM Invoice STARTPOSITION 1 MAXRESULTS 1");
$invoice = reset($invoices);
$id = substr($invoice->getId(), 2, -1);

header('Content-Disposition: attachment; filename="example_invoice.pdf"');
header('Content-Type: application/x-pdf');
print $InvoiceService->pdf($Context, $realm, $id);
