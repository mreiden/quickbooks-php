<?php declare(strict_types=1);

/**
 * Simple QuickBooks XML parsing class
 *
 * Copyright (c) 2010-04-16 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * This is intended as a simple alternative to the PHP SimpleXML or DOM
 * extensions (some of the machines I'm working on don't have the SimpleXML
 * extension enabled) for parsing XML documents.
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage XML
 */

namespace QuickBooksPhpDevKit\XML;

use QuickBooksPhpDevKit\{
	XML,
	XML\Document,
	XML\Node,
};

/**
 * QuickBooks XML Parser
 *
 * Create an instance of the QuickBooks_XML parser by calling the constructor
 * with either a file-path or the contents of an XML document.
 * <code>
 * $xml = '<Tag1><NestedTag age="25" gender="male"><AnotherTag>Keith</AnotherTag></NestedTag></Tag1>';
 *
 * // Create the new object
 * $Parser = new QuickBooks_XML_Parser($xml);
 *
 * // Parse the XML document
 * $errnum = 0;
 * $errmsg = '';
 * if ($Parser->validate($errnum, $errmsg))
 * {
 * 	$Doc = $Parser->parse($errnum, $errmsg);
 *
 * 	// Now fetch some stuff from the parsed document
 * 	print('Hello there ' . $Doc->getChildDataAt('Tag1 NestedTag AnotherTag') . "\n");
 * 	print_r($Doc->getChildAttributesAt('Tag1 NestedTag'));
 * 	print("\n");
 * 	print('Root tag name is: ' . $Doc->name() . "\n");
 *
 * 	$NestedTag = $Doc->getChildAt('Tag1 NestedTag');
 * 	print_r($NestedTag);
 * }
 * </code>
 */
class Parser
{
	/**
	 *
	 */
	protected $_xml;

	/**
	 * What back-end XML parser to use
	 * @var Quickbooks_XML_Backend
	 */
	protected $_backend;

	/**
	 *
	 */
	public const BACKEND_SIMPLEXML = 'simplexml';

	/**
	 *
	 */
	public const BACKEND_BUILTIN = 'builtin';

	/**
	 * Create a new QuickBooks_XML parser object
	 *
	 * @param string $xml_or_file
	 */
	public function __construct(?string $xml_or_file = null, ?string $use_backend = null)
	{
		$this->_xml = $this->_read($xml_or_file);

		if (is_null($use_backend) && function_exists('simplexml_load_string'))
		{
			$use_backend = XML::PARSER_SIMPLEXML;
		}
		else if (is_null($use_backend))
		{
			$use_backend = XML::PARSER_BUILTIN;
		}

		$class = __NAMESPACE__ ."\\Backend\\" . ucfirst(strtolower($use_backend));
		$this->_backend = new $class($this->_xml);
	}

	/**
	 * Read an open file descriptor, XML file, or string
	 *
	 * @param mixed $mixed
	 */
	protected function _read($mixed): string
	{
		if (empty($mixed))
		{
			return '';
		}
		else if (is_resource($mixed) && get_resource_type($mixed) == 'stream')
		{
			$buffer = '';
			$tmp = '';
			while ($tmp = fread($mixed, 8192))
			{
				$buffer .= $tmp;
			}

			return $buffer;
		}
		else if (substr(trim($mixed), 0, 6) == '{"warn')
		{
			// Intuit has a bug where some of their services return JSON erors
			// instead of XML, so we catch these here...

			return '';
		}
		else if (substr(trim($mixed), 0, 1) != '<')
		{
			return file_get_contents($mixed);
		}

		return $mixed;
	}

	/**
	 * Load the XML parser with data from a string or file
	 *
	 * @param string $xml_or_file		An XML string or file path
	 */
	public function load(string $xml_or_file): bool
	{
		$this->_xml = $this->_read($xml_or_file);

		return $this->_backend->load($this->_xml);
	}

	/**
	 * Check if the XML document is valid
	 *
	 * *** WARNING *** This does not check against the actual QuickBooks
	 * schemas, and in reality even the XML validation stuff it *does* do is
	 * pretty light. You should probably double check any validation you're
	 * doing in a better XML validator.
	 *
	 * @param integer $errnum
	 * @param string $errmsg
	 * @return boolean
	 */
	public function validate(?int &$errnum, ?string &$errmsg): bool
	{
		return $this->_backend->validate($errnum, $errmsg);
	}

	/**
	 *
	 */
	public function beautify(?int &$errnum, ?string &$errmsg, bool $compress_empty_elements = true)
	{
		$errnum = 0;
		$errmsg = '';

		$Node = $this->parse($errnum, $errmsg);

		if (!$errnum && is_object($Node))
		{
			return $Node->asXML($compress_empty_elements);
		}

		return false;
	}

	/**
	 * Parse an XML document into an XML node structure
	 *
	 * This function returns either a QuickBooks_XML_Document on success, or false
	 * on failure. You can use the ->validate() method first so you can tell
	 * whether or not the parser will succeed.
	 *
	 * @param integer $errnum
	 * @param string $errmsg
	 * @return XML\Document|null
	 */
	public function parse(?int &$errnum, ?string &$errmsg): ?Document
	{
		if (!strlen($this->_xml))
		{
			$errnum = XML::ERROR_CONTENT;
			$errmsg = 'No XML content to parse.';

			return null;
		}

		// first, let's remove all of the comments
		if ($this->validate($errnum, $errmsg))
		{
			return $this->_backend->parse($errnum, $errmsg);
		}

		return null;
	}

	public function backend(): string
	{
		return strtolower(get_class($this->_backend));
	}
}
