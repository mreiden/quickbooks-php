<?php declare(strict_types=1);

namespace SomeNamespace {
    function namespaceTest1(){}
    function namespaceTest2(){}
}



namespace {

// PHPunit Based Test Class
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

// Testing Helper Classes
use GuzzleHttp\Client;
use Symfony\Component\Process\Process;


use \SQLite3 as PhpSQLite3;
//use \SQLite3Result;
//use \SQLite3Stmt;

use QuickBooksPhpDevKit\Adapter\SOAP\Server\AdapterInterface;
use QuickBooksPhpDevKit\Adapter\SOAP\Server\BuiltinAdapter;
use QuickBooksPhpDevKit\Adapter\SOAP\Server\PhpExtensionAdapter;

use QuickBooksPhpDevKit\Callbacks\SQL\Callbacks;
//use QuickBooksPhpDevKit\Driver;
//use QuickBooksPhpDevKit\Driver\Factory;
//use QuickBooksPhpDevKit\Driver\Sql;
//use QuickBooksPhpDevKit\Driver\Sql\Pgsql;
use QuickBooksPhpDevKit\PackageInfo;
//use QuickBooksPhpDevKit\QBXML\Object\Invoice as QbInvoice;
//use QuickBooksPhpDevKit\QBXML\Object\SalesTaxCode;
use QuickBooksPhpDevKit\Utilities;
use QuickBooksPhpDevKit\WebConnector\Server as WebConnectorServer;
use QuickBooksPhpDevKit\WebConnector\Server\SQL as WebConnectorServerSQL;
use QuickBooksPhpDevKit\WebConnector\Queue;
use QuickBooksPhpDevKit\WebConnector\QWC;




// Test functions and classes for User Mapping tests
function globalTest1(){}
function globalTest2(){}
class StaticTest {
    public static function staticTest1(){}
    public static function staticTest2(){}
}
class ObjectTest {
    public function objectTest1(){}
    public function objectTest2(){}
    protected function objectProtected(){}
    private function objectPrivate(){}
}
class Hook {
    public function hookMissing(){}
}
class SubHook extends Hook {
    public function hook(){}
}



final class WebConnectorTest extends XmlBaseTest
{
    protected $__init_options = [
        'quickbooks_sql_enabled' => false,
    ];

    private static $scriptWebConnector;
    private static $soapUrl;

    /** @var Process */
    private static $process;

    private static $dbFile;

    private static $ticket;


    public static function setUpBeforeClass(): void
    {
        self::$scriptWebConnector =  __DIR__ . '/standalone_webconnector.php';

        self::$soapUrl = 'http://127.0.0.1:8080/';

        self::$dbFile = tempnam(sys_get_temp_dir(), 'sqlite3-WebConnectorTest.');


        //self::$process = new Process("php -S 127.0.0.1:8080 -t .");
        //self::$process = new Process('php -S 127.0.0.1:8080 ' . self::$scriptWebConnector);
        self::$process = new Process(['php', '-d', 'variables_order=EGPCS', '-S', '127.0.0.1:8080', self::$scriptWebConnector], null, ['DBFILE' => self::$dbFile]);
        self::$process->start();
        sleep(2); //wait for server to get going
    }

    public static function tearDownAfterClass(): void
    {
        self::$process->stop();
        self::$process = null;
        if (file_exists(self::$dbFile))
        {
            unlink(self::$dbFile);
        }
    }
    public function __destruct()
    {
        if (null !== self::$process)
        {
            self::$process->stop();
        }
    }



    protected function sendSoapRequest(string $soapAction, string $xml, ?string $url = null)
    {
        if (null === $url)
        {
            $url = self::$soapUrl;
        }

        //fwrite(STDERR, "\nXML Request ($url):\n$xml\n");
        $xml = ($this->sanitizeXML($xml))->saveXML();
        //fwrite(STDERR, "\nXML Request Formatted:\n$xml\n");

        $client = new Client();
        $response = $client->request('POST', $url, [
            //'Authenticate' => [
            //    'userName' => $userName,
            //    'password' => $password
            //],
            'body' => $xml,
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => $soapAction,
            ],
            'curl' => [
                //CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V6,
                //CURLOPT_INTERFACE => '::1',

                //CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                //CURLOPT_INTERFACE => '127.0.0.1',
            ],
        ]);

        return $response;
    }


    /**
     * Test standalone php server is up and running and returns a HTTP 200 status code.
     */
    public function testInfoPage(): void
    {
        $client = new Client(['http_errors' => false]);
        $response = $client->request('GET', self::$soapUrl);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test IPv6 standalone php server is up and running and returns a HTTP 200 status code.
     */
    public function testInfoPageV6(): void
    {
        if (getenv('TRAVIS') == 'true)
        {
            $this->markTestSkipped('IPv6 Skipped on Travis because it lacks IPv6 support.');
        }

        try {
            $ip6_8080 = new Process(['php', '-d', 'variables_order=EGPCS', '-S', '[::1]:8080', self::$scriptWebConnector], null, ['DBFILE' => self::$dbFile]);
            $ip6_8080->start();
            $ip6_8888 = new Process(['php', '-d', 'variables_order=EGPCS', '-S', '[::1]:8888', self::$scriptWebConnector], null, ['DBFILE' => self::$dbFile]);
            $ip6_8888->start();
            sleep(2);

            $client = new Client(['http_errors' => false]);
            $response = $client->request('GET', 'http://[::1]:8080/');
            $this->assertEquals(200, $response->getStatusCode());

            $response = $client->request('GET', 'http://[::1]:8888/');
            $this->assertEquals(200, $response->getStatusCode());
        }
        finally
        {
            $ip6_8080->stop();
            $ip6_8888->stop();
        }
    }


    /**
     * Test various combinations of user configured mappings:
     * 1) Action to Request and Response functions.
     * 2) Error number to error handler function.
     * 3) Hooks
     */
    public function testUserMappingsValidation(): void
    {
/*
        $callback = [$this, 'thisPublicMethod'];
        $errmsg ='';
        $test = Utilities::callbackType($callback,$errmsg);
        fwrite(STDERR, "TEST: ".var_export($test, true));

        $map = [
            PackageInfo::Actions['ADD_CUSTOMER'] => //[
                [$this, 'thisPublicMethod'],
            //]
        ];
        WebConnectorServer::validateMappedFunctions($map);
        exit;
*/
        $mappingObjectInstances = [
            'ObjectTest' => new ObjectTest(),
            'Hook' => new Hook(),
            'SubHook' => new SubHook(),
        ];

        // Map QuickBooks actions to handler functions
        $map = [
            PackageInfo::Actions['ADD_CUSTOMER'] => [
                [$this, 'missingMethod'],      // ERROR: Missing Object Method
                [$this, 'thisPublicMethod'],
            ],
            PackageInfo::Actions['MOD_CUSTOMER'] => [
                [$this, 'thisProtectedMethod'],
                [$this, 'thisPrivateMethod'],
            ],

            PackageInfo::Actions['QUERY_RECEIVE_PAYMENT'] => [
                'SomeNamespace\namespaceTest1',
                'SomeNamespace\namespaceTest1',
            ],
            PackageInfo::Actions['ADD_RECEIVE_PAYMENT'] => [
                'globalTest1',
                'globalTest2',
            ],

            PackageInfo::Actions['VOID_TRANSACTION'] => [
                ['StaticTest', 'staticTest1'],
                'StaticTest::staticTest2',
            ],


            PackageInfo::Actions['ADD_BILL'] => [
                [$mappingObjectInstances['ObjectTest'], 'objectTest1'],
                ['ObjectTest', 'objectTest2'],
            ],
            PackageInfo::Actions['MOD_BILL'] => [
                [get_class($mappingObjectInstances['ObjectTest']), 'objectTest1'],
                'ObjectTest::objectTest2',
            ],
            PackageInfo::Actions['QUERY_BILL'] => [
                [get_class($mappingObjectInstances['ObjectTest']), 'objectProtected'], // ERROR: Protected method
                'ObjectTest::objectPrivate',                                           // ERROR: Private method
            ],

            PackageInfo::Actions['ADD_CHECK'] => [
                $mappingObjectInstances['Hook'],      // ERROR: Hook class does not have a hook method (hookMissing instead)
                $mappingObjectInstances['SubHook'],
            ],
            PackageInfo::Actions['MOD_CHECK'] => [
                [$this, 'method', 'EXTRA_WRONG'],     // ERROR: Not a callable class method (must be [instance|classname, methodname])
                ['strlen', 'substr'],                 // ERROR: Two functions instead of a class and method
            ],
            PackageInfo::Actions['QUERY_CHECK'] => [
                [$this, 'thisPublicMethod'],      // ERROR: One class method callback (array size 2) instead of 2 callbacks
            ],
            /*PackageInfo::Actions['MOD_BILL'] => [
                [get_class($mappingObjectInstances['ObjectTest']), 'objectTest1'],
                'ObjectTest::objectTest2'
            ],
            PackageInfo::Actions['QUERY_BILL'] => [
                [get_class($mappingObjectInstances['ObjectTest']), 'objectProtected'],
                'ObjectTest::objectPrivate'
            ],*/
        ];
/*
        // This is entirely optional, use it to trigger actions when an error is returned by QuickBooks
        $errmap = [
            // Catch "your XML is invalid" errors
            '0x80040400' => [$this, '_error_handler_0x80040400'],
            // Catch "string is too long to fit in that field" errors
            3070 => [$this, '_error_handler_3070'],
            // Catch errors with CustomerAdd requests
            'CustomerAdd' => [$this, '_error_customeradd'],
            // Catch errors with CustomerAdd requests
            'CustomerMod' => [$this, '_error_customermod'],
            // Catch errors with InvoiceQuery requests
            'InvoiceQuery' => [$this, '_error_invoicequery'],
            // Catch errors with InvoiceMod requests
            'InvoiceMod' => [$this, '_error_invoicemod'],
            // This is the catch-all, it'll catch any errors the other handlers don't
            '*' => [$this, '_error_handler_catchall'],
        ];

        // The LoginSuccess hook is what kicks things off by queuing all the customers, invoices, payments, etc.
        $hooks = [
            Handlers::HOOK_LOGINSUCCESS => [
                [$this, 'queueData'],
            ],
        ];
*/
        $expected = [
            'BillAdd - Response',
            'BillMod - Request',
            'BillMod - Response',
            'BillQuery - Request',
            'BillQuery - Response',
            'CheckAdd - Request',
            'CheckMod - Request',
            'CheckMod - Response',
            'CheckQuery',
            'CustomerAdd - Request',
        ];

        $invalidErrors = array_keys(WebConnectorServer::validateMappedFunctions($map));
        sort($invalidErrors);
        //fwrite(STDERR, "ERRORS:\n".print_r($invalidErrors,true));
        $this->assertEquals($expected, $invalidErrors);
    }

    public function thisPublicMethod(){}
    public function thisProtectedMethod(){}
    public function thisPrivateMethod(){}



    /**
     * Test for SOAP ServerVersion Request
     */
    public function testSoapServerVersion(): void
    {
        // No database is needed for ServerVersion, so skip initialization

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
            '      <ns1:serverVersionResult>PHP QuickBooks SOAP Server v' . PackageInfo::Package['VERSION'] . ' at ' . self::$soapUrl . '</ns1:serverVersionResult>',
            '    </ns1:serverVersionResponse>',
            '  </SOAP-ENV:Body>',
            '</SOAP-ENV:Envelope>',
        ]);

        $response = $this->sendSoapRequest($soapAction, $xmlRequest);
        $this->assertEquals($response->getStatusCode(), 200);

        $xmlResponse = $response->getBody()->getContents();
        //fwrite(STDERR, "\nRESPONSE for ServerVersion:\n$xmlResponse");
        $this->commonTests($xmlExpected, $xmlResponse);
    }

    /**
     * Test for SOAP ClientVersion Requests
     */
    public function testSoapClientVersion(): void
    {
        //$initialized = Utilities::initialize($this->__db, ['new_link' => true], $this->__init_options);
        //$this->assertEquals($initialized, true);


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

            $response = $this->sendSoapRequest($soapAction, $xmlRequest);
            $this->assertEquals($response->getStatusCode(), 200);

            $xmlResponse = $response->getBody()->getContents();
            //fwrite(STDERR, "\nRESPONSE for ClientVersion:\n$xmlResponse");
            $this->commonTests($xmlExpected, $xmlResponse);
        }
    }

    /**
     * Test for SOAP Authenticate Requests
     */
    public function testSoapAuthenticate(): void
    {
        $dsn = 'sqlite3://localhost/' . self::$dbFile;
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

        $q = new Queue($dsn, 'user1', []);
        $dbId = 100;
        $priority = 1000;
        $extra = [];

        // Add some records to the queue to check the Queue size reported in the Authenticate response
        $test = $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], $dbId, $priority, $extra, 'user1');
        $this->assertEquals(true, $test);
        $test = $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], 101, $priority, $extra, 'user1');
        $this->assertEquals(true, $test);
        $test = $q->enqueue(PackageInfo::Actions['ADD_INVOICE'], 1234, 900, $extra, 'user1');
        $this->assertEquals(true, $test);
        $test = $q->size('user1');
        $this->assertEquals(3, $test);


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
            //fwrite(STDERR, "\n\n" . $xmlRequest);

            $response = $this->sendSoapRequest($soapAction, $xmlRequest);
            $this->assertEquals($response->getStatusCode(), 200);

            $xmlResponse = $response->getBody()->getContents();
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
                        self::$ticket = $ticket;
                        //fwrite(STDERR, "\n\nTicket: " . self::$ticket ."\n");
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
                //fwrite(STDERR, print_r($xmlExpected, true));
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

            $this->commonTests($xmlExpected, $xmlResponse);
        }
    }



    /**
     * Test for SOAP sendRequestXML Requests
     *
     * @depends testSoapAuthenticate
     */
    public function testsendRequestXML(): void
    {
        $dsn = 'sqlite3://localhost/' . self::$dbFile;
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
                '      <ticket>' . self::$ticket . '</ticket>',
                '      <strHCPResponse/>',
                '      <strCompanyFileName>C:\Directory\File.qbw</strCompanyFileName>',
                '      <qbXMLCountry>US</qbXMLCountry>',
                '      <qbXMLMajorVers>13</qbXMLMajorVers>',
                '      <qbXMLMinorVers>0</qbXMLMinorVers>',
                '    </sendRequestXML>',
                '  </soap:Body>',
                '</soap:Envelope>',
            ]);
            //fwrite(STDERR, "\n\nREQUEST ($i):\n$xmlRequest");

            $response = $this->sendSoapRequest($soapAction, $xmlRequest);
            $this->assertEquals($response->getStatusCode(), 200);


            //fwrite(STDERR, "\n\nEXPECTED ($i):\n$xmlExpected");

            $xmlResponse = $response->getBody()->getContents();
            //fwrite(STDERR, "\n\n\nRESPONSE ($i):\n$xmlResponse");

            $this->commonTests($xmlExpected, $xmlResponse);
        }

        // 4 requests (2 customers, 1 invoice, and one to find there's nothing to do)
        $this->assertEquals($i, 4);
        // Queue for user1 should be empty now
        $this->assertEquals($q->size('user1'), 0);
    }


    /**
     * Test for SOAP CloseConnection Requests
     */
    public function testSoapCloseConnection(): void
    {
        $dsn = 'sqlite3://localhost/' . self::$dbFile;
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
            '      <ticket>' . self::$ticket . '</ticket>',
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

        $response = $this->sendSoapRequest($soapAction, $xmlRequest);
        $this->assertEquals($response->getStatusCode(), 200);

        $xmlResponse = $response->getBody()->getContents();
        //fwrite(STDERR, "\nRESPONSE for ClientVersion:\n$xmlResponse");
        $this->commonTests($xmlExpected, $xmlResponse);
    }
}

} // End Namespace
