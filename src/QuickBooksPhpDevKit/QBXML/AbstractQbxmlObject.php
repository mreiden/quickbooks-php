<?php declare(strict_types=1);

/**
 * Base class for QuickBooks objects
 *
 * Copyright (c) {2010-04-16} {Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage Object
 */

namespace QuickBooksPhpDevKit\QBXML;

use QuickBooksPhpDevKit\{
	Cast,
	PackageInfo,
	Utilities,
	XML,
	XML\Node,
	XML\Parser,
};
use QuickBooksPhpDevKit\QBXML\{
	Object\Generic,
	Schema\AbstractSchemaObject,
};

/**
 * Base class for QuickBooks objects
 */
abstract class AbstractQbxmlObject
{
	/**
	 * QuickBooks XML parser option - preserve empty XML elements
	 */
	public const XML_PRESERVE = XML::XML_PRESERVE;

	/**
	 * QuickBooks XML parser option - drop empty XML elements
	 */
	public const XML_DROP = XML::XML_DROP;

	/**
	 * QuickBooks XML parser option - compress /> empty XML elements
	 */
	public const XML_COMPRESS = XML::XML_COMPRESS;

	/**
	 * Keys/values stored within the object
	 *
	 * @var array
	 */
	protected $_object = [];

	/**
	 * Controls whether this object adds the parent node's path to its own when it is a child node.
	 * Needed for ItemSitesQuery where multiple ListID tags are present but they are not contained in a child node (i.e. they are all direct children of the ItemSitesQueryRq node.
	 *
	 * @var bool
	 */
	protected $_parent_is_node = true;

	/**
	 * Create a new instance of this QuickBooks class
	 */
	public function __construct(array $arr)
	{
		$this->_object = $arr;
	}

	/**
	 * Return a constant indicating the type of object
	 */
	abstract public function object(): string;


	/**
	 * Set whether the parent's path should be added to the object's paths (see var $_parent_is_node)
	 */
	public function setParentIsNode(bool $isNode): bool
	{
		$this->_parent_is_node = $isNode;

		return true;
	}
	/**
	 * Get whether the parent's path should be added to the object's paths (see var $_parent_is_node)
	 */
	public function getParentIsNode(): bool
	{
		return $this->_parent_is_node;
	}

	/**
	 * Get the date/time this object was created in QuickBooks
	 *
	 * @param string $format		If you want the date/time in a particular format, specify the format here (use the notation from {@link http://www.php.net/date})
	 * @return string
	 */
	public function getTimeCreated(?string $format = null): string
	{
		if (!is_null($format))
		{
			return date($format, strtotime($this->get('TimeCreated')));
		}

		return $this->get('TimeCreated');
	}

	/**
	 * Get the date/time when this object was last modified in QuickBooks
	 *
	 * @param string $format		If you want the date/time in a particular format, specify the format here (use the notation from {@link http://www.php.net/date})
	 * @return string
	 */
	public function getTimeModified(?string $format = null): string
	{
		if (!is_null($format))
		{
			return date($format, strtotime($this->get('TimeModified')));
		}

		return $this->get('TimeModified');
	}

	public function setEditSequence($value): bool
	{
		return $this->set('EditSequence', (string) $value);
	}

	/**
	 * Get the QuickBooks EditSequence for this object
	 */
	public function getEditSequence(): ?string
	{
		return $this->get('EditSequence');
	}

	/**
	 * Set a value within the object
	 */
	public function set(string $key, $value, bool $cast = true): bool
	{
		if (is_array($value))
		{
			$this->_object[$key] = $value;
		}
		else if (false === $cast && null === $value)
		{
			// Unset (remove) the object property completely
			unset($this->_object[$key]);
		}
		else
		{
			//print('set(' . $key . ', ' . $value . ', ' . $cast . ')' . "\n");

			if ($cast && $value != '__EMPTY__')
			{
				// Use the full namespaced classname.
				// The classname excluding namespace is replaced with ->object().
				// This is necessary for the *Item Objects (DiscountItem, etc) and the Generic Object
				$classname = get_class($this);
				$classname = substr($classname, 0, 1 + strrpos($classname, "\\")) . $this->object();
				$value = Cast::cast($classname, $key, $value, true, false);
			}

			//print('	setting [' . $key . '] to value {' . $value . '}' . "\n");
			$this->_object[$key] = $value;
		}

		return true;
	}

	/**
	 * Get a value from the object
	 *
	 * @param string $key		The key to fetch the value for
	 * @param mixed $default	If there is no value set for the given key, this will be returned
	 * @return mixed			The value fetched
	 */
	public function get(string $key, $default = null)
	{
		if (isset($this->_object[$key]))
		{
			return $this->_object[$key];
		}

		return $default;
	}

	/**
	 * Get a FullName value (where : separates parent and child items)
	 *
	 * @param string $fullname_key		The key to set, i.e. FullName
	 * @param string $name_key			The 'Name' key, i.e. Name
	 * @param string $parent_key		The parent key, i.e. ParentRef_FullName
	 * @param mixed $default
	 * @return string
	 */
	public function getFullNameType(string $fullname_key, string $name_key, string $parent_key, ?string $default = null): string
	{
		$fullname = $this->get($fullname_key);
		if (!$fullname)
		{
			$name = $this->get($name_key);
			$parent = $this->get($parent_key);

			$fullname = ($name && $parent) ? "$parent:$name" : $name;
		}

		return $fullname;
	}

	public function setFields(array $fields): void
	{
		// Set field data
		foreach ($fields as $key => $value)
		{
			$setMethod = 'set' . $key;
			if (!method_exists($this, $setMethod))
			{
				throw new \Exception('Class ' . get_class($this) . ' does not have a method ' . $setMethod .'.');
			}

			if (is_array($value))
			{
				$this->$setMethod(...$value);
			}
			else
			{
				$this->$setMethod($value);
			}
		}
	}

	/**
	 * Set a Name field
	 */
	public function setNameType(string $name_key, string $value): bool
	{
		return $this->set($name_key, str_replace(':', '-', $value));
	}

	/**
	 * Set a FullName field
	 */
	public function setFullNameType(string $fullname_key, ?string $name_key, ?string $parent_key, string $value): bool
	{
		if (false === strpos($value, ':'))
		{
			// Value does not contain a colon, so there is no parent

			$key = $name_key ?? $fullname_key;
			$this->set($key, $value);

			if (null !== $parent_key && !empty($this->get($parent_key)))
			{
				// Parent key is set and shouldn't be since there is no parent
				$this->set($parent_key, '');
			}

			// Fetch the Name (need to fetch because they might have been casted/truncate)
			$value = $this->get($key);
		}
		else
		{
			if (!is_null($name_key) && !is_null($parent_key))
			{
				// This covers the case where we are setting FullName, which
				//	needs to be broken up into:
				//		Name
				//		ParentRef FullName

				$explode = explode(':', $value);
				$name = array_pop($explode);
				$parent = implode(':', $explode);

				$this->set($name_key, $name);
				$this->set($parent_key, $parent);

				// Build the parent name from the newly set Name and ParentRef (need to fetch because they might have been casted/truncate)
				$value = $this->get($parent_key) . ':' . $this->get($name_key);
			}
			else
			{
				// This covers the case where we are setting
				//	CustomerType_FullName, there is no separate parent element,
				//	so we just set the whole chunk
			}
		}

		return $this->set($fullname_key, $value);
	}

	/**
	 * Set a boolean value
	 */
	public function setBooleanType(string $key, $value): bool
	{
		//print('setting BooleanType [' . $key . '] to ' . $value . "\n");

		if (null === $value)
		{
			return $this->set($key, $value);
		}

		$value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (null === $value)
		{
			// Something other than true/false, 1/0, or boolean strings (i.e. 'true', 'false', 'on', 'off', 'yes', 'no'
			return false;
		}

		return $this->set($key, $value ? 'true' : 'false');
	}

	/**
	 *
	 */
	public function getBooleanType(string $key, ?bool $default = null): ?bool
	{
		if (!$this->exists($key))
		{
			return $default;
		}

		return filter_var($this->get($key), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
	}

	/**
	 * Set a date
	 *
	 * @param string $key		The key for where to store the date
	 * @param mixed $date		The date value (accepts anything www.php.net/strtotime can convert or unix timestamps)
	 * @return boolean
	 */
	public function setDateType(string $key, $date, bool $dont_allow_19691231 = true): bool
	{
		if (null === $date)
		{
			// This will unset the value
			return $this->set($key, null);
		}

		// $date must be a string or number
		if (!(is_string($date) || is_int($date) || is_float($date)))
		{
			return false;
		}

		// Convert numbers to strings (for unix timestamps)
		if (!is_string($date))
		{
			$date = (string) $date;
		}

		// Disallow empty string and '0'
		if ($date == '0' || !strlen($date))
		{
			return false;
		}

		// Disallow special date 1969-12-31.  This is the date you get from date('Y-m-d', null|false)
		if ($date == '1969-12-31' && $dont_allow_19691231)
		{
			return false;
		}

		// We assume an 8 digit number is a date in YYYMMDD format like 20190704 and
		// assume number strings longer than 8 digits are unix timestamps.
		if (ctype_digit($date) && strlen($date) > 8)
		{
			// This is a unix timestamp that needs to be cast back to an int for strict typing
			$date = (int) $date;
		}
		else
		{
			// Convert date string to a unix timestamp
			$date = strtotime($date);
			if ($date === false)
			{
				// Invalid Date: could not be parsed by strtotime
				return false;
			}
		}

		// Set the DateType value in the Y-m-d format QuickBooks expects
		return $this->set($key, date('Y-m-d', $date));
	}

	/**
	 * Get a date value
	 *
	 * @param string $key		Get a date value
	 * @param string $format	The format (any format from www.php.net/date)
	 * @return string
	 */
	public function getDateType(string $key, ?string $format = 'Y-m-d'): ?string
	{
		if (is_null($format) || !strlen($format))
		{
			$format = 'Y-m-d';
		}

		if ($this->exists($key))
		{
			$value = $this->get($key);

			// Convert value to unix timestamp
			$timestamp = strtotime($value);
			if ($timestamp !== false)
			{
				// Return the formatted date if strtotime successfully parsed the date
				return date($format, $timestamp);
			}
		}

		return null;
	}

	public function setAmountType(string $key, $amount): bool
	{
		$float = filter_var($amount, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
		if ($float === false)
		{
			throw new \TypeError('Argument 2 passed to ' . __METHOD__ . ' must be convertable to the type float');
		}

		return $this->set($key, sprintf('%01.2f', $float));
	}

	public function getAmountType(string $key): string
	{
		return sprintf('%01.2f', floatval($this->get($key)));
	}

	/**
	 * Tell if a data field exists within the object
	 */
	public function exists(string $key): bool
	{
		return isset($this->_object[$key]);
	}

	/**
	 * Removes a key from this object
	 */
	public function remove(string $key): bool
	{
		if (isset($this->_object[$key]))
		{
			unset($this->_object[$key]);
			return true;
		}

		return false;
	}

	public function getListItem(string $key, int $index)
	{
		$list = $this->getList($key);

		if (isset($list[$index]))
		{
			return $list[$index];
		}

		return null;
	}

	/**
	 *
	 *
	 */
	public function addListItem(string $key, $obj): bool
	{
		$list = $this->getList($key);

		$list[] = $obj;

		return $this->set($key, $list);
	}

	/**
	 *
	 */
	public function getList(string $key): array
	{
		$list = $this->get($key, []);

		if (!is_array($list))
		{
			$list = [];
		}

		return $list;
	}

	/**
	 *
	 */
	public function getArray(string $pattern, array $defaults = [], bool $defaults_if_empty = true): array
	{
		$list = [];
		foreach ($this->_object as $key => $value)
		{
			if ($this->_fnmatch($pattern, $key))
			{
				$list[$key] = $value;

				if ($defaults_if_empty &&
					empty($value) &&
					isset($defaults[$key]))
				{
					$list[$key] = $defaults[$key];
				}
			}
		}

		return array_merge($defaults, $list);
	}

	/**
	 * Do some fancy string matching
	 */
	protected function _fnmatch(string $pattern, string $str): bool
	{
		return Utilities::fnmatch($pattern, $str);
	}

	/**
	 * Get a qbXML schema object for a particular type of request
	 *
	 * Schema objects are used to build and validate qbXML requests and the
	 * fields and data types of qbXML elements.
	 *
	 * @param string $request		A valid QuickBooks API request (for example: CustomerAddRq, InvoiceQueryRq, CustomerModRq, etc.)
	 * @return QuickBooks_QBXML_Schema_Object
	 */
	protected function _schema(string $request): AbstractSchemaObject
	{
		if (strtolower(substr($request, -2, 2)) != 'rq')
		{
			$request = $request . 'Rq';
		}

		$class = PackageInfo::NAMESPACE_QBXML_SCHEMA_OBJECT ."\\$request";
		try
		{
			return new $class();
		}
		catch (\Exception $e)
		{
			return null;
		}
	}

	/**
	 * Convert this QuickBooks object to an XML node object representation
	 *
	 * @param string $root			The node to use as the root node of the XML node structure
	 * @param string $parent
	 * @return Node
	 */
	public function asXML(?string $root = null, ?string $parent = null, ?array $object = null): Node
	{
		if (is_null($root))
		{
			$root = $this->object();
		}

		if (is_null($object))
		{
			$object = $this->_object;
		}

		$Node = new Node($root);

		foreach ($object as $key => $value)
		{
			if (is_array($value))
			{
				$Node->setChildDataAt($root . ' ' . $key, '', true);

				foreach ($value as $sub)
				{
					//print('printing sub' . "\n");
					//print_r($sub);
					//print($sub->asXML());
					$Node->addChildAt($root, $sub->asXML(null, $root));
				}
			}
			else
			{
				$Node->setChildDataAt($root . ' ' . $key, $value, true);
			}
		}
		//print_r($Node);

		return $Node;
	}

	/**
	 * Get an array representation of this Class object
	 */
	public function asArray(string $request, bool $nest = true): array
	{
		$this->_cleanup();

		return [];
	}

	/**
	 * Perform any needed clean-up of the object data members
	 */
	protected function _cleanup(): bool
	{
		return true;
	}

	/**
	 * Convert this object to a valid qbXML request
	 *
	 * @todo Support for qbXML versions
	 *
	 * @param string $request	The type of request to convert this to (examples: "{Object}AddRq", "{Object}ModRq", "{Object}QueryRq")
	 * @param float  $version	The required qbXML version  ***[unused - does nothing and subclasses default to XML::XML_DROP]
	 * @param string $locale	The QuickBooks locale ('OE', 'AU', 'CA', 'UK', 'US')  ***[unused - subclasses default to "\t"]
	 * @param string $root		The node to use as the root node of the XML node structure  ***[unused in function]
	 * @return string
	 */
	public function asQBXML(string $request, ?float $version = null, ?string $locale = null, ?string $root = null): string
	{
		$todo_for_empty_elements = XML::XML_DROP;
		$indent = '  ';

		// Call any cleanup routines
		$this->_cleanup();

		// Add the Request suffix (Rq)
		if (strtolower(substr($request, -2, 2)) != 'rq')
		{
			$request .= 'Rq';
		}

		$RequestNode = new Node($request);

		$schema = $this->_schema($request);
		if ($schema)
		{
			$tmp = [];

			// Restrict it to a specific qbXML version?
			if ($version)
			{

			}

			// Restrict it to a specific qbXML locale?
			if ($locale)
			{
				$locales = $schema->localePaths();
			}

			// Get all this objects values as a list
			$thelist = $this->asList($request);

			// ReorderPaths reorders the properties in the order they appear in the QBXML spec.
			// It also discards fields that do not belong in the Action (e.g. AppliedToTxn and IsAutoApply are dropped from ReceivePaymentMod)
			$reordered = $schema->reorderPaths(array_keys($thelist));

			foreach ($reordered as $path) // This is a standard array, so we do not need the $key in the loop
			{
				$value = $this->_object[$path];
				if (is_array($value))
				{
					// This path is an array of objects (subnodes)

					$tmp[$path] = [];
					foreach ($value as $arr_original)
					{
						// Clone the object so we don't alter the original
						$arr = clone $arr_original;
						// $arr is the subnode QBXML object

						// $tmp2 will hold the subnode xml values
						$tmp2 = [];
						if ($arr->getParentIsNode())
						{
							foreach ($arr->asList('') as $inkey => $invalue)
							{
								//fwrite(STDERR, "\n\tsubnode: path=$path, inkey=$inkey, invalue=$invalue");
								$arr->set($path . ' ' . $inkey, $invalue);
								$arr->set($inkey, null, false);
							}
						}

						$schema_reordered_paths = $schema->reorderPaths(array_keys($arr->asList('')));
						foreach ($schema_reordered_paths as $fullpath)
						{
							// We need this later, so store it
							$subpath = $arr->getParentIsNode() ? substr($fullpath, strlen($path) + 1) : $path;
							//fwrite(STDERR, "\n\n\t\tfullpath: ". print_r($fullpath,true) . ' | subpath: '. print_r($subpath,true));

							// Skip adding this path if a version of QBXML is set and it was not added until a later QBXML version
							if (!$version || $version >= $schema->sinceVersion($fullpath))
							{
								// Skip adding this path if a locale is set and this path is in the list of disallowed locales
								if (!$locale || !isset($locales[$fullpath]) || !in_array($locale, $locales[$fullpath]))
								{
									$tmp2[$subpath] = $arr->get($fullpath);
								}

								if (isset($tmp2[$subpath]) && $schema->dataType($fullpath) == AbstractSchemaObject::TYPE_AMTTYPE)
								{
									// This value is an amount type, so format it with 2 decimal places
									$tmp2[$subpath] = sprintf('%01.2f', $tmp2[$subpath]);
								}
							}
						}

						//fwrite(STDERR, "\n\tSubnode values: ". print_r($tmp2,true) ."\n\nSubnode arrObject: \n".var_export($arr->object(),true));
						$tmp[$path][] = new Generic($tmp2, $arr->object());
					}
				}
				else
				{
					// This is a value and not a subnode, so format it based on its datatype in the schema.
					// Exclude it if it is in $locales ($locales is an exclude list of fields for the locale)

					// Do some simple data type casting...
					if ($schema->dataType($path) == AbstractSchemaObject::TYPE_AMTTYPE)
					{
						$this->_object[$path] = sprintf('%01.2f', $this->_object[$path]);
					}

					// Skip adding this path if a version of QBXML is set and it was not added until a later QBXML version
					if (!$version || $version >= $schema->sinceVersion($path))
					{
						// Skip adding this path if a locale is set and this path is in the list of disallowed locales
						if (!$locale || !isset($locales[$path]) || !in_array($locale, $locales[$path]))
						{
							$tmp[$path] = $this->_object[$path];
						}
					}
				}
			}

			// *DO NOT* change the source values of the original object!
			//$this->_object = $tmp;

			// Create and add the wrapper node if needed
			$wrapper = $schema->qbxmlWrapper();
			if ($wrapper)
			{
				$Node = $this->asXML($wrapper, null, $tmp);
				$RequestNode->addChild($Node);

				return $RequestNode->asXML($todo_for_empty_elements, $indent);
			}
			else if (count($this->_object) == 0)
			{
				// This catches the cases where we just want to get *all* objects
				//	back (no filters) and thus the root level qbXML element is *empty*
				//	and we need to *preserve* this empty element rather than just
				//	drop it (which results in an empty string, and thus invalid query)

				$Node = $this->asXML($request, null, $tmp);

				return $Node->asXML(XML::XML_PRESERVE, $indent);
			}

			$Node = $this->asXML($request, null, $tmp);

			return $Node->asXML($todo_for_empty_elements, $indent);
		}

		return '';
	}

	/**
	 * Convert this object to a valid qbXML request and warp in the appropriate QBXML or QBXMLPOS wrapper
	 */
	public function asCompleteQBXML(string $request, ?float $version = null, ?string $locale = null, ?string $root = null, ?string $onError = null): string
	{
		// Create the QBXML for the request
		$qbxml = $this->asQBXML($request, $version, $locale, $root);

		// Find the schema object and find if it is only in the QB POS sdk
		$schema = $this->_schema($request);
		$isQBXMLPOS = ($schema instanceof AbstractSchemaObject) ? $schema->isOnlyInQBPOS() : false;

		return $this->addQbXmlRequestWrapper($qbxml, $version, $onError, $isQBXMLPOS);
	}

	/**
	 * Add the XML and QBXML version declarations and the QBXML and QBXMLMsgsRq wrapping tags
	 */
	public function addQbXmlRequestWrapper(string $xmlRequest, ?float $qbxmlVersion = null, ?string $onError = null, bool $isQBXMLPOS = false): string
	{
		$onError = $onError ?? 'continueOnError';

		if ($isQBXMLPOS === true)
		{
			$tagName = 'QBXMLPOS';
			$default_version = 3.0;
		}
		else
		{
			$tagName = 'QBXML';
			$default_version = 13.0;
		}

		$qbxmlVersion = $qbxmlVersion ?? $default_version;
		if (is_float($qbxmlVersion) || is_int($qbxmlVersion))
		{
			$qbxmlVersion = number_format($qbxmlVersion, 1, '.', '');
		}

		$completeQBXML  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$completeQBXML .= '<?' . strtolower($tagName) .' version="' . $qbxmlVersion . '"?>' . "\n";
		$completeQBXML .= '<' . $tagName . '>' . "\n";
		$completeQBXML .= '  <' . $tagName . 'MsgsRq onError="' . $onError . '">' . "\n";
		$completeQBXML .=      $xmlRequest;
		$completeQBXML .= '  </' . $tagName . 'MsgsRq>' . "\n";
		$completeQBXML .= '</' . $tagName . '>' . "\n";

		return $completeQBXML;
	}

	/**
	 *
	 *
	 *
	 */
	public function asList(string $request)
	{
		$arr = $this->_object;

		/*
		foreach ($arr as $key => $value)
		{
			$arr[$key] = Cast::cast($object, $key, $value);
		}
		*/

		return $arr;
	}

	/**
	 *
	 *
	 */
	static protected function _fromXMLHelper(string $class, ?Node $XML): ?AbstractQbxmlObject
	{
		if (is_object($XML))
		{
			$paths = $XML->asArray(XML::ARRAY_PATHS);
			foreach ($paths as $path => $value)
			{
				$newpath = implode(' ', array_slice(explode(' ', $path), 1));
				$paths[$newpath] = $value;
				unset($paths[$path]);
			}

			return new $class($paths);
		}

		return null;
	}

	/**
	 * Convert a Node object to a QuickBooks_Object_* object instance
	 */
	static public function fromXML(Node $XML, string $action_or_object = null): ?AbstractQbxmlObject
	{
		if (!$action_or_object || $action_or_object == PackageInfo::Actions['QUERY_ITEM'])
		{
			$action_or_object = $XML->name();
		}

		$type = Utilities::actionToObject($action_or_object);

		//print('trying to create type: {' . $type . '}' . "\n");

		$class = PackageInfo::NAMESPACE_QBXML_OBJECT . "\\" . $type;
		$Object = static::_fromXMLHelper($class, $XML);

		if (!is_object($Object))
		{
			return null;
		}

		$children = [];
		switch ($Object->object())
		{
			case PackageInfo::Actions['OBJECT_BILL']:
				$children = [
					'ItemLineRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\Bill\\ItemLine", 'addItemLine'],
					'ExpenseLineRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\Bill\\ExpenseLine", 'addExpenseLine'],
				];
				break;

			case PackageInfo::Actions['OBJECT_CREDITMEMO']:
				$children = [
					'CreditMemoLineRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\CreditMemo\\CreditMemoLine", 'addCreditMemoLine'],
				];
				break;

			case PackageInfo::Actions['OBJECT_ESTIMATE']:
				$children = [
					'EstimateLineRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\Estimate\\EstimateLine", 'addEstimateLine'],
				];
				break;

			case PackageInfo::Actions['OBJECT_INVOICE']:
				$children = [
					'InvoiceLineRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\Invoice\\InvoiceLine", 'addInvoiceLine'],
				];
				break;

			case PackageInfo::Actions['OBJECT_JOURNALENTRY']:
				$children = [
					'JournalCreditLineRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\JournalEntry\\JournalCreditLine", 'addCreditLine'],
					'JournalDebitLineRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\JournalEntry\\JournalDebitLine", 'addDebitLine'],
				];
				break;

			case PackageInfo::Actions['OBJECT_PURCHASEORDER']:
				$children = [
					'PurchaseOrderLineRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\PurchaseOrder\\PurchaseOrderLine", 'addPurchaseOrderLine'],
				];
				break;

			case PackageInfo::Actions['OBJECT_RECEIVEPAYMENT']:
				$children = [
					'AppliedToTxnRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\ReceivePayment\\AppliedToTxn", 'addAppliedToTxn'],
				];
				break;

			case PackageInfo::Actions['OBJECT_SALESRECEIPT']:
				$children = [
					'SalesReceiptLineRet' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\SalesReceipt\\SalesReceiptLine", 'addSalesReceiptLine'],
				];
				break;

			case PackageInfo::Actions['OBJECT_SALESTAXGROUPITEM']:
				$children = [
					'ItemSalesTaxRef' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\ItemSalesTaxGroup\\ItemSalesTaxRef", 'addItemSalesTaxRef'],
				];
				break;

			case PackageInfo::Actions['OBJECT_UNITOFMEASURESET']:
				$children = [
					'RelatedUnit' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\UnitOfMeasureSet\\RelatedUnit", 'addRelatedUnit'],
					'DefaultUnit' => [PackageInfo::NAMESPACE_QBXML_OBJECT . "\\UnitOfMeasureSet\\DefaultUnit", 'addDefaultUnit'],
				];
				break;
		}

		foreach ($children as $node => $tmp)
		{
			$childclass = $tmp[0];
			$childmethod = $tmp[1];

			if (class_exists($childclass))
			{
				foreach ($XML->children() as $ChildXML)
				{
					if ($ChildXML->name() == $node)
					{
						$ChildObject = static::_fromXMLHelper($childclass, $ChildXML);
						$Object->$childmethod($ChildObject);
					}
				}
			}
			else
			{
				print('Missing class: ' . $childclass . "\n");
			}
		}

		return $Object;
	}

	/**
	 * Convert a qbXML string to a QBXML\Object\* object instance
	 */
	static public function fromQBXML(string $qbxml, ?string $action_or_object = null): ?AbstractQbxmlObject
	{
		$errnum = null;
		$errmsg = null;

		$Parser = new Parser($qbxml);
		$Doc = $Parser->parse($errnum, $errmsg);
		if ($Doc)
		{
			$XML = $Doc->getRoot();

			return static::fromXML($XML, $action_or_object);
		}

		return null;
	}
}
