<?php declare(strict_types=1);

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

use QuickBooksPhpDevKit\IPP\IntuitAnywhere;

$err = '';
$reconnected = false;

$expiry = $IntuitAnywhere->expiryV2($the_tenant);

if ($expiry == IntuitAnywhere::EXPIRY_SOON)
{
	if ($IntuitAnywhere->reconnectV2($the_tenant))
	{
		$reconnected = true;
	}
	else
	{
		$reconnected = false;
		$err = $IntuitAnywhere->errorNumber() . ': ' . $IntuitAnywhere->errorMessage();
	}

	//print_r($IntuitAnywhere->load($the_username, $the_tenant));
	//print("\n\n\n");
	//print($IntuitAnywhere->lastRequest());
	//print("\n\n\n");
	//print($IntuitAnywhere->lastResponse());
}
else if ($expiry == IntuitAnywhere::EXPIRY_NOTYET)
{
	$err = 'This connection is not old enough to require reconnect/refresh.';
}
else if ($expiry == IntuitAnywhere::EXPIRY_EXPIRED)
{
	$err = 'This connection has already expired. You\'ll have to go through the initial connection process again.';
}
else if ($expiry == IntuitAnywhere::EXPIRY_UNKNOWN)
{
	$err = 'Are you sure you\'re connected? No connection information was found for this user/tenant...';
}

?>

	<?php if ($reconnected): ?>

		<div style="text-align: center; font-family: sans-serif; font-weight: bold; color: green">
			RECONNECTED! (refreshed OAuth tokens)
		</div>

		<script>
			window.setTimeout("window.location = 'index<?= $site_php_extension ?>';", 2500);
		</script>

	<?php else: ?>

		<div style="text-align: center; font-family: sans-serif; font-weight: bold; color: red">
			ERROR: <?php print($err); ?>
		</div>

	<?php endif; ?>

<?php

require_once __DIR__ . '/views/footer.tpl.php';
