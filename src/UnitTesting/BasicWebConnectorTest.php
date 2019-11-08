<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit_UnitTesting;

use \DomDocument as DomDocument;
use \SQLite3 as PhpSQLite3;

use QuickBooksPhpDevKit\Callbacks\SQL\Callbacks;
use QuickBooksPhpDevKit\{
    Driver,
    Driver\Sql,
    Driver\Factory,
    PackageInfo,
    Utilities,
};
use QuickBooksPhpDevKit\QBXML\Object\{
    Customer as qbxmlCustomer,
    Invoice as qbxmlInvoice,
    Invoice\DiscountLine as qbxmlDiscountLine,
    Invoice\InvoiceLine as qbxmlInvoiceLine,
    SalesTaxCode,
};
use QuickBooksPhpDevKit\WebConnector\{
    Handlers,
    Server as WebConnectorServer,
    Queue,
};

class BasicWebConnectorTest
{
    private $WebConnectorServer;
    private $dsn;


    public function queueData(?int $requestID, string $user, string $hook, ?string &$err, array $hook_data, array $callback_config): bool
    {
        $dsn =& $this->dsn;

        //fwrite(STDERR, "rID=$requestID, user=$user, hook=$hook, hookdata=".print_r($hook_data,true)."\n");
        if ($user == 'user1')
        {
            $q = new Queue($dsn, 'user1', []);
            $dbId = 100;
            $priority = 1000;
            $extra = ['ticket_id' => $hook_data['ticket']];

            // Add some records to the queue to check the Queue size reported in the Authenticate response
            $test = $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], $dbId, $priority, $extra, 'user1');
            //$this->assertEquals(true, $test);
            $test = $q->enqueue(PackageInfo::Actions['ADD_CUSTOMER'], 150, $priority, $extra, 'user1');
            //$this->assertEquals(true, $test);
            $test = $q->enqueue(PackageInfo::Actions['ADD_INVOICE'], 1234, 900, $extra, 'user1');
            //$this->assertEquals(true, $test);
            //$test = $q->size('user1');
            //$this->assertEquals(3, $test);
        }

        return true;
    }

    public function &getWebConnectorInstance(): WebConnectorServer
    {
        return $this->WebConnectorServer;
    }

    public function __construct(string $soapAdapterClass, Driver $Driver)
    {
        $this->dsn = $Driver;

        // $invoice = $this->invoice('InvoiceAdd');
        // var_dump($this->quickbooksInvoiceRequestXml($invoice, 'InvoiceAdd'));
        // var_dump($this->quickbooksInvoiceRequestXml($invoice, 'InvoiceMod'));
        // $invoice->setTxnID('');
        // var_dump($this->quickbooksInvoiceRequestXml($invoice, 'InvoiceQuery'));
        // exit;

        // Use strict error reporting
        error_reporting(E_ALL | E_STRICT);

        // TimeZone should match the computer running Quickbooks WebConnector or some installations may complain
        //PackageInfo::$TIMEZONE = 'America/New_York';
        PackageInfo::$TIMEZONE = 'America/Chicago';
        //PackageInfo::$TIMEZONE = 'America/Denver';
        //PackageInfo::$TIMEZONE = 'America/Los_Angeles';

        /*
        // Logging level
        //PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['NONE'];
        //PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['NORMAL'];
        //PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['VERBOSE'];
        //PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['DEBUG'];
        PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['DEVELOP'];      // Use this level until you're sure everything works!!!


        // Driver Options are used when creating the Driver, which happens to be passed in as a constructor argument to this class.

        // These really must come before anything is done with the dsn when using SQLite's memory database,
        // otherwise a new one gets used because the instance is based on the dsn and the serialized $driver_options.
        $driver_options = [
            //'log_level' => PackageInfo::LogLevel['VERBOSE'],

            // See the comments in the QuickBooks/Driver/<YOUR DRIVER HERE>.php file ( i.e. 'Mysqli.php', etc. )
            'max_log_history' => 16384,   // Limit the number of quickbooks_log entries
            'max_queue_history' => 1024,   // Limit the number of *successfully processed* quickbooks_queue entries
            'max_ticket_history' => 1024,   // Limit the number of quickbooks_tickets entries
        ];
        */

        //phpinfo();
        //echo $this->dsn;
        //exit;

        $initialized = Utilities::initialized($this->dsn);
        //var_dump($initialized);
        if (false === $initialized)
        {
            Utilities::initialize($this->dsn);
        }
        //var_dump(Utilities::initialized($this->dsn);


        // Which SOAP server you're using
        //$SoapAdapter = new BuiltinAdapter();                // Included Built-In SOAP server (use this one for development at least)
        //$SoapAdapter = new PhpExtensionAdapter();         // PHP SOAP Extension server (not sure the benefits of using this one... maybe slightly faster)
        $SoapAdapter = new $soapAdapterClass();



        // Map QuickBooks actions to handler functions
        $map = [
            PackageInfo::Actions['QUERY_CUSTOMER'] => [
                [$this, '_customer_request'],
                [$this, '_customer_response'],
            ],
            PackageInfo::Actions['ADD_CUSTOMER'] => [
                [$this, '_customer_request'],
                [$this, '_customer_response'],
            ],
            PackageInfo::Actions['MOD_CUSTOMER'] => [
                [$this, '_customer_request'],
                [$this, '_customer_response'],
            ],

            PackageInfo::Actions['QUERY_INVOICE'] => [
                [$this, '_invoice_request'],
                [$this, '_invoice_response'],
            ],
            PackageInfo::Actions['ADD_INVOICE'] => [
                [$this, '_invoice_request'],
                [$this, '_invoice_response'],
                //Utilities::class . '::convertActionToMod',
            ],
            PackageInfo::Actions['MOD_INVOICE'] => [
                [$this, '_invoice_request'],
                [$this, '_invoice_response'],
            ],
/*
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
                [$tests['ObjectTest'], 'objectTest1'],
                ['ObjectTest', 'objectTest2'],
            ],
            PackageInfo::Actions['MOD_BILL'] => [
                [get_class($tests['ObjectTest']), 'objectTest1'],
                'ObjectTest::objectTest2'
            ],
*/
/*
            PackageInfo::Actions['QUERY_RECEIVE_PAYMENT'] => [
                [$this, '_payment_request'],
                [$this, '_payment_response'],
                //'strlen',
                //'substr'
            ],
            PackageInfo::Actions['ADD_RECEIVE_PAYMENT'] => [
                [$this, '_payment_request'],
                //[$this, '_payment_response'],
                Utilities::class . '::convertActionToMod',
                //'Utilities::convertActionToMod',
            ],

            PackageInfo::Actions['VOID_TRANSACTION'] => [
                [$this, '_void_request'],
                [$this, '_void_response'],
            ],
*/
        ];


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

        $handler_options = [
            // See the comments in the QuickBooks/Server/Handlers.php file
            //'authenticate' => ' *** YOU DO NOT NEED TO PROVIDE THIS CONFIGURATION VARIABLE TO USE THE DEFAULT AUTHENTICATION METHOD FOR THE DRIVER YOU'RE USING (I.E.: MYSQLi) *** '
            //'authenticate' => 'your_function_name_here',
            //'authenticate' => ['YourClassName', 'YourStaticMethod'],
            'deny_concurrent_logins' => false,
            'deny_reallyfast_logins' => false,
            'qbwc_min_version' => '2.2.0.71',
            'allow_remote_addr' => [
                '127.0.0.1',
                '127.0.0.2',
                '127.0.0.3',
                '127.0.0.4',
                '::1',
            ],
            'deny_remote_addr' => [
                '127.0.0.3'
            ],
        ];


        // Create the WebConnector server
        $this->WebConnectorServer = new WebConnectorServer($this->dsn, $SoapAdapter, $map, $errmap, $hooks, PackageInfo::$LOGLEVEL, $handler_options);

        // Quickbooks requires the response be text/xml
        //header('Content-Type: text/xml');

        // Handle the request
        //$WebConnectorServer->handle(true, true);
    }

    protected function sanitizeXml(string $xml): ?DomDocument
    {
        $load_options = LIBXML_COMPACT | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOXMLDECL;

        $dom = new DomDocument();
        $dom->preserveWhiteSpace = false;

        $loaded = $dom->loadXML(trim($xml), $load_options);
        if (false === $loaded) {
            return null;
        }

        $dom->formatOutput = true;
        $dom->xmlVersion = '1.0';
        $dom->encoding = 'UTF-8';

        return $dom;
    }

    private function addQbXmlWrapper(string $xmlRequest): string
    {
        $xml = implode("\n", [
            '<?qbxml version="13.0" ?>',
            '<QBXML>',
            '  <QBXMLMsgsRq onError="continueOnError">',
            "     $xmlRequest",
            '  </QBXMLMsgsRq>',
            '</QBXML>',
        ]);

        return ($this->sanitizeXml($xml))->saveXML();
    }




    /**
     * Create a QBXML Customer Object for Testing
     */
    public function &customer(string $customerName = 'My Customer Name'): qbxmlCustomer
    {
        //$accountNumber = 12345;

        $qbCustomer = new qbxmlCustomer();
        //// Quickbooks Specific Fields
        // Customer Type
        //$customer->setCustomerTypeFullName('Web:Direct');

        // ListID is Quickbooks Unique Identifier
        $qbCustomer->setListId('8000008B-1286207897');
        // EditSequence is a Secondary Quickbooks Unique Identifier
        $qbCustomer->setEditSequence(1191523643);

        // General Information Fields
        $qbCustomer->setAccountNumber(12345);

        $qbCustomer->setSalesRepName('SR');

        // This is the Customer Name that shows in QuickBook's customer list
        // I like to include my web app's id
        $qbCustomer->setFullName("$customerName -12345");

        // This is the Company name for the customer
        $qbCustomer->setCompanyName('My Company Name');

        $qbCustomer->setBillAddress(
            // Address Line 1
            'My Customer Name - Addr line 1',
            // Address Line 2 | default: ''
            'Customer Department',
            // Address Line 3 | default: ''
            'Customer Representative',
            // Address Line 4 | default: ''
            'Street Address - Addr line 2',
            // Address Line 5 | default: ''
            'APT 500 - Addr line 3',
            // City | default: ''
            'City',
            // State | default: ''
            'State',
            // County | default: ''
            '',
            // Zip Code
            '90210',
            // Country
            ''//'USA'
            // Address Notes?
            //'Address Notes'
        );
        //$qbCustomer->setNotes('Test notes go here.');
        //var_dump($qbCustomer);

        return $qbCustomer;
    }


    /**
     * Create the actual QBXML Customer based on the action (CustomerAdd, CustomerMod, CustomerQuery)
     */
    private function quickbooksCustomerRequestXml(qbxmlCustomer &$customer, $request_type): string
    {
        switch ($request_type) {
            case PackageInfo::Actions['QUERY_CUSTOMER']:
            case PackageInfo::Actions['ADD_CUSTOMER']:
            case PackageInfo::Actions['MOD_CUSTOMER']:
                // Valid Request Type
                break;

            default:
                throw new \Exception('Customer Request of Type "' . $request_type . '" Is Not Valid');
        }

        return $this->addQbXmlWrapper($customer->asQBXML($request_type, null, PackageInfo::Locale['UNITED_STATES']));
    }



    /*
     * @param string $requestID                 You should include this in your qbXML request (it helps with debugging later)
     * @param string $action                    The QuickBooks action being performed (CustomerAdd in this case)
     * @param mixed $ID                         The unique identifier for the record (maybe a customer ID number in your database or something)
     * @param array $extra                      Any extra data you included with the queued item when you queued it up
     * @param string $err                       An error message, assign a value to $err if you want to report an error
     * @param integer $last_action_time         A unix timestamp (seconds) indicating when the last action of this type was dequeued
     *                                          (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param integer $last_actionident_time    A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued
     *                                          (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param float $version                    The max qbXML version your QuickBooks version supports
     * @param string $locale
     * @return string                           A valid qbXML request
     */
    public function _customer_request(string $requestID, string $user, string $action, $ID, array $extra, ?string &$err, ?int $last_action_time, ?int $last_actionident_time, float $version, string $locale): string
    {
        $qbCustomer = $this->customer("My Customer Name #$ID");
        $listId = '8000008B-1286207897';

        if ($action === PackageInfo::Actions['QUERY_CUSTOMER']) {
            if (!empty($extra['queryByFullName'])) {
                $qbCustomer->setListID('');
            } elseif (!empty($listId)) {
                // CustomerQuery can only use listID or FullName, not both
                $qbCustomer->setFullName('');
            }
            //var_dump($qbCustomer);
        }

        //$qbXml = $this->quickbooksCustomerRequestXml($qbCustomer, $this->actionToQuickbooksConstant($action));
        $qbXml = $this->quickbooksCustomerRequestXml($qbCustomer, $action);
        //var_dump($qbXml);
        $contents = "$action \n" . var_export($qbCustomer, true). "\n\n";
        $contents .= $qbCustomer->asQBXML(PackageInfo::Actions['QUERY_CUSTOMER'], null, PackageInfo::Locale['UNITED_STATES']) ."\n\n -- \n$qbXml";

        return $qbXml;
    }

    /**
     * Receive a response from QuickBooks
     *
     * @param string $requestID                 The requestID you passed to QuickBooks previously
     * @param string $action                    The action that was performed (CustomerAdd in this case)
     * @param mixed $ID                         The unique identifier of the record
     * @param array $extra
     * @param string $err                       An error message, assign a valid to $err if you want to report an error
     * @param integer $last_action_time         A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param integer $last_actionident_time    A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param string $xml                       The complete qbXML response
     * @param array $idents                     An array of identifiers that are contained in the qbXML response
     * @return void
     */
    public function _customer_response(int $requestID, string $user, string $action, $ID, ?array $extra, &$err, $last_action_time, $last_actionident_time, $xml, array $idents): void
    {
        // Save ListID and EditSequence
        //$company = $this->repCompany->find($ID);
        //$company
        //    ->setListID($idents['ListID'])
        //    ->setEditSequence($idents['EditSequence'])
        //;
        //$this->em->persist($company);
        //$this->em->flush();

        $tmp = [
            'requestID'              => $requestID,
            'user'                   => $user,
            'action'                 => $action,
            'ID'                     => $ID,
            'extra'                  => $extra,
            'err'                    => $err,
            'last_action_time'       => $last_action_time,
            'last_actionident_time'  => $last_actionident_time,
            'xml'                    => $xml,
            'idents'                 => $idents,
        ];
        $subject = 'QuickBooks SOAP Customer Added - ' . date('Y-m-d H:i:s');

        $item = $this->dsn->queueGet($user, $requestID, null);
        $msg = [
            'idents' => $tmp,
        ];

        $err = 0;
        $errmsg = '';
        $this->dsn->query(
            'UPDATE '. Sql::$TablePrefix['BASE'] . Sql::$Table['QUEUE'] . "
            SET msg = '" . $this->dsn->escape(json_encode($idents)) . "'
            WHERE quickbooks_queue_id = $requestID", $errnum, $errmsg);
    }

    public function _error_customeradd($requestID, $user, $action, $ident, $extra, &$err, $xml, $errnum, $errmsg)
    {
        if ($errnum == 3100) {
            // Customer already exists
            $tmp = [
                'requestID'  => $requestID,
                'user'       => $user,
                'action'     => $action,
                'ident'      => $ident,
                'extra'      => $extra,
                'err'        => $err,
                'xml'        => $xml,
                'errnum'     => $errnum,
                'errmsg'     => $errmsg,
            ];

            // Queury the customer to update its quickbooks IDs
            $this->qbQueue->enqueue(PackageInfo::Actions['QUERY_CUSTOMER'], $ident, 1500, $extra, $user, $qbxml = null, true);

            return true;
        }

        return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
    }

    public function _error_customermod(int $requestID, string $user, string $action, $ident, $extra, &$err, string $xml, $errnum, $errmsg)
    {
        $doQueryErrors = [
            3200,
            3120, // Customer ListID cannot be found
        ];
        if (in_array($errnum, $doQueryErrors)) {
            $tmp = [
                'requestID'  => $requestID,
                'user'       => $user,
                'action'     => $action,
                'ident'      => $ident,
                'extra'      => $extra,
                'err'        => $err,
                'xml'        => $xml,
                'errnum'     => $errnum,
                'errmsg'     => $errmsg
            ];

            if ($errnum == 3120) {
                // ListID could not be found, so query by name to update IDs
                $extra['queryByFullName'] = true;
            }
            $this->qbQueue->enqueue(PackageInfo::Actions['QUERY_CUSTOMER'], $ident, 1500, $extra, $user, $qbxml = null, true);

            return true;
        }

        return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
    }






    /**
     * Create a QBXML Invoice Object for Testing
     */
    public function &invoice(string $action, string $customerFullName = 'My Customer')
    {
        $qbInvoice = new qbxmlInvoice();

        $qbInvoice->setIsToBePrinted(false);
        $qbInvoice->setisToBeEmailed(true);

        // Sets the Quickbooks Class (Business Segment)
        $qbInvoice->setClassName('QuickBooksClass');
        $qbInvoice->setTermsName('Net 30');
        $qbInvoice->setTemplateName('CustomTemplate');
        $qbInvoice->setIncludeLinkedTxns(true);

        // Invoice Details (Number, Dates, PO, SalesRep)
        $qbInvoice->setRefNumber('10001');
        $qbInvoice->setTxnDate('2019-07-04');
        $qbInvoice->setDueDate('2019-08-03');
        $qbInvoice->setPONumber('PO12345');
        $qbInvoice->setSalesRepName('SR');

        $qbInvoice->setCustomerListID('70000007-1234567890');
        $qbInvoice->setCustomerFullName($customerFullName);

        // QuickBooks addresses cannot exceed 5 lines, so you can use:
        //  1) Address lines 1-3 plus city, state, zip, and country
        // *2) Address lines 1-4 plus city, state, and zip
        //  3) Address lines 1-5 without specifying any other fields (No city, state, zip, province, or country)
        $qbInvoice->setBillAddress(
            'My Customer',        // Address Line 1
            'attn: John Doe',     // Address Line 2
            '123 Bill Street',    // Address Line 3
            'APT 5555',           // Address Line 4
            '',                   // Address Line 5
            'BillCity',           // City
            'NY',                 // State
            '',                   // Province
            '10019',              // Postal Code
            '',                   // Country
            ''                    // Note for Address
        );
        $qbInvoice->setShipAddress(
            'My Customer',        // Address Line 1
            'attn: John Doe',     // Address Line 2
            '123 Ship Street',    // Address Line 3
            'APT 5555',           // Address Line 4
            '',                   // Address Line 5
            'ShipCity',           // City
            'NY',                 // State
            '',                   // Province
            '10019',              // Postal Code
            '',                   // Country
            ''                    // Note for Address
        );

        // Need for InvoiceMod requests
        //$qbInvoice->setListID('80000181-1234567890');
        $qbInvoice->setTxnID('7623-1234567890');
        $qbInvoice->setEditSequence('9876543210');


        // Include linked transactions (payments received & credit memos)
        $qbInvoice->setIncludeLinkedTxns(true);



        // Add line items
        $line_items = [

            [
                'fullName' => 'Item Full Name',
                'description' => 'My item description',
                'amount' => '250.00',
            ],

            [
                'fullName' => 'Item2 Full Name',
                'description' => 'My item2 description',
                'qty' => '125',
                'rate' => '2.45',
            ],

        ];
        foreach ($line_items as $item)
        {
            $line = new qbxmlInvoiceLine();
            $line->setItemFullName($item['fullName']);
            $line->setDesc($item['description']);

            // Set item Quantity and Rate or an Amount
            if (array_key_exists('qty', $item) && array_key_exists('rate', $item))
            {
                $line->setQuantity($item['qty']);
                $line->setRate($item['rate']);
            }
            elseif (array_key_exists('amount', $item))
            {
                $line->setAmount($item['amount']);
            }
            else
            {
                throw new \Exception('Invoice Line Item must have an amount or a qty and rate.');
            }

            //if ($action === QUICKBOOKS_MOD_INVOICE) {
                // A TnxLineID of -1 means add a new line item.
                $line->setTxnLineID(-1);
            //}

            $qbInvoice->addInvoiceLine($line);
        }


        // Add Discounts
        //$discounts = [
        //    [
        //        'AccountNameFull' => 'Discounts:Discount1',
        //        'Amount' => '35.45',
        //    ],
        //    [
        //        'AccountNameFull' => 'Discounts:Discount2',
        //        'Amount' => 125 * 0.05,
        //    ],
        //];
        //foreach ($discounts as $discount)
        //{
        //    $line = new qbxmlDiscountLine();
        //    $line->setAccountName($discount['AccountNameFull']);
        //    $line->setAmount($discount['Amount']);
        //    $qbInvoice->addDiscountLine($line);
        //}

/*

        if ($action === QUICKBOOKS_MOD_INVOICE) {
            // These are required to modify an existing invoice
            $qbInvoice->setTxnID($invoice->getTxnId());
            $qbInvoice->setEditSequence($invoice->getEditSequence());
        } elseif ($action === QUICKBOOKS_QUERY_INVOICE) {
            $qbInvoice->setIncludeLinkedTxns(true);
        }


        if (!empty($data['companyListId'])) {
            $qbInvoice->setCustomerListID($data['companyListId']);
        } else {
            $qbInvoice->setCustomerFullName($this->getQbNameAccount($invoice->getCompanyName(), $invoice->getCompany()->getId()));
        }

        $qbInvoice->setBillAddress(
            // Address Line 1
            $data['address'][0],
            // Address Line 2 | default: ''
            $data['address'][1],
            // Address Line 3 | default: ''
            $data['address'][2] ?? '',
            // Address Line 4 | default: ''
            $data['address'][3] ?? '',
            // Address Line 5 | default: ''
            $data['address'][4] ?? '',
            // City | default: ''
            $data['city'],
            // State | default: ''
            $data['state'],
            // County | default: ''
            '',
            // Zip Code
            $data['zip'],
            // Country
            ''//'USA'
            // Address Notes?
            //'Address Notes'
        );

        // Add line items
        foreach ($data['items'] as $item) {
            $line = new QbInvoiceLine();
            $line->setItemFullName($item['fullName']);
            $line->setDesc($item['description']);

            if (array_key_exists('qty', $item) && array_key_exists('rate', $item)) {
                $line->setQuantity($item['qty']);
                $line->setRate($item['rate']);
            } elseif (array_key_exists('amount', $item)) {
                $line->setAmount($item['amount']);
            } else {
                throw new \Exception('Invoice Line Item must have an amount or a qty and rate.');
            }

            if ($action === QUICKBOOKS_MOD_INVOICE) {
                // A TnxLineID of -1 means add a new line item.
                $line->setTxnLineID('-1');
            }

            $qbInvoice->addInvoiceLine($line);
        }
*/
        return $qbInvoice;
    }

    private function quickbooksInvoiceRequestXml(qbxmlInvoice &$invoice, $request_type): string
    {
        switch ($request_type) {
            case PackageInfo::Actions['QUERY_INVOICE']:
            case PackageInfo::Actions['ADD_INVOICE']:
            case PackageInfo::Actions['MOD_INVOICE']:
                // Valid Request Type
                break;

            default:
                throw new \Exception('Invoice Request of Type ' . $request_type . ' Not Valid');
        }

        return $this->addQbXmlWrapper($invoice->asQBXML($request_type, null, PackageInfo::Locale['UNITED_STATES']));
    }

    /*
     * @param string $requestID                 You should include this in your qbXML request (it helps with debugging later)
     * @param string $action                    The QuickBooks action being performed (InvoiceQuery in this case)
     * @param mixed $ID                         The unique identifier for the record (maybe an invoice ID number in your database or something)
     * @param array $extra                      Any extra data you included with the queued item when you queued it up
     * @param string $err                       An error message, assign a value to $err if you want to report an error
     * @param integer $last_action_time         A unix timestamp (seconds) indicating when the last action of this type was dequeued
     *                                          (i.e.: for InvoiceAdd, the last time an invoice was added, for InvoiceQuery, the last time an InvoiceQuery ran, etc.)
     * @param integer $last_actionident_time    A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued
     *                                          (i.e.: when the last time an InvoiceQuery with ident of get-new-invoices was dequeued)
     * @param float $version                    The max qbXML version your QuickBooks version supports
     * @param string $locale
     * @return string                           A valid qbXML request
     */
    public function _invoice_request(int $requestID, string $user, string $action, string $ID, array $extra, ?string &$err, ?int $last_action_time, ?int $last_actionident_time, float $version, string $locale): string
    {
        $invoice = $this->invoice($action, 'My Customer Name');

        //$qbInvoice = $this->qbInvoice($invoice, $action);
        //var_dump($qbInvoice);

        $qbXml = $this->quickbooksInvoiceRequestXml($invoice, $action);
        //var_dump($qbXml);

        return $qbXml;
    }

    public function _invoice_response(int $requestID, string $user, string $action, string $ID, $extra, &$err, $last_action_time, $last_actionident_time, string $xml, array $idents): void
    {
        $ID = (int) $ID;
        $invoice = $this->repInvoice->find($ID);
        if ($invoice === null) {
            throw $this->createNotFoundException("No invoice found for id $ID.");
        }

        $invoice
            ->setListID($idents['ListID'])
            ->setEditSequence($idents['EditSequence'])
            ->setTxnId($idents['TxnID'])
        ;
        $this->em->persist($invoice);
        $this->em->flush();

        $tmp = [
            'requestID'              => $requestID,
            'user'                   => $user,
            'action'                 => $action,
            'ID'                     => $ID,
            'extra'                  => $extra,
            'err'                    => $err,
            'last_action_time'       => $last_action_time,
            'last_actionident_time'  => $last_actionident_time,
            'xml'                    => $xml,
            'idents'                 => $idents,
        ];
        $subject = 'QuickBooks SOAP Invoice Added - ' . date('Y-m-d H:i:s');
        $message = '';
        file_put_contents('/tmp/qb_invoice_response.log', "$subject\n\n$message\n\n\n".print_r($tmp, true));

        if (!empty($extra['voidNow'])) {
            $this->qbQueue->enqueue(QUICKBOOKS_VOID_TRANSACTION, $invoice->getId(), 650, ['TxnType' => 'Invoice', 'TxnID' => $idents['TxnID']], $this->qbUser, $qbxml = null, true);

        } elseif (!empty($extra['isModQuery'])) {
            $this->qbQueue->enqueue(QUICKBOOKS_MOD_INVOICE, $invoice->getId(), 625, [], $this->qbUser, $qbxml = null, true);

        } elseif (!empty($extra['isPaymentQuery'])) {
            if (empty($extra['transId'])) {
                 throw $this->createNotFoundException('$extra[\'transId\'] not set in '. __METHOD__ .' on line '. __LINE__);
            }
            $payment = $this->repTransaction->find($extra['transId']);
            if ($payment === null) {
                 throw $this->createNotFoundException("Transaction ID {$extra['transId']} does not exist.");
            }

            $payments = false;
            $qbXml = new \SimpleXMLElement($xml);

            $addPayment = true;
            $paymentsXML = $qbXml->xpath('//LinkedTxn');
            $numPayments = count($paymentsXML);
            for ($i = 0; $i < $numPayments; $i++) {
                // Check if this is a duplicate payment (same date, amount, and payment source)
                //echo "<hr>$i: ". $paymentsXML->item($i)->getElementsByTagName('TxnType')->item(0)->nodeValue;
                if ((string) $paymentsXML[$i]->TxnType === 'ReceivePayment') {
                    $TxnID = (string) $paymentsXML[$i]->TxnID;
                    $txnDate = new \DateTime((string) $paymentsXML[$i]->TxnDate);
                    $amount = abs((float) $paymentsXML[$i]->Amount);
                    $refNumber = (count($paymentsXML[$i]->RefNumber) === 0) ? '' : (string) $paymentsXML[$i]->RefNumber;

                    $sourceToken = explode('_', $payment->getSourceToken(), 2);
                    if (
                        ($txnDate->format('Y-m-d') === $payment->getTransDate()->format('Y-m-d'))
                        && ($amount === $payment->getAmount())
                        && (strpos($sourceToken[1], $refNumber) === 0) // Quickbooks only stores 20 characters, so check if sourceToken begins with $refNumber
                    ) {
                        $addPayment = false;
                        $payment->setQbIdent($TxnID);
                        $this->em->persist($invoice);
                        $this->em->flush();
                        break;
                    }
                }
            }
/*
            $qb = $this->em->createQueryBuilder();
            $qb
                ->select('t')
                ->from('App:Transaction', 't')
                ->join('t.invoice', 'i')
                ->join('i.company', 'c')
                ->andWhere($qb->expr()-neq('t.qb_ident', ''))
                ->andWhere($qb->expr()->like('t.qb_ident', $qb->expr()->literal('2018-11-13%')))
                ->andWhere($qb->expr()->eq('t.amount', ':amount'))
                ->andWhere($qb->expr()->eq('t.chargeToken', ':chargeToken'))
                ->orderBy('t.transDate', 'ASC')
                ->addOrderBy('t.chargeToken', 'ASC')
                ->addOrderBy('t.amount', 'ASC')
                //->setMaxResults(5)
                ->setParameters(['amount' => 5.00, 'chargeToken' => '_dmtcheck_56789']);
* /
            // Check if this payment has already been applied and discard if the date, number, and amount match

            //cast(p.trans_time as timestamp(0)) as trans_date,
            //p.invoice_id,
            //p.trans_type,
            //p.authcode,
            //p.amt,

            //$extra['payment_lookups'] = $payments;

            //$this->qbQueue->enqueue('ReceivePaymentQuery', $extra['trans_id'], 350, $extra, $this->qbUser, $qbxml = null, true);
            if ($addPayment === true) {
                $this->qbQueue->enqueue(QUICKBOOKS_ADD_RECEIVE_PAYMENT, $extra['transId'], 600, $extra, $this->qbUser, $qbxml = null, true);
            }
*/
        }
    }


    public function _error_invoicequery($requestID, string $user, string $action, $ident, $extra, &$err, $xml, $errnum, $errmsg)
    {
        if ($errnum == 500) {
            $tmp = [
                'requestID'  => $requestID,
                'user'       => $user,
                'action'     => $action,
                'ident'      => $ident,
                'extra'      => $extra,
                'err'        => $err,
                'xml'        => $xml,
                'errnum'     => $errnum,
                'errmsg'     => $errmsg
            ];

            // Invoice does not exist, let's Queue the invoice for addition
            $this->qbQueue->enqueue(PackageInfo::Actions['ADD_INVOICE'], $ident, 750, $extra, $this->qbUser, $qbxml = null, true);

            return true;
        }

        return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
    }

    public function _error_invoicemod($requestID, string $user, string $action, $ident, $extra, &$err, $xml, $errnum, $errmsg)
    {
        $invoice = $this->repInvoice->find($ident);
        if ($invoice === null) {
            throw $this->createNotFoundException("Invoice $ident does not exist.");
        }

        if ($errnum == 3200) {
            // Edit Sequence is out-of-date.
            $tmp = [
                'requestID'  => $requestID,
                'user'       => $user,
                'action'     => $action,
                'ident'      => $ident,
                'extra'      => $extra,
                'err'        => $err,
                'xml'        => $xml,
                'errnum'     => $errnum,
                'errmsg'     => $errmsg
            ];

            $qbXml = new \SimpleXMLElement($xml);
            $items = $qbXml->xpath('//InvoiceRet');
            if (count($items) == 1) {
                // Update the QB IDs and retry the InvoiceMod request
                foreach (['TxnID', 'EditSequence', 'ListID'] as $key) {
                    $val = (string) $items[0]->$key;
                    $method = 'set' . str_replace('ID', 'Id', $key);
                    $invoice->$method($val);
                }
                $this->em->persist($invoice);
                $this->em->flush();
                $this->qbQueue->enqueue(PackageInfo::Actions['MOD_INVOICE'], $ident, 600, [], $this->qbUser, $qbxml = null, true);

            } else {
                // Query the invoice to update its QuickBooks IDs
                $this->qbQueue->enqueue(PackageInfo::Actions['QUERY_INVOICE'], $ident, 750, ['isModQuery' => true], $this->qbUser, $qbxml = null, true);
            }

            return true;
        }

        return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
    }

    public function _error_handler_0x80040400($requestID, $user, $action, $ident, $extra, &$err, $xml, $errnum, $errmsg)
    {
        return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
    }
    public function _error_handler_3070($requestID, $user, $action, $ident, $extra, &$err, $xml, $errnum, $errmsg)
    {
        return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
    }

    public function _error_handler_catchall($requestID, $user, $action, $ident, $extra, &$err, $xml, $errnum, $errmsg)
    {
        return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
    }
    public function _error_handler_generic($handler, $requestID, $user, $action, $ident, $extra, &$err, $xml, $errnum, $errmsg)
    {
        $tmp = [
            'handler'    => $handler,
            'requestID'  => $requestID,
            'user'       => $user,
            'action'     => $action,
            'ident'      => $ident,
            'extra'      => $extra,
            'err'        => $err,
            'xml'        => $xml,
            'errnum'     => $errnum,
            'errmsg'     => $errmsg
        ];

        $subject = 'QuickBooks SOAP Server Error - ' . date('Y-m-d H:i:s');
        $message = '';

        $message .= 'The error handler callback function that caught this error is: ' . $handler . "\n";
        $message .= 'User: ' . $user . "\n";
        $message .= 'Action: ' . $action . "\n";
        $message .= 'Ident: ' . $ident . "\n";
        $message .= 'Date/Time: ' . date('Y-m-d H:i:s') . "\n";
        $message .= 'Error Num.: ' . $errnum . "\n";
        $message .= 'Error Message: ' . $errmsg . "\n";
        $message .= "\n";
        $message .= $xml;

        return false;
    }
}
