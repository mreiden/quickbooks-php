<?php declare(strict_types=1);

ini_set('display_errors', '1');

// Remove any directory traversal characters
$file = str_replace(['.', '/', '\\', ':'], '', $_GET['file']);
// The dot in .php was removed, so replace the ending "php" with ".php"
$file = substr($file, 0, -3) . '.php';

highlight_file(__DIR__ . '/' . $file);
