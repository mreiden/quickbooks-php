<?php declare(strict_types=1);

use QuickBooksPhpDevKit\IPP\Service\TimeActivity;

require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

?>

<pre>

<?php

$TimeActivityService = new TimeActivity();

$time = $TimeActivityService->query($Context, $realm, "SELECT * FROM TimeActivity ");

print_r($time);

?>

</pre>

<?php

require_once __DIR__ . '/views/footer.tpl.php';
