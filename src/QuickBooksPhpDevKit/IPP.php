<?php declare(strict_types=1);

/**
 * QuickBooks IPP class for communicating with the Intuit Partner Platform
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @license LICENSE.txt
 * @author Keith Palmer <Keith@ConsoliBYTE.com>
 *
 * @package QuickBooks
 * @subpackage IPP
 */

namespace QuickBooksPhpDevKit;

use QuickBooksPhpDevKit\{
	HTTP,					// HTTP request class
	Driver\Factory,			// SQL Driver Factory
	IPP\BaseObject,			// Base IPP Object
	IPP\Context,			// Context element (holds application information)
	IPP\Federator,			// SAML federation of applications
	IPP\IDS,				// IDS (Intuit Data Services) base class
	IPP\IntuitAnywhere,		// IntuitAnywhere widgets
	IPP\OAuth,				// OAuth
	IPP\Parser,				// IPP XML parser
	IPP\Service,			// IDS base service class
	PackageInfo,			// Project constants and configuration
	Utilities,				// Project Utilities
	XML,					// XML parser
};

/**
 *
 *
 *
 */
class IPP
{
	public const API_ADDRECORD = 'API_AddRecord';

	public const API_GETBILLINGSTATUS = 'API_GetBillingStatus';

	/**
	 * This is not a real API call!
	 */
	public const API_GETBASEURL = '_getBaseURL_';

	public const API_GETDBINFO = 'API_GetDBInfo';

	public const API_GETDBVAR = 'API_GetDBVar';

	public const API_GETUSERINFO = 'API_GetUserInfo';

	public const API_GETUSERROLE = 'API_GetUserRole';

	public const API_GETSCHEMA = 'API_GetSchema';

	public const API_SETDBVAR = 'API_SetDBVar';

	public const API_GETISREALMQBO = 'API_GetIsRealmQBO';

	public const API_GETIDSREALM = 'API_GetIDSRealm';

	public const API_ATTACHIDSREALM = 'API_AttachIDSRealm';

	public const API_DETACHIDSREALM = 'API_DetachIDSRealm';

	public const API_RENAMEAPP = 'API_RenameApp';

	public const API_ASSERTFEDERATEDIDENTITY = 'API_AssertFederatedIdentity';

	public const API_GETENTITLEMENTVALUES = 'API_GetEntitlementValues';

	public const API_GETENTITLEMENTVALUESANDUSERROLE = 'API_GetEntitlementValuesAndUserRole';

	public const AUTHMODE_FEDERATED = 'federated';
	public const AUTHMODE_OAUTHV1 = 'oauthv1';
	public const AUTHMODE_OAUTHV2 = 'oauthv2';

	/**
	 *
	 * @var unknown_type
	 */
	public const COOKIE = 'ippfedcookie';

	/**
	 *
	 * @var string
	 */
	public const REQUEST_IPP = 'ipp';

	/**
	 *
	 * @var string
	 */
	public const REQUEST_IDS = 'ids';

	/**
	 * No error occurred
	 * @var integer
	 */
	public const OK = PackageInfo::Error['OK'];

	/**
	 * No error occurred
	 * @var integer
	 */
	public const ERROR_OK = PackageInfo::Error['OK'];

	/**
	 * Indicates a generic internal error
	 * @param integer
	 */
	public const ERROR_INTERNAL = -1091;

	/**
	 * Indicates an error when parsing an XML stream
	 * @param integer
	 */
	public const ERROR_XML = -1092;

	/**
	 * Indicates an error establishing a socket connection to QBMS
	 * @param integer
	 */
	public const ERROR_SOCKET = -1093;

	/**
	 * Indicates an error with a parameter passed to QBMS
	 * @param integer
	 */
	public const ERROR_PARAM = -1094;

	/**
	 * Indicates an internal SSL-related error
	 * @param integer
	 */
	public const ERROR_SSL = -1095;

	/**
	 *
	 *
	 */
	public const ERROR_HTTP = -1096;

	protected $_key;

	protected $_username;
	protected $_password;
	protected $_ticket;
	protected $_token;
	protected $_dbid;

	protected $_flavor;
	protected $_baseurl;
	protected $_sandbox;

	protected $_authmode;
	protected $_authuser;
	protected $_authcred;

	/**
	 * Auth signing method (if applicable)
	 * @var string
	 */
	protected $_authsign;

	/**
	 * Auth key (if applicable)
	 * @var string
	 */
	protected $_authkey;

	protected $_debug;

	protected $_last_request;
	protected $_last_response;
	protected $_last_debug;

	protected $_masking;

	protected $_driver;

	protected $_certificate;

	protected $_errcode;
	protected $_errtext;
	protected $_errdetail;

	/**
	 * An array of cookies returned by the deprecated ->authenticate() method
	 * @var array
	 */
	protected $_cookies;

	/**
	 * Whether or not to use the IDS parser and parse XML responses into objects
	 * @var boolean
	 */
	protected $_ids_parser;

	/**
	 * The version of IDS to use
	 * @var string
	 */
	protected $_ids_version;

	public function __construct($dsn, string $encryption_key, array $config = [], $log_level = PackageInfo::LogLevel['NORMAL'])
	{
		// Are we in sandbox mode?
		$this->_sandbox = false;

		// Use debug mode?
		$this->_debug = false;

		// Mask sensitive data in the logs (tickets, credit card numbers, etc.)
		$this->_masking = true;

		// Parse returned IDS responses into objects?
		$this->_ids_parser = true;

		// What version of IDS to use
		$this->_ids_version = IDS::VERSION_LATEST;

		// Driver class for logging
		$this->_driver = null;

		if ($dsn)
		{
			$this->_driver = Factory::create($dsn, $config, [], $log_level);
			$this->_driver->setLogLevel($log_level);
		}

		$this->_cookies = [];

		$this->_certificate = null;

		$this->_errcode = IPP::OK;
		$this->_errtext = '';
		$this->_errdetail = '';

		$this->_last_request = null;
		$this->_last_response = null;
		$this->_last_debug = [];

		$this->_authmode = IPP::AUTHMODE_FEDERATED;
		$this->_authuser = null;
		$this->_authcred = null;

		$this->_authsign = null;
		$this->_authkey = null;

		// Encryption key (used for database storage)
		$this->_key = $encryption_key;
	}

	/**
	 * Create a Context object (used for session management) for a given ticket and token
	 *
	 *
	 */
	public function context(): ?Context
	{
		$Context = new Context($this, null, null);

		return $Context;
	}

	/**
	 *
	 *
	 */
	public function flavor(?string $flavor = null): ?string
	{
		if ($flavor)
		{
			$this->_flavor = $flavor;

			if ($flavor == IDS::FLAVOR_DESKTOP)
			{
				$this->baseURL(IDS::BASEURL_DESKTOP);
			}
		}

		return $this->_flavor;
	}

	public function sandbox(?bool $sandbox = null): bool
	{
		if (!is_null($sandbox))
		{
			$this->_sandbox = $sandbox;
		}

		return $this->_sandbox;
	}

	public function baseURL(?string $baseURL = null): string
	{
		if ($baseURL)
		{
			$this->_baseurl = $baseURL;
		}

		return $this->_baseurl;
	}

	public function authcreds(): ?array
	{
		return $this->_authcred;
	}

	/**
	 * Get the current authmode (if called with no arguments) or set the authorization mode for HTTP requests (Federated, or OAuth)
	 */
	public function authMode(?string $authmode = null, ?array $authcred = null, ?string $authsign = null, ?string $authkey = null): string
	{
		if ($authmode)
		{
			$this->_authmode = $authmode;
			$this->_authcred = $authcred;

			$this->_authsign = $authsign;
			$this->_authkey = $authkey;
		}

		return $this->_authmode;
	}

	/**
	 * Get or set the DBID of the attached federated app
	 */
	public function dbid(?string $dbid = null): string
	{
		if ($dbid)
		{
			$this->_dbid = $dbid;
		}

		return $this->_dbid;
	}

	/**
	 *
	 *
	 *
	 */
	protected function _IPP(Context $Context, string $url, string $action, string $xml, bool $post = true)
	{
		// Ick, special case here...
		$type = IPP::REQUEST_IPP;
		if ($action == IPP::API_GETBASEURL)
		{
			$type = IPP::REQUEST_IDS;
		}

		// Make the HTTP request
		$response = $this->_request($Context, $type, $url, $action, $xml, $post);

		if ($this->_hasErrors($response))
		{
			return false;
		}

		// These methods don't need a parsed response. If we've gotten this far,
		//	then we know there wasn't an API error, and we can just return TRUE
		//	because the request succeeded and there's no real meaningful data
		//	that we need to parse out and return in the response.
		switch ($action)
		{
			case IPP::API_SETDBVAR:
			case IPP::API_ATTACHIDSREALM:
			case IPP::API_DETACHIDSREALM:
			case IPP::API_RENAMEAPP:
				return true;
		}

		// Remove HTTP headers from response
		$data = $this->_stripHTTPHeaders($response);

		$xml_errnum = null;
		$xml_errmsg = null;
		$err_code = null;
		$err_desc = null;
		$err_db = null;

		$Parser = $this->_parserInstance();

		// Try to parse the response from IPP
		$parsed = $Parser->parseIPP($data, $action, $xml_errnum, $xml_errmsg, $err_code, $err_desc, $err_db);

		/*
		print('parsed out: [');
		print_r($parsed);
		print(']');
		*/

		//$this->_setLastDebug(__CLASS__, array( 'ipp_parser_duration' => microtime(true) - $start ));

		if ($xml_errnum != XML::ERROR_OK)
		{
			// Error parsing the returned XML?
			$this->_setError(IPP::ERROR_XML, 'XML parser said: ' . $xml_errnum . ': ' . $xml_errmsg);

			return false;
		}
		else if ($err_code != IPP::ERROR_OK)
		{
			// Some other IPP error
			$this->_setError($err_code, $err_desc, 'Database error code: ' . $err_db);

			return false;
		}

		return $parsed;
	}

	public function getBaseURL(Context $Context, string $realmID): string
	{
		/*
		$url = 'https://qbo.intuit.com/qbo1/rest/user/v2/' . $realmID;
		$action = IPP::API_GETBASEURL;
		$xml = null;

		$post = false;
		return $this->_IPP($Context, $url, $action, $xml, $post);
		*/

		return IDS::URL_V3;
	}

	public function getIsRealmQBO(Context $Context)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_GETISREALMQBO;

		$xml = '<qdbapi>
				<ticket>' . $Context->ticket() . '</ticket>
   				<apptoken>' . $Context->token() . '</apptoken>
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function assertFederatedIdentity(Context $Context, string $provider, string $target_url, $udata = null)
	{
		$url = 'https://workplace.intuit.com/db/main';
		$action = IPP::API_ASSERTFEDERATEDIDENTITY;

		$xml = '<qdbapi>
				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>
				<serviceProviderID>' . htmlspecialchars($provider) . '</serviceProviderID>
				<targetURL>' . htmlspecialchars($target_url, ENT_QUOTES) . '</targetURL>
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function renameApp(Context $Context, string $name)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_RENAMEAPP;

		$xml = '<qdbapi>
				<ticket>' . $Context->ticket() . '</ticket>
   				<apptoken>' . $Context->token() . '</apptoken>
   				<newappname>' . htmlspecialchars($name) . '</newappname>
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function getIDSRealm(Context $Context)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_GETIDSREALM;

		$xml = '<qdbapi>
   				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function getAvailableCompanies(Context $Context)
	{
		$url = 'https://services.intuit.com/sb/company/' . $this->_ids_version . '/available';
		$action = null;
		$xml = null;

		$response = $this->_request($Context, IPP::REQUEST_IDS, $url, $action, $xml);

		if ($this->_hasErrors($response))
		{
			return false;
		}

		// @todo Parse and return an object?
		return $response;
	}

	/*
	public function getEntitlementValues(Context $Context)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_GETENTITLEMENTVALUES;

		$xml = '<qdbapi>
			<ticket>' . $Context->ticket() . '</ticket>
			<apptoken>' . $Context->token() . '</apptoken>
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function getEntitlementValuesAndUserRole(Context $Context)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_GETENTITLEMENTVALUESANDUSERROLE;

		$xml = '<qdbapi>
			<ticket>' . $Context->ticket() . '</ticket>
			<apptoken>' . $Context->token() . '</apptoken>
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}
	*/

	public function provisionUser(Context $Context, string $email, string $fname, string $lname, $roleid = null, $udata = null)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = 'API_ProvisionUser';

		$xml = '<qdbapi>
					<ticket>' . $Context->ticket() . '</ticket>
					<apptoken>' . $Context->token() . '</apptoken>';

		if ($roleid)
		{
			$xml .= '<roleid>' . $roleid . '</roleid>';
		}

		$xml .= '
				<email>' . $email . '</email>
				<fname>' . $fname . '</fname>
				<lname>' . $lname . '</lname>';

		if ($udata)
		{
			$xml .= '<udata>' . $udata . '</udata>';
		}

		$xml .= '
			</qdbapi>';

		$response = $this->_request($Context, IPP::REQUEST_IPP, $url, $action, $xml);

		if ($this->_hasErrors($response))
		{
			return false;
		}

		return true;
	}

	public function getUserRoles(Context $Context, string $userid, $udata = null)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_GETUSERROLE;
		$xml = '<qdbapi>
				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>
				<userid>' . htmlspecialchars($userid) . '</userid>';

		if ($udata)
		{
			$xml .= '<udata>' . $udata . '</udata>';
		}

		$xml .= '
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function getUserInfo(Context $Context, ?string $email = null, ?string $udata = null)
	{
		$url = 'https://workplace.intuit.com/db/main';
		$action = IPP::API_GETUSERINFO;
		$xml = '<qdbapi>
   				<ticket>' . $Context->ticket() . '</ticket>
   				<apptoken>' . $Context->token() . '</apptoken>';

		if ($email)
		{
			$xml .= '<email>' . htmlspecialchars($email) . '</email>';
		}

		if ($udata)
		{
			$xml .= '<udata>' . htmlspecialchars($udata) . '</udata>';
		}

		$xml .= '
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function sendInvitation(Context $Context, string $userid, string $usertext, $udata = null)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = 'API_SendInvitation';
		$xml = '<qdbapi>
				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>
				<userid>' . htmlspecialchars($userid) . '</userid>
				<usertext>' . htmlspecialchars($usertext) . '</usertext>';

		if ($udata)
		{
			$xml .= '<udata>' . $udata . '</udata>';
		}

		$xml .= '
			</qdbapi>';

		$response = $this->_request($Context, IPP::REQUEST_IPP, $url, $action, $xml);

		if ($this->_hasErrors($response))
		{
			return false;
		}

		return true;
	}

	public function getDBInfo(Context $Context, $udata = null)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_GETDBINFO;
		$xml = '<qdbapi>
				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>';

		if ($udata)
		{
			$xml .= '<udata>' . $udata . '</udata>';
		}

		$xml .= '
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function setDBVar(Context $Context, string $varname, $value, $udata = null)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_SETDBVAR;
		$xml = '<qdbapi>
				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>
				<varname>' . XML::encode($varname) . '</varname>
				<value>' . XML::encode($value) . '</value>';

		if ($udata)
		{
			$xml .= '<udata>' . $udata . '</udata>';
		}

		$xml .= '
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function getDBVar(Context $Context, string $varname, $udata = null)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_GETDBVAR;
		$xml = '<qdbapi>
				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>
				<varname>' . XML::encode($varname) . '</varname>';

		if ($udata)
		{
			$xml .= '<udata>' . $udata . '</udata>';
		}

		$xml .= '
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function createTable(Context $Context, $tname, $pnoun, $udata = null)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = 'API_CreateTable';
		$xml = '<qdbapi>
				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>
				<tname>' . $tname . '</tname>
				<pnoun>' . $pnoun . '</pnoun>';

		if ($udata)
		{
			$xml .= '<udata>' . $udata . '</udata>';
		}

		$xml .= '
			</qdbapi>';

		$response = $this->_request($Context, IPP::REQUEST_IPP, $url, $action, $xml);

		if ($this->_hasErrors($response))
		{
			return false;
		}

		return true;
	}

	public function attachIDSRealm(Context $Context, string $realm)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_ATTACHIDSREALM;
		$xml = '<qdbapi>
				<realm>' . $realm . '</realm>
				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	public function detachIDSRealm(Context $Context, string $realm)
	{
		$url = 'https://workplace.intuit.com/db/' . $this->_dbid;
		$action = IPP::API_DETACHIDSREALM;
		$xml = '<qdbapi>
				<realm>' . $realm . '</realm>
				<ticket>' . $Context->ticket() . '</ticket>
				<apptoken>' . $Context->token() . '</apptoken>
			</qdbapi>';

		return $this->_IPP($Context, $url, $action, $xml);
	}

	/**
	 *
	 *
	 *
	 * @param boolean $true_or_false
	 * @return boolean
	 */
	public function useIDSParser($true_or_false)
	{
		$this->_ids_parser = (boolean) $true_or_false;
		return $this->_ids_parser;
	}

	/**
	 * Get or set the IDS version to use
	 *
	 * @param string $version		One of the IDS::VERSION_* constants
	 * @return string				The IDS version currently being used
	 */
	public function version($version = null)
	{
		if ($version)
		{
			if (in_array($version, [IDS::VERSION_1, IDS::VERSION_2]))
			{
				throw new \Exception('IDS version "'. $version .'" is unsupported and non-functional.  You should use "'. IDS::VERSION_LATEST .'" instead.');
			}
			$this->_ids_version = $version;
		}

		return $this->_ids_version;
	}

	/**
	 * Do we need to renew the OAuth access token? If so, renew it
	 */
	protected function _handleRenewal(): bool
	{
		static $attempted_renew = false;

		if (!$attempted_renew &&
			is_object($this->_driver) &&
			$this->_authmode == IPP::AUTHMODE_OAUTHV2 &&
			strtotime($this->_authcred['oauth_access_expiry']) + 60 < time())
		{
			$attempted_renew = true;

			$discover = IntuitAnywhere::discover($this->_sandbox);
			if ($discover)
			{
				$ch = curl_init($discover['token_endpoint']);
				curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FOLLOWLOCATION => false,   // Do not follow; security risk here
					CURLOPT_USERPWD => $this->_authcred['oauth_client_id'] . ':' . $this->_authcred['oauth_client_secret'],
					CURLOPT_POSTFIELDS => http_build_query([
						'grant_type' => 'refresh_token',
						'refresh_token' => $this->_authcred['oauth_refresh_token'],
					]),
				]);
				$retr = curl_exec($ch);
				$info = curl_getinfo($ch);

				if ($info['http_code'] == 200)
				{
					$json = json_decode($retr, true);

					$this->_driver->oauthAccessRefreshV2(
						$this->_key,
						$this->_authcred['quickbooks_oauthv2_id'],
						$json['access_token'],
						$json['refresh_token'],
						date('Y-m-d H:i:s', time() + (int) $json['expires_in']),
						date('Y-m-d H:i:s', time() + (int) $json['x_refresh_token_expires_in']));

					// Replace our auth creds with the new ones
					$this->_authcred = $this->_driver->oauthLoadV2($this->_key, $this->_authcred['app_tenant']);

					// Successfully renewed!
					return true;
				}
			}

			return false;
		}

		// No renewal needed
		return true;
	}

	/**
	 * Make an IDS request (Intuit Data Services) to the remote server
	 *
	 * @param QuickBooks_IPP_Context $Context		The context (token and ticket) to use
	 * @param integer $realmID						The realm to query against
	 * @param string|null $resource					A QuickBooks_IDS::RESOURCE_* constant (or null for Query optype)
	 * @param string $optype
	 * @param string|null|array $xml						The XML for the request or null if there is none
	 * @return QuickBooks_IPP_Object|string|bool
	 */
	public function IDS(Context $Context, string $realm, ?string $resource, string $optype, $xml = '', $ID = null)
	{
		$IPP = $Context->IPP();

		// Do any renewals we need to do first
		$this->_handleRenewal();

		switch ($IPP->version())
		{
			case IDS::VERSION_3:
			default:
				return $this->_IDS_v3($Context, $realm, $resource, $optype, $xml, $ID);
		}

		return null;
	}

	protected function _IDS_v3(Context $Context, string $realm, ?string $resource, string $optype, $xml_or_query, $ID)
	{
		// All v3 URLs have the same baseURL
		$this->baseURL(IDS::URL_V3);

		// If we're in sandbox mode, use the sandbox URL instead
		if ($this->sandbox())
		{
			$this->baseURL(IDS::URL_V3_SANDBOX);
		}

		$post = false;
		$xml = null;
		$query = null;

		$guid = Utilities::GUID();

		if ($optype == IDS::OPTYPE_ADD or $optype == IDS::OPTYPE_MOD)
		{
			$post = true;
			$url = $this->baseURL() . '/company/' . $realm . '/' . strtolower($resource) . '?requestid=' . $guid . '&minorversion=6';
			$xml = $xml_or_query;
		}
		else if ($optype == IDS::OPTYPE_QUERY)
		{
			$post = false;
			$url = $this->baseURL() . '/company/' . $realm . '/query?query=' . $xml_or_query . '&requestid=' . $guid . '&minorversion=6';
		}
		else if ($optype == IDS::OPTYPE_CDC)
		{
			$post = false;
			$url = $this->baseURL() . '/company/' . $realm . '/cdc?entities=' . implode(',', $xml_or_query[0]) . '&changedSince=' . $xml_or_query[1] . '&minorversion=6';
		}
		else if ($optype == IDS::OPTYPE_ENTITLEMENTS)
		{
			$post = false;
			$url = 'https://qbo.sbfinance.intuit.com/manage/entitlements/v3/' . $realm;
		}
		else if ($optype == IDS::OPTYPE_DELETE)
		{
			$post = true;
			$url = $this->baseURL() . '/company/' . $realm . '/' . strtolower($resource) . '?operation=delete&requestid=' . $guid . '&minorversion=6';
			$xml = $xml_or_query;
		}
		else if ($optype == IDS::OPTYPE_VOID)
		{
			$qs = '?operation=void&requestid=' . $guid . '&minorversion=6';        // Used for invoices...

			if ($resource == IDS::RESOURCE_PAYMENT)    // ... and something different used for payments *sigh*
			{
				$qs = '?operation=update&include=void&requestid=' . $guid . '&minorversion=6';
			}

			$post = true;
			$url = $this->baseURL() . '/company/' . $realm . '/' . strtolower($resource) . $qs;
			$xml = $xml_or_query;
		}
		else if ($optype == IDS::OPTYPE_PDF)
		{
			$post = false;
			$url = $this->baseURL() . '/company/' . $realm . '/' . strtolower($resource) . '/' . $ID . '/pdf?requestid=' . $guid . '&minorversion=6';
		}
		else if ($optype == IDS::OPTYPE_DOWNLOAD)
		{
			$post = false;
			$url = $this->baseURL() . '/company/' . $realm . '/' . strtolower($resource) . '/' . $ID;
		}
		else if ($optype == IDS::OPTYPE_SEND)
		{
			$post = true;
			$url = $this->baseURL() . '/company/' . $realm . '/' . strtolower($resource) . '/' . $ID . '/send?requestid=' . $guid . '&minorversion=6';
		}

		$response = $this->_request($Context, IPP::REQUEST_IDS, $url, $optype, $xml, $post);

		// print('URL is [' . $url . ']');
		//die('RESPONSE IS [' . $response . ']');

		// Check for generic IPP errors and HTTP errors
		if ($this->_hasErrors($response))
		{
			return false;
		}

		$data = $this->_stripHTTPHeaders($response);

		if (!$this->_ids_parser)
		{
			// If they don't want the responses parsed into objects, then just return the raw XML data
			return $data;
		}

		$start = microtime(true);

		$Parser = $this->_parserInstance();

		$xml_errnum = null;
		$xml_errmsg = null;
		$err_code = null;
		$err_desc = null;
		$err_db = null;

		// Try to parse the responses into QuickBooks_IPP_Object_* classes
		$parsed = $Parser->parseIDS($data, $optype, $this->flavor(), IDS::VERSION_3, $xml_errnum, $xml_errmsg, $err_code, $err_desc, $err_db);

		$this->_setLastDebug(__CLASS__, array( 'ids_parser_duration' => microtime(true) - $start ));

		if ($xml_errnum != XML::ERROR_OK)
		{
			// Error parsing the returned XML?
			$this->_setError(IPP::ERROR_XML, 'XML parser said: ' . $xml_errnum . ': ' . $xml_errmsg);

			return false;
		}
		else if ($err_code != IPP::ERROR_OK)
		{
			// Some other IPP error
			$this->_setError($err_code, $err_desc, 'Database error code: ' . $err_db);

			return false;
		}

		// Return the parsed response
		return $parsed;
	}

	/**
	 * Strips HTTP Headers from response
	 */
	protected function _stripHTTPHeaders(string $response): string
	{
		$pos = strpos($response, "\r\n\r\n");

		// @todo Error checking, what if \r\n\r\n isn't present?
		$stripped = substr($response, $pos + 4);

		// To handle "HTTP/1.1 100 Continue\r\n\r\nHTTP/1.1 200 OK\r\n .... " responses
		if (substr($stripped, 0, 8) == 'HTTP/1.1')
		{
			return $this->_stripHTTPHeaders($stripped);
		}

		return $stripped;
	}

	protected function _parserInstance(): Parser
	{
		static $Parser = null;
		if (is_null($Parser))
		{
			$Parser = new Parser();
		}

		return $Parser;
	}

	/**
	 *
	 */
	protected function _hasErrors(string $response): bool
	{
		// @todo This should first check for HTTP errors
		// ...

		// v3 errors
		if (false !== strpos($response, '<Error'))
		{
			$errcode = XML::extractTagAttribute('code', $response);
			$errtext = XML::extractTagContents('Message', $response);
			$errdetail = XML::extractTagContents('Detail', $response);

			$this->_setError($errcode, $errtext, $errdetail);

			return true;		// Yes, there's an error!
		}
		else if (false !== strpos($response, '<title>504 Gateway Time-out'))
		{
			// QBO v3 sometimes blows up with a 504 gateway error, catch these!

			$errcode = PackageInfo::Error['INTERNAL'];
			$errtext = '504 Gateway Time-out';
			$errdetail = 'A service call to the QuickBooks Online gateway has timed out and returned a 504 HTTP error.';

			$this->_setError($errcode, $errtext, $errdetail);

			return true;
		}
		else
		{
			// Check for generic IPP XML node errors
			$errcode = XML::extractTagContents('errcode', $response);
			$errtext = XML::extractTagContents('errtext', $response);
			$errdetail = XML::extractTagContents('errdetail', $response);

			if ($errcode != IPP::OK)
			{
				// Has errors!
				$this->_setError($errcode, $errtext, $errdetail);
				return true;
			}

			// Check for IDS XML error codes
			$errorcode = XML::extractTagContents('ErrorCode', $response);
			$errordesc = XML::extractTagContents('ErrorDesc', $response);

			if ($errorcode)
			{
				$this->_setError($errorcode, $errordesc);
				return true;
			}

			// Does not have any errors
			return false;
		}
	}

	/**
	 * If masking is enabled (default) then credit card numbers, connection tickets, and session tickets will be masked when output or logged
	 */
	public function useMasking(bool $yes_or_no): void
	{
		$this->_masking = $yes_or_no;
	}

	/**
	 * Turn debugging mode on or off
	 *
	 * Turning debugging mode on will result in a large amount of output being
	 * printed directly to stdout (the web browser or the console)
	 */
	public function useDebugMode(bool $yes_or_no): void
	{
		$this->_debug = $yes_or_no;
	}


	/**
	 *
	 *
	 *
	 */
	protected function _log(string $message, int $level = PackageInfo::LogLevel['NORMAL']): bool
	{
		if ($this->_masking)
		{
			$message = Utilities::mask($message);
		}

		if ($this->_debug)
		{
			print($message . PackageInfo::$CRLF);
		}

		if ($this->_driver)
		{
			//die('logging to driver: [' . $level . ']');
			// Send it to the driver to be logged
			$this->_driver->log($message, null, $level);
		}

		return true;
	}

	/**
	 * Log a message
	 *
	 *
	 */
	public function log(string $message, int $level = PackageInfo::LogLevel['NORMAL'])
	{
		return $this->_log($message, $level);
	}

	protected function _request(Context $Context, string $type, string $url, string $action, ?string $data, bool $post = true)
	{
		$headers = [];

		if ($action == IDS::OPTYPE_ADD ||
			$action == IDS::OPTYPE_MOD ||
			$action == IDS::OPTYPE_VOID ||
			$action == IDS::OPTYPE_DELETE)
		{
			$headers['Content-Type'] = 'application/xml';
		}
		else
		{
			$headers['Content-Type'] = 'text/plain';
		}

		// Oauth2 Authorization stuff
		if ($this->_authcred['oauth_access_token'])
		{
			$headers['Authorization'] = 'Bearer ' . $this->_authcred['oauth_access_token'];
		}

		// Our HTTP requestor
		$HTTP = new HTTP($url);

		// Set the headers
		$HTTP->setHeaders($headers);

		// Turn on debugging for the HTTP object if it's been enabled in the payment processor
		$HTTP->useDebugMode($this->_debug);

		//
		$HTTP->setRawBody($data);

		// We need the headers back
		$HTTP->returnHeaders(true);

		// Send the request
		if ($post)
		{
			$return = $HTTP->POST();
		}
		else
		{
			$return = $HTTP->GET();
		}

		$this->_setLastRequestResponse($HTTP->lastRequest(), $HTTP->lastResponse());
		$this->_setLastDebug(__CLASS__, ['http_request_response_duration' => $HTTP->lastDuration()]);

		//
		$this->_log($HTTP->getLog(), PackageInfo::LogLevel['DEBUG']);

		$errnum = $HTTP->errorNumber();
		$errmsg = $HTTP->errorMessage();

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

	/**
	 * Get the last raw XML response that was received
	 */
	public function lastResponse(): ?string
	{
		return $this->_last_response;
	}

	/**
	 * Get the last raw XML request that was sent
	 */
	public function lastRequest(): ?string
	{
		return $this->_last_request;
	}

	public function lastDebug()
	{
		return $this->_last_debug;
	}

	/**
	 * Get the error number of the last error that occurred
	 *
	 * @return mixed		The error number (or error code, some QuickBooks error codes are hex strings)
	 */
	public function errorCode()
	{
		return $this->_errcode;
	}

	/**
	 * Alias if ->errorCode()   (here for consistency with rest of framework)
	 */
	public function errorNumber()
	{
		return $this->errorCode();
	}

	/**
	 * Get the last error message that was reported
	 *
	 * Remember that issuing new commands may cause previous unchecked errors
	 * to be *cleared*, so make sure you check for errors if you expect an
	 * error might occur!
	 */
	public function errorText(): string
	{
		return $this->_errtext;
	}

	/**
	 * Alias of ->errorText()   (here for consistency with rest of framework)
	 */
	public function errorMessage(): string
	{
		return $this->errorText();
	}

	/**
	 *
	 */
	public function errorDetail(): string
	{
		return $this->_errdetail;
	}

	public function hasErrors(): bool
	{
		return $this->_errcode != IPP::ERROR_OK;
	}

	public function lastError(): string
	{
		return $this->_errcode . ': [' . $this->_errtext . ', ' . $this->_errdetail . ']';
	}

	/**
	 * Set an error message
	 *
	 * @param integer $errcode	The error number/code
	 * @param string $errtext	The text error message
	 */
	protected function _setError($errcode, ?string $errtext = '', ?string $errdetail = ''): void
	{
		$this->_errcode = $errcode;
		$this->_errtext = $errtext ?? '';
		$this->_errdetail = $errdetail ?? '';
	}

	protected function _setLastRequestResponse($request, $response): void
	{
		$this->_last_request = $request;
		$this->_last_response = $response;
	}

	protected function _setLastDebug($class, $arr): void
	{
		$existing = [];
		if (isset($this->_last_debug[$class]))
		{
			$existing = $this->_last_debug[$class];
		}

		$this->_last_debug[$class] = array_merge($existing, $arr);
	}
}
