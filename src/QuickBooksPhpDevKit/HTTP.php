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
 * Used by:
 * 	- QuickBooks Merchant Service
 * 	- QuickBooks Online Edition
 * 	- QuickBooks WHMCS Integrator (API fetching)
 * 	- QuickBooks HTTP Hooks
 *	- QuickBooks HTTP Bridges
 *  - QuickBooks Foxycart integrator (relaying)
 *
 *
 * @todo Documentation?
 *
 * @license LICENSE.txt
 * @author Keith Palmer <keith@ConsoliBYTE.com>
 *
 * @package QuickBooks
 * @subpackage HTTP
 */

namespace QuickBooksPhpDevKit;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;

class HTTP
{
	public const HTTP_400 = 400;
	public const HTTP_401 = 401;
	public const HTTP_404 = 404;
	public const HTTP_500 = 500;

	public const ERROR_OK = PackageInfo::Error['OK'];
	public const ERROR_CERTIFICATE = 1;
	public const ERROR_UNSUPPORTED = 2;

	protected $_url;

	protected $_request_headers;

	protected $_body;

	protected $_post;

	protected $_get;

	protected $_last_response;

	protected $_last_request;

	protected $_last_duration;

	protected $_last_info;

	protected $_errnum;

	protected $_errmsg;

	protected $_verify_peer;

	protected $_verify_host;

	protected $_certificate;

	protected $_masking;

	protected $_log;

	protected $_debug;

	protected $_test;

	protected $_return_headers;

	/**
	 * A variable indicating whether or not to make a synchronous request
	 * @var boolean
	 */
	protected $_sync;

	/**
	 * Create a new QuickBooks_HTTP object instance to make HTTP requests to remote URLs
	 *
	 * @param string $url		The URL to make an HTTP request to
	 */
	public function __construct(?string $url = null)
	{
		$this->_url = $url;

		$this->_verify_peer = true;
		$this->_verify_host = true;

		$this->_masking = true;

		$this->_log = '';

		$this->_debug = false;
		$this->_test = false;

		$this->_sync = true;

		$this->_request_headers = [];
		$this->_return_headers = false;

		$this->_last_request = null;
		$this->_last_response = null;
		$this->_last_duration = 0.0;
	}

	/**
	 * Set the URL
	 */
	public function setURL(?string $url): void
	{
		$this->_url = $url;
	}

	/**
	 * Get the URL
	 *
	 * @return string
	 */
	public function getURL(): ?string
	{
		// @TODO Support for query string args from ->setGETValues()
		return $this->_url;
	}

	public function verifyPeer(bool $yes_or_no): void
	{
		$this->_verify_peer = $yes_or_no;
	}

	public function verifyHost(bool $yes_or_no): void
	{
		$this->_verify_host = $yes_or_no;
	}

	public function setHeaders(array $arr): void
	{
		foreach ($arr as $key => $value)
		{
			if (is_numeric($key) &&
				false !== ($pos = strpos($value, ':')))
			{
				// 0 => "Header: value" format

				$key = substr($value, 0, $pos);
				$value = ltrim(substr($value, $pos + 1));
			}

			// "Header" => "value" format

			$this->setHeader($key, $value);
		}
	}

	/**
	 * Tell whether or not to return the HTTP response headers
	 */
	public function returnHeaders(bool $return): void
	{
		$this->_return_headers = $return;
	}

	public function setHeader(string $key, $value): void
	{
		$this->_request_headers[$key] = $value;
	}

	public function getHeaders(bool $as_combined_array = false): array
	{
		if ($as_combined_array)
		{
			$list = [];
			foreach ($this->_request_headers as $key => $value)
			{
				$list[] = $key . ': ' . $value;
			}

			return $list;
		}

		return $this->_request_headers;
	}

	public function getHeader(string $key): ?string
	{
		if (isset($this->_request_headers[$key]))
		{
			return $this->_request_headers[$key];
		}

		return null;
	}

	public function setRawBody(?string $str): void
	{
		$this->_body = $str ?? '';
	}

	public function setPOSTValues($arr): void
	{
		$this->_post = $arr;
	}

	public function setGETValues($arr): void
	{
		$this->_get = $arr;
	}

	/**
	 *
	 *
	 * @return string
	 */
	public function getRawBody()
	{
		if ($this->_body)
		{
			return $this->_body;
		}
		else if (is_countable($this->_post) && count($this->_post))
		{
			return http_build_query($this->_post);
		}

		return '';
	}

	public function setCertificate($cert)
	{
		$this->_certificate = $cert;
	}

	public function GET()
	{
		return $this->_request('GET');
	}

	public function POST()
	{
		return $this->_request('POST');
	}

	public function DELETE()
	{
		return $this->_request('DELETE');
	}

	public function HEAD()
	{
		return $this->_request('HEAD');
	}

	public function useDebugMode(bool $yes_or_no): bool
	{
		$prev = $this->_debug;
		$this->_debug = $yes_or_no;

		return $prev;
	}

	/**
	 * If masking is enabled (default) then credit card numbers, connection tickets, and session tickets will be masked when output or logged
	 */
	public function useMasking(bool $yes_or_no): void
	{
		$this->_masking = $yes_or_no;
	}

	public function useTestEnvironment(bool $yes_or_no): bool
	{
		$prev = $this->_test;
		$this->_test = $yes_or_no;

		return $prev;
	}

	public function useLiveEnvironment(bool $yes_or_no): bool
	{
		$prev = $this->_test;
		$this->_test = !$yes_or_no;

		return $prev;
	}

	/**
	 * Get the error number of the last error that occurred
	 */
	public function errorNumber(): ?int
	{
		return $this->_errnum;
	}

	/**
	 * Get the error message of the last error that occurred
	 */
	public function errorMessage(): ?string
	{
		return $this->_errmsg;
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

	/**
	 *
	 *
	 */
	public function lastDuration(): float
	{
		return $this->_last_duration;
	}

	public function lastInfo()
	{
		return $this->_last_info;
	}

	/**
	 * Set an error message
	 *
	 * @param integer $errnum	The error number/code
	 * @param string $errmsg	The text error message
	 * @return void
	 */
	protected function _setError(int $errnum, string $errmsg = ''): void
	{
		$this->_errnum = $errnum;
		$this->_errmsg = $errmsg;
	}

	/**
	 *
	 */
	protected function _log(string $message): bool
	{
		if ($this->_masking)
		{
			$message = Utilities::mask($message);
		}

		if ($this->_debug)
		{
			print($message . PackageInfo::$CRLF);
		}

		//
		$this->_log .= $message . PackageInfo::$CRLF;

		return true;
	}

	public function resetLog(): void
	{
		$this->_log = '';
	}

	public function getLog()
	{
		return $this->_log;
	}

	/**
	 *
	 * @todo Implement support for asynchronous requests
	 *
	 */
	public function setSynchronous(bool $yes_or_no): void
	{
		$this->_sync = $yes_or_no;
	}

	public function setAsynchronous(bool $yes_or_no): void
	{
		$this->_sync = !$yes_or_no;
	}

	/**
	 * Make an HTTP request
	 */
	protected function _request(string $method): string
	{
		$start = microtime(true);

		if (!function_exists('curl_init'))
		{
			die('You must have the PHP cURL extension (php.net/curl) enabled to use this (' . PackageInfo::Package['NAME'] . ' v' . PackageInfo::Package['VERSION'] . ').');
		}

		$this->_log('Using CURL to send request!', PackageInfo::LogLevel['DEVELOP']);
		$return = $this->_requestCurl($method, $errnum, $errmsg);

		if ($errnum)
		{
			$this->_setError($errnum, $errmsg);
		}

		// Calculate and set how long the last HTTP request/response took to make
		$this->_last_duration = microtime(true) - $start;

		return $return;
	}

	protected function _requestCurl(string $method, ?int &$errnum, ?string &$errmsg)
	{
		$url = $this->getURL();
		$raw_body = $this->getRawBody();

		$headers = $this->getHeaders(true);

		$this->_log('Opening connection to: ' . $url, PackageInfo::LogLevel['VERBOSE']);

		$params = [];

		if ($method == 'POST')
		{
			$headers[] = 'Content-Length: ' . strlen($raw_body);
			$params[CURLOPT_POST] = true;
			$params[CURLOPT_POSTFIELDS] = $raw_body;
		}

		$query = '';
		if (is_countable($this->_get) && count($this->_get))
		{
			$query = '?' . http_build_query($this->_get);
		}

		if ($qs = parse_url($url, PHP_URL_QUERY) and
			false !== strpos($qs, ' '))
		{
			$url = str_replace($qs, str_replace(' ', '+', $qs), $url);
		}

		//print(' [[[ ' . parse_url($url, PHP_URL_QUERY) . '  /  ' . $url . ' ]]] ');

		$params[CURLOPT_RETURNTRANSFER] = true;
		$params[CURLOPT_URL] = $url . $query;
		//$params[CURLOPT_TIMEOUT] = 15;
		$params[CURLOPT_HTTPHEADER] = $headers;
		$params[CURLOPT_ENCODING] = '';			// This makes it say it supports gzip *and* deflate

		$params[CURLOPT_VERBOSE] = $this->_debug;

		if ($this->_return_headers)
		{
			$params[CURLOPT_HEADER] = true;
		}

		// Some Windows servers will fail with SSL errors unless we turn this off
		if (!$this->_verify_peer)
		{
			$params[CURLOPT_SSL_VERIFYPEER] = false;
		}

		if (!$this->_verify_host)
		{
			$params[CURLOPT_SSL_VERIFYHOST] = 0;
		}

		// Check for an SSL certificate (HOSTED model of communication)
		if ($this->_certificate)
		{
			if (file_exists($this->_certificate))
			{
				$this->_log('Using SSL certificate at: ' . $this->_certificate, PackageInfo::LogLevel['DEBUG']);
				$params[CURLOPT_SSLCERT] = $this->_certificate;
			}
			else
			{
				$msg = 'Specified SSL certificate could not be located: ' . $this->_certificate;

				$this->_log($msg, PackageInfo::LogLevel['NORMAL']);
				$errnum = self::ERROR_CERTIFICATE;
				$errmsg = $msg;
				return false;
			}
		}

		// Fudge the outgoing request because CURL won't give us it
		$request = '';

		if ($method == 'POST')
		{
			$request .= 'POST ';
		}
		elseif ($method == 'DELETE')
		{
			$request .= 'DELETE ';
			$params[CURLOPT_CUSTOMREQUEST] = 'DELETE';
		}
		elseif ($method == 'HEAD')
		{
			$request .= 'HEAD ';
		}
		else
		{
			$request .= 'GET ';
		}
		$request .= $params[CURLOPT_URL] . ' HTTP/1.1' . "\r\n";

		foreach ($headers as $header)
		{
			$request .= $header . "\r\n";
		}
		$request .= "\r\n";
		$request .= $this->getRawBody();

		$this->_log('CURL options: ' . print_r($params, true), PackageInfo::LogLevel['DEBUG']);

		$this->_last_request = $request;
		$this->_log('HTTP request: ' . $request, PackageInfo::LogLevel['DEBUG']);	// Set as DEBUG so that no one accidentally logs all the credit card numbers...

		$ch = curl_init();
		curl_setopt_array($ch, $params);
		$response = curl_exec($ch);

		/*
		print("\n\n\n" . '---------------------' . "\n");
		print('[[request ' . $request . ']]' . "\n\n\n");
		print('[[resonse ' . $response . ']]' . "\n\n\n\n\n");

		print_r($params);
		print_r(curl_getinfo($ch));
		print_r($headers);
		print("\n" . '---------------------' . "\n\n\n\n");
		*/

		$this->_last_response = $response;
		$this->_log('HTTP response: ' . substr($response, 0, 500) . '...', PackageInfo::LogLevel['VERBOSE']);

		$this->_last_info = curl_getinfo($ch);

		if (curl_errno($ch))
		{
			$errnum = curl_errno($ch);
			$errmsg = curl_error($ch);

			$this->_log('CURL error: ' . $errnum . ': ' . $errmsg, PackageInfo::LogLevel['NORMAL']);

			return false;
		}

		// Close the connection
		@curl_close($ch);

		return $response;
	}
}
