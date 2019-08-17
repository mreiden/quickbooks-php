<?php declare(strict_types=1);

// Composer Autoloader
require(dirname(__FILE__, 3) . '/vendor/autoload.php');

use QuickBooksPhpDevKit\Cast;
use QuickBooksPhpDevKit\PackageInfo;

// error reporting
ini_set('display_errors', '1');
error_reporting(E_ALL | E_STRICT);

header('Content-Type: text/html; charset=utf-8');

$arr = [
	'Keith Palmer, Shannon Daniels, Kurtis & Karli',
	'Test of some UTF8 chars- Á, Æ, Ë, ¾, Õ, ä, ß, ú, ñ',
	'Test & Then Some',
	'Test of already encoded &amp; data.',
	'Tapio Törmänen',
	'Here is the £ pound sign for you British gents...',
	"Quotes \" in 'String'",
	'Freddy Krûegër’s — “Nîghtmåre ¾"',
];

$fields = [
	'Name',
	'CompanyName',
	'FirstName',
	'LastName',
	'BillAddress_Addr1',
	'BillAddress_Addr2',
	'BillAddress_Addr3',
	'BillAddress_City',
	'BillAddress_State',
	'BillAddress_Country',
	'BillAddress_PostalCode',
	'ShipAddress_Addr1',
	'ShipAddress_Addr2',
	'ShipAddress_Addr3',
	'ShipAddress_City',
	'ShipAddress_State',
	'ShipAddress_Country',
	'ShipAddress_PostalCode',
	'Phone',
	'AltPhone',
	'Fax',
	'Email',
	'Contact',
	'AltContact',
];

print("\n");
print('****** ' . PackageInfo::Actions['ADD_CUSTOMER'] . " Casts: ******\n");
foreach ($fields as $field)
{
	foreach ($arr as $key => $value)
	{
		$cast = Cast::cast(PackageInfo::Actions['ADD_CUSTOMER'], str_replace('_', ' ', $field), ucfirst($value));
		print("\t" . $field . ' : {' . $cast . '} (length: ' . strlen($cast) . ')' . "\n");
	}

	print("\n");
}
print("\n");


print("\n");
print("\n");
print("\n");

//exit;

$invoice = [
	'IsPaid' => true,
	'IsToBePrinted' => false,
	'IsToBeEmailed' => true,
	'IsFinanceCharge' => false,
	'IsPending' => true,

	'InvoiceLine Class FullName' => 'Test & Class',
	'InvoiceLine Item FullName' => 'Item & Test',
];

print('****** ' . PackageInfo::Actions['ADD_INVOICE'] . " Casts: ******\n");
foreach ($invoice as $key => $value)
{
	print("\t" . $key . ' => ' . Cast::cast(PackageInfo::Actions['ADD_INVOICE'], $key, $value) . "\n");
}
