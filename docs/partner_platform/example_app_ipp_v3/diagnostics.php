<?php declare(strict_types=1);

header('Content-Type: text/plain');

require_once __DIR__ . '/config_oauthv2.php';

$check = $IntuitAnywhere->check($the_tenant);
$test = $IntuitAnywhere->test($the_tenant);

$creds = $IntuitAnywhere->load($the_tenant);

$diagnostics = array_merge([
	'check' => $check,
	'test' => $test,
	], (array) $creds);

print_r($diagnostics);
