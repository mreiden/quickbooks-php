<?php declare(strict_types=1);

/**
 *
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
 * @subpackage Adapter
 */

namespace QuickBooksPhpDevKit\Adapter\Client;

use \SoapClient as SoapClient;
use QuickBooksPhpDevKit\Adapter\Client;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\WebConnector\Request\Authenticate;
use QuickBooksPhpDevKit\WebConnector\Request\Clientversion;
use QuickBooksPhpDevKit\WebConnector\Request\Closeconnection;
use QuickBooksPhpDevKit\WebConnector\Request\Connectionerror;
use QuickBooksPhpDevKit\WebConnector\Request\Getlasterror;
use QuickBooksPhpDevKit\WebConnector\Request\Receiveresponsexml;
use QuickBooksPhpDevKit\WebConnector\Request\Sendrequestxml;
use QuickBooksPhpDevKit\WebConnector\Request\Serverversion;

/**
 *
 */
class Php extends SoapClient implements Client
{
	public function __construct(string $endpoint, ?string $wsdl = null, bool $trace = true)
	{
		ini_set('soap.wsdl_cache_enabled', '0');

		$wsdl = $wsdl ?? PackageInfo::$WSDL;

		$options= [
			'location' => $endpoint,
		];

		if ($trace)
		{
			$options['trace'] = 1;
		}

		parent::__construct($wsdl, $options);
	}

	/**
	 * Authenticate against a QuickBooks SOAP server
	 */
	public function authenticate(string $user, string $pass): array
	{
		$req = new Authenticate($user, $pass);

		$resp = parent::__soapCall('authenticate', [$req]);
		$tmp = current($resp);

		return current($tmp);
	}

	/**
	 * Get the QBXML from the server to send to quickbooks
	 */
	public function sendRequestXML(string $ticket, string $hcpresponse, string $companyfile, string $country, int $majorversion, int $minorversion): array
	{
		$req = new Sendrequestxml($ticket, $hcpresponse, $companyfile, $country, $majorversion, $minorversion);

		//print("SENDING:<pre>");
		//print_r($req);
		//print('</pre>');

		$resp = parent::__soapCall('sendRequestXML', [$req]);
		$tmp = current($resp);

		return $tmp;
	}

	public function receiveResponseXML(string $ticket, string $response, $hresult, string $message): array
	{
		$req = new Receiveresponsexml($ticket, $response, $hresult, $message);

		$resp = parent::__soapCall('receiveResponseXML', [$req]);
		$tmp = current($resp);

		return $tmp;
	}

	public function getLastRequest()
	{
		return parent::__getLastRequest();
	}

	public function getLastResponse()
	{
		return parent::__getLastResponse();
	}
}
