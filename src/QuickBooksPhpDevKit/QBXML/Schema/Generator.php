<?php declare(strict_types=1);

/**
 *
 *
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage QBXML
 */

namespace QuickBooksPhpDevKit\QBXML\Schema;

use QuickBooksPhpDevKit\QBXML\Schema\AbstractSchemaObject;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;
use QuickBooksPhpDevKit\XML;
use QuickBooksPhpDevKit\XML\Parser;

/**
 *
 */
class Generator
{
	protected $_xml;

	protected $_xmlqb_file;
	protected $_xmlqb;

	protected $_xmlpos_file;
	protected $_xmlpos;

	protected $actions_written = [];


	protected $_php_object_template;
	protected $_object_dir;
	protected $_dir_data_schema_query_rq_rs;

	protected $_debug_actions = [];

	protected $_dom_document;
	protected $_dom_xpath;
	protected $_dom_paths_checked = [];


	public function __construct(?string $qbxml = null, ?string $qbxmlpos = null, ?string $php_object_template = null, ?string $object_dir = null, ?string $dir_data_schema_query_rq_rs = null)
	{
		ini_set('memory_limit', '128M');

		// Only generate the actions in $debug_actions (set to an empty array to generate everything)
		$this->_debug_actions = [
			//'AccountTaxLineInfoQueryRq',
			//'BillAddRq',
			//'BillAddRs',
			//'CustomerAddRq',
			//'CustomerMsgQueryRq',
			//'CustomerMsgQueryRs',
			//'InvoiceQueryRq',
			//'InvoiceQueryRs',
			//'ItemServiceQueryRq',
			//'ItemServiceQueryRs',
			//'SalesTaxCodeAdd',
			//'SalesTaxCodeMod',
			//'VendorAddRq',
		];


		if (PHP_SAPI !== 'cli')
		{
			//throw new \Exception('QuickBooks Schema Object generation must be run from command line (cli)');
			//exit;
		}

		// QuickBooks XML file
		if (is_null($qbxml)) {
			$qbxml = dirname(__FILE__, 5) . '/data/qbxmlops130.xml';
		}
		$this->_xmlqb_file = realpath($qbxml);
		if (false === $this->_xmlqb_file)
		{
			// Realpath returns false if the file does not exist
			throw new \Exception('QuickBooks XML file does not exist: "' . $qbxml . '"');
		}
		// We must load the xml contents into _xml instead of using a filepath.
		// Using a filepath discards the comments and need to extract information out of the Intuit comments.
		$this->_xmlqb = file_get_contents($this->_xmlqb_file);
		if (false === $this->_xmlqb)
		{
			throw new \Exception('QuickBooks XML file could not be read: "' . $qbxml . '"');
		}


		// QuickBooks POS XML file
		if (is_null($qbxmlpos)) {
			$qbxmlpos = dirname(__FILE__, 5) . '/data/qbposxmlops30.xml';
		}
		$this->_xmlpos_file = realpath($qbxmlpos);
		if (false === $this->_xmlpos_file)
		{
			// Realpath returns false if the file does not exist
			throw new \Exception('QuickBooks POS XML file does not exist: "' . $qbxmlpos . '"');
		}
		// We must load the xml contents into _xml instead of using a filepath.
		// Using a filepath discards the comments and need to extract information out of the Intuit comments.
		$this->_xmlpos = file_get_contents($this->_xmlpos_file);
		if (false === $this->_xmlpos)
		{
			throw new \Exception('QuickBooks POS XML file could not be read: "' . $qbxmlpos . '"');
		}



		// Template file for generating PHP classes
		if (is_null($php_object_template))
		{
			$php_object_template = __DIR__ . '/Template.php';
		}
		$template_file = $php_object_template;
		$php_object_template = realpath($php_object_template);
		if (false === $php_object_template)
		{
			// Realpath returns false if the file does not exist
			throw new \Exception('PHP Schema Object Class template file does not exist: "' . $template_file . '"');
		}
		$this->_php_object_template = file_get_contents($php_object_template);
		if (false === $this->_php_object_template)
		{
			throw new \Exception('PHP Schema Object Class template could not be read: "' . $template_file . '"');
		}


		// Object output directory
		if (is_null($object_dir)) {
			$object_dir = __DIR__ . '/Object';
		}
		$dir = $object_dir;
		$object_dir = realpath($object_dir);
		if (false === $object_dir)
		{
			// Realpath returns false if the directory does not exist
			throw new \Exception('Output directory does not exist: "' . $dir . '"');
		}
		$this->_object_dir = $object_dir;


		// Object output directory
		if (is_null($dir_data_schema_query_rq_rs)) {
			$dir_data_schema_query_rq_rs = dirname(__FILE__, 5) . '/data/schema';
		}
		$dir_queries = $dir_data_schema_query_rq_rs;
		$dir_data_schema_query_rq_rs = realpath($dir_data_schema_query_rq_rs);
		if (false === $dir_data_schema_query_rq_rs)
		{
			// Realpath returns false if the directory does not exist
			throw new \Exception('Query schema output directory does not exist: "' . $dir_queries . '"');
		}
		$this->_dir_data_schema_query_rq_rs = $dir_data_schema_query_rq_rs;


		// Output locations
		echo "\n";
		echo "XML File Location:                {$this->_xmlqb_file}\n";
		echo "XML POS File Location:            {$this->_xmlpos_file}\n";
		echo "Template File Location:           $template_file\n";
		echo "Object Output Directory:          $dir/\n";
		echo "Query Rq and Rs Schema Directory: $dir_data_schema_query_rq_rs/\n\n";
		//sleep(10);
		//exit;
	}


	public function saveAll(): bool
	{
		$xml_sources = [
			'qbxml' => &$this->_xmlqb,
			'qbpos' => &$this->_xmlpos,
		];

		$package_root = realpath(dirname(__FILE__, 5));

		$arr_actions_adds = Utilities::listActions('*Add', false);
		$arr_actions_mods = Utilities::listActions('*Mod', false);
		$arr_actions_query = Utilities::listActions('*Query', false);
		//echo "\n\nAdd Actions: ", count($arr_actions_adds);
		//print_r($arr_actions_adds);
		//echo "\nMod Actions: ", count($arr_actions_mods) ."\n\n";
		//print_r($arr_actions_mods);
		//echo "\nQuery Actions: ", count($arr_actions_query) ."\n\n";
		//print_r($arr_actions_query);
		//exit;


		foreach ($xml_sources as $sourceXmlType => &$this->_xml)
		{
			echo "\n\n\n **********************   Parsing $sourceXmlType  file ************\n";

			// Find Intuit SDK version from XML
			if ($sourceXmlType == 'qbxml')
			{
				if (1 !== preg_match('/<\?qbxml version\s*=\s*"([0-9.]+)"\?>/i', $this->_xml, $matches))
				{
					throw new \Exception("XML is not a QBXML file (cannot find QBXML version declaration.");
				}

				$file_location = $this->_xmlqb_file;
				$file_header_version = '<?qbxml version="' . $matches[1] . '" ?>';
				$tag_container = 'QBXML';
				$_is_qbxmlpos_constant = '';

				$xml_groups = [0,1,2];
			}
			else if ($sourceXmlType == 'qbpos')
			{
				if (1 !== preg_match('/<\?qbposxml version\s*=\s*"([0-9.]+)"\?>/i', $this->_xml, $matches))
				{
					throw new \Exception("XML is not a QBXML POS file (cannot find QBXML POS version declaration.");
				}

				$file_location = $this->_xmlpos_file;
				$file_header_version = '<?qbposxml version="' . $matches[1] . '" ?>';
				$tag_container = 'QBPOSXML';
				$_is_qbxmlpos_constant = "\n" . implode("\n", [
					"\t/**",
					"\t * This QuickBooks Action is found only in the QuickBooks Point of Sale (QBPOS) sdk.",
					"\t * @var bool",
					"\t */",
					"\tprotected \$_isQbxmlPosOnly = true;"
				]) . "\n";

				$xml_groups = [0,1];
			}

			// Use a package relative path for the qbxmlops xml files in the generated file warning
			$qxmlops_file = str_replace($package_root, '', $file_location);
			$file_header_warning = 'WARNING!!!: This file is generated by QuickBooksPhpDevKit\QBXML\Schema\Generator using the ' . $qxmlops_file . ' file in this package.';

			// Set up the header and footer template for the QueryRq and QueryRs xml files in data/schema
			$file_header = '<?xml version="1.0" encoding="utf-8" ?>' . "\n";
			$file_header .= $file_header_version . "\n";
			$file_header .= '<!-- ' . $file_header_warning . ' -->' . "\n";
			$file_header .= '<' . $tag_container . '>' . "\n";

			$file_footer = '</' . $tag_container . '>' . "\n";



			$errnum = 0;
			$errmsg = '';
			$action_counter = 0;

			// Parse the QuickBooks XML file
			$Parser = new Parser($this->_xml);
			if ($Doc = $Parser->parse($errnum, $errmsg))
			{
				$groups = $Doc->children();
				foreach ($xml_groups as $iChild) {
					$children = $groups[$iChild]->children();
					foreach ($children as $Action)
					{
						// If _debug_actions is set in the constructor, skip any actions not in that array
						if (!empty($this->_debug_actions) && !in_array($Action->name(), $this->_debug_actions))
						{
							continue;
						}

						// Print what QuickBooks action is being worked on
						print(' ***** Action name is: ' . $Action->name() . "\n");

						// Check if this action has already been done so we don't overwrite it with the QB POS
						if (array_key_exists(strtolower($Action->name()), $this->actions_written))
						{
							print('    -> Skipped (already written)' . "\n");
							continue;
						}


						$isQueryRq = false;
						$isQueryRs = false;
						if (substr($Action->name(), -2) == 'Rs')
						{
							if (substr($Action->name(), -7) != 'QueryRs')
							{
								// We only need the Response XML for Query Actions, so skip this non-Query Response Action
								continue;
							}
							$isQueryRs = true;
						}
						else if (substr($Action->name(), -7) == 'QueryRq')
						{
							$isQueryRq = true;
						}


						// This prints out the Node object for this Action
						//print_r($Action);

						// Get the XML string for this Action so the comments can be parsed
						$section = $this->_extractSectionForTag($this->_xml, $Action->name());
						//print("\n\nXML Section for " . $Action->name() .":\n".$section."\n");


						//print "\n\n".$Action->name();
						if ($isQueryRq || $isQueryRs)
						{
							//print("\tIs a Query Request/Response node\n");
							// LIBXML_NOXMLDECL does not seem to work with DomDocument->saveXML() ??
							$load_options = LIBXML_COMPACT | LIBXML_NOXMLDECL;

							$this->_dom_document = new \DomDocument();
							$dom = &$this->_dom_document;
							$dom->preserveWhiteSpace = false;

							$loaded = $dom->loadXML(trim($section), $load_options);
							if (false === $loaded)
							{
								print("\n\n\n$section\n");
								throw new \Exception('Error loading xml section for ' . $Action->name());
							}
							$dom->formatOutput = true;
							$dom->xmlVersion = '1.0';
							$dom->encoding = 'utf-8';

							$this->_dom_xpath = new \DOMXpath($dom);
							$xpath = &$this->_dom_xpath;

							// Replace all the comments with "optional/required" and "may repeat" notation for
							// the QueryRq/QueryRs xml template files in data/schema
							$result = $xpath->query('//comment()');
							if ($result->length > 0)
							{
								for ($i = $result->length - 1; $i >= 0; $i--)
								{
									$commentNode = $result->item($i);

									$comment = $commentNode->nodeValue;
									if (1 === preg_match('/^\s*((?:BEGIN\s+|END\s+)?OR\b)/i', $comment, $matches))
									{
										// Only Keep " BEGIN OR ", " OR ", OR " END OR" and drop everything after
										$commentNode->data = " {$matches[1]} ";
									}
									else
									{
										$parse = $this->_parseComment($commentNode->nodeValue);
										//print("Comment: ". $commentNode->nodeValue."\n");
										//print_r($parse);

										// Change the opening comment tag to "<!----" as a default if the comment is not the list of modifiers
										$new_comment = '--' . rtrim($commentNode->nodeValue);
										if (true === $parse['found'])
										{
											// Use the parsed modifiers information to set a new comment

											$new_comment = [];

											$new_comment[] = (true === $parse['isoptional']) ? 'optional' : 'required';

											if (true === $parse['mayrepeat'])
											{
												$new_comment[] = 'may repeat';
											}

											$new_comment = '  ' . implode(', ', $new_comment);
										}

										$commentNode->data = $new_comment . ' ';
									}
								}
							}

							// Remove the requestID from the $Action->name() opening tag
							$result = $xpath->query('/'.$Action->name());
							if ($result->length !== 1)
							{
								throw new \Exception("Error finding primary node for " . $Action->name());
							}
							$node = $result->item(0);
							if (null !== $node->attributes)
							{
								$remove = ['requestid'];
								for ($i = $node->attributes->length - 1; $i >= 0; $i--)
								{
									$attr = $node->attributes->item($i);

									if (in_array(strtolower($attr->nodeName), $remove))
									{
										$node->removeAttribute($attr->nodeName);
									}
								}
							}
							//var_dump($node);
						}



						$paths_datatype = [];
						$paths_maxlength = [];
						$paths_isoptional = [];
						$paths_sinceversion = [];
						$paths_isrepeatable = [];
						$paths_reorder = [];
						$paths_notinlocale = [];

						// Set the wrapping XML tag
						$wrapper = '';
						if ($Action->hasChildren())
						{
							$first = $Action->getChild(0);

							//print_r($first);

							if (in_array($first->name(), $arr_actions_mods) ||
								in_array($first->name(), $arr_actions_adds))
							{
								$wrapper = $first->name();
								//print('	WRAPPER NODE IS: ' . $wrapper . "\n");
							}
						}

						//exit;

						$lastdepth = 0;
						$paths = $Action->asArray(XML::ARRAY_PATHS);

						// Actions with no parameters cause problems, so add the root element if there are no paths
						if (is_array($paths) && (count($paths) === 0))
						{
							$paths[$Action->name()] = 'string';
						}

						foreach ($paths as $path => $datatype)
						{
							$path_parts = explode(' ', $path);
							$tag = end($path_parts);

							//print("Path: $path\n");
							$parentComment = '';
							$parentCommentOverrides = [];
							if (count($path_parts) < 4)
							{
								$small_section = $section;
							}
							else
							{
								// Traverse down parent nodes to carry those restrictions down to the path node.
								// For example, if a node is restricted from OE, then all its children should be too.
								// If the node itself has a futher resticted from AU, then that node should be [OE, AU].
								// If a parent node has a version restriction of 10.0, then all children nodes should be 10.0 even
								// if a child node has a restriction of 4.0 (like some of the Address Addr4 nodes).
								$small_section = $section;
								for ($ss = 2; $ss < count($path_parts)-1; $ss++)
								{
									$small_section = $this->_extractSectionForTag($small_section, $path_parts[$ss]);
									$small_lines = explode("\n", trim($small_section));
									if (count($small_lines) > 1)
									{
										if (preg_match('/<!--(.*)-->/', $small_lines[0], $matches))
										{
											$parentComment = trim($matches[1]);
											$parentParse = $this->_parseComment($parentComment);
											if (!empty($parentParse['version']))
											{
												if (empty($parentCommentOverrides['version']) ||
													($parentParse['version'] > $parentCommentOverrides['version']))
												{
													$parentCommentOverrides['version'] = $parentParse['version'];
												}
											}
											if (!empty($parentParse['locales']))
											{
												if (empty($parentCommentOverrides['locales']))
												{
													$parentCommentOverrides['locales'] = $parentParse['locales'];
												}
												else
												{
													$parentCommentOverrides['locales'] = array_unique(array_merge($parentParse['locales'], $parentCommentOverrides['locales']));
												}
											}
										}
									}
								}
							}

							// This is path node we are interested in.
							$comment = $this->_extractCommentForTag($small_section, $tag);
							//print("\t{" . $path . '} => ' . $datatype . ' (' . $comment . ') [' . $parentComment ."]\n");
							$parse = $this->_parseComment($comment);
							if (!empty($parentCommentOverrides['version']))
							{
								if (empty($parse['version']))
								{
									$parse['version'] = $parentCommentOverrides['version'];
								}
								else
								{
									$parse['version'] = max($parse['version'], $parentCommentOverrides['version']);
								}
							}
							if (!empty($parentCommentOverrides['locales']))
							{
								$parse['locales'] = array_unique(array_merge($parse['locales'], $parentCommentOverrides['locales']));
							}
							$parse = array_merge($parse, $parentCommentOverrides);
							//print_r($parse);
							//print("\n");

							if ($isQueryRq || $isQueryRs)
							{
								// Query nodes should be optional if there is a "BEGIN OR" or a "OR" line above it.
								$part_path = array_shift($path_parts);
								while ($path_sub = array_shift($path_parts))
								{
									$part_path .= " $path_sub";
									$this->addNodeOptionalComment($part_path);
								}

								$outXML = '<' . $groups[$iChild]->name() .' onError="stopOnErrror">' . "\n";
								$outXML .= $dom->saveXML($dom->documentElement, LIBXML_NOEMPTYTAG)."\n";
								$outXML .= '</' . $groups[$iChild]->name() .'>';

								$outXML = preg_replace('/^\s+/m', '', $outXML);
								$outXML = preg_replace('/\n\s*<!-- /', ' <!--', $outXML);
								$outXML = preg_replace('/\s*<!--\s*((?:(BEGIN|END)\s+)?OR\b)/i', "\n".'<!-- $1', $outXML);

								$outXML = str_replace('<!---- ', '<!-- ', $outXML);
								//print("\n\nXML Section (2) for " . $Action->name() .":\n$outXML\n");
							}

							if ($path != $Action->name())
							{
								$path = trim(substr($path, strlen($Action->name())));
							}

							if (strlen($wrapper) &&
								substr($path, 0, strlen($wrapper)) == $wrapper)
							{
								$path = substr($path, strlen($wrapper) + 1);
							}

							// This is what is used to create the schema paths and restriction files (QBXML\Schema\Object\*)
							$paths_datatype[$path] = $datatype;
							$paths_isoptional[$path] = $parse['isoptional'];

							if (!empty($parse['maxlength']))
							{
								$paths_maxlength[$path] = $parse['maxlength'];
							}

							if (!empty($parse['version']))
							{
								$paths_sinceversion[$path] = $parse['version'];
							}

							if (!empty($parse['mayrepeat']))
							{
								$paths_isrepeatable[$path] = $parse['mayrepeat'];
							}

							if (!empty($parse['locales']))
							{
								$paths_notinlocale[$path] = $parse['locales'];
							}


							$curdepth = substr_count($path, ' ');
							if ($curdepth - $lastdepth > 1)
							{
								$tmp2 = explode(' ', $path);
								for ($i = 1; $i < count($tmp2); $i++)
								{
									$paths_reorder[] = implode(' ', array_slice($tmp2, 0, $i));
								}
							}
							$lastdepth = substr_count($path, ' ');

							$paths_reorder[] = $path;
						}


						if (substr($Action->name(), -7) == 'QueryRq')
						{
							// Save Query Request XML to $dir_data_schema_query_rq_rs
							$query_rs_rq_content = $file_header . $outXML . $file_footer;
							$file = $this->_dir_data_schema_query_rq_rs . '/'. substr($Action->name(), 0, -2) . '.xml';
							//echo "\n\n$file:\n$query_rs_rq_content";
							if (false === file_put_contents($file, $query_rs_rq_content))
							{
								throw new \Exception('Could NOT write Query/Request XML to file: ' . $file);
							}

							// Save that we completed this QueryRq action so the QB POS file run does not overwrite it
							$this->actions_written[strtolower($Action->name())] = true;
						}
						else if (substr($Action->name(), -2) == 'Rs')
						{
							if (substr($Action->name(), -7) == 'QueryRs')
							{
								// Save Query Response XML to $dir_data_schema_query_rq_rs
								$file = $this->_dir_data_schema_query_rq_rs . '/'. substr($Action->name(), 0, -2) . '.xml';
								if (!file_exists($file) || !is_readable($file))
								{
									throw new \Exception('Could NOT read Query/Request XML file: '. $file);
								}
								$query_rs_rq_content = trim(file_get_contents($file));
								$query_rs_rq_content = substr($query_rs_rq_content, 0, strrpos($query_rs_rq_content, '</' . $tag_container . '>'));
								$query_rs_rq_content .= "\n" . $outXML . "\n" . $file_footer;

								//echo "\n\n$file:\n$query_rs_rq_content";
								if (false === file_put_contents($file, $query_rs_rq_content))
								{
									throw new \Exception('Could NOT write Query/Response XML to file: ' . $file);
								}

								// Save that we completed this QueryRs action so the QB POS file run does not overwrite it
								$this->actions_written[strtolower($Action->name())] = true;
							}

							// Do not proceed with saving the parsed php files for Rs actions.
							// We do not want to generate QBXML\Schema\Object classes for Queries and only
							// use the modified xml files we just wrote.
							continue;
						}

						//print(var_export($paths_datatype));
						//print(var_export($paths_maxlength));
						//print(var_export($paths_isoptional));
						//print(var_export($paths_sinceversion));
						//print(var_export($paths_isrepeatable));
						//print(var_export($paths_reorder));
						foreach ($paths_notinlocale as $key => &$value)
						{
							$value = "['" . implode("','", $value) . "'],";
						}
						$paths_notinlocale = $this->renderVar($paths_notinlocale, true);
						$paths_notinlocale = str_replace(["'[", "\\'", "],'"], ['[', "'", ']'], $paths_notinlocale);
						//print(var_export($paths_notinlocale));

						$class_name = $Action->name();
						// Special handling for the Class object since "Class" is a PHP reserved word
						if ($class_name == 'Class')
						{
							$class_name = 'Qbclass';
						}

						$contents = $this->_php_object_template;
						$contents = str_replace('Template', $class_name, $contents);
						$contents = str_replace("'_warning'", $file_header_warning, $contents);
						$contents = str_replace("'_is_qbxmlpos_constant'", $_is_qbxmlpos_constant, $contents);
						$contents = str_replace("'_qbxmlWrapper'", $this->renderVar($wrapper), $contents);
						$contents = str_replace("'_dataTypePaths'", $this->renderVar($paths_datatype), $contents);
						$contents = str_replace("'_maxLengthPaths'", $this->renderVar($paths_maxlength), $contents);
						$contents = str_replace("'_isOptionalPaths'", $this->renderVar($paths_isoptional), $contents);
						$contents = str_replace("'_sinceVersionPaths'", $this->renderVar($paths_sinceversion), $contents);
						$contents = str_replace("'_isRepeatablePaths'", $this->renderVar($paths_isrepeatable), $contents);
						$contents = str_replace("'_notInLocale'", $paths_notinlocale, $contents);
						$contents = str_replace("'_reorderPaths'", $this->renderVar($paths_reorder, true), $contents);

						$file = $this->_object_dir . '/' . $class_name . '.php';
						$written = file_put_contents($file, $contents);
						if (false === $written)
						{
							throw new \Exception('Could NOT write '. $class_name .' file: "' . $file . '"');
						}

						// Save that we completed this Action so the QB POS run does not overwrite it
						$this->actions_written[strtolower($Action->name())] = true;

						print("\n\n");
						if ($action_counter > 350)
						{
							exit("\n\n\n *****  LIMIT OF 350 REACHED  *****\n\n");
						}

						$action_counter++;
						//exit("\n\n***** Line ". __LINE__."\n");
					}
				}
			}
		}

		return true;
	}


	protected function addNodeOptionalComment(string $path): void
	{
		if (array_key_exists($path, $this->_dom_paths_checked))
		{
			// Already did this path
			return;
		}
		$this->_dom_paths_checked[$path] = true;
		//print("\n\n**** Part Path: $path\n");

		$dom = &$this->_dom_document;
		$xpath = &$this->_dom_xpath;

		$xquery = '/'.str_replace(' ', '/', trim($path));
		//print("XPATH Query: $xquery ($path)\n");
		$result = $xpath->query($xquery);
		if ($result->length !== 1)
		{
			throw new \Exception("Error finding primary node for " . $Action->name());
		}
		$node = $result->item(0);
		//var_dump($node);

		// Need to see if this node contains any child xml nodes
		$isParentNode = false;
		if (null !== $node->childNodes)
		{
			for ($i = $node->childNodes->length - 1; $i >= 0; $i--)
			{
				$child = $node->childNodes->item($i);
				if ($child instanceof \DOMElement)
				{
					$isParentNode = true;
					break;
				}
			}
		}
		//print("\nisParentNode: ".var_export($isParentNode,true)."\n");

		// Force as optional if the comment before the node is " BEGIN OR " or " OR "
		$is_optional = 'required';
		if (null !== $node->previousSibling && $node->previousSibling instanceof \DOMComment)
		{
			//echo "\nComment: "; var_dump($node->previousSibling->nodeValue);
			if (1 === preg_match('/^\s*((?:BEGIN\s+)?OR\b)/i', $node->previousSibling->nodeValue, $matches))
			{
				// Node is optional
				$is_optional = 'optional';
				//print('  Forced as OPTIONAL');
			}
		}


		$is_optional_new_comment = "  $is_optional ";
		if (true === $isParentNode)
		{
			// Parent Node: Optional comment should be first child

			if (null === $node->firstChild)
			{
				$comment = $dom->createComment($is_optional_new_comment);
				$node->appendChild($comment);
			}
			else
			{
				if (($node->firstChild instanceof \DOMComment)
					&& (substr($node->firstChild->nodeValue, 0, 2) != '--')
					&& (1 !== preg_match('/^\s*(?:(?:BEGIN|END)\s+)?OR\s*$/i', $node->firstChild->nodeValue)))
				{
					// Use the existing comment tag
					$comment = $node->firstChild;
					$new_comment = $node->firstChild->nodeValue;
					if (1 === preg_match('/\b(optional|required)\b/', $new_comment, $matches))
					{
						$new_comment = preg_replace('/\b(optional|required)\b/', 'optional', $new_comment);
					}
					else
					{
						$new_comment = '  optional, ' . trim($new_comment) . ' ';
					}
					$comment->data = $new_comment;
				}
				else
				{
					// Create a new comment tag
					$comment = $dom->createComment($is_optional_new_comment);
					$parent = $node->parentNode;
					$child = $node->firstChild;
					//print("Parent:\n");var_dump($parent);
					//print("First Child:\n");var_dump($child);
					$node->insertBefore($comment, $node->firstChild);
				}
			}
		}
		else
		{
			// Not Parent Node: Optional comment should be nextSibling

			if (null === $node->nextSibling)
			{
				$comment = $dom->createComment($is_optional_new_comment);
				$node->parentNode->appendChild($comment);
			}
			else
			{
				if (($node->nextSibling instanceof \DOMComment)
					&& (substr($node->nextSibling->nodeValue, 0, 2) != '--')
					&& (1 !== preg_match('/^\s*(?:(?:BEGIN|END)\s+)?OR\s*$/i', $node->nextSibling->nodeValue)))
				{
					// Use the existing comment tag

					//print("\nExisting Comment: ". $node->nextSibling->nodeValue);
					$comment = $node->nextSibling;
					$new_comment = $node->nextSibling->nodeValue;
					if ($is_optional == 'optional')
					{
						if (1 === preg_match('/\b(optional|required)\b/i', $new_comment, $matches))
						{
							$new_comment = preg_replace('/\b(optional|required)\b/', 'optional', $new_comment);
						}
						else
						{
							$new_comment = '  optional, ' . trim($new_comment) . ' ';
						}

						// Update the XML comment string
						$comment->data = $new_comment;
					}
				}
				else
				{
					// Create a new comment tag
					$comment = $dom->createComment($is_optional_new_comment);
					$node->parentNode->insertBefore($comment, $node->nextSibling);
				}
			}
		}

		$outXML = $dom->saveXML();
		$outXML = preg_replace('/^\s+/m', '', $outXML);
		$outXML = preg_replace('/\n\s*<!-- /', ' <!--', $outXML);
		$outXML = preg_replace('/\s*<!--\s*((?:(BEGIN|END)\s+)?OR\b)/i', "\n".'<!-- $1', $outXML);
		//print("\n\nXML Section (2) for " . $Action->name() .":\n$outXML\n");


		// Useful to debug if a node's comment is incorrect (optional vs required, etc)
		$break_on_xpath = [
			//'/InvoiceQueryRq/TxnDateRangeFilter',
			//'/InvoiceQueryRs/InvoiceRet/EditSequence',
			//'/ItemServiceQueryRq/ClassFilter',
			//'/ItemServiceQueryRq/NameFilter',
		];
		if (!empty($break_on_xpath) && in_array($xquery, $break_on_xpath))
		{
			echo "\n\n\n********\n$outXML\n";
			exit("\n\n***** Line ". __LINE__."\n");
		}
	}




	/**
	 * Extracts the actual XML node string (including comments!!) for the specified tag from the XML document
	 */
	protected function _extractSectionForTag(string $xml, string $tag): string
	{
		if (false !== ($start_open = strpos($xml, '<' . $tag)) &&
			false !== ($start_close = strpos($xml, '>', $start_open)) &&
			false !== ($stop_open = strpos($xml, '</' . $tag . '>')))
		{
			return substr($xml, $start_open, $stop_open - $start_open + strlen($tag) + 3);
		}

		// This is for single node empty tags (e.g. '<AccountTaxLineInfoQueryRq requestID = "UUIDTYPE"/>	 <!-- not in QBUK|QBOE, v7.0 -->')
		if (1 === preg_match('#^\s*(<'.$tag.'[^/]*)/>(\s*<!--.*-->)?\s*$#mi', $xml, $matches))
		{
			return $matches[1] . '></' . $tag .'>' . ($matches[2] ?? '');
		}

		return '';
	}

	/**
	 * Extracts the comment associated with the specified tag
	 */
	protected function _extractCommentForTag(string $section, string $tag): string
	{
		if (false !== ($start_open = strpos($section, '<' . $tag)) &&
			false !== ($start_close = strpos($section, '>', $start_open)) &&
			false !== ($stop_open = strpos($section, '</' . $tag . '>')))
		{
			$str = substr($section, $stop_open + strlen($tag) + 3);
			$arr = explode("\n", $str);

			$line = $arr[0];

			return trim($line, "<!- >\n\r");
		}

		return '';
	}

	/**
	 * Parse the XML comment to set the important flags mayrepeat, isoptional, maxlength, and version
	 */
	protected function _parseComment(string $comment): array
	{
		$values = [
			'isoptional' => false,
			'mayrepeat' => false,
			//'maxlength' => 0,
			'locales' => [],
		];

		$found = false;
		if (false !== strpos($comment, 'opt'))
		{
			$found = true;
			$values['isoptional'] = true;
		}

		if (false !== strpos($comment, 'rep'))
		{
			$found = true;
			$values['mayrepeat'] = true;
		}

		if (false !== ($pos = strpos($comment, 'max length')))
		{
			// QBXML
			$found = true;
			$values['maxlength'] = (int) trim(substr($comment, $pos + 10), ' =');
		}
		else if (false !== ($pos = strpos($comment, 'field max')))
		{
			// QBXMLPOS
			$found = true;
			$values['maxlength'] = (int) trim(substr($comment, $pos + 9), ' =');
		}

		if (1 === preg_match('/not in ((?:QB[A-Z]+\|?)+)/i', $comment, $matches))
		{
			// Don't set $found=true to leave the Action Node comment alone (e.g. "CustomerMsgAddRq    <!-- not in QBOE -->")
			// Replace the QuickBooks QBD, QBCA, QBUK, QBAU, QBOE labels with ones that match PackageInfo::Locale[]
			//$found = true;
			$values['locales'] = explode('|', str_replace(['QBD','QBCA','QBUK','QBAU','QBOE'], ['US','CA','UK','AU','OE'], $matches[1]));
		}

		if (1 === preg_match('/v(\d+\.\d+)\s*$/', $comment, $matches))
		{
			//$found = true;
			$values['version'] = (float) $matches[1];
		}

		$values['found'] = $found;

		return $values;
	}



	/**
	 * @param array|string	$input
	 * @param bool			$fixRootNodes
	 * @return string
	 */
	private function renderVar($input, bool $fixRootNodes = false): string
	{
		if (is_array($input))
		{
			$spacer_inner = "\t\t";
			$spacer_outer = "\t";

			if ($this->isSequential($input))
			{
				if ($fixRootNodes)
				{
					$input = $this->fixRootNodes($input);
				}
				$input = array_map('trim', $input);

				return "[\n$spacer_inner'" . implode("',\n$spacer_inner'", $input) . "',\n$spacer_outer]";
			}
			else
			{
				$str_export = var_export($input, true);

				// Convert new lines to "\n" only
				$str_export = str_replace("\r\n", "\n", $str_export);
				$str_export = str_replace("\r", "\n", $str_export);

				$str_export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $str_export);
				$lines = explode("\n", $str_export);
				$lines = array_map('trim', $lines);
				$lines = preg_replace(['/\s*array\s*\($/', '/\)(,)?$/', '/\s*=>\s*$/'], [null, ']$1', ' => ['], $lines);
				$last = array_pop($lines);
				//$str_export = implode("\n", array_filter(['['] + $lines));
				return implode("\n$spacer_inner", ['['] + $lines) . "\n$spacer_outer$last";

			}
		}

		return var_export($input, true);
	}

	private function fixRootNodes(array $input): array
	{
		$new = [];
		foreach ($input as $item) {
			if (false !== strpos($item, ' '))
			{
				list($key) = explode(' ', $item);
				if (!in_array($key, $new, true))
				{
					$new[] = $key;
				}
			}
			$new[] = $item;
		}
		return $new;
	}

	/**
	 * Determine if an array is Sequential (non-associative)
	 */
	private function isSequential(array $arr): bool
	{
		return array_keys($arr) === range(0, count($arr) - 1);
	}
}
