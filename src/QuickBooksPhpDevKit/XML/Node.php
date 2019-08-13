<?php declare(strict_types=1);

/**
 * QuickBooks XML node - individual tags within an XML document
 *
 * Copyright (c) 2010-04-16 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @todo YAML exporting *does not* work correctly
 * @todo Array exporting *does not* work correctly
 * @todo JSON exporting *does not* work correctly
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage XML
 */

namespace QuickBooksPhpDevKit\XML;

use QuickBooksPhpDevKit\XML;

/**
 * QuickBooks XML node
 */
class Node
{
	/**
	 * Tag name
	 * @var string
	 */
	protected $_name;

	/**
	 * Tag data
	 * @var string
	 */
	protected $_data;

	/**
	 * An associative array of attributes for this tag
	 * @var array
	 */
	protected $_attributes;

	/**
	 * A string containing comments that were found immediately after this tag
	 * @var string
	 */
	protected $_comment;

	/**
	 * An array of child tags within this tag
	 * @var Node[]
	 */
	protected $_children;

	/**
	 * Create a new XML\Node
	 */
	public function __construct(?string $name = null, ?string $data = null)
	{
		$this->_name = $name;

		$this->_data = $data;
		$this->_children = [];
		$this->_attributes = [];
	}

	/**
	 * Add a child tag (another node) to this tag
	 */
	public function addChild(Node $node, bool $prepend = false): bool
	{
		if ($prepend)
		{
			array_unshift($this->_children, $node);
		}
		else
		{
			$this->_children[] = $node;
		}

		return true;
	}

	/**
	 * Add an attribute to this XML tag/node
	 *
	 * @param string $attr		The attribute name
	 * @param mixed $value		The attribute value
	 * @return boolean
	 */
	public function addAttribute(string $attr, $value): bool
	{
		$this->_attributes[$attr] = $value;

		return true;
	}

	/**
	 * Set an attribute in this XML tag/node
	 *
	 * @param string $attr
	 * @param mixed $value
	 * @return boolean
	 */
	public function setAttribute(string $attr, $value): bool
	{
		return $this->addAttribute($attr, $value);
	}

	/**
	 * Get an attribute from this XML tag/node
	 *
	 * @param string $attr
	 * @return mixed
	 */
	public function getAttribute(string $attr)
	{
		if ($this->attributeExists($attr))
		{
			return $this->_attributes[$attr];
		}

		return false;
	}

	/**
	 * Tell whether or not an attribute exists
	 */
	public function attributeExists(string $attr): bool
	{
		return isset($this->_attributes[$attr]);
	}

	/**
	 * Set the name of this tag/node
	 */
	public function setName(string $name): bool
	{
		$this->_name = $name;

		return true;
	}

	/**
	 * Set the data contained by the XML node
	 */
	public function setData(?string $data): bool
	{
		$this->_data = $data;

		return true;
	}

	/**
	 *
	 */
	public function setChildAttributeAt(string $path, string $attr, $value, bool $create = false): bool
	{
		if ($child = $this->getChildAt($path))
		{
			if ($child->attributeExists($attr))
			{
				$child->setAttribute($attr, $value);
			}
			else
			{
				$child->addAttribute($attr, $value);
			}
		}
		else if ($create)
		{
			$this->setChildDataAt($path, null, true);
			return $this->setChildAttributeAt($path, $attr, $value, false);
		}

		return false;
	}

	/**
	 * Recursive helper function - get child at location
	 */
	protected function _getChildAtHelper(Node $root, string $path): ?Node
	{
		if (false !== strpos($path, ' ') && false === strpos($path, '/'))
		{
			$path = str_replace(' ', '/', $path);
		}

		$explode = explode('/', $path);
		//$explode = explode(' ', $path);
		$current = array_shift($explode);
		$next = current($explode);

		if ($path == $root->name())
		{
			return $root;
		}
		else if ($current == $root->name())
		{
			$path = implode('/', $explode);

			foreach ($root->children() as $child)
			{
				if ($child->name() == $next)
				{
					return $this->_getChildAtHelper($child, $path);
				}
			}
		}

		return null;
	}

	/**
	 * Get the data segment of an XML child node at a particular path
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function getChildDataAt(string $path)
	{
		if ($child = $this->getChildAt($path))
		{
			return $child->data();
		}

		return false;
	}

	/**
	 * Get a child XML node at a particular path
	 */
	public function getChildAt(string $path): ?Node
	{
		return $this->_getChildAtHelper($this, $path);
	}

	/**
	 *
	 */
	public function childExistsAt(string $path): bool
	{
		$child = $this->getChildAt($path);

		return is_object($child);
	}

	/**
	 * Add a child XML node at a particular path
	 */
	public function addChildAt(string $path, Node $node, bool $create = false): bool
	{
		return $this->_addChildAtHelper($this, $path, $node, $create);
	}

	/**
	 *
	 */
	protected function _addChildAtHelper(Node &$root, string $path, Node $node, bool $create = false): bool
	{
		if (false !== strpos($path, ' ') && false === strpos($path, '/'))
		{
			$path = str_replace(' ', '/', $path);
		}

		$explode = explode('/', $path);
		/*$explode = explode(' ', $path);*/
		$current = array_shift($explode);
		$next = current($explode);

		if ($path == $root->name())
		{
			return $root->addChild($node);
		}
		else
		{
			$path = implode('/', $explode);

			foreach ($root->children() as $child)
			{
				if ($child->name() == $next)
				{
					return $this->_addChildAtHelper($child, $path, $node, $create);
				}
			}
		}

		if ($create)
		{
			$root->addChild(new Node($next));
			foreach ($root->children() as $child)
			{
				if ($child->name() == $next)
				{
					return $this->_addChildAtHelper($child, $path, $node, $create);
				}
			}
		}

		return false;
	}

	public function setChildDataAt(string $path, $data, bool $create = false)
	{
		if (false !== strpos($path, ' ') && false === strpos($path, '/'))
		{
			$path = str_replace(' ', '/', $path);
		}

		$explode = explode('/', $path);
		$end = array_pop($explode);
		$allbutend = implode('/', $explode);

		$child = $this->getChildAt($path);

		if (!$child && $create)
		{
			$this->addChildAt($allbutend, new Node($end), true);
			$child = $this->getChildAt($path);
		}

		if ($child)
		{
			if (is_int($data) || is_float($data))
			{
				// Convert numeric data to a string for use in xml string
				$data = (string) $data;
			}

			$child->setData($data);
		}

		return false;
	}

	/**
	 * Get a child XML node's attributes (associative array)
	 */
	public function getChildAttributesAt(string $path): ?array
	{
		if ($child = $this->getChildAt($path))
		{
			return $child->attributes();
		}

		return null;
	}

	/**
	 * Get an array of child nodes for this XML node
	 *
	 * @param string $pattern
	 * @return XML\Node[]
	 */
	public function children(?string $pattern = null): array
	{
		if (!is_null($pattern))
		{
			$list = [];

			foreach ($this->_children as $Child)
			{
				if (Utilities::fnmatch($pattern, $Child->name()))
				{
					$list[] = $Child;
				}
			}

			return $list;
		}

		return $this->_children;
	}

	/**
	 * Get the data contained by this XML node
	 *
	 * @return mixed
	 */
	public function data()
	{
		return $this->_data;
	}

	/**
	 * Tell whether or not this XML node contains data
	 */
	public function hasData(): bool
	{
		return null !== $this->_data && strlen($this->_data) > 0;
	}

	/**
	 * Set a comment for this node
	 */
	public function setComment(string $comment): bool
	{
		$this->_comment = $comment;

		return true;
	}

	/**
	 * Retrieve any comment set for this XML node
	 */
	public function comment(): string
	{
		return $this->_comment;
	}

	/**
	 * Tell whether or not this XML node contains a comment
	 */
	public function hasComment(): bool
	{
		return null !== $this->_comment && strlen($this->_comment) > 0;
	}

	/**
	 * Tell the name of this XML node/tag
	 */
	public function name(): string
	{
		return $this->_name;
	}

	/**
	 * Get an associative array of attributes the XML node contains
	 */
	public function attributes(): array
	{
		return $this->_attributes;
	}

	/**
	 * Tell how many child elements this particular XML node has
	 */
	public function childCount(): int
	{
		return is_countable($this->_children) ? count($this->_children) : 0;
	}

	/**
	 * Tell whether or not this XML node has any children
	 */
	public function hasChildNodes(): bool
	{
		return $this->childCount() > 0;
	}

	/**
	 * Alias of {@link XML\Node::hasChildNodes()}
	 */
	public function hasChildren(): bool
	{
		return $this->hasChildNodes();
	}

	/**
	 * Tell how many attributes this particular XML node has
	 */
	public function attributeCount(): int
	{
		return is_countable($this->_attributes) ? count($this->_attributes) : 0;
	}

	public function removeChild($which): bool
	{
		if (isset($this->_children[$which]))
		{
			unset($this->_children[$which]);
			$this->_children = array_merge($this->_children);

			return true;
		}

		return false;
	}

	public function getChild($which)
	{
		return $this->_children[$which] ?? null;
	}

	public function hasAttributes(): bool
	{
		return $this->attributeCount() > 0;
	}

	/**
	 * Remove an attribute from the XML node
	 */
	public function removeAttribute(string $attr): bool
	{
		if ($this->attributeExists($attr))
		{
			unset($this->_attributes[$attr]);
			return true;
		}

		return false;
	}

	/**
	 * Resursive helper function for converting to XML
	 *
	 * @param XML\Node $node
	 * @param integer $tabs
	 * @param boolean $empty				A constant, one of: XML::XML_PRESERVE, XML::XML_DROP, XML::XML_COMPRESS
	 * @param string $indent
	 * @return string
	 */
	public function _asXMLHelper(Node $node, int $tabs, int $empty, string $indent): string
	{
		$xml = '';

		if ($node->childCount())
		{
			if (!empty($node->name()))
			{
				// Add the node's opening tag
				$xml .= str_repeat($indent, $tabs) . '<' . $node->name();
				foreach ($node->attributes() as $key => $value)
				{
					// Make sure double-encode is *off*
					//$xml .= ' ' . $key . '="' . XML::encode($value, true, false) . '"';
					$xml .= ' ' . $key . '="' . XML::encode($value) . '"';
				}
				$xml .= '>' . "\n";
			}

			// Add the child nodes
			foreach ($node->children() as $child)
			{
				$xml .= $this->_asXMLHelper($child, $tabs + 1, $empty, $indent);
			}

			if (!empty($node->name()))
			{
				// Add the node's closing tag
				$xml .= str_repeat($indent, $tabs) . '</' . $node->name() . '>' . "\n";
			}
		}
		else
		{
			if ($node->hasAttributes())		// if the node has attributes, we'll build the whole thing no matter what
			{
				$xml .= str_repeat($indent, $tabs) . '<' . $node->name();

				foreach ($node->attributes() as $key => $value)
				{
					// Double-encode is *off*
					//$xml .= ' ' . $key . '="' . XML::encode($value, true, false) . '"';
					$xml .= ' ' . $key . '="' . XML::encode($value) . '"';
				}

				// Double-encode is *off*
				//$xml .= '>' . XML::encode($node->data(), true, false) . '</' . $node->name() . '>' . "\n";
				$xml .= '>' . XML::encode($node->data()) . '</' . $node->name() . '>' . "\n";
			}
			else
			{
				if ($node->data() == '__EMPTY__')		// ick, bad hack
				{
					$xml .= str_repeat($indent, $tabs) . '<' . $node->name() . '></' . $node->name() . '>' . "\n";
				}
				else if ($node->hasData() || $empty == XML::XML_PRESERVE)
				{
					// Double-encode is *off*
					//$xml .= str_repeat($indent, $tabs) . '<' . $node->name() . '>' . XML::encode($node->data(), true, false) . '</' . $node->name() . '>' . "\n";
					$xml .= str_repeat($indent, $tabs) . '<' . $node->name() . '>' . XML::encode($node->data()) . '</' . $node->name() . '>' . "\n";
				}
				else if ($empty == XML::XML_COMPRESS)
				{
					$xml .= str_repeat($indent, $tabs) . '<' . $node->name() . ' />' . "\n";
				}
				else if ($empty == XML::XML_DROP)
				{
					// do nothing, drop the empty element
				}
			}
		}

		return $xml;
	}

	/**
	 * Get an XML representation of this XML node and it's child XML nodes
	 *
	 * @param int $todo_for_empty_elements	A constant, one of: QUICKBOOKS_XML_XML_COMPRESS, QUICKBOOKS_XML_XML_DROP, QUICKBOOKS_XML_XML_PRESERVE
	 * @param string $indent					The character to use to indent the XML with
	 * @return string
	 */
	public function asXML(int $todo_for_empty_elements = XML::XML_DROP, string $indent = "\t"): string
	{
		return $this->_asXMLHelper($this, 0, $todo_for_empty_elements, $indent);
	}

	/**
	 *
	 */
	protected function _asJSONHelper(Node $node, int $tabs, string $indent): string
	{
		$json = '';

		if ($node->childCount() or $node->attributeCount())	// container elements surrounded with { ... }
		{
			$json .= str_repeat($indent, $tabs) . $node->name() . ':{' . "\n";

			$list = [];
			foreach ($node->children() as $child)
			{
				$json .= $this->_asJSONHelper($child, $tabs + 1, $indent);
			}

			foreach ($node->attributes() as $key => $value)
			{
				$json .= str_repeat($indent, $tabs) . '"' . $key . '": "' . $value . '", ' . "\n";
			}

			$json .= "\n" . str_repeat($indent, $tabs) . '}';
		}
		else
		{
			$json .= str_repeat($indent, $tabs) . '"' . $node->name() . '": "' . $node->data() . '", ' . "\n";
		}

		return $json;
	}

	/**
	 * Get a JSON representation of this XML node
	 */
	public function asJSON(string $indent = "\t"): string
	{
		return '{' . "\n" . $this->_asJSONHelper($this, 1, $indent) . '}';
	}

	/**
	 * Get an array represtation of this XML node
	 */
	public function asArray(string $mode = XML::ARRAY_NOATTRIBUTES): array
	{
		switch ($mode)
		{
			case XML::ARRAY_EXPANDATTRIBUTES:
				return $this->_asArrayExpandAttributesHelper($this);

			case XML::ARRAY_BRANCHED:
				return $this->_asArrayBranchedHelper($this);

			case XML::ARRAY_PATHS:
				$current = '';
				$paths = [];
				$this->_asArrayPathsHelper($this, $current, $paths);
				return $paths;

			case XML::ARRAY_NOATTRIBUTES:
			default:
				return $this->_asArrayNoAttributesHelper($this);
		}
	}

	/**
	 * Helper function for converting to an array mapping paths to tag values
	 */
	protected function _asArrayPathsHelper(Node $node, string $current, array &$paths): void
	{
		if ($node->hasChildNodes())
		{
			foreach ($node->children() as $child)
			{
				$this->_asArrayPathsHelper($child, $current . ' ' . $node->name(), $paths);
			}
		}
		else if ($node->hasData())
		{
			$paths[trim($current . ' ' . $node->name())] = $node->data();
		}
	}

	/**
	 * Save this XML node structure as an XML file
	 *
	 * @param mixed $path_or_resource			The filepath of the file you want write to *or* an opened file resource
	 * @param string $mode						The mode to open the file in
	 * @param boolean $todo_for_empty_elements	A constant, one of: QUICKBOOKS_XML_XML_COMPRESS, QUICKBOOKS_XML_XML_DROP, QUICKBOOKS_XML_XML_PRESERVE
	 * @return bool								Whether or not the save was successful
	 */
	public function saveXML($path_or_resource, string $mode = 'wb', int $todo_for_empty_elements = XML::XML_COMPRESS): bool
	{
		$xml = $this->asXML($todo_for_empty_elements);

		if (is_resource($path_or_resource))
		{
			return false !== fwrite($path_or_resource, $xml);
		}

		return false !== file_put_contents($path_or_resource, $xml);
	}

	/**
	 * Save the XML node structure as a JSON document
	 *
	 * @param mixed $path_or_resource
	 * @param string $mode
	 * @return bool
	 */
	public function saveJSON($path_or_resource, string $mode = 'wb'): bool
	{
		$json = $this->_root->asJSON();

		if (is_resource($path_or_resource))
		{
			return false !== fwrite($path_or_resource, $json);
		}

		return false !== file_put_contents($path_or_resource, $json);
	}
}
