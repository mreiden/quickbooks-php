<?php declare(strict_types=1);

// For OAuth2 (all new application, and what you should be migrating to)
require_once __DIR__ . '/config_oauthv2.php';

require_once __DIR__ . '/views/header.tpl.php';

$examples = [];

$files = scandir(__DIR__, SCANDIR_SORT_ASCENDING);
foreach ($files as $file)
{
	// Remove the ending ".php" from filename
	$file = basename($file, '.php');

	// Split the filename into parts
	$parts = explode('_', $file);

	// Remove the first word and make sure it is "example" (e.g. example_vendor_query.php)
	$start = array_shift($parts);
	if ($start != 'example')
	{
		continue;
	}

	// Remove the last word to get what kind of example this file is
	$last = array_pop($parts);
	switch ($last)
	{
		case 'get':
			$examples[$file . $site_php_extension] = 'Get ' . implode(' ', $parts);
			break;
		case 'add':
			$examples[$file . $site_php_extension] = 'Add a ' . implode(' ', $parts);
			break;
		case 'void':
			$examples[$file . $site_php_extension] = 'Void a ' . implode(' ', $parts);
			break;
		case 'update':
			$examples[$file . $site_php_extension] = 'Update a ' . implode(' ', $parts);
			break;
		case 'query':
			$examples[$file . $site_php_extension] = 'Query for ' . implode(' ', $parts);
			break;
		case 'count':
			$examples[$file . $site_php_extension] = 'Count ' . implode(' ', $parts);
			break;
		case 'cdc':
			$examples[$file . $site_php_extension] = 'Get objects that have changed since a timestamp';
			break;
		case 'entitlements':
			$examples[$file . $site_php_extension] = 'Get entitlement values (e.g. find out what features QBO supports)';
			break;
		case 'delete':
			$examples[$file . $site_php_extension] = 'Delete a ' . implode(' ', $parts);
			break;
		case 'objects';
			$examples[$file . $site_php_extension] = 'Object ' . implode(' ', $parts);
			break;
	}
}

?>

<div>
	<h1>
		Welcome to the QuickBooks PHP DevKit - IPP Intuit Anywhere Demo App!
	</h1>

	<p>
		This app demos a PHP connection to QuickBooks Online via the v3 REST APIs.
	</p>
	<p>
		<strong>Please make sure you review the <a target="_blank" href="http://www.consolibyte.com/docs/index.php/PHP_DevKit_for_QuickBooks_-_Intuit_Partner_Platform_Quick-Start">quick-start tutorial</a>!</strong>
	</p>
	<h2>
		Need Help?
	</h2>
	<p>
		You can get support on the forums:
	</p>
	<ul>
		<li><a target="_blank" href="http://www.consolibyte.com/forum/">ConsoliBYTE forums</a></li>
		<li><a target="_blank" href="https://intuitpartnerplatform.lc.intuit.com/">Intuit Developer forums</a></li>
	</ul>
	<p>
		When you post, <strong>make sure that you:</strong>
	</p>
	<ul>
		<li>Post your code.</li>
		<li>Check that all of your OAuth2 credentials and URLs match between your code and your Intuit account.</li>
		<li>Post your XML request/response. <a href="debugging<?= $site_php_extension ?>">Don't know how to get the request/response?</a></li>
		<li>Post the results of the <a href="troubleshooting<?= $site_php_extension ?>">troubleshooting script</a>.</li>
	</ul>


	<h2>QuickBooks connection status:</h2>
		<?php if ($quickbooks_is_connected): ?>
			<div style="border: 2px solid green; text-align: center; padding: 8px; color: green;">
				CONNECTED!<br>
				<br>
				<i>
					Realm: <?php print($realm); ?><br>
					Company: <?php print($quickbooks_CompanyInfo->getCompanyName()); ?><br>
					Email: <?php print($quickbooks_CompanyInfo->getEmail()->getAddress()); ?><br>
					Country: <?php print($quickbooks_CompanyInfo->getCountry()); ?>
				</i>
			</div>

			<h2>Example QuickBooks Stuff</h2>

			<table>
				<?php foreach ($examples as $file => $title): ?>
					<tr>
						<td>
							<a href="<?= $file ?>"><?= $title ?></a>
						</td>
						<td>
							<a href="source<?= $site_php_extension ?>?file=<?= basename($file, $site_php_extension) ?>.php">(view source)</a>
						</td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
						<a href="disconnect<?= $site_php_extension ?>">Disconnect from QuickBooks</a>
					</td>
					<td>
						(If you do this, you'll have to go back through the authorization/connection process to get connected again)
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
						<a href="reconnect<?= $site_php_extension ?>">Reconnect / refresh connection</a>
					</td>
					<td>
						(QuickBooks refresh tokens expire after 100 days, after which the user will have to reauthorize the app)
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
						<a href="diagnostics<?= $site_php_extension ?>">Diagnostics about QuickBooks connection</a>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
						<a href="debugging<?= $site_php_extension ?>">Need help debugging/troubleshooting?</a>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
			</table>

		<?php else: ?>
			<div style="border: 2px solid red; text-align: center; padding: 8px; color: red;">
				<b>NOT</b> CONNECTED!<br>
				<br>
				<ipp:connectToIntuit></ipp:connectToIntuit>
				<br>
				<br>
				<p>You must authenticate to QuickBooks <b>once</b> before you can exchange data with it.</p>
				<p><strong>You only have to do this once!</strong></p>

				After you've authenticated once, you never have to go
				through this connection process again. <br>
				Click the button above to
				authenticate and connect.
			</div>
		<?php endif; ?>

</div>

<?php

require_once __DIR__ . '/views/footer.tpl.php';
