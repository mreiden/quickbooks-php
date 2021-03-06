<?php declare(strict_types=1);

/**
 * XML constants (and backward compat. class)
 *
 * Copyright (c) 2010-04-16 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @package QuickBooks
 * @subpackage XML
 */

namespace QuickBooksPhpDevKit;

use QuickBooksPhpDevKit\XML\{
	Backend,
	Document,
	Node,
	Parser,
};

/**
 * QuickBooks XML base class
 */
class XML
{
	/**
	 * Indicates an error *did not* occur
	 * @var integer
	 */
	const ERROR_OK = 0;

	/**
	 * Alias of QUICKBOOKS_XML_ERROR_OK
	 */
	const OK = 0;

	/**
	 * Indicates a tag mismatch/bad tag order
	 * @var integer
	 */
	const ERROR_MISMATCH = 1;

	/**
	 * Indicates garbage somewhere in the XML stream
	 * @var integer
	 */
	const ERROR_GARBAGE = 2;

	/**
	 * Indicates a bad XML entity
	 * @var integer
	 */
	const ERROR_ENTITY = 3;

	/**
	 * Indicates a dangling XML attribute after parsing
	 * @var integer
	 */
	const ERROR_DANGLING = 4;

	/**
	 * Internal XML parser error
	 * @var integer
	 */
	const ERROR_INTERNAL = 5;

	/**
	 * No content to parse error
	 * @var integer
	 */
	const ERROR_CONTENT = 6;

	/**
	 *
	 */
	const PARSER_BUILTIN = 'builtin';

	/**
	 *
	 */
	const PARSER_SIMPLEXML = 'simplexml';

	/**
	 * <code>
	 * $xml = '
	 * 	<Person>
	 * 		<Name type="firstname">Keith</Name>
	 * 	</Person>
	 * ';
	 *
	 * $arr = array(
	 * 	'Person' => array(
	 * 		'Name' => 'Keith',
	 * 		),
	 * 	);
	 * </code>
	 */
	const ARRAY_NOATTRIBUTES = 'no-attrs';

	/**
	 *
	 * <code>
	 * $arr = [
	 * 	'Person' => [
	 * 		'Name' => 'Keith',
	 * 		'Name_type' => 'firstname',
	 * 		],
	 * ];
	 * </code>
	 *
	 */
	const ARRAY_EXPANDATTRIBUTES = 'child-attrs';

	/**
	 * <code>
	 * $arr = [
	 * 	0 => [
	 * 		'name' => 'Person',
	 * 		'attributes' => [],
	 * 		'children' => [
	 * 			0 => [
	 * 				'name' => 'Name',
	 * 				'attributes' => [
	 * 					'type' => 'firstname',
	 * 				],
	 * 				'children' => [],
	 * 				'data' => 'Keith',
	 * 			],
	 * 		],
	 * 		'data' => null,
	 * 	],
	 * );
	 * </code>
	 */
	const ARRAY_BRANCHED = 'branched';

	/**
	 *
	 * <code>
	 * $arr = [
	 * 	'Person Name' => 'Keith',
	 * 	];
	 * </code>
	 *
	 */
	const ARRAY_PATHS = 'paths';

	/**
	 * Flag to compress empty XML elements
	 *
	 * <Customer>
	 * 	<FirstName>Keith</FirstName>
	 * 	<LastName />
	 * </Customer>
	 *
	 * @note Defined as an integer for backwards compat.
	 * @var integer
	 */
	const XML_COMPRESS = 1;

	/**
	 * Flag to drop empty XML elements
	 *
	 * <Customer>
	 * 	<FirstName>Keith</FirstName>
	 * </Customer>
	 *
	 * @note Defined as an integer for backwards compat.
	 * @var integer
	 */
	const XML_DROP = -1;

	/**
	 * Flag to preserve empty elements
	 *
	 * <Customer>
	 * 	<FirstName>Keith</FirstName>
	 * 	<LastName></LastName>
	 * </Customer>
	 *
	 * @note Defined as an integer for backwards compat.
	 * @var integer
	 */
	const XML_PRESERVE = 0;

	/**
	 * Extract the contents from a particular XML tag in an XML string
	 *
	 * <code>
	 * $xml = '<document><stuff>bla bla</stuff><other>ble ble</other></document>';
	 * $contents = self::extractTagContents('stuff', $xml);
	 * print($contents); 	// prints "bla bla"
	 * </code>
	 *
	 * @param string $tag		The XML tag to extract the contents from
	 * @param string $data		The XML document
	 * @return string			The contents of the tag
	 */
	static public function extractTagContents(string $tag, string $data): ?string
	{
		$tag = trim($tag, '<> ');

		if (false !== strpos($data, '<' . $tag . '>') &&
			false !== strpos($data, '</' . $tag . '>'))
		{
			$data = strstr($data, '<' . $tag . '>');
			$end = strpos($data, '</' . $tag . '>');

			return substr($data, strlen($tag) + 2, $end - (strlen($tag) + 2));
		}

		return null;
	}

	// @todo Documentation
	static public function extractTagAttribute($attribute, $tag_w_attrs, $which = 0)
	{
		/*
		if (false !== ($start = strpos($tag_w_attrs, $attribute . '="')) &&
			false !== ($end = strpos($tag_w_attrs, '"', $start + strlen($attribute) + 2)))
		{
			return substr($tag_w_attrs, $start + strlen($attribute) + 2, $end - $start - strlen($attribute) - 2);
		}

		return null;
		*/

		$attr = $attribute;
		$data = $tag_w_attrs;

		if ($which == 1)
		{
			$spos = strpos($data, $attr . '="');
			$data = substr($data, $spos + strlen($attr));
		}

		if (false !== ($spos = strpos($data, $attr . '="')) &&
			false !== ($epos = strpos($data, '"', $spos + strlen($attr) + 2)))
		{
			//print('start: ' . $spos . "\n");
			//print('end: ' . $epos . "\n");

			return substr($data, $spos + strlen($attr) + 2, $epos - $spos - strlen($attr) - 2);
		}

		return '';
	}

	/**
	 * Extract the attributes from a tag container
	 *
	 * @todo Holy confusing code Batman!
	 */
	static public function extractTagAttributes(string $tag_w_attrs, bool $return_tag_first = false): array
	{
		$tag = '';
		$attributes = [];

		$tag_w_attrs = trim($tag_w_attrs);

		/*if (substr($tag_w_attrs, -1, 1) == '/')		// condensed empty tag
		{
			$tag = trim($tag_w_attrs, '/ ');
			$attributes = [];
		}
		else*/
		if (false !== strpos($tag_w_attrs, ' '))
		{
			$tmp = explode(' ', $tag_w_attrs);
			//$tag = trim(array_shift($tmp), " \n\r\t<>");
			$tag = trim(array_shift($tmp));

			$attributes = [];

			$attrs = trim(implode(' ', $tmp));
			$length = strlen($attrs);

			$key = '';
			$value = '';
			$in_key = true;
			$in_value = false;
			$expect_key = false;
			$expect_value = false;

			for ($i = 0; $i < $length; $i++)
			{
				if ($attrs{$i} == '=')
				{
					$in_key = false;
					$in_value = false;
					$expect_value = true;
				}
				/*
				else if ($attrs{$i} == '"' && $expect_value)
				{
					$in_value = true;
					$expect_value = false;
				}
				*/
				/*else if ($attrs{$i} == '"' && $in_value)*/
				else if (($attrs{$i} == '"' || $attrs{$i} == '\'') && $expect_value)
				{
					$in_value = true;
					$expect_value = false;
				}
				else if (($attrs{$i} == '"' || $attrs{$i} == '\'') && $in_value)
				{
					$attributes[trim($key)] = $value;

					$key = '';
					$value = '';

					$in_value = false;
					$expect_key = true;
				}
				else if ($attrs{$i} == ' ' && $expect_key)
				{
					$expect_key = false;
					$in_key = true;
				}
				else if ($in_key)
				{
					$key .= $attrs{$i};
				}
				else if ($in_value)
				{
					$value .= $attrs{$i};
				}
			}

			/*
			foreach ($tmp as $attribute)
			{
				if (false !== ($pos = strpos($attribute, '=')))
				{
					$key = trim(substr($attribute, 0, $pos));
					$value = trim(substr($attribute, $pos + 1), '"');

					$attributes[$key] = $value;
				}
			}*/
		}
		else
		{
			$tag = $tag_w_attrs;
			$attributes = [];
		}

		// This returns the actual tag without attributes as the first key of the array
		if ($return_tag_first)
		{
			array_unshift($attributes, $tag);
		}

		return $attributes;
	}

	/**
	 * Encode a string for use within an XML document
	 *
	 * @param string $str				The string to encode
	 * @param boolean $for_qbxml
	 * @return string
	 */
	static public function encode(?string $str, bool $for_qbxml = true, bool $double_encode = true): string
	{
		if (null === $str || '' === $str) {
			return '';
		}

		// Convert existing HTML named entities (&Acirc;) back into their UTF-8 characters (Ã)
		// so mb_encode_numericentity can encode them as numeric entities (&#194;).
		//
		// ENT_XHTML is used because ENT_XML1 failes to convert the named entities
		// because it supports the UTF-8 characters natively (no need for named entities).
		// Coverting named entities into UTF-8 characters and then into numeric entities
		// is better when dealing with QuickBooks.
		$str = html_entity_decode($str, ENT_COMPAT | ENT_XHTML, 'UTF-8');

		// Encode Special Characters [&"<>]
		if ($for_qbxml)
		{
			$str = htmlspecialchars($str, ENT_COMPAT | ENT_XML1, 'UTF-8', $double_encode);
		}

		// Convert non-ASCII UTF8 characters to numeric entities
		$conversion_map = [0x0080, 0x10FFFF, 0x0, 0xFFFFFF];
		$str = mb_encode_numericentity($str, $conversion_map, 'UTF-8');

		return $str;
	}

	/**
	 * Decode a string for use within an XML document
	 *
	 * @todo Investigate QuickBooks qbXML encoding and implement solution
	 *
	 * @param string $str				The string to encode
	 * @param boolean $for_qbxml
	 * @return string
	 */
	static public function decode(string $str, bool $for_qbxml = true): string
	{

		// Decode Special Characters (&amp;, &quot;, &lt;, &gt;) to [&"<>]
		$str = htmlspecialchars_decode($str, ENT_COMPAT | ENT_XML1);

		// Convert numeric entities to non-ASCII UTF8 characters
		$conversion_map = [0x0080, 0x10FFFF, 0x0, 0xFFFFFF];
		$str = mb_decode_numericentity($str, $conversion_map, 'UTF-8');

		return $str;
	}

	/**
	 * Validate XML, add missing XML declaration, and format output
	 *
	 * @param string 		$xml		The XML
	 * @param string|null 	$xmlVersion	Defaults to 1.0
	 * @param string|null 	$encoding	Defaults to UTF-8
	 * @return string
	 */
	static public function cleanXML(string $xml, ?string $xmlVersion = null, ?string $encoding = null): string
	{
		$dom = new \DomDocument();
		$dom->preserveWhiteSpace = false;

		$saved_state = libxml_use_internal_errors(true);
		$loaded = $dom->loadXML($xml);
		if ($loaded !== true)
		{
			$error = libxml_get_errors()[0];
			throw new \Exception('DomDocument::loadXML(): ' . trim($error->message) . ' in Entity, line: ' . $error->line);
		}
		libxml_clear_errors();
		libxml_use_internal_errors($saved_state);

		$dom->formatOutput = true;
		$dom->xmlVersion = $xmlVersion ?? '1.0';
		$dom->encoding = $encoding ?? 'UTF-8';

		return $dom->saveXML();
	}
}
