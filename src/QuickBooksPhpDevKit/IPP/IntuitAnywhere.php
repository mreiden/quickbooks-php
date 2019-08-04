<?php declare(strict_types=1);

/**
 * QuickBooks PHP DevKit
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 */

namespace QuickBooksPhpDevKit\IPP;

use QuickBooksPhpDevKit\Driver\Factory;
use QuickBooksPhpDevKit\HTTP;
use QuickBooksPhpDevKit\IPP;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\IPP\OAuthv1;
use QuickBooksPhpDevKit\IPP\Service\Customer;
use QuickBooksPhpDevKit\XML;

class IntuitAnywhere
{
	protected $_oauth_version;
	protected $_oauth_scope;

	protected $_sandbox;

	protected $_this_url;
	protected $_that_url;

	protected $_consumer_key;
	protected $_consumer_secret;

	protected $_client_id;
	protected $_client_secret;

	protected $_errnum;
	protected $_errmsg;

	protected $_debug;

	protected $_dsn;
	protected $_driver;

	protected $_crypt;

	protected $_key;

	protected $_last_request;
	protected $_last_response;

	//public const URL_REQUEST_TOKEN = 'https://oauth.intuit.com/oauth/v1/get_request_token';
	//public const URL_ACCESS_TOKEN = 'https://oauth.intuit.com/oauth/v1/get_access_token';
	public const URL_CONNECT_BEGIN = 'https://appcenter.intuit.com/Connect/Begin';
	//public const URL_CONNECT_DISCONNECT = 'https://appcenter.intuit.com/api/v1/Connection/Disconnect';
	//public const URL_CONNECT_RECONNECT = 'https://appcenter.intuit.com/api/v1/Connection/Reconnect';
	//public const URL_APP_MENU = 'https://appcenter.intuit.com/api/v1/Account/AppMenu';

	public const URL_DISCOVERY_SANDBOX = 'https://developer.api.intuit.com/.well-known/openid_sandbox_configuration';
	public const URL_DISCOVERY_PRODUCTION = 'https://developer.api.intuit.com/.well-known/openid_configuration';

	public const EXPIRY_EXPIRED = 'expired';
	public const EXPIRY_NOTYET = 'notyet';
	public const EXPIRY_SOON = 'soon';
	public const EXPIRY_UNKNOWN = 'unknown';

	//public const OAUTH_V1 = 'oauthv1';
	public const OAUTH_V2 = 'oauthv2';

	/**
	 *
	 * @param string $_oauth_version	The OAuth version.  Either IntuitAnywhere::OAUTH_V1 (oauthv1) or Either IntuitAnywhere::OAUTH_V2 (oauthv2)
	 * @param bool   $sandbox			Whether or not to use the sandbox
	 * @param string $consumer_key		The OAuth consumer key Intuit gives you
	 * @param string $consumer_secret	The OAuth consumer secret Intuit gives you
	 * @param string $this_url			The URL of your QuickBooks_IntuitAnywhere class instance
	 * @param string $that_url			The URL the user should be sent to after being authenticated
	 */
	public function __construct(string $IGNORED_oauth_version, bool $sandbox, string $scope, $dsn, string $encryption_key, string $consumer_key_or_client_id, string $consumer_secret_or_client_secret, string $this_url = null, string $that_url = null)
	{
		$this->_dsn = $dsn;
		$this->_driver = Factory::create($dsn);

		$this->_key = trim($encryption_key);

		$this->_this_url = trim($this_url);
		$this->_that_url = trim($that_url);

		$this->_oauth_version = IPP::AUTHMODE_OAUTHV2;
		$this->_oauth_scope = trim($scope);

		$this->_sandbox = $sandbox;

		$this->_client_id = trim($consumer_key_or_client_id);
		$this->_client_secret = trim($consumer_secret_or_client_secret);

		$this->_debug = false;
	}

	/**
	 * Turn on/off debug mode
	 */
	public function useDebugMode(bool $true_or_false): void
	{
		$this->_debug = $true_or_false;
	}

	/**
	 * Get the last error number
	 */
	public function errorNumber(): ?int
	{
		return $this->_errnum;
	}

	/**
	 * Get the last error message
	 */
	public function errorMessage(): ?string
	{
		return $this->_errmsg;
	}

	/**
	 * Set an error message
	 *
	 * @param integer $errnum	The error number/code
	 * @param string $errmsg	The text error message
	 */
	protected function _setError(int $errnum, string $errmsg = ''): void
	{
		$this->_errnum = $errnum;
		$this->_errmsg = $errmsg;
	}

	public function lastRequest(): ?string
	{
		return $this->_last_request;
	}

	public function lastResponse(): ?string
	{
		return $this->_last_response;
	}

	/**
	 * Returns TRUE if an OAuth token exists for this user, FALSE otherwise
	 *
	 * @param   string  $app_tenant   The tenant to check to see if they are connected/auth'd
	 */
	public function check(string $app_tenant): bool
	{
		$arr = $this->load($app_tenant);

		return false !== $arr;
	}

	/**
	 * Test to see if a connection actually works (make sure you haven't been disconnected on Intuit's end)
	 */
	public function test(string $app_tenant): bool
	{
		$creds = $this->load($app_tenant);
		if ($creds)
		{
			$IPP = new IPP($this->_dsn, $this->_key);
			$IPP->authMode(IPP::AUTHMODE_OAUTHV2, $creds);

			if ($this->_sandbox)
			{
				$IPP->sandbox(true);
			}

			$Context = $IPP->context();
			if ($Context)
			{
				// Set the IPP flavor
				$IPP->flavor($creds['qb_flavor']);

				// Get the base URL if it's QBO
				if ($creds['qb_flavor'] == IDS::FLAVOR_ONLINE)
				{
					$cur_version = $IPP->version();

					$IPP->version(IDS::VERSION_3);		// Need v3 for this
					$CustomerService = new Customer();
					$customers = $CustomerService->query($Context, $creds['qb_realm'], "SELECT * FROM Customer MAXRESULTS 1");

					$IPP->version($cur_version);		// Revert back to whatever they set
				}
				else
				{
					$companies = $IPP->getAvailableCompanies($Context);
				}

				// Check the last error code now...
				if ($IPP->errorCode() == 401 || 			// most calls return this
					$IPP->errorCode() == 3100 ||            // OAuth token error
					$IPP->errorCode() == 3200)				// but for some stupid reason the getAvailableCompanies call returns this
				{
					return false;
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Load OAuth credentials from the database
	 */
	public function load(string $app_tenant): ?array
	{
		$arr = $this->_driver->oauthLoadV2($this->_key, $app_tenant);
		if ($arr &&
			!empty($arr['oauth_access_token']) &&
			!empty($arr['oauth_refresh_token']))
		{
			$arr['oauth_client_id'] = $this->_client_id;
			$arr['oauth_client_secret'] = $this->_client_secret;

			$arr['qb_flavor'] = IDS::FLAVOR_ONLINE;

			return $arr;
		}

		return null;
	}

	/**
	 * Check whether a connection is due for refresh/reconnect
	 *
	 * @param string $app_username
	 * @param string $app_tenant
	 * @param integer $within
	 * @return One of the static::EXPIRY_* constants
	 */
	public function expiry(string $app_username, string $app_tenant, int $within = 2592000): string
	{
		if ($this->_oauth_version == self::OAUTH_V2)
		{
			return $this->expiryV2($app_tenant);
		}

		// OAuthv1
		$lifetime = 15552000;

		if ($arr = $this->_driver->oauthLoad($this->_key, $app_username, $app_tenant) &&
			!empty($arr['oauth_access_token']) &&
			!empty($arr['oauth_access_token_secret']))
		{
			$expires = $lifetime + strtotime($arr['access_datetime']);

			$diff = $expires - time();

			if ($diff < 0)
			{
				// Already expired
				return static::EXPIRY_EXPIRED;
			}
			else if ($diff < $within)
			{
				return static::EXPIRY_SOON;
			}

			return static::EXPIRY_NOTYET;
		}

		return static::EXPIRY_UNKNOWN;
	}

	public function expiryV2(string $app_tenant, int $within = 2592000): string
	{
		$app_tenant = (string) $app_tenant;

		$creds = $this->load($app_tenant);
		if (null === $creds || empty($creds['oauth_access_token']) || empty($creds['oauth_access_expiry']))
		{
			return static::EXPIRY_UNKNOWN;
		}

		$expires = strtotime($creds['oauth_access_expiry']);
		if (false === $expires)
		{
			return static::EXPIRY_UNKNOWN;
		}

		$diff = $expires - time();
		if ($diff < 0)
		{
			// Already expired
			return static::EXPIRY_EXPIRED;
		}
		else if ($diff < $within)
		{
			return static::EXPIRY_SOON;
		}

		return static::EXPIRY_NOTYET;
	}


	/**
	 * Reconnect/refresh the OAuth tokens
	 *
	 * For this to succeed, the token expiration must be within 30 days of the
	 * date that this method is called (6 months after original token was
	 * created). This is an Intuit-imposed security restriction. Calls outside
	 * of that date range will fail with an error.
	 */
	public function reconnect(string $app_username, string $app_tenant): ?bool
	{
		if ($this->_oauth_version == self::OAUTH_V2)
		{
			return $this->reconnectV2($app_tenant);
		}


		// OAuthv1 (This should be removed)
		if ($arr = $this->_driver->oauthLoad($this->_key, $app_username, $app_tenant) &&
			strlen($arr['oauth_access_token']) > 0 &&
			strlen($arr['oauth_access_token_secret']) > 0)
		{
			$arr['oauth_consumer_key'] = $this->_consumer_key;
			$arr['oauth_consumer_secret'] = $this->_consumer_secret;

			$retr = $this->_request(OAuthv1::METHOD_GET,
				static::URL_CONNECT_RECONNECT,
				[],
				$arr['oauth_access_token'],
				$arr['oauth_access_token_secret']);

			// Extract the error code
			$code = (int) XML::extractTagContents('ErrorCode', $retr);
			$message = XML::extractTagContents('ErrorMessage', $retr);

			if ($message)
			{
				$this->_setError($code, $message);

				return false;
			}
			else
			{
				// Success! Update the tokens
				$token = XML::extractTagContents('OAuthToken', $retr);
				$secret = XML::extractTagContents('OAuthTokenSecret', $retr);

				$this->_driver->oauthAccessWrite(
					$this->_key,
					$arr['oauth_request_token'],
					$token,
					$secret,
					null,
					null);

				return true;
			}
		}

		return null;
	}

	public function reconnectV2($app_tenant): bool
	{
		$app_tenant = (string) $app_tenant;

		$creds = $this->load($app_tenant);
		if (null === $creds || empty($creds['oauth_refresh_token']))
		{
			// A Refresh Token is Mandatory
			return false;
		}

		$discover = $this->_discover();
		if ((null !== $discover) && !empty($discover['token_endpoint']))
		{
			$ch = curl_init($discover['token_endpoint']);
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => false,  // Do not follow; security risk here

				CURLOPT_USERPWD => $this->_client_id . ':' . $this->_client_secret,
				CURLOPT_HTTPHEADER => [
					'Accept: application/json',
				],
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query([
					'grant_type' => 'refresh_token',
					'refresh_token' => $creds['oauth_refresh_token'],
				]),

				//CURLINFO_HEADER_OUT => true,
			]);
			$retr = curl_exec($ch);
			$info = curl_getinfo($ch);

			if ($info['http_code'] == 200)
			{
				$json = json_decode($retr, true);
				if (!empty($json['access_token']))
				{
					$this->_driver->oauthAccessRefreshV2(
						$this->_key,
						(int) $creds['quickbooks_oauthv2_id'],
						$json['access_token'],
						$json['refresh_token'],
						date('Y-m-d H:i:s', time() + (int) $json['expires_in']),
						date('Y-m-d H:i:s', time() + (int) $json['x_refresh_token_expires_in'])
					);
				}

				return true;
			}
		}

		return false;
	}

	public function disconnect(string $UNUSED_INVALID_IN_OAUTHV2_app_username, string $app_tenant, bool $force = false): bool
	{
		if ($this->_oauth_version == self::OAUTH_V2)
		{
			$creds = $this->load($app_tenant);
			if (null === $creds || empty($creds['oauth_refresh_token']))
			{
				// Already disconnected
				return true;
			}

			$discover = $this->_discover();
			if (null !== $discover)
			{
				$ch = curl_init($discover['revocation_endpoint']);
				curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FOLLOWLOCATION => false,  // Do not follow; security risk here

					CURLOPT_USERPWD => $this->_client_id . ':' . $this->_client_secret,
					CURLOPT_HTTPHEADER => [
						'Accept: application/json',
						'Content-Type: application/json',
					],
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => json_encode([
						'token' => $creds['oauth_refresh_token'],
					]),

					//CURLOPT_HEADER => true,
					//CURLINFO_HEADER_OUT => true,
					//CURLOPT_VERBOSE => true,
				]);
				$retr = curl_exec($ch);
				$info = curl_getinfo($ch);

				if ($force === true || in_array($info['http_code'], [200, 400]))
				{
					// 200: Token was found and disconnected successfully
					// 400: Token already disabled or invalid (anyway, it is no longer able to be used)
					$this->_driver->oauthAccessDelete('UNUSED_INVALID_IN_OAUTHV2_app_username', $app_tenant);

					return true;
				}
			}
		}

		throw new \Exception('Cannot disconnect using '. $this->_oauth_version);

		return false;
	}

	public function fudge(string $request_token, string $access_token, string $access_token_secret, $realm, $flavor): void
	{
		$this->_driver->oauthAccessWrite(
			$this->_key,
			$request_token,
			$access_token,
			$access_token_secret,
			$realm,
			$flavor);
	}

	/**
	 * Handle an OAuth request login thing
	 *
	 *
	 */
	public function handle(string $app_tenant): bool
	{
		if ($this->check($app_tenant) && 		// We have tokens ...
			$this->test($app_tenant))			// ... and they are valid
		{
			// They are already logged in, send them on to exchange data
			header('Location: ' . $this->_that_url);
			exit;
		}
		else
		{
			error_log(print_r($_REQUEST, true));

			if ($this->_oauth_version == self::OAUTH_V2 &&
				!empty($_GET['code']) &&
				!empty($_GET['state']) &&
				($info = $this->_driver->oauthRequestResolveV2($_GET['state'])))
			{
				// Try to get an access/refresh token here
				$discover = $this->_discover();
				if ($discover)
				{
					$ch = curl_init($discover['token_endpoint']);

					curl_setopt_array($ch, [
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_FOLLOWLOCATION => false,  // Do not follow; security risk here

						CURLOPT_USERPWD => $this->_client_id . ':' . $this->_client_secret,
						CURLOPT_POST => true,
						CURLOPT_POSTFIELDS => http_build_query([
							'code' => $_GET['code'],
							'redirect_uri' => $this->_this_url,
							'grant_type' => 'authorization_code',
						]),
					]);

					$retr = curl_exec($ch);
					$info = curl_getinfo($ch);

					if ($info['http_code'] == 200)
					{
						$json = json_decode($retr, true);

						$this->_driver->oauthAccessWriteV2(
							$this->_key,
							$_GET['state'],
							$json['access_token'],
							$json['refresh_token'],
							date('Y-m-d H:i:s', time() + (int) $json['expires_in']),
							date('Y-m-d H:i:s', time() + (int) $json['x_refresh_token_expires_in']),
							$_GET['realmId']);

						header('Location: ' . $this->_that_url);
						exit;
					}
					else
					{
						print('An error occurred fetching the access/refresh token.');
						return false;
					}
				}
			}
			else
			{
				if ($this->_oauth_version == self::OAUTH_V2)
				{
					$auth_url = $this->_getAuthenticateURLV2($app_tenant, $this->_this_url);
				}

				if (!$auth_url)
				{
					print('Could not build an authorization URL.');
					return false;
				}

				// Forward them to the auth page
				header('Location: ' . $auth_url);
				exit;
			}
		}

		return true;
	}

	public function getAuthenticateUrl(string $app_tenant, string $returnUrl): ?string
	{
		return $this->_getAuthenticateURLV2($app_tenant, $returnUrl);
	}

	protected function _getAuthenticateURLV2(string $app_tenant, string $url): ?string
	{
		$discover = $this->_discover();
		if (null !== $discover)
		{
			// Write the request to the database
			$state = md5(mt_rand() . microtime(true));

			$this->_driver->oauthRequestWriteV2($app_tenant, $state);

			$url = $discover['authorization_endpoint'] . '?' . http_build_query([
				'client_id' => $this->_client_id,
				'scope' => $this->_oauth_scope,
				'redirect_uri' => $url,
				'response_type' => 'code',
				'state' => $state,
			]);

			return $url;
		}

		return null;
	}

	protected function _discover(): ?array
	{
		return self::discover($this->_sandbox);
	}

	static public function discover(bool $sandbox): ?array
	{
		$url = $sandbox ? self::URL_DISCOVERY_SANDBOX : self::URL_DISCOVERY_PRODUCTION;

		// Make a request to the discovery URL
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);   // Do not follow; security risk here
		$retr = curl_exec($ch);
		$info = curl_getinfo($ch);

		if ($info['http_code'] == 200)
		{
			return json_decode($retr, true);
		}

		return null;
	}

	/**
	 *
	 *
	 * @param string $url
	 * @return string
	 * /
	protected function _getAuthenticateURLV1(string $app_tenant, string $url): string
	{
		// Fetch a request token from the OAuth service
		$info = $this->_request(OAuthv1::METHOD_GET, static::URL_REQUEST_TOKEN, array( 'oauth_callback' => $url ));

		$vars = [];
		parse_str($info, $vars);

		// Write the request tokens to the database
		$this->_driver->oauthRequestWriteV1($app_tenant, $vars['oauth_token'], $vars['oauth_token_secret']);

		// Return the auth URL
		return static::URL_CONNECT_BEGIN . '?oauth_callback=' . urlencode($url) . '&oauth_consumer_key=' . $this->_consumer_key . '&oauth_token=' . $vars['oauth_token'];
	}

	protected function _getAccessToken(string $oauth_token, string $oauth_token_secret, $verifier)
	{
		$str = $this->_request(OAuthv1::METHOD_GET, static::URL_ACCESS_TOKEN, [
			'oauth_token' => $oauth_token,
			'oauth_secret' => $oauth_token_secret,
			'oauth_verifier' => $verifier,
		]);

		if ($str)
		{
			$info = [];
			parse_str($str, $info);

			return $info;
		}

		return false;
	}

	public function widgetConnect(): void
	{

	}
	*/

	/**
	 * This function returns the html for displaying the "Blue Dot" menu
	 *
	 * As per Intuit's recommendation, your app should call this function before the user clicks the
	 * blue dot menu and cache it. This will improve the user's experience and prevent unnecessary API
	 * calls to Intuit's web service. See:
	 * https://ipp.developer.intuit.com/0010_Intuit_Partner_Platform/0025_Intuit_Anywhere/1000_Getting_Started_With_IA/0500_Add_IA_Widgets/3000_Blue_Dot_Menu/Menu_Proxy_Code
	 *
	 * Example usage:
	 *     // Your app should read from cache here if possible before calling widgetMenu()
	 *     $html = $object->widgetMenu($app_username, $app_tenant);
	 *     if (strlen($html)) {
	 *         // Your app should write to cache here if possible
	 *         print $html;
	 *         exit;
	 *     }
	 *
	 * @param string $app_username
	 * @param string $app_tenant
	 * @return html string
	 * /
	public function widgetMenu(string $app_username, string $app_tenant): ?string
	{
		$token = null;
		$secret = null;

		$creds = $this->load($app_username, $app_tenant);
		if (null !== $creds)
		{
			return $this->_request(
				OAuthv1::METHOD_GET,
				static::URL_APP_MENU,
				[],
				$creds['oauth_access_token'],
				$creds['oauth_access_token_secret']);
		}

		return null;
	}

	protected function _request(string $method, string $url, array $params = [], ?string $token = null, ?string $secret = null, $data = null)
	{
		$OAuth = new OAuthv1($this->_consumer_key, $this->_consumer_secret);

		// This returns a signed request
		//
		// 0 => signature base string
		// 1 => signature
		// 2 => normalized url
		// 3 => header string
		$signed = $OAuth->sign($method, $url, $token, $secret, $params);

		//print_r($signed);

		// Create the new HTTP object
		//$HTTP = new HTTP($url);
		$HTTP = new HTTP($signed[2]);

		$headers = [
			//'Authorization' => $signed[3],
		];

		$HTTP->setHeaders($headers);

		//
		$HTTP->setRawBody($data);

		// We need the headers back
		//$HTTP->returnHeaders(true);

		// Send the request
		$return = $HTTP->GET();

		$errnum = $HTTP->errorNumber();
		$errmsg = $HTTP->errorMessage();

		$this->_last_request = $HTTP->lastRequest();
		$this->_last_response = $HTTP->lastResponse();

		if ($errnum)
		{
			// An error occurred!
			$this->_setError(IPP::ERROR_HTTP, $errnum . ': ' . $errmsg);

			return false;
		}

		// Everything is good, return the data!
		$this->_setError(IPP::ERROR_OK, '');

		return $return;
	}
	*/
}
