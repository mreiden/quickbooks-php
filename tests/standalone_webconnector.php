<?php declare(strict_types=1);

namespace SomeNamespace {
	function namespaceTest1(){}
	function namespaceTest2(){}
}

namespace {
	// Global Namespace


	// Composer Autoloader
	require(dirname(__DIR__) . '/vendor/autoload.php');


	use QuickBooksPhpDevKit\Adapter\SOAP\Server\AdapterInterface;
	use QuickBooksPhpDevKit\Adapter\SOAP\Server\BuiltinAdapter as SoapAdapter;
	//use QuickBooksPhpDevKit\Adapter\SOAP\Server\PhpExtensionAdapter as SoapAdapter;

	use QuickBooksPhpDevKit\Callbacks\SQL\Callbacks;
	use QuickBooksPhpDevKit\Driver\Sql;
	use QuickBooksPhpDevKit\PackageInfo;
	use QuickBooksPhpDevKit\QBXML\Object\Customer as qbxmlCustomer;
	use QuickBooksPhpDevKit\QBXML\Object\Invoice as qbxmlInvoice;
	use QuickBooksPhpDevKit\QBXML\Object\Invoice\InvoiceLine as qbxmlInvoiceLine;
	use QuickBooksPhpDevKit\QBXML\Object\SalesTaxCode;
	use QuickBooksPhpDevKit\Utilities;
	use QuickBooksPhpDevKit\WebConnector\Handlers;
	use QuickBooksPhpDevKit\WebConnector\Server as WebConnectorServer;
	use QuickBooksPhpDevKit\WebConnector\Server\SQL as WebConnectorServerSQL;
	use QuickBooksPhpDevKit\WebConnector\Queue;
	use QuickBooksPhpDevKit\WebConnector\QWC;

	use \SQLite3 as PhpSQLite3;
	use QuickBooksPhpDevKit\Driver\Factory;



	// This file is the standalone php server used for the WebConnectorTest.
	// dbFile is set via ENV command line option in WebConnectorTest.php
	//$dbFile = '/tmp/sqlite3.standalone_webconnector';
	if (empty($dbFile))
	{
		if (empty($_ENV['DBFILE']))
		{
			throw new \Exception('No DBFILE environment variable passed to allow us to clean up after the test is done.');
		}

		$dbFile = realpath($_ENV['DBFILE']);
		if (false === $dbFile)
		{
			throw new \Exception('DBFILE "' . $dbFile . '" cannot be found.');
		}
	}

	$WebConnector = new BasicWebConnector();
	$WebConnector->setupAndRun('sqlite://localhost/'. $dbFile);



	function globalTest1(){}
	function globalTest2(){}
	class StaticTest {
		public static function staticTest1(){}
		public static function staticTest2(){}
	}
	class ObjectTest {
		public function objectTest1(){}
		public function objectTest2(){}
	}
	class Hook {
		public function hook(){}
	}
	class SubHook {
		public function hook(){}
	}


	class BasicWebConnector
	{
		public function queueData(){}


		public function setupAndRun($dsn)
		{
			$tests = [
				'ObjectTest' => new ObjectTest(),
				'Hook' => new Hook(),
				'SubHook' => new SubHook(),
			];


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
			PackageInfo::$LOGLEVEL = PackageInfo::LogLevel['DEVELOP'];		// Use this level until you're sure everything works!!!

			// These really must come before anything is done with the dsn when using SQLite's memory database,
			// otherwise a new one gets used because the instance is based on the dsn and the serialized $driver_options.
			$driver_options = [
				// See the comments in the QuickBooks/Driver/<YOUR DRIVER HERE>.php file ( i.e. 'Mysqli.php', etc. )
				'max_log_history' => 16384,   // Limit the number of quickbooks_log entries
				'max_queue_history' => 1024,   // Limit the number of *successfully processed* quickbooks_queue entries
				'max_ticket_history' => 1024,   // Limit the number of quickbooks_tickets entries
			];

			$initialized = Utilities::initialized($dsn, array_merge($driver_options, ['new_link' => true]));
			//var_dump($initialized);
			if (false === $initialized)
			{
				Utilities::initialize($dsn, $driver_options);
			}
			//var_dump(Utilities::initialized($dsn, $driver_options));


			// Which SOAP server you're using
			$SoapAdapter = new SoapAdapter();	// SoapAdapter is defined by the use definition of SoapAdapter (either BuiltinAdapter or PhpExtensionAdapter)

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

			$callback_options = [
			];


			// Create the WebConnector server
			$WebConnectorServer = new WebConnectorServer($dsn, $SoapAdapter, $map, $errmap, $hooks, PackageInfo::$LOGLEVEL, $handler_options, $driver_options, $callback_options);

			// Quickbooks requires the response be text/xml
			header('Content-Type: text/xml; charset=utf-8');

			// Handle the request
			$WebConnectorServer->handle(true, true);
		}

		protected function sanitizeXml(string $xml): ?DomDocument
		{
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = false;

			$loaded = $dom->loadXML(trim($xml));
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
			$qbCustomer = new qbxmlCustomer();

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
		public function _customer_response(int $requestID, string $user, string $action, string $ID, array $extra, ?string &$err, ?int $last_action_time, ?int $last_actionident_time, string $xml, array $idents): void
		{
			// Save ListID and EditSequence
			//$company = $this->repCompany->find($ID);
			//$company
			//	->setListID($idents['ListID'])
			//	->setEditSequence($idents['EditSequence'])
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
		}

		public function _error_customeradd(int $requestID, string $user, string $action, $ident, array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg): bool
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

		public function _error_customermod(int $requestID, string $user, string $action, $ident, array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg): bool
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
		public function &invoice(string $action): qbxmlInvoice
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
			$qbInvoice->setCustomerFullName('My Customer');

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

				// Set the Transaction Line ID to -1 (Replace existing line items with new ones in a Mod Action)
				$line->setTxnLineID(-1);

				$qbInvoice->addInvoiceLine($line);
			}

			return $qbInvoice;
		}

		private function quickbooksInvoiceRequestXml(qbxmlInvoice &$invoice, string $request_type): string
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
			$invoice = $this->invoice($action);

			$qbXml = $this->quickbooksInvoiceRequestXml($invoice, $action);
			//var_dump($qbXml);

			return $qbXml;
		}

		public function _invoice_response(int $requestID, string $user, string $action, string $ID, array $extra, ?string &$err, ?int $last_action_time, ?int $last_actionident_time, string $xml, array $idents): void
		{
			/*
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
			*/

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

			if (!empty($extra['voidNow'])) {
				$this->qbQueue->enqueue(PackageInfo::Actions['VOID_TRANSACTION'], $invoice->getId(), 650, ['TxnType' => 'Invoice', 'TxnID' => $idents['TxnID']], $this->qbUser, $qbxml = null, true);

			} elseif (!empty($extra['isModQuery'])) {
				$this->qbQueue->enqueue(PackageInfo::Actions['MOD_INVOICE'], $invoice->getId(), 625, [], $this->qbUser, $qbxml = null, true);

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
					}
				}
			}
		}


		public function _error_invoicequery(int $requestID, string $user, string $action, $ident, array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg): bool
		{
			if ($errnum == 500) {
				// Invoice does not exist.  let's Queue the invoice for addition

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

				$this->qbQueue->enqueue(PackageInfo::Actions['ADD_INVOICE'], $ident, Utilities::priorityForAction(PackageInfo::Actions['ADD_INVOICE']), $extra);

				return true;
			}

			return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
		}

		public function _error_invoicemod(int $requestID, string $user, string $action, $ident, array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg): bool
		{
			if ($errnum == 3200) {
				// Edit Sequence is out-of-date.

				$InvoiceModPriority = Utilities::priorityForAction(PackageInfo::Actions['MOD_INVOICE']);

				// Queue a query for this invoice so it updates the QuickBooks ids (ListID, EditSequence, TxnID)
				$this->qbQueue->enqueue(PackageInfo::Actions['QUERY_INVOICE'], $ident, $InvoiceModPriority+1);

				// Requeue the invoice mod at a lower priority so the Query will have updated the ids
				$this->qbQueue->enqueue(PackageInfo::Actions['MOD_INVOICE'], $ident, $InvoiceModPriority);

				// Return true so that Web Connector will continue with other requests
				return true;
			}

			return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
		}

		public function _error_handler_0x80040400(int $requestID, string $user, string $action, $ident, array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg): bool
		{
			return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
		}
		public function _error_handler_3070(int $requestID, string $user, string $action, $ident, array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg): bool
		{
			return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
		}

		public function _error_handler_catchall(int $requestID, string $user, string $action, $ident, array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg): bool
		{
			return $this->_error_handler_generic(__FUNCTION__, $requestID, $user, $action, $ident, $extra, $err, $xml, $errnum, $errmsg);
		}
		public function _error_handler_generic(int $requestID, string $user, string $action, $ident, array $extra, ?string &$err, ?string $xml, ?int $errnum, ?string $errmsg): bool
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
}
