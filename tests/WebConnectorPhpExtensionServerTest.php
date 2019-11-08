<?php declare(strict_types=1);

// Extends WebConnectorServerTest to duplicate SOAP tests using PHP SoapServer Extension
require_once(__DIR__ . '/WebConnectorServerTest.php');

use QuickBooksPhpDevKit\Adapter\SOAP\Server\PhpExtensionAdapter;

class WebConnectorPhpExtensionServerTest extends WebConnectorServerTest
{
    protected $soapAdapterClass = PhpExtensionAdapter::class;
}
