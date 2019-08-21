<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\XML\Backend;

use \SimpleXMLElement;
use QuickBooksPhpDevKit\XML;
use QuickBooksPhpDevKit\XML\{
	Backend\BackendInterface,
	Document,
	Node,
};

class Simplexml implements BackendInterface
{
	protected $_xml;
	protected $_root;

	public function __construct(string $xml)
	{
		$this->_xml = $xml;
	}

	public function load(string $xml): bool
	{
		$this->_xml = $xml;
		$this->_root = null;

		return strlen($xml) > 0;
	}

	public function validate(?int &$errnum, ?string &$errmsg): bool
	{
		// Turn off the displayed error warnings
		$previous = libxml_use_internal_errors(true);
		libxml_clear_errors();

		// Parse it
		$root = new SimpleXMLElement($this->_xml);

		// Check for errors
		$errs = libxml_get_errors();

		$pcdata_chars = 'PCDATA invalid Char';
		if (count($errs) > 0 &&
			substr($errs[0]->message, 0, strlen($pcdata_chars)) == $pcdata_chars)
		{
			// Try to remove special chars, and try again
			libxml_clear_errors();

			$this->_xml = preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $this->_xml);
			$root = new SimpleXMLElement($this->_xml);
			$errs = libxml_get_errors();

			//die('this ran!  ' . print_r($errs, true));
		}

		//print_r($errs);
		//exit;

		// Reset it to it's previous state
		libxml_clear_errors();
		libxml_use_internal_errors($previous);

		if (is_array($errs) && count($errs))
		{
			$errnum = XML::ERROR_INTERNAL;

			$errmsg = '';
			$tmp = [];
			foreach ($errs as $err)
			{
				$tmp[] = '{' . $err->code . ': ' . trim($err->message) . ' on line ' . $err->line . '}';
			}
			$errmsg = implode(', ', $tmp);

			return false;
		}

		$this->_root = $root;
		$errnum = XML::ERROR_OK;

		return true;
	}

	public function parse(?int &$errnum, ?string &$errmsg)
	{
		if ($this->validate($errnum, $errmsg))
		{
			$Root = new Node($this->_root->getName());

			$Root = $this->_parseHelper($Root, $this->_root);

			//exit;
			return new Document($Root);
		}

		// Don't worry about the error code, validate() will take care of that
		return false;
	}

	protected function _parseHelper(Node $Base, SimpleXMLElement $simplexml_node, string $data = ''): Node
	{
		$Base->setData($data);

		$children = $simplexml_node->children();
		foreach ($children as $simplexml_child)
		{
			$children = $simplexml_child->children();

			$Child = new Node($simplexml_child->getName());

			$data = '';
			$children = $simplexml_child->children();
			if (count($children) == 0)
			{
				$data = $simplexml_child . '';
			}

			$Child = $this->_parseHelper($Child, $simplexml_child, $data);

			$Base->addChild($Child);
		}

		foreach ($simplexml_node->attributes() as $key => $value)
		{
			$Base->addAttribute($key, $value . '');
		}

		return $Base;
	}
}
