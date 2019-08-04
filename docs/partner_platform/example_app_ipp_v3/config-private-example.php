<?php declare(strict_types=1);

/**
 * Intuit Partner Platform configuration variables
 *
 * See the scripts that use these variables for more details.
 *
 * @package QuickBooks
 * @subpackage Documentation
 */


//**************************************************************************************************************************
//*****
//*****   BEGIN BASIC USER CONFIGURATION
//*****
//**************************************************************************************************************************

// If you're using DEVELOPMENT TOKENS, you MUST USE SANDBOX MODE!!!  If you're in PRODUCTION, then DO NOT use sandbox.
$sandbox = true;     // When you're using development tokens
//$sandbox = false;    // When you're using production tokens


// Your OAuth2 client_id and client_secret
//
// These are available in the "Keys and Oauth" section once you create your Intuit App
// on https://developer.intuit.com/
//
// IMPORTANT:
//	To pass your tech review with Intuit, you'll have to AES encrypt these and
//	store them somewhere safe.
//
// The OAuth request/access tokens will be encrypted and stored for you by the
//	PHP DevKit IntuitAnywhere classes automatically.
$oauth_client_id = '';
$oauth_client_secret = '';

// Encryption key to encrypt tokens in the database.
// You can copy what is randomly generated when you view index.php or by
// running the following yourself:  base64_encode(sodium_crypto_secretbox_keygen())
$encryption_key = '';


// Scope required (see https://developer.intuit.com/app/developer/qbo/docs/develop/authentication-and-authorization/faq#what-is-the-purpose-of-scopes)
//
//  com.intuit.quickbooks.accounting  — QuickBooks Online API
//  com.intuit.quickbooks.payment     — QuickBooks Payments API
//  openid                            — OpenID Connect processing
//  profile                           — user’s given and family names
//  email                             — user’s email address
//  phone                             — user’s phone number
//  address                           — user’s physical address
$scope = 'com.intuit.quickbooks.accounting ';
//$scope .= 'com.intuit.quickbooks.payment ';
//$scope .= 'openid ';
//$scope .= 'profile ';
//$scope .= 'email ';
//$scope .= 'phone ';
//$scope .= 'address ';


// The tenant the user is accessing within your app
// This is the id you assign to the clients using your intuit app.
// You can use '12345' if you are using this for your company only and are not selling your app to others.
$the_tenant = (string) 12345;


// This is a database connection string that will be used to store the OAuth credentials
//$dsn = 'mysqli://dev:password@localhost/quickbooks';
$dsn = [
	'backend' => 'pgsql',
	'database' => 'quickbooks_ipp_demo',
	'host' => 'localhost',
	//'port' => 5432,
	'username' => 'postgres',
	'password' => '',
];
$dsn = [
	'backend' => 'mysqli',
	'database' => 'quickbooks_ipp_demo',
	'host' => 'localhost',
	//'port' => 3306,
	'username' => 'mysql',
	'password' => '',
];
$dsn = [
	'backend' => 'sqlite3',
	'database' => sys_get_temp_dir().'/quickbooks_ipp_demo.sqlite3',
];


// *** What do your urls for this demo app end in?
//    /**
//     * @Route("/ipp/{page}", name="Intuit")
//     */
//    public function quickbooksIntuitAction(string $page)
//    {
//        $base_dir = dirname(__DIR__, 2) . '/quickbooks-php/docs/partner_platform/example_app_ipp_v3';
//        $file = "$base_dir/$page.php";
//
//        if (!file_exists($file)) {
//            throw new \Exception('No such file: '. $file);
//        }
//        require($file);
//        exit;
//    }
// *** Comment out the .php line if you use friendly urls (e.g. Symfony or another framework)
$site_php_extension = '';
$site_php_extension = '.php';


// These really should just work, but you might have to set them in special circumstances.
//
// NOTE!!!: You must enter $quickbooks_oauth_url as an allowed Redirect URI in your Intuit App's Keys & OAuth configuration.

// The site_base_url is is the URL of this Example minus the php file (the directory url)
//$site_base_url = 'https://example.com/pathToExample';

// This is the URL of your OAuth auth handler page (must be entered as a Redirect URI in your app on developer.intuit.com)
//$quickbooks_oauth_url = $site_base_url .'/oauth' . $site_php_extension;

// This is the URL to forward the user to after they have connected to IPP/IDS via OAuth
//$quickbooks_success_url = $site_base_url .'/success' . $site_php_extension;

// This is the menu URL script
//$quickbooks_menu_url = $site_base_url .'/menu' . $site_php_extension;


//**************************************************************************************************************************
//*****
//*****   END BASIC USER CONFIGURATION
//*****
//**************************************************************************************************************************
