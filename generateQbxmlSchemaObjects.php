<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use QuickBooksPhpDevKit\QBXML\Schema\Generator;


header('Content-Type: text/plain; charset=utf-8');

//$content = file_get_contents('data/qbxmlops130.xml');

$generator = new Generator();
$generator->saveAll();
echo "\n\nDone\n";
