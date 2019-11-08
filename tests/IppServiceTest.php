<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\IPP\IntuitAnywhere;
use QuickBooksPhpDevKit\Driver\Factory;
use QuickBooksPhpDevKit\Driver\Sql;

final class IppServiceTest extends XmlBaseTest
{
	private $classMap = [];

	public function setUp(): void
	{
		$this->classMap = $this->classFinder(dirname(__FILE__, 2) . '/src/QuickBooksPhpDevKit/IPP/Service');
	}

	public function tearDown(): void
	{
	}


	/**
	 * Create an object of the same name as the IPP Service files.
	 * Catches parse errors and naming mismatches.
	 */
	public function testCreateIppService(): void
	{
		foreach ($this->classMap as $className) {
			$fullClassName = "{$this->namespace_ipp}Service\\$className";
			//fwrite(STDOUT, "\t$fullClassName\n");

			$obj = new $fullClassName();
			$this->assertInstanceOf($fullClassName, $obj);

			// Unset $obj after testing it to clear it out for the next loop
			unset($obj);
		}
	}

	/**
	 * Test retrieving discovery information
	 */
	public function testIntuitAnywhere_discover(): void
	{
		$sandbox = true;
		$expected = [
		    'issuer' => 'https://oauth.platform.intuit.com/op/v1',
		    'authorization_endpoint' => 'https://appcenter.intuit.com/connect/oauth2',
		    'token_endpoint' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
		    'userinfo_endpoint' => 'https://sandbox-accounts.platform.intuit.com/v1/openid_connect/userinfo',
		    'revocation_endpoint' => 'https://developer.api.intuit.com/v2/oauth2/tokens/revoke',
		    'jwks_uri' => 'https://oauth.platform.intuit.com/op/v1/jwks',
		    'response_types_supported' => ['code'],
		    'subject_types_supported' => ['public'],
			'id_token_signing_alg_values_supported' => ['RS256'],
		    'scopes_supported' => [
		        'openid',
		        'email',
		        'profile',
		        'address',
		        'phone',
		    ],
		    'token_endpoint_auth_methods_supported' => [
				'client_secret_post',
		        'client_secret_basic',
		    ],
		    'claims_supported' => [
		    	'aud',
		    	'exp',
		    	'iat',
		    	'iss',
		    	'realmid',
		    	'sub',
		    ],
		];
		$test = IntuitAnywhere::discover($sandbox);
		//fwrite(STDERR, print_r($test, true));
		$this->assertEquals('https://appcenter.intuit.com/connect/oauth2', $test['authorization_endpoint']);
		$this->assertEquals('https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer', $test['token_endpoint']);
		$this->assertEquals('https://developer.api.intuit.com/v2/oauth2/tokens/revoke', $test['revocation_endpoint']);
		$this->assertEquals('https://sandbox-accounts.platform.intuit.com/v1/openid_connect/userinfo', $test['userinfo_endpoint']);

		$sandbox = false;
		$test = IntuitAnywhere::discover($sandbox);
		//fwrite(STDERR, print_r($test, true));
		$this->assertEquals('https://appcenter.intuit.com/connect/oauth2', $test['authorization_endpoint']);
		$this->assertEquals('https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer', $test['token_endpoint']);
		$this->assertEquals('https://developer.api.intuit.com/v2/oauth2/tokens/revoke', $test['revocation_endpoint']);
		$this->assertEquals('https://accounts.platform.intuit.com/v1/openid_connect/userinfo', $test['userinfo_endpoint']);
	}

	/**
	 * Test getting the authentication url
	 */
	public function testIntuitAnywhere_getAuthenticateUrl(): void
	{
		$dsn = 'sqlite3://UnusedHost/:memory:';
		$Driver = Factory::create($dsn, ['new_link' => true]);
		$Driver->initialize([]);

		// Test variables
		$the_tenant = (string) 12345;
		$scope = 'com.intuit.quickbooks.accounting ';
		$sandbox = true;
		$encryption_key = 'x0/gslqDWcUhLrEzn35z6qJuQAxwHq1O0ksHWdTBh3o=';
		$oauth_client_id = 'bogus_invalid_client_id';
		$oauth_client_secret = 'bogus_invalid_client_secret';
		$quickbooks_oauth_url = 'https://localhost/oauth';
		$quickbooks_success_url = 'https://localhost/success';

		// Create an IntuitAnywhere instance
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

		$test = $IntuitAnywhere->getAuthenticateUrl($the_tenant, $quickbooks_oauth_url);
		$this->assertStringStartsWith('https://appcenter.intuit.com/connect/oauth2?client_id=bogus_invalid_client_id&scope=com.intuit.quickbooks.accounting&redirect_uri=https%3A%2F%2Flocalhost%2Foauth&response_type=code&state=', $test);
	}

	/**
	 * Cannot test much without having valid oauth tokens (which expire).
	 */
	public function testIntuitAnywhereFailure(): void
	{
		$dsn = 'sqlite3://UnusedHost/:memory:';
//		$dsn = 'sqlite3://UnusedHost//tmp/ipp_test.sqlite3';
		$Driver = Factory::create($dsn, ['new_link' => true]);
		$Driver->initialize([]);

		// Test variables
		$the_tenant = (string) 12345;
		$scope = 'com.intuit.quickbooks.accounting ';
		$sandbox = true;
		$encryption_key = 'x0/gslqDWcUhLrEzn35z6qJuQAxwHq1O0ksHWdTBh3o=';
		$oauth_client_id = 'bogus_invalid_client_id';
		$oauth_client_secret = 'bogus_invalid_client_secret';
		$quickbooks_oauth_url = 'https://localhost/oauth';
		$quickbooks_success_url = 'https://localhost/success';

		// Have to create a request before it can be updated
		$state = '797dc8f808f7f5b82afaad8ef6ac8b5a';
		$Driver->oauthRequestWriteV2($the_tenant, $state);


		// Create a new IntuitAnywhere instance
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

		// Not authenticated yet
		$test = ($IntuitAnywhere->check($the_tenant) && $IntuitAnywhere->test($the_tenant));
		$this->assertSame(false, $test);

		// Try to "reconnect"
		$test = $IntuitAnywhere->reconnect('unused_user_variable_with_oauth2', $the_tenant);
		$this->assertSame(false, $test);


		$test = $IntuitAnywhere->disconnect('unused_user_variable_with_oauth2', $the_tenant, true);
		$this->assertSame(true, $test);


		// Pretend we received this from the OAuth2 code returned after a user completes the OAuth2 flow
		$realmId = '111111111111111';
		$access_token = 'pvAyhb993IbJN1LfoT4Dzjhmcg5nVNliR+G2Bg==';
		$refresh_token = 'sxTaHzewdmVYpHbAyWjyVoIjAHAIpr4ZkxmJkQ==';

		$access_token_lifetime = strtotime('1 hour') - strtotime('now');
		$refresh_token_lifetime = strtotime('100 days') - strtotime('now');

		$access_token_expires = date('Y-m-d H:i:s', strtotime('now') + $access_token_lifetime);
		$refresh_token_expires = date('Y-m-d H:i:s', strtotime('now') + $refresh_token_lifetime);
		$Driver->oauthAccessWriteV2(
			$encryption_key,
			$state,
			$access_token,
			$refresh_token,
			$access_token_expires,
			$refresh_token_expires,
			$realmId);

		// Create a new IntuitAnywhere instance
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

/*
		// Pretend they authenticated
		$updates = [
			'oauth_access_expiry' => $access_token_expires,
			'oauth_refresh_expiry' => $refresh_token_expires,

			'oauth_access_token' => 'o6iu2hop7/njK63PLvtP93d3Th4i4vpnbbVEupvCw7MbC9YsRZHVOEZK4ro1gAeTQ8t9xt/QwOqTiPt/1U80FU28t3LznoN41tAcCZqQj9tUOmuY/t',
			'oauth_refresh_token' => 'fq125UYZq6oHNRH7B4/PuVeYPXRDS27wOztKeOihGe+auuLdYGGaQ+M3JSvap9+rDX83LU6lnViylcjyGdiSPvNk50nXAJBGSvvrJbaTWWMoS4ybpSbPBNqmbxfrKW7afpSNxb94IyQ=',
		];
		$resync = false;
		$Driver->update(Sql::$TablePrefix['BASE'] . Sql::$Table['OAUTHV2'], $updates, [['app_tenant' => $the_tenant]], $resync);
*/
		// Are they connected to QuickBooks right now?
		$test = ($IntuitAnywhere->check($the_tenant) && $IntuitAnywhere->test($the_tenant));
		$this->assertSame(false, $test);

		// Try to "reconnect"
		$test = $IntuitAnywhere->reconnect('unused_user_variable_with_oauth2', $the_tenant);
		$this->assertSame(false, $test);


		$test = $IntuitAnywhere->disconnect('unused_user_variable_with_oauth2', $the_tenant, true);
		$this->assertSame(true, $test);
	}
}
