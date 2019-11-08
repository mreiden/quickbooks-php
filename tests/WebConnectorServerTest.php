<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\BasicWebConnectorTest;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\Adapter\SOAP\Server\BuiltinAdapter;
use QuickBooksPhpDevKit\Driver\Factory;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;
use QuickBooksPhpDevKit\WebConnector\Queue;
use QuickBooksPhpDevKit\WebConnector\Server as WebConnectorServer;

class WebConnectorServerTest extends XmlBaseTest
{
	protected static $dbFile;
	protected static $dsn;
	protected static $ticket;

	protected $__init_options = [
		'quickbooks_sql_enabled' => false,
	];

	protected $setupInstance;
	protected $WebConnectorServer;

	protected $soapAdapterClass = BuiltinAdapter::class;


	public static function setUpBeforeClass(): void
	{
		static::$dbFile = '/tmp/WebConnectorServerTest.sqlite3';

		static::$dsn = [
			'backend' => 'Sqlite3',
			//'database' => ':memory:',
			'database' => static::$dbFile,
		];

		static::removeDbFile();
	}

	public static function tearDownAfterClass(): void
	{
		//static::removeDbFile();
	}


	public function tearDown(): void
	{
	}

	static public function removeDbFile(): void
	{
		$file = static::$dbFile;
		if (file_exists($file) && is_writable($file))
		{
			// Remove the test database file
			$removed = unlink($file);
			if (false === $removed)
			{
				throw new \Exception('Could not delete SQLite3 file: ' . $file);
			}
			//exit;
		}
	}



	public function setUp(): void
	{
		// Use strict error reporting
		error_reporting(E_ALL | E_STRICT);

		// TimeZone should match the computer running Quickbooks WebConnector or some installations may complain
		//PackageInfo::$TIMEZONE = 'America/New_York';
		PackageInfo::$TIMEZONE = 'America/Chicago';
		//PackageInfo::$TIMEZONE = 'America/Denver';
		//PackageInfo::$TIMEZONE = 'America/Los_Angeles';

		// Logging level
		//PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['NONE'];
		//PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['NORMAL'];
		//PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['VERBOSE'];
		//PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['DEBUG'];
		PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['DEVELOP'];	 // Use this level until you're sure everything works!!!

		// These really must come before anything is done with the dsn when using SQLite's memory database,
		// otherwise a new one gets used because the instance is based on the dsn and the serialized $driver_options.
		$driver_options = [
			'new_link' => true,   // This is required for child classes to avoid getting "attempt to write a readonly database" errors

			//'log_level' => PackageInfo::LogLevel['VERBOSE'],

			// See the comments in the QuickBooks/Driver/<YOUR DRIVER HERE>.php file ( i.e. 'Mysqli.php', etc. )
			'max_log_history' => 16384,     // Limit the number of quickbooks_log entries
			'max_queue_history' => 1024,    // Limit the number of *successfully processed* quickbooks_queue entries
			'max_ticket_history' => 1024,   // Limit the number of quickBooks_tickets entries
		];

		static::$dsn = Factory::Create(static::$dsn, $driver_options);

		$this->setupInstance = new BasicWebConnectorTest($this->soapAdapterClass, static::$dsn);

		$this->WebConnectorServer = $this->setupInstance->getWebConnectorInstance();

		// Set this so we process the SOAP requests
		$_SERVER['REQUEST_METHOD'] = 'POST';

		// Set an allowed IP address
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	}

	/**
	 * Test ->debug()
	 */
	public function testDebug(): void
	{
		$test = $this->WebConnectorServer->debug();
		$this->assertStringStartsWith(get_class($this->WebConnectorServer), $test);
	}

	/**
	 * Test Auto-Timezone
	 *
	 * @ runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testAutoTimezone(): void
	{
		// Set this so we test GET intead of POST
		$_SERVER['REQUEST_METHOD'] = 'GET';

		// Unset user specified timezone
		PackageInfo::$TIMEZONE = null;

		// Have it set the timezone automatically
		$test = $this->WebConnectorServer->setDefaultTimeZone(null);
		$this->assertSame(true, $test);

		$this->assertSame(true, PackageInfo::$TIMEZONE_AUTOSET);
		$this->assertNotNull(PackageInfo::$TIMEZONE);
		/*
		$test = @date_default_timezone_get();
		try
		{
			new DateTimeZone($timezoneId);
		}
		catch(Exception $e)
		{
			$valid = false;
		}
		return TRUE;
		*/
	}

	/**
	 * Test GET information page request (i.e. /MyWebConnectURL)
	 *
	 * @ runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testGetInformationPage(): void
	{
		// Set this so we test GET intead of POST
		$_SERVER['REQUEST_METHOD'] = 'GET';

		// Handle the request
		$this->WebConnectorServer->setRawRequestInput('');

		// Buffer to output
		ob_start();
		// Get the response
		$content = $this->WebConnectorServer->handle(true, true);
		// Get the buffered response
		$output = ob_get_clean();

		$this->assertStringContainsString('Visit us at: https://github.com/consolibyte/quickbooks-php', $output);
	}

	/**
	 * Test wsdl request (i.e. /MyWebConnectURL?wsdl)
	 *
	 * @ runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testWsdl(): void
	{
		// Make sure the WSDL file exists and is readable
		$this->assertSame(true, (file_exists(PackageInfo::$WSDL) && is_readable(PackageInfo::$WSDL)));

		$wsdl_contents = file_get_contents(PackageInfo::$WSDL);
		foreach (['wsdl', 'WsDl'] as $key)
		{
			// Set this so we test GET intead of POST
			$_SERVER['REQUEST_METHOD'] = 'GET';
			$_GET[$key] = '';

			// Handle the request
			$this->WebConnectorServer->setRawRequestInput('');

			// Buffer the output
			ob_start();
			// Get the response
			$wsdl = $this->WebConnectorServer->handle(true, true);
			// Get the buffered response
			$output = ob_get_clean();

			// Unset the key to clear it for the next loop
			unset($_GET[$key]);

			$this->assertEquals($wsdl_contents, $output);
		}
	}


	/**
	 * Test SOAP ServerVersion Request
	 *
	 * @ runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testSoapServerVersion(): void
	{
		$soapAction = 'http://developer.intuit.com/serverVersion';
		$xmlRequest  = implode("\n", [
			'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">',
			'  <soap:Body>',
			'    <serverVersion xmlns="http://developer.intuit.com/" />',
			'  </soap:Body>',
			'</soap:Envelope>',
		]);
		$xmlExpected = implode("\n", [
			'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
			'  <SOAP-ENV:Body>',
			'    <ns1:serverVersionResponse>',
			'      <ns1:serverVersionResult>PHP QuickBooks SOAP Server v' . PackageInfo::Package['VERSION'] . ' at http://?</ns1:serverVersionResult>',
			'    </ns1:serverVersionResponse>',
			'  </SOAP-ENV:Body>',
			'</SOAP-ENV:Envelope>',
		]);

		// Handle the request
		$this->WebConnectorServer->setRawRequestInput($xmlRequest);

		// Buffer the output
		ob_start();
		// Handle the mock SOAP request
		//   NOTE: The @ Error Control Operator prevents the PHP Extension SOAP Server from failing due to sending headers in these tests)
		$xmlResponse = @$this->WebConnectorServer->handle(true, true);
		// Get the buffered response
		$output = ob_get_clean();

		//fwrite(STDERR, "\nRESPONSE for ServerVersion:\n$xmlResponse");
		$this->commonTests($xmlExpected, $xmlResponse);
		// Test to make sure the output of the entire WebConnectorServer->handle() function matches
		$this->commonTests($xmlExpected, $output);
	}

	/**
	 * Test SOAP ClientVersion Requests
	 *
	 * @ runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testSoapClientVersion(): void
	{
		$soapAction = 'http://developer.intuit.com/clientVersion';

		// The standalone server is set to require 2.2.0.71
		$versions = [
			'2.1.0.30' => '<ns1:clientVersionResult>O:2.2.0.71</ns1:clientVersionResult>',
			'2.2.0.71' => '<ns1:clientVersionResult/>',
			'2.3.0.36' => '<ns1:clientVersionResult/>',
		];
		foreach ($versions as $version => $result)
		{
			$xmlRequest  = implode("\n", [
				'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">',
				'  <soap:Body>',
				'    <clientVersion xmlns="http://developer.intuit.com/">',
				'      <strVersion>' . $version . '</strVersion>',
				'    </clientVersion>',
				'  </soap:Body>',
				'</soap:Envelope>',
			]);
			$xmlExpected = implode("\n", [
				'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
				'  <SOAP-ENV:Body>',
				'    <ns1:clientVersionResponse>',
				'      ' . $result,
				'    </ns1:clientVersionResponse>',
				'  </SOAP-ENV:Body>',
				'</SOAP-ENV:Envelope>',
			]);

			// Handle the request
			$this->WebConnectorServer->setRawRequestInput($xmlRequest);

			// Buffer the output
			ob_start();
			// Handle the mock SOAP request
			//   NOTE: The @ Error Control Operator prevents the PHP Extension SOAP Server from failing due to sending headers in these tests)
			$xmlResponse = @$this->WebConnectorServer->handle(true, true);
			// Get the buffered response
			$output = ob_get_clean();

			//fwrite(STDERR, "\nRESPONSE for ServerVersion:\n$xmlResponse");
			$this->commonTests($xmlExpected, $xmlResponse);
			// Test to make sure the output of the entire WebConnectorServer->handle() function matches
			$this->commonTests($xmlExpected, $output);
		}
	}

	/**
	 * Test SOAP Authenticate Requests
	 *
	 * @ runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testSoapAuthenticate(): void
	{
		$dsn = static::$dsn;

		$initialized = Utilities::initialized($dsn);
		if (false === $initialized)
		{
			Utilities::initialize($dsn);
		}
		$initialized = Utilities::initialized($dsn);

		$success = Utilities::createUser($dsn, 'webconnector', 'password');
		$this->assertEquals(true, $success);

		$success = Utilities::createUser($dsn, 'disableduser', 'password');
		$this->assertEquals(true, $success);
		$success = Utilities::disableUser($dsn, 'disableduser');
		$this->assertEquals(true, $success);

		// Add to the Queue to test a response that includes the number of queue items
		$success = Utilities::createUser($dsn, 'user1', 'password');
		$this->assertEquals(true, $success);

		$soapAction = 'http://developer.intuit.com/authenticate';

		$data = [
			['webconnector', 'password', true,  'none',],
			['user1',        'password', true,  '',],
			['webconnector', 'bad-pass', false, null,],
			['disableduser', 'password', false, null,],
			['nonexistent',  'password', false, null,],
		];
		foreach ($data as $user)
		{
			$xmlRequest  = implode("\n", [
				'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">',
				'  <soap:Body>',
				'    <authenticate xmlns="http://developer.intuit.com/">',
				'      <strUserName>' . $user[0] . '</strUserName>',
				'      <strPassword>' . $user[1] . '</strPassword>',
				'    </authenticate>',
				'  </soap:Body>',
				'</soap:Envelope>',
			]);
			//fwrite(STDERR, "\n\nRequest:\n" . $xmlRequest."\n");

			// Handle the request
			$this->WebConnectorServer->setRawRequestInput($xmlRequest);

			// Buffer the output
			ob_start();
			// Handle the mock SOAP request
			//   NOTE: The @ Error Control Operator prevents the PHP Extension SOAP Server from failing due to sending headers in these tests)
			$xmlResponse = @$this->WebConnectorServer->handle(true, true);
			// Get the buffered response
			$output = ob_get_clean();


			//fwrite(STDERR, "\n\n\nRESPONSE (user: {$user[0]}, pass: {$user[1]}):\n$xmlResponse");
			if ($user[2])
			{
				// Successful Authorization Expected
				$ticket = '01234567-89ab-cdef-0123-456789abcdef';
				if (preg_match('#<ns1:authenticateResult>\s*<ns1:string>([A-Z0-9\-]+)</ns1:string>*#i', $xmlResponse, $matches))
				{
					// Check their ticket
					$ticket = $matches[1];

					// Username should be the same as in the request
					$Driver = Utilities::driverFactory($dsn);
					$userName = $Driver->authResolve($ticket);
					$this->assertEquals($userName, $user[0]);

					if ($userName == 'user1')
					{
						// We need to use the ticket in future requests (Tests for sendRequestXML, etc)
						//$this->ticket = $ticket;
						static::$ticket = $ticket;
						//fwrite(STDERR, "\n\nTicket: " . $this->ticket ."\n");
					}
				}
				//var_dump($matches);
				$xmlExpected = implode("\n", [
					'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
					'  <SOAP-ENV:Body>',
					'    <ns1:authenticateResponse>',
					'      <ns1:authenticateResult>',
					'        <ns1:string>' . $ticket . '</ns1:string>',
					'        <ns1:string>' . $user[3] . '</ns1:string>',
					'      </ns1:authenticateResult>',
					'    </ns1:authenticateResponse>',
					'  </SOAP-ENV:Body>',
					'</SOAP-ENV:Envelope>',
				]);
				//fwrite(STDERR, "ExpectedXML:\n". print_r($xmlExpected, true)."\n");
			}
			else
			{
				// Failed Authorization Expected
				$xmlExpected = implode("\n", [
					'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
					'  <SOAP-ENV:Body>',
					'    <ns1:authenticateResponse>',
					'      <ns1:authenticateResult>',
					'        <ns1:string/>',
					'        <ns1:string>nvu</ns1:string>',
					'      </ns1:authenticateResult>',
					'    </ns1:authenticateResponse>',
					'  </SOAP-ENV:Body>',
					'</SOAP-ENV:Envelope>',
				]);
			}

			//fwrite(STDERR, "\nRESPONSE for ServerVersion:\n$xmlResponse");
			$this->commonTests($xmlExpected, $xmlResponse);
			// Test to make sure the output of the entire WebConnectorServer->handle() function matches
			$this->commonTests($xmlExpected, $output);
		}
	}


	/**
	 * Test SOAP Custom User Authentication
	 *
	 * @ runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testSoapAuthenticateCustom(): void
	{
		$dsn = static::$dsn;

		$initialized = Utilities::initialized($dsn);
		if (false === $initialized)
		{
			Utilities::initialize($dsn);
		}


		$handler_options = [
			// See the comments in the QuickBooks/Server/Handlers.php file
			//'authenticate' => ' *** YOU DO NOT NEED TO PROVIDE THIS CONFIGURATION VARIABLE TO USE THE DEFAULT AUTHENTICATION METHOD FOR THE DRIVER YOU'RE USING (I.E.: MYSQLi) *** '
			//'authenticate' => 'your_function_name_here',
			'authenticate' => [$this, '__webconnector_custom_auth'],
			'deny_concurrent_logins' => false,
			'deny_reallyfast_logins' => false,
		];

		// Create the WebConnector server
		$placeholder = [];
		$WebConnectorServerCustomAuth = new WebConnectorServer($dsn, new $this->soapAdapterClass(), $placeholder, $placeholder, $placeholder, PackageInfo::$LOGLEVEL, $handler_options);



		$soapAction = 'http://developer.intuit.com/authenticate';

		$data = [
			['keith',        'rocks',    true,  'none',],
			['KeItH',        'rocks',    true,  'none',],
			['webconnector', 'password', false, null,],
			['keith',        'RoCkS'   , false, null,],
		];
		foreach ($data as $user)
		{
			$xmlRequest  = implode("\n", [
				'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">',
				'  <soap:Body>',
				'    <authenticate xmlns="http://developer.intuit.com/">',
				'      <strUserName>' . $user[0] . '</strUserName>',
				'      <strPassword>' . $user[1] . '</strPassword>',
				'    </authenticate>',
				'  </soap:Body>',
				'</soap:Envelope>',
			]);
			//fwrite(STDERR, "\n\nRequest:\n" . $xmlRequest."\n");



			// Handle the request
			$WebConnectorServerCustomAuth->setRawRequestInput($xmlRequest);

			// Buffer the output
			ob_start();
			// Handle the mock SOAP request
			//   NOTE: The @ Error Control Operator prevents the PHP Extension SOAP Server from failing due to sending headers in these tests)
			$xmlResponse = @$WebConnectorServerCustomAuth->handle(true, true);
			// Get the buffered response
			$output = ob_get_clean();


			//fwrite(STDERR, "\n\n\nRESPONSE (user: {$user[0]}, pass: {$user[1]}):\n$xmlResponse");
			if ($user[2])
			{
				// Successful Authorization Expected
				$ticket = '01234567-89ab-cdef-0123-456789abcdef';
				if (preg_match('#<ns1:authenticateResult>\s*<ns1:string>([A-Z0-9\-]+)</ns1:string>*#i', $xmlResponse, $matches))
				{
					// Check their ticket
					$ticket = $matches[1];

					// Username should be the same as in the request
					$Driver = Utilities::driverFactory($dsn);
					$userName = $Driver->authResolve($ticket);
					$this->assertEquals($userName, $user[0]);
				}
				//var_dump($matches);
				$xmlExpected = implode("\n", [
					'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
					'  <SOAP-ENV:Body>',
					'    <ns1:authenticateResponse>',
					'      <ns1:authenticateResult>',
					'        <ns1:string>' . $ticket . '</ns1:string>',
					'        <ns1:string>' . $user[3] . '</ns1:string>',
					'      </ns1:authenticateResult>',
					'    </ns1:authenticateResponse>',
					'  </SOAP-ENV:Body>',
					'</SOAP-ENV:Envelope>',
				]);
				//fwrite(STDERR, "ExpectedXML:\n". print_r($xmlExpected, true)."\n");
			}
			else
			{
				// Failed Authorization Expected
				$xmlExpected = implode("\n", [
					'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
					'  <SOAP-ENV:Body>',
					'    <ns1:authenticateResponse>',
					'      <ns1:authenticateResult>',
					'        <ns1:string/>',
					'        <ns1:string>nvu</ns1:string>',
					'      </ns1:authenticateResult>',
					'    </ns1:authenticateResponse>',
					'  </SOAP-ENV:Body>',
					'</SOAP-ENV:Envelope>',
				]);
			}

			//fwrite(STDERR, "\nRESPONSE for ServerVersion:\n$xmlResponse");
			$this->commonTests($xmlExpected, $xmlResponse, false, 'Failed using username=' . $user[0] . ', password=' . $user[1]);
			// Test to make sure the output of the entire WebConnectorServer->handle() function matches
			$this->commonTests($xmlExpected, $output);
		}
	}
	public function __webconnector_custom_auth(string $username, string $password, ?string &$qb_company_file, ?int &$customauth_wait_before_next_update, ?int &$customauth_min_run_every_n_seconds, ?string &$err): bool
	{
		if (strtolower($username) == 'keith' && $password == 'rocks')
		{
			// Use this company file and auth successfully
			$qb_company_file = 'C:\path\to\the\file-function.QBW';

			return true;
		}

		// Login failure
		return false;
	}


	/**
	 * Test SOAP sendRequestXML Requests
	 *
	 * @ depends testSoapAuthenticate
	 * @ runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testSendRequestXML(): void
	{
		$dsn = static::$dsn;

		$Driver = Utilities::driverFactory($dsn);

		$initialized = Utilities::initialized($dsn);
		if (false === $initialized)
		{
			Utilities::initialize($dsn);
		}
		$initialized = Utilities::initialized($dsn);
		$this->assertEquals(true, $initialized);

		$q = new Queue($dsn, 'user1', []);
		$test = $q->size('user1');
		$this->assertEquals(3, $test);


		$soapAction = 'http://developer.intuit.com/sendRequestXML';

		$expecteds = [
			implode("\n", [
				'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
				'  <SOAP-ENV:Body>',
				'    <ns1:sendRequestXMLResponse>',
				'      <ns1:sendRequestXMLResult>&lt;?xml version="1.0" encoding="UTF-8"?&gt;',
				'&lt;?qbxml version="13.0" ?&gt;',
				'&lt;QBXML&gt;',
				'  &lt;QBXMLMsgsRq onError="continueOnError"&gt;',
				'    &lt;CustomerAddRq requestID="1"&gt;',
				'      &lt;CustomerAdd&gt;',
				'        &lt;Name&gt;My Customer Name #100 -12345&lt;/Name&gt;',
				'        &lt;CompanyName&gt;My Company Name&lt;/CompanyName&gt;',
				'        &lt;BillAddress&gt;',
				'          &lt;Addr1&gt;My Customer Name - Addr line 1&lt;/Addr1&gt;',
				'          &lt;Addr2&gt;Customer Department&lt;/Addr2&gt;',
				'          &lt;Addr3&gt;Customer Representative&lt;/Addr3&gt;',
				'          &lt;Addr4&gt;Street Address - Addr line 2&lt;/Addr4&gt;',
				'          &lt;Addr5&gt;APT 500 - Addr line 3&lt;/Addr5&gt;',
				'          &lt;City&gt;City&lt;/City&gt;',
				'          &lt;State&gt;State&lt;/State&gt;',
				'          &lt;PostalCode&gt;90210&lt;/PostalCode&gt;',
				'        &lt;/BillAddress&gt;',
				'        &lt;SalesRepRef&gt;',
				'          &lt;FullName&gt;SR&lt;/FullName&gt;',
				'        &lt;/SalesRepRef&gt;',
				'        &lt;AccountNumber&gt;12345&lt;/AccountNumber&gt;',
				'      &lt;/CustomerAdd&gt;',
				'    &lt;/CustomerAddRq&gt;',
				'  &lt;/QBXMLMsgsRq&gt;',
				'&lt;/QBXML&gt;</ns1:sendRequestXMLResult>',
				'    </ns1:sendRequestXMLResponse>',
				'  </SOAP-ENV:Body>',
				'</SOAP-ENV:Envelope>',
			]),

			implode("\n", [
				'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
				'  <SOAP-ENV:Body>',
				'    <ns1:sendRequestXMLResponse>',
				'      <ns1:sendRequestXMLResult>&lt;?xml version="1.0" encoding="UTF-8"?&gt;',
				'&lt;?qbxml version="13.0" ?&gt;',
				'&lt;QBXML&gt;',
				'  &lt;QBXMLMsgsRq onError="continueOnError"&gt;',
				'    &lt;CustomerAddRq requestID="2"&gt;',
				'      &lt;CustomerAdd&gt;',
				'        &lt;Name&gt;My Customer Name #150 -12345&lt;/Name&gt;',
				'        &lt;CompanyName&gt;My Company Name&lt;/CompanyName&gt;',
				'        &lt;BillAddress&gt;',
				'          &lt;Addr1&gt;My Customer Name - Addr line 1&lt;/Addr1&gt;',
				'          &lt;Addr2&gt;Customer Department&lt;/Addr2&gt;',
				'          &lt;Addr3&gt;Customer Representative&lt;/Addr3&gt;',
				'          &lt;Addr4&gt;Street Address - Addr line 2&lt;/Addr4&gt;',
				'          &lt;Addr5&gt;APT 500 - Addr line 3&lt;/Addr5&gt;',
				'          &lt;City&gt;City&lt;/City&gt;',
				'          &lt;State&gt;State&lt;/State&gt;',
				'          &lt;PostalCode&gt;90210&lt;/PostalCode&gt;',
				'        &lt;/BillAddress&gt;',
				'        &lt;SalesRepRef&gt;',
				'          &lt;FullName&gt;SR&lt;/FullName&gt;',
				'        &lt;/SalesRepRef&gt;',
				'        &lt;AccountNumber&gt;12345&lt;/AccountNumber&gt;',
				'      &lt;/CustomerAdd&gt;',
				'    &lt;/CustomerAddRq&gt;',
				'  &lt;/QBXMLMsgsRq&gt;',
				'&lt;/QBXML&gt;</ns1:sendRequestXMLResult>',
				'    </ns1:sendRequestXMLResponse>',
				'  </SOAP-ENV:Body>',
				'</SOAP-ENV:Envelope>',
			]),

			implode("\n", [
				'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
				'  <SOAP-ENV:Body>',
				'    <ns1:sendRequestXMLResponse>',
				'      <ns1:sendRequestXMLResult>&lt;?xml version="1.0" encoding="UTF-8"?&gt;',
				'&lt;?qbxml version="13.0" ?&gt;',
				'&lt;QBXML&gt;',
				'  &lt;QBXMLMsgsRq onError="continueOnError"&gt;',
				'    &lt;InvoiceAddRq requestID="3"&gt;',
				'      &lt;InvoiceAdd&gt;',
				'        &lt;CustomerRef&gt;',
				'          &lt;ListID&gt;70000007-1234567890&lt;/ListID&gt;',
				'          &lt;FullName&gt;My Customer Name&lt;/FullName&gt;',
				'        &lt;/CustomerRef&gt;',
				'        &lt;ClassRef&gt;',
				'          &lt;FullName&gt;QuickBooksClass&lt;/FullName&gt;',
				'        &lt;/ClassRef&gt;',
				'        &lt;TemplateRef&gt;',
				'          &lt;FullName&gt;CustomTemplate&lt;/FullName&gt;',
				'        &lt;/TemplateRef&gt;',
				'        &lt;TxnDate&gt;2019-07-04&lt;/TxnDate&gt;',
				'        &lt;RefNumber&gt;10001&lt;/RefNumber&gt;',
				'        &lt;BillAddress&gt;',
				'          &lt;Addr1&gt;My Customer&lt;/Addr1&gt;',
				'          &lt;Addr2&gt;attn: John Doe&lt;/Addr2&gt;',
				'          &lt;Addr3&gt;123 Bill Street&lt;/Addr3&gt;',
				'          &lt;Addr4&gt;APT 5555&lt;/Addr4&gt;',
				'          &lt;City&gt;BillCity&lt;/City&gt;',
				'          &lt;State&gt;NY&lt;/State&gt;',
				'          &lt;PostalCode&gt;10019&lt;/PostalCode&gt;',
				'        &lt;/BillAddress&gt;',
				'        &lt;ShipAddress&gt;',
				'          &lt;Addr1&gt;My Customer&lt;/Addr1&gt;',
				'          &lt;Addr2&gt;attn: John Doe&lt;/Addr2&gt;',
				'          &lt;Addr3&gt;123 Ship Street&lt;/Addr3&gt;',
				'          &lt;Addr4&gt;APT 5555&lt;/Addr4&gt;',
				'          &lt;City&gt;ShipCity&lt;/City&gt;',
				'          &lt;State&gt;NY&lt;/State&gt;',
				'          &lt;PostalCode&gt;10019&lt;/PostalCode&gt;',
				'        &lt;/ShipAddress&gt;',
				'        &lt;PONumber&gt;PO12345&lt;/PONumber&gt;',
				'        &lt;TermsRef&gt;',
				'          &lt;FullName&gt;Net 30&lt;/FullName&gt;',
				'        &lt;/TermsRef&gt;',
				'        &lt;DueDate&gt;2019-08-03&lt;/DueDate&gt;',
				'        &lt;SalesRepRef&gt;',
				'          &lt;FullName&gt;SR&lt;/FullName&gt;',
				'        &lt;/SalesRepRef&gt;',
				'        &lt;IsToBePrinted&gt;false&lt;/IsToBePrinted&gt;',
				'        &lt;IsToBeEmailed&gt;true&lt;/IsToBeEmailed&gt;',
				'        &lt;InvoiceLineAdd&gt;',
				'          &lt;ItemRef&gt;',
				'            &lt;FullName&gt;Item Full Name&lt;/FullName&gt;',
				'          &lt;/ItemRef&gt;',
				'          &lt;Desc&gt;My item description&lt;/Desc&gt;',
				'          &lt;Amount&gt;250.00&lt;/Amount&gt;',
				'        &lt;/InvoiceLineAdd&gt;',
				'        &lt;InvoiceLineAdd&gt;',
				'          &lt;ItemRef&gt;',
				'            &lt;FullName&gt;Item2 Full Name&lt;/FullName&gt;',
				'          &lt;/ItemRef&gt;',
				'          &lt;Desc&gt;My item2 description&lt;/Desc&gt;',
				'          &lt;Quantity&gt;125.00&lt;/Quantity&gt;',
				'          &lt;Rate&gt;2.45&lt;/Rate&gt;',
				'        &lt;/InvoiceLineAdd&gt;',
				'      &lt;/InvoiceAdd&gt;',
				'    &lt;/InvoiceAddRq&gt;',
				'  &lt;/QBXMLMsgsRq&gt;',
				'&lt;/QBXML&gt;</ns1:sendRequestXMLResult>',
				'    </ns1:sendRequestXMLResponse>',
				'  </SOAP-ENV:Body>',
				'</SOAP-ENV:Envelope>',
			]),

			implode("\n", [
				'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
				'  <SOAP-ENV:Body>',
				'    <ns1:sendRequestXMLResponse>',
				'      <ns1:sendRequestXMLResult/>',
				'    </ns1:sendRequestXMLResponse>',
				'  </SOAP-ENV:Body>',
				'</SOAP-ENV:Envelope>',
			]),
		];
		//fwrite(STDERR, "\nExpecteds:".print_r($expecteds,true));

		//$xmlExpected = $expecteds[0];
		//fwrite(STDERR, "\nxmlExpected:".print_r($xmlExpected,true));


		// Get a ticket since setting this on the class seems to break with @runInSeparateProcess
		$company_file = null;
		$wait_before_next_update = null;
		$min_run_every_n_seconds = null;
		//$ticket = $Driver->authLogin('user1', 'password', $company_file, $wait_before_next_update, $min_run_every_n_seconds);

		$item = $Driver->queueDequeue('user1', true);
		$ticket = $item['extra']['ticket_id'];

		//$test = $Driver->queueStatus($item['extra']['ticket_id'], 1, PackageInfo::Status['PROCESSING'], json_encode(['ticket_id' => 5000]));
		//$this->assertEquals(false, $test);

		//fwrite(STDERR, "Item: ". print_r($item,true)."\n");
		//exit;


		$i = 0;
		while (($xmlExpected = array_shift($expecteds)))
		{
			$i++;
			//$xmlExpected = array_shift($expecteds);
//fwrite(STDERR, "\nxmlExpected ($i):".print_r($xmlExpected,true));

			$xmlRequest  = implode("\n", [
				'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">',
				'  <soap:Body>',
				'    <sendRequestXML xmlns="http://developer.intuit.com/">',
				'      <ticket>' . $ticket . '</ticket>',
				'      <strHCPResponse/>',
				'    <strCompanyFileName>C:\Directory\File.qbw</strCompanyFileName>',
				'      <qbXMLCountry>US</qbXMLCountry>',
				'      <qbXMLMajorVers>13</qbXMLMajorVers>',
				'      <qbXMLMinorVers>0</qbXMLMinorVers>',
				'    </sendRequestXML>',
				'  </soap:Body>',
				'</soap:Envelope>',
			]);

			// Handle the request
			//fwrite(STDERR, "\n\nREQUEST ($i):\n$xmlRequest");
			$this->WebConnectorServer->setRawRequestInput($xmlRequest);

			// Buffer the output
			ob_start();
			// Handle the mock SOAP request
			//   NOTE: The @ Error Control Operator prevents the PHP Extension SOAP Server from failing due to sending headers in these tests)
			$xmlResponse = @$this->WebConnectorServer->handle(true, true);
			// Get the buffered response
			$output = ob_get_clean();

			//fwrite(STDERR, "\n\nEXPECTED ($i):\n$xmlExpected");
			//fwrite(STDERR, "\n\n\nRESPONSE ($i):\n$xmlResponse");

			$this->commonTests($xmlExpected, $xmlResponse);
			// Test to make sure the output of the entire WebConnectorServer->handle() function matches
			$this->commonTests($xmlExpected, $output);
		}

		// 4 requests (2 customers, 1 invoice, and one to find there's nothing to do)
		$this->assertEquals(4, $i);
		// Queue for user1 should be empty now
		$this->assertEquals(0, $q->size('user1'));
	}


	/**
	 * Test SOAP receiveResponseXML Requests
	 *
	 * @ depends testSoapAuthenticate
	 * @ runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testReceiveResponseXML(): void
	{
		$dsn = static::$dsn;

		$Driver = Utilities::driverFactory($dsn);

		$initialized = Utilities::initialized($dsn);
		if (false === $initialized)
		{
			Utilities::initialize($dsn);
		}
		$initialized = Utilities::initialized($dsn);
		$this->assertEquals(true, $initialized);

		// Set Request 2 and 3 to queued status so they are grabbed as the next queue items for processing
		foreach ([1,2,3] as $requestID)
		{
			$item = $Driver->queueGet('user1', $requestID, null);
			//fwrite(STDERR, "Item: ". print_r($item,true)."\n");
			$ticket = $item['extra']['ticket_id'];

			$test = $Driver->queueStatus($item['extra']['ticket_id'], $requestID, PackageInfo::Status['QUEUED']);
			$this->assertEquals(true, $test);
		}


		$soapAction = 'http://developer.intuit.com/receiveResponseXML';

		$requests = [
			implode("\n", [
				'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">',
				'  <soap:Body>',
				'    <receiveResponseXML xmlns="http://developer.intuit.com/">',
				'      <ticket>' . $ticket . '</ticket>',
				'      <response>&lt;?xml version="1.0" ?&gt;',
				'&lt;QBXML&gt;',
				'&lt;QBXMLMsgsRs&gt;',
				'&lt;CustomerAddRs requestID="1" statusCode="0" statusSeverity="Info" statusMessage="Status OK"&gt;',
				'&lt;CustomerRet&gt;',
				'&lt;ListID&gt;8000020D-1561739164&lt;/ListID&gt;',
				'&lt;TimeCreated&gt;2019-07-07T07:07:07-07:00&lt;/TimeCreated&gt;',
				'&lt;TimeModified&gt;2019-07-07T07:07:07-07:00&lt;/TimeModified&gt;',
				'&lt;EditSequence&gt;1561739164&lt;/EditSequence&gt;',
				'&lt;Name&gt;My Customer Name #100 -12345&lt;/Name&gt;',
				'&lt;FullName&gt;Parent:My Customer Name -12345&lt;/FullName&gt;',
				'&lt;IsActive&gt;true&lt;/IsActive&gt;',
				'&lt;Sublevel&gt;1&lt;/Sublevel&gt;',
				'&lt;CompanyName&gt;My Company Name&lt;/CompanyName&gt;',
				'&lt;BillAddress&gt;',
				'&lt;Addr1&gt;My Customer Name - Addr line 1&lt;/Addr1&gt;',
				'&lt;Addr2&gt;Customer Department&lt;/Addr2&gt;',
				'&lt;Addr3&gt;Customer Representative&lt;/Addr3&gt;',
				'&lt;Addr4&gt;Street Address - Addr line 2&lt;/Addr4&gt;',
				'&lt;Addr5&gt;APT 500 - Addr line 3&lt;/Addr5&gt;',
				'&lt;City&gt;City&lt;/City&gt;',
				'&lt;State&gt;State&lt;/State&gt;',
				'&lt;PostalCode&gt;90210&lt;/PostalCode&gt;',
				'&lt;/BillAddress&gt;',
				'&lt;BillAddressBlock&gt;',
				'&lt;Addr1&gt;My Customer Name - Addr line 1&lt;/Addr1&gt;',
				'&lt;Addr2&gt;Customer Department&lt;/Addr2&gt;',
				'&lt;Addr3&gt;Street Address - Addr line 2&lt;/Addr3&gt;',
				'&lt;Addr4&gt;APT 500 - Addr line 3&lt;/Addr4&gt;',
				'&lt;Addr5&gt;City, State 90210&lt;/Addr5&gt;',
				'&lt;/BillAddressBlock&gt;',
				'&lt;SalesRepRef&gt;',
				'&lt;ListID&gt;80000008-1541613549&lt;/ListID&gt;',
				'&lt;FullName&gt;SR&lt;/FullName&gt;',
				'&lt;/SalesRepRef&gt;',
				'&lt;Balance&gt;0.00&lt;/Balance&gt;',
				'&lt;TotalBalance&gt;0.00&lt;/TotalBalance&gt;',
				'&lt;AccountNumber&gt;12345&lt;/AccountNumber&gt;',
				'&lt;JobStatus&gt;None&lt;/JobStatus&gt;',
				'&lt;PreferredDeliveryMethod&gt;None&lt;/PreferredDeliveryMethod&gt;',
				'&lt;/CustomerRet&gt;',
				'&lt;/CustomerAddRs&gt;',
				'&lt;/QBXMLMsgsRs&gt;',
				'&lt;/QBXML&gt;',
				'</response>',
				'      <hresult />',
				'      <message />',
				'    </receiveResponseXML>',
				'  </soap:Body>',
				'</soap:Envelope>',
			]),
		];

		$expecteds = [
			implode("\n", [
				'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
				'    <SOAP-ENV:Body>',
				'        <ns1:receiveResponseXMLResponse>',
				'        	<ns1:receiveResponseXMLResult>33</ns1:receiveResponseXMLResult>',
				'        </ns1:receiveResponseXMLResponse>',
				'    </SOAP-ENV:Body>',
				'</SOAP-ENV:Envelope>',
			]),
/*
			implode("\n", [
				'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
				'  <SOAP-ENV:Body>',
				'    <ns1:sendRequestXMLResponse>',
				'      <ns1:sendRequestXMLResult>&lt;?xml version="1.0" encoding="UTF-8"?&gt;',
				'&lt;?qbxml version="13.0" ?&gt;',
				'&lt;QBXML&gt;',
				'  &lt;QBXMLMsgsRq onError="continueOnError"&gt;',
				'    &lt;CustomerAddRq requestID="2"&gt;',
				'      &lt;CustomerAdd&gt;',
				'        &lt;Name&gt;My Customer Name #101 -12345&lt;/Name&gt;',
				'        &lt;CompanyName&gt;My Company Name&lt;/CompanyName&gt;',
				'        &lt;BillAddress&gt;',
				'          &lt;Addr1&gt;My Customer Name - Addr line 1&lt;/Addr1&gt;',
				'          &lt;Addr2&gt;Customer Department&lt;/Addr2&gt;',
				'          &lt;Addr3&gt;Customer Representative&lt;/Addr3&gt;',
				'          &lt;Addr4&gt;Street Address - Addr line 2&lt;/Addr4&gt;',
				'          &lt;Addr5&gt;APT 500 - Addr line 3&lt;/Addr5&gt;',
				'          &lt;City&gt;City&lt;/City&gt;',
				'          &lt;State&gt;State&lt;/State&gt;',
				'          &lt;PostalCode&gt;90210&lt;/PostalCode&gt;',
				'        &lt;/BillAddress&gt;',
				'        &lt;SalesRepRef&gt;',
				'          &lt;FullName&gt;SR&lt;/FullName&gt;',
				'        &lt;/SalesRepRef&gt;',
				'        &lt;AccountNumber&gt;12345&lt;/AccountNumber&gt;',
				'      &lt;/CustomerAdd&gt;',
				'    &lt;/CustomerAddRq&gt;',
				'  &lt;/QBXMLMsgsRq&gt;',
				'&lt;/QBXML&gt;</ns1:sendRequestXMLResult>',
				'    </ns1:sendRequestXMLResponse>',
				'  </SOAP-ENV:Body>',
				'</SOAP-ENV:Envelope>',
			]),

			implode("\n", [
				'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
				'  <SOAP-ENV:Body>',
				'    <ns1:sendRequestXMLResponse>',
				'      <ns1:sendRequestXMLResult>&lt;?xml version="1.0" encoding="UTF-8"?&gt;',
				'&lt;?qbxml version="13.0" ?&gt;',
				'&lt;QBXML&gt;',
				'  &lt;QBXMLMsgsRq onError="continueOnError"&gt;',
				'    &lt;InvoiceAddRq requestID="3"&gt;',
				'      &lt;InvoiceAdd&gt;',
				'        &lt;CustomerRef&gt;',
				'          &lt;ListID&gt;70000007-1234567890&lt;/ListID&gt;',
				'          &lt;FullName&gt;My Customer&lt;/FullName&gt;',
				'        &lt;/CustomerRef&gt;',
				'        &lt;ClassRef&gt;',
				'          &lt;FullName&gt;QuickBooksClass&lt;/FullName&gt;',
				'        &lt;/ClassRef&gt;',
				'        &lt;TemplateRef&gt;',
				'          &lt;FullName&gt;CustomTemplate&lt;/FullName&gt;',
				'        &lt;/TemplateRef&gt;',
				'        &lt;TxnDate&gt;2019-07-04&lt;/TxnDate&gt;',
				'        &lt;RefNumber&gt;10001&lt;/RefNumber&gt;',
				'        &lt;BillAddress&gt;',
				'          &lt;Addr1&gt;My Customer&lt;/Addr1&gt;',
				'          &lt;Addr2&gt;attn: John Doe&lt;/Addr2&gt;',
				'          &lt;Addr3&gt;123 Bill Street&lt;/Addr3&gt;',
				'          &lt;Addr4&gt;APT 5555&lt;/Addr4&gt;',
				'          &lt;City&gt;BillCity&lt;/City&gt;',
				'          &lt;State&gt;NY&lt;/State&gt;',
				'          &lt;PostalCode&gt;10019&lt;/PostalCode&gt;',
				'        &lt;/BillAddress&gt;',
				'        &lt;ShipAddress&gt;',
				'          &lt;Addr1&gt;My Customer&lt;/Addr1&gt;',
				'          &lt;Addr2&gt;attn: John Doe&lt;/Addr2&gt;',
				'          &lt;Addr3&gt;123 Ship Street&lt;/Addr3&gt;',
				'          &lt;Addr4&gt;APT 5555&lt;/Addr4&gt;',
				'          &lt;City&gt;ShipCity&lt;/City&gt;',
				'          &lt;State&gt;NY&lt;/State&gt;',
				'          &lt;PostalCode&gt;10019&lt;/PostalCode&gt;',
				'        &lt;/ShipAddress&gt;',
				'        &lt;PONumber&gt;PO12345&lt;/PONumber&gt;',
				'        &lt;TermsRef&gt;',
				'          &lt;FullName&gt;Net 30&lt;/FullName&gt;',
				'        &lt;/TermsRef&gt;',
				'        &lt;DueDate&gt;2019-08-03&lt;/DueDate&gt;',
				'        &lt;SalesRepRef&gt;',
				'          &lt;FullName&gt;SR&lt;/FullName&gt;',
				'        &lt;/SalesRepRef&gt;',
				'        &lt;IsToBePrinted&gt;false&lt;/IsToBePrinted&gt;',
				'        &lt;IsToBeEmailed&gt;true&lt;/IsToBeEmailed&gt;',
				'        &lt;InvoiceLineAdd&gt;',
				'          &lt;ItemRef&gt;',
				'        	&lt;FullName&gt;Item Full Name&lt;/FullName&gt;',
				'          &lt;/ItemRef&gt;',
				'          &lt;Desc&gt;My item description&lt;/Desc&gt;',
				'          &lt;Amount&gt;250.00&lt;/Amount&gt;',
				'        &lt;/InvoiceLineAdd&gt;',
				'        &lt;InvoiceLineAdd&gt;',
				'          &lt;ItemRef&gt;',
				'        	&lt;FullName&gt;Item2 Full Name&lt;/FullName&gt;',
				'          &lt;/ItemRef&gt;',
				'          &lt;Desc&gt;My item2 description&lt;/Desc&gt;',
				'          &lt;Quantity&gt;125.00&lt;/Quantity&gt;',
				'          &lt;Rate&gt;2.45&lt;/Rate&gt;',
				'        &lt;/InvoiceLineAdd&gt;',
				'      &lt;/InvoiceAdd&gt;',
				'    &lt;/InvoiceAddRq&gt;',
				'  &lt;/QBXMLMsgsRq&gt;',
				'&lt;/QBXML&gt;</ns1:sendRequestXMLResult>',
				'    </ns1:sendRequestXMLResponse>',
				'  </SOAP-ENV:Body>',
				'</SOAP-ENV:Envelope>',
			]),

			implode("\n", [
				'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
				'  <SOAP-ENV:Body>',
				'    <ns1:sendRequestXMLResponse>',
				'      <ns1:sendRequestXMLResult/>',
				'    </ns1:sendRequestXMLResponse>',
				'  </SOAP-ENV:Body>',
				'</SOAP-ENV:Envelope>',
			]),
*/

		];
//fwrite(STDERR, "\nExpecteds:".print_r($expecteds,true));

			//$xmlExpected = $expecteds[0];
//fwrite(STDERR, "\nxmlExpected:".print_r($xmlExpected,true));



		for ($i = 0; $i < count($expecteds); $i++)
		{
			// Save the ticket_id in the queue msg column so it can be retreived in the response handler
			$test = $Driver->queueStatus($ticket, $i+1, PackageInfo::Status['PROCESSING']);
			$this->assertEquals(true, $test);

			$xmlRequest = $requests[$i];
			$xmlExpected = $expecteds[$i];

//fwrite(STDERR, "\nxmlExpected ($i):".print_r($xmlExpected,true));

			//fwrite(STDERR, "\n\nREQUEST ($i):\n$xmlRequest");

			// Handle the request
			$this->WebConnectorServer->setRawRequestInput($xmlRequest);

			// Buffer the output
			ob_start();
			// Handle the mock SOAP request
			//   NOTE: The @ Error Control Operator prevents the PHP Extension SOAP Server from failing due to sending headers in these tests)
			$xmlResponse = @$this->WebConnectorServer->handle(true, true);
			// Get the buffered response
			$output = ob_get_clean();

			//fwrite(STDERR, "\n\nEXPECTED ($i):\n$xmlExpected");
			//fwrite(STDERR, "\n\n\nRESPONSE ($i):\n$xmlResponse");

			$this->commonTests($xmlExpected, $xmlResponse);
			// Test to make sure the output of the entire WebConnectorServer->handle() function matches
			$this->commonTests($xmlExpected, $output);
		}

		// 4 requests (2 customers, 1 invoice, and one to find there's nothing to do)
		//$this->assertEquals(4, $i);
		// Queue for user1 should be empty now
		//$this->assertEquals(0, $q->size('user1'));
	}


	/**
	 * Test SOAP CloseConnection Requests
	 */
	public function testSoapCloseConnection(): void
	{
		$dsn = static::$dsn;
		$Driver = Utilities::driverFactory($dsn);

		$initialized = Utilities::initialized($dsn);
		if (false === $initialized)
		{
			Utilities::initialize($dsn);
		}
		$initialized = Utilities::initialized($dsn);
		$this->assertEquals(true, $initialized);


		$soapAction = 'http://developer.intuit.com/closeConnection';

		$xmlRequest  = implode("\n", [
			'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">',
			'  <soap:Body>',
			'    <closeConnection xmlns="http://developer.intuit.com/">',
			'      <ticket>' . static::$ticket . '</ticket>',
			'    </closeConnection>',
			'  </soap:Body>',
			'</soap:Envelope>',
		]);
		$xmlExpected = implode("\n", [
			'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">',
			'  <SOAP-ENV:Body>',
			'    <ns1:closeConnectionResponse>',
			'      <ns1:closeConnectionResult>Complete!</ns1:closeConnectionResult>',
			'    </ns1:closeConnectionResponse>',
			'  </SOAP-ENV:Body>',
			'</SOAP-ENV:Envelope>',
		]);


		// Handle the request
		//fwrite(STDERR, "\n\nREQUEST:\n$xmlRequest");
		$this->WebConnectorServer->setRawRequestInput($xmlRequest);

		// Buffer the output
		ob_start();
		// Handle the mock SOAP request
		//   NOTE: The @ Error Control Operator prevents the PHP Extension SOAP Server from failing due to sending headers in these tests)
		$xmlResponse = @$this->WebConnectorServer->handle(true, true);
		// Get the buffered response
		$output = ob_get_clean();

		//fwrite(STDERR, "\n\nEXPECTED:\n$xmlExpected");
		//fwrite(STDERR, "\n\n\nRESPONSE:\n$xmlResponse");

		$this->commonTests($xmlExpected, $xmlResponse);
		// Test to make sure the output of the entire WebConnectorServer->handle() function matches
		$this->commonTests($xmlExpected, $output);
	}
}
