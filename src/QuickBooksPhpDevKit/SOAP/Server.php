<?php declare(strict_types=1);

/**
 * QuickBooks SOAP server component
 *
 * This pure PHP SOAP server is provided for users that do not have access to
 * the PHP ext/soap SOAP extension. It's also useful for testing, as it makes
 * debugging a little bit easier (non-fatal errors and print() statements will
 * show up, where-as with the PHP extension, it gobbles up all regular PHP
 * standard output)
 *
 * Note: This is *not* a generic SOAP server, and *will not* work with other
 * SOAP services outside of the QuickBooks Web Connector SOAP specification.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage SOAP
 */

namespace QuickBooksPhpDevKit\SOAP;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\XML;
use QuickBooksPhpDevKit\XML\Parser;
use QuickBooksPhpDevKit\WebConnector\Request;						// WebConnector Request base class
use QuickBooksPhpDevKit\WebConnector\Request\Authenticate;			// Object container for request calls to ->authenticate()
use QuickBooksPhpDevKit\WebConnector\Request\Clientversion;			// Object container for request calls to ->clientVersion()
use QuickBooksPhpDevKit\WebConnector\Request\Closeconnection;		// Object container for request calls to ->closeConnection()
use QuickBooksPhpDevKit\WebConnector\Request\Connectionerror;		// Object container for request calls to ->connectionError()
use QuickBooksPhpDevKit\WebConnector\Request\Getinteractiveurl;		// Object container for request calls to ->getInteractiveURL()
use QuickBooksPhpDevKit\WebConnector\Request\Getlasterror;			// Object container for request calls to ->getLastError()
use QuickBooksPhpDevKit\WebConnector\Request\Interactivedone;		// Object container for request calls to ->interactiveDone()
use QuickBooksPhpDevKit\WebConnector\Request\Interactiverejected;	// Object container for request calls to ->interactiveRejected()
use QuickBooksPhpDevKit\WebConnector\Request\Receiveresponsexml;	// Object container for request calls to ->receiveResponseXML()
use QuickBooksPhpDevKit\WebConnector\Request\Sendrequestxml;		// Object container for request calls to ->sendRequestXML()
use QuickBooksPhpDevKit\WebConnector\Request\Serverversion;			// Object container for request calls to ->serverVersion()


/**
 * QuickBooks SOAP server component
 */
class Server
{
	/**
	 * An instance of the class which handles the SOAP methods
	 */
	protected $_class;

	/**
	 * Create a new QuickBooks_SOAP_Server instance
	 */
	public function __construct(string $wsdl, array $soap_options = [])
	{
	}

	/**
	 * Create an instance of a request type object
	 */
	protected function _requestFactory(string $request): Request
	{
		$class = "QuickBooksPhpDevKit\\WebConnector\\Request\\" . ucfirst(strtolower($request));
		return new $class();
	}

	/**
	 * Handle a SOAP request
	 */
	public function handle(string $raw_http_input): void
	{
		// Determine the method, call the correct handler function

		//$Parser = new QuickBooks_XML_Parser($raw_http_input, $builtin);
		//$builtin = QuickBooks_XML::PARSER_BUILTIN;		// The SimpleXML parser has a difference namespace behavior, so force this to use the builtin parser
		$Parser = new Parser($raw_http_input, XML::PARSER_BUILTIN);

		$errnum = 0;
		$errmsg = '';
		$Doc = $Parser->parse($errnum, $errmsg);
		if ($Doc)
		{
			//print('parsing...');

			$Root = $Doc->getRoot();

			$Body = $Root->getChildAt('SOAP-ENV:Envelope SOAP-ENV:Body');
			if (!$Body)
			{
				$Body = $Root->getChildAt('soap:Envelope soap:Body');
			}

			$Container = $Body->getChild(0);

			$Request = null;
			$method = '';
			if ($Container)
			{
				$namespace = '';
				$method = $this->_namespace($Container->name(), $namespace);
				$Request = $this->_requestFactory($method);

				foreach ($Container->children() as $Child)
				{
					$namespace = '';
					$member = $this->_namespace($Child->name(), $namespace);

					//$Request->$member = html_entity_decode($Child->data(), ENT_QUOTES);
					$Request->$member = $Child->data();
				}
			}

			//print('method is: ' . $method . "\n");

			$Response = null;
			if (method_exists($this->_class, $method))
			{
				$Response = $this->_class->$method($Request);
				$Response = is_object($Response) ? get_object_vars($Response) : null;
			}

			$soap = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://developer.intuit.com/">';
			$soap .= '  <SOAP-ENV:Body>';
			$soap .= '    <ns1:' . $method . 'Response>' . $this->_serialize($Response) . '</ns1:' . $method . 'Response>';
			$soap .= '  </SOAP-ENV:Body>';
			$soap .= '</SOAP-ENV:Envelope>';
		}
		else
		{
			$soap = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">';
			$soap .= '	<SOAP-ENV:Body>';
			$soap .= '		<SOAP-ENV:Fault>';
			$soap .= '			<faultcode>SOAP-ENV:Client</faultcode>';
			$soap .= '			<faultstring>Bad Request: ' . htmlspecialchars($errnum) . ': ' . htmlspecialchars($errmsg) . '</faultstring>';
			$soap .= '		</SOAP-ENV:Fault>';
			$soap .= '	</SOAP-ENV:Body>';
			$soap .= '</SOAP-ENV:Envelope>';
		}

		$soap = XML::cleanXML($soap);
		if (null == $soap)
		{
			throw new \Exception("Soap response could not be parsed by DomDocument");
		}

		echo $soap;
	}

	protected function _namespace(string $full_tag, string &$namespace): string
	{
		if (false !== strpos($full_tag, ':'))
		{
			$tmp = explode(':', $full_tag);

			$namespace = current($tmp);
			return next($tmp);
		}

		$namespace = '';

		return $full_tag;
	}

	/**
	 *
	 */
	protected function _serialize(?array $vars): string
	{
		$soap = '';

		if (null !== $vars)
		{
			foreach ($vars as $key => $value)
			{
				// Do not use any spaces (Do not indent)
				$soap .= '<ns1:' . $key . '>';

				if (is_array($value))
				{
					foreach ($value as $subkey => $subvalue)
					{
						$soap .= '<ns1:string>' . htmlspecialchars((string) $subvalue) . '</ns1:string>' . "\n";
					}
				}
				else
				{
					$soap .= htmlspecialchars((string) $value);
				}

				$soap .= '</ns1:' . $key . '>';
			}
		}

		return $soap;
	}

	/**
	 *
	 */
	public function setClass(string $class, $dsn_or_conn, array $map, array $onerror, array $hooks, int $log_level, string $raw_http_input, array $handler_options, array $driver_options, array $callback_options): void
	{
		$this->_class = new $class($dsn_or_conn, $map, $onerror, $hooks, $log_level, $raw_http_input, $handler_options, $driver_options, $callback_options);
	}

	/**
	 *
	 */
	public function getFunctions(): array
	{
		return get_class_methods($this->_class);
	}
}
