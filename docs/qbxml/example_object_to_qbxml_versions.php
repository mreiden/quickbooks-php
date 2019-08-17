<?php declare(strict_types=1);

/**
 * Example of building qbXML for specific versions of QuickBooks using the QuickBooks_Object_* classes
 *
 * Certain versions of QuickBooks may or may not support certain different
 * features of the qbXML specification. For instance, Online Edition may not
 * support the 'Customer Type' field. The use of locale constants allows us to
 * build qbXML requests from objects, tailoring those requests to the specific
 * qbXML version and locale we want to send the request to.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 *
 * @package QuickBooks
 * @subpackage Documentation
 */

// Composer Autoloader
require(dirname(__FILE__, 3) . '/vendor/autoload.php');

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\Customer;
//use QuickBooksPhpDevKit\QBXML\Object\SalesReceipt;
//use QuickBooksPhpDevKit\QBXML\Object\SalesReceipt\SalesTaxLine;
//use QuickBooksPhpDevKit\QBXML\Object\ReceivePayment;
//use QuickBooksPhpDevKit\QBXML\Object\ReceivePayment\AppliedToTxn;


// Plain text output
header('Content-Type: text/plain; charset=utf-8');

// Error reporting
ini_set('display_errors', '1');
error_reporting(E_ALL | E_STRICT);


/*
// Create a new SalesReceipt object
$SalesReceipt = new SalesReceipt();

// Set some fields
$SalesReceipt->setCustomerFullName('ConsoliBYTE:Keith Palmer');

// This is for US editions
$SalesReceipt->setItemSalesTaxFullName('CT Sales Tax');

// This is for Online Edition
$SalesTaxLine = new SalesTaxLine();
$SalesTaxLine->setAmount(7.50);
$SalesReceipt->addSalesTaxLine($SalesTaxLine);

// Add a line items
$Line1 = new SalesReceiptLine();
$Line1->setItemFullName('QuickBooks Integration:PHP Integration');
$Line1->setQuantity(1);
$Line1->setRate(125.0);
$Line1->setSalesTaxCodeFullName('TAX');

$SalesReceipt->addSalesReceiptLine($Line1);

print('qbXML SalesReceipt for QuickBooks qbXML US editions: ' . "\n");
print($SalesReceipt->asQBXML(PackageInfo::Actions['ADD_SALESRECEIPT'], null, PackageInfo::Locale['UNITED_STATES']));

print("\n\n");

print('qbXML SalesReceipt for QuickBooks qbXML Online Edition: ' . "\n");
print($SalesReceipt->asQBXML(PackageInfo::Actions['ADD_SALESRECEIPT'], null, PackageInfo::Locale['ONLINE_EDITION']));

exit;
*/



// Create a Customer object
$Customer = new Customer();

// Set some fields
$Customer->setFullName('Contractors:ConsoliBYTE, LLC:Keith Palmer');
$Customer->setCustomerTypeFullName('Web:Direct');
$Customer->setCompanyName('"ConsoliBYTE" aka \'Keith Palmer\'');

// Not in QBOE
$Customer->setIsActive(false);
$Customer->setNotes('Test notes go here.');

// QBXML 12.0+
$Customer->setClassListID('50000005-1234567890');



print('qbXML Customer for QuickBooks qbXML (latest version the framework supports): ' . "\n");
print($Customer->asQBXML(PackageInfo::Actions['ADD_CUSTOMER']));
print("\n\n");

print('qbXML Customer for QuickBooks qbXML US editions: ' . "\n");
print($Customer->asQBXML(PackageInfo::Actions['ADD_CUSTOMER'], null, PackageInfo::Locale['UNITED_STATES']));
print("\n\n");

print('qbXML Customer for QuickBooks qbXML v10.0 US editions: ' . "\n");
print($Customer->asQBXML(PackageInfo::Actions['ADD_CUSTOMER'], 10, PackageInfo::Locale['UNITED_STATES']));
print("\n\n");

print('qbXML Customer for QuickBooks qbXML Online Edition: ' . "\n");
print($Customer->asQBXML(PackageInfo::Actions['ADD_CUSTOMER'], null, PackageInfo::Locale['ONLINE_EDITION']));
print("\n\n");


// The QuickBooks ID string assigned to this Customer
$Customer->setListID('70000007-1234567890');
// The QuickBooks EditSequence string assigned to this Customer
$Customer->setEditSequence(9876543210);

print('qbXML Customer (modification) for QuickBooks qbXML Online Edition: ' . "\n");
print($Customer->asQBXML(PackageInfo::Actions['MOD_CUSTOMER'], null, PackageInfo::Locale['ONLINE_EDITION']));
