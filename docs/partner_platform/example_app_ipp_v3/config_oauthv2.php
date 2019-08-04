<?php declare(strict_types=1);

/**
 * Intuit Partner Platform configuration variables
 *
 * See the scripts that use these variables for more details.
 *
 * @package QuickBooks
 * @subpackage Documentation
 */

// Require Composer Autoloader
require_once(dirname(__DIR__,3) . '/vendor/autoload.php');

use QuickBooksPhpDevKit\IPP;
use QuickBooksPhpDevKit\IPP\IntuitAnywhere;
use QuickBooksPhpDevKit\IPP\Service\CompanyInfo;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;



if (!file_exists(__DIR__ . '/config-private.php'))
{
	$random_key = function_exists('sodium_crypto_secretbox_keygen') ? base64_encode(sodium_crypto_secretbox_keygen()) : 'sodium extension is required';

	echo '<html><body><h1>You must copy/rename config-private-example.php to config-private.php</h1>';
	echo '<p>You can use <span style="font-family:monospace; background-color:#ccc">' . $random_key . '</span> for $encryption_key';
	echo ' or generate one yourself using <span style="font-family:monospace; background-color:#ccc">sodium_crypto_secretbox_keygen())</span>';
	echo '<hr>';
	if (file_exists(__DIR__ . '/config-private-example.php'))
	{
		highlight_file(__DIR__ . '/config-private-example.php');
	}
	echo '</body></html>';
	exit;
}
require_once(__DIR__ . '/config-private.php');


// Make sure there is an encryption key
if (empty($encryption_key))
{
	print("<h1>Missing encryption Key</h1>\n");
	print('<p>You must save the key in config-private.php</p>' . "\n");
	exit;
}


// Turn on some error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Guess at the demo app base url
if (empty($site_base_url))
{
	$site_base_url = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	$site_base_url = substr($site_base_url, 0, strrpos($site_base_url, '/'));
}

// This is the URL of your OAuth auth handler page
if (empty($quickbooks_oauth_url))
{
	$quickbooks_oauth_url = $site_base_url .'/oauth' . $site_php_extension;
}

// This is the URL to forward the user to after they have connected to IPP/IDS via OAuth
if (empty($quickbooks_success_url))
{
	$quickbooks_success_url = $site_base_url .'/success' . $site_php_extension;
}

// This is the menu URL script
if (empty($quickbooks_menu_url))
{
	$quickbooks_menu_url = $site_base_url .'/menu' . $site_php_extension;
}



// Initialize the database tables for storing OAuth information
if (!Utilities::initialized($dsn))
{
	// Initialize creates the neccessary database schema for queueing up requests and logging
	Utilities::initialize($dsn);
}

// Instantiate our Intuit Anywhere auth handler
//
// The parameters passed to the constructor are:
//  $oauth_version          QuickBooks_IPP_IntuitAnywhere::OAUTH_V2
//	$dsn
//	$oauth_client_id		Intuit will give this to you when you create a new Intuit Anywhere application at AppCenter.Intuit.com
//	$oauth_client_secret	Intuit will give this to you too
//	$this_url				This is the full URL (e.g. http://path/to/this/file.php) of THIS SCRIPT
//	$that_url				After the user authenticates, they will be forwarded to this URL
//
$IntuitAnywhere = new IntuitAnywhere(
	IntuitAnywhere::OAUTH_V2,
	$sandbox,
	$scope,
	$dsn,
	$encryption_key,
	$oauth_client_id,
	$oauth_client_secret,
	$quickbooks_oauth_url,
	$quickbooks_success_url);

$quickbooks_is_connected = false;
// Are they connected to QuickBooks right now?
if ($IntuitAnywhere->check($the_tenant) &&
	$IntuitAnywhere->test($the_tenant))
{
	// Yes, they are
	$quickbooks_is_connected = true;

	// Set up the IPP instance
	$IPP = new IPP($dsn, $encryption_key);

	// Get our OAuth credentials from the database
	$creds = $IntuitAnywhere->load($the_tenant);

	// Tell the framework to load some data from the OAuth store
	$IPP->authMode(IPP::AUTHMODE_OAUTHV2, $creds);

	if ($sandbox)
	{
		// Turn on sandbox mode/URLs
		$IPP->sandbox(true);
	}

	// Print the credentials we're using
	//print_r($creds);

	// This is our current realm
	$realm = $creds['qb_realm'];

	// Load the OAuth information from the database
	$Context = $IPP->context();

	// Get some company info
	$CompanyInfoService = new CompanyInfo();
	$quickbooks_CompanyInfo = $CompanyInfoService->get($Context, $realm);
}
