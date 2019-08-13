<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use QuickBooksPhpDevKit\QBXML\Schema\Generator;


header('Content-Type: text/plain; charset=utf-8');

$generator = new Generator();
$generator->saveAll();
echo "\n\nDone\n";
