<?php declare(strict_types=1);

require_once __DIR__ . '/config_oauthv2.php';

// Display the menu
die($IntuitAnywhere->widgetMenu($the_username, $the_tenant));
