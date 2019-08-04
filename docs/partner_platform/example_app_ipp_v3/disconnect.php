<?php declare(strict_types=1);

require_once __DIR__ . '/config_oauthv2.php';

if ($IntuitAnywhere->disconnect('UNUSED_IN_OAUTHV2', $the_tenant))
{
    // Do something.  This just redirects to the index page.
}

require_once __DIR__ . '/views/header.tpl.php';

?>


		<div style="text-align: center; font-family: sans-serif; font-weight: bold;">
			DISCONNECTED! Please wait...
		</div>


		<script>
			window.setTimeout("window.location = 'index<?= $site_php_extension ?>';", 2500);
		</script>


<?php
require_once __DIR__ . '/views/footer.tpl.php';
