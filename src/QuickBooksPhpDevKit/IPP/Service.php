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
 *
 * @package QuickBooks
 * @subpackage IPP
 */

namespace QuickBooksPhpDevKit\IPP;

use QuickBooksPhpDevKit\{
	IPP,
	IPP\BaseObject,
	IPP\Context,
	IPP\IDS,
	PackageInfo,
	XML,
};

/**
 *
 *
 *
 */
abstract class Service
{
	/**
	 * The last raw XML request
	 * @var string
	 */
	protected $_last_request;

	/**
	 * The last raw XML response
	 * @var string
	 */
	protected $_last_response;

	/**
	 *
	 * @var unknown_type
	 */
	protected $_last_debug;

	/**
	 *
	 */
	protected $_flavor;

	/**
	 * The last error code
	 * @var string
	 */
	protected $_errcode;

	/**
	 * The last error message
	 * @var string
	 */
	protected $_errtext;

	/**
	 * The last error detail
	 * @var string
	 */
	protected $_errdetail;

	/**
	 *
	 *
	 */
	public function __construct()
	{
		$this->_errcode = IPP::ERROR_OK;

		$this->_last_request = null;
		$this->_last_response = null;
		$this->_last_debug = [];

		$this->_flavor = null;		// auto-detect
	}

	public function useIDSParser(Context $Context, bool $true_or_false)
	{
		$IPP = $Context->IPP();

		return $IPP->useIDSParser($true_or_false);
	}

	protected function _entitlements(Context $Context, string $realmID)
	{
		$IPP = $Context->IPP();

		// Send the data to IPP
		//                  $Context, $realm, $resource, $optype, $xml = '', $ID = null
		$return = $IPP->IDS($Context, $realmID, null, IDS::OPTYPE_ENTITLEMENTS);

		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if ($IPP->errorCode() != IPP::ERROR_OK)
		{
			$this->_setError(
				$IPP->errorCode(),
				$IPP->errorText(),
				$IPP->errorDetail());

			return false;
		}

		return $return;
	}

	protected function _cdc(Context $Context, string $realmID, $entities, $timestamp, $page, $size)
	{
		$IPP = $Context->IPP();

		// Send the data to IPP
		//                  $Context, $realm, $resource, $optype, $xml = '', $ID = null
		$return = $IPP->IDS($Context, $realmID, null, IDS::OPTYPE_CDC, [
			$entities,
			$timestamp,
		]);

		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if ($IPP->errorCode() != IPP::ERROR_OK)
		{
			$this->_setError(
				$IPP->errorCode(),
				$IPP->errorText(),
				$IPP->errorDetail());

			return false;
		}

		return $return;
	}

	protected function _syncStatus(Context $Context, string $realmID, string $resource, string $IDType): bool
	{
		$IPP = $Context->IPP();

		switch ($IPP->version())
		{
			case IDS::VERSION_2:
				throw new \Exception('IDS version "'. IDS::VERSION_2 .'" is unsupported and non-functional.  You should use "'. IDS::VERSION_LATEST .'" instead.');
				//return $this->_syncStatus_v2($Context, $realmID, $resource, $IDType);
				break;
		}

		return false;
	}

/*
	protected function _syncStatus_v2($Context, $realmID, $resource, $IDType)
	{
		$IPP = $Context->IPP();

		$xml = [
			'<?xml version="1.0" encoding="UTF-8" standalone="no" ?>',
			'<SyncStatusRequest xmlns="http://www.intuit.com/sb/cdm/v2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.intuit.com/sb/cdm/xmlrequest RestDataFilter.xsd">',
			'  <OfferingId>ipp</OfferingId>',
			'  <SyncStatusParam>',
			'    <IdSet>',
		];

		//foreach ($arr as $IDType)
		//{
			$parse = IDS::parseIDType($IDType);
			$xml[] = '      <Id idDomain="' . $parse['domain'] . '">' . $parse['ID'] . '</Id>';
		//}

		$xml[] = '    </IdSet>';
		$xml[] = '    <ObjectType>' . $resource . '</ObjectType>';
		$xml[] = '  </SyncStatusParam>';
		$xml[] = '</SyncStatusRequest>';

		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_SYNCSTATUS, $xml);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		return $return;
	}
*/
	protected function _report(Context $Context, string $realmID, string $resource, ?string $xml = '')
	{
		$IPP = $Context->IPP();

		if (!$xml)
		{
			$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PackageInfo::$CRLF;
			$xml .= '<' . $resource . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '"></' . $resource . '>';
		}

		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_REPORT, $xml);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		return $return;
	}

	/*
	protected function _delete($Context, $realmID, $resource, $IDType, $xml = '')
	{
		$IPP = $Context->IPP();

		if (!$xml)
		{
			$parse = IDS::parseIDType($IDType);

			$xml = '';
			$xml .= '<?xml version="1.0" encoding="UTF-8"?>' . PackageInfo::$CRLF;
			$xml .= '<' . $resource . 'Query xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '">' . PackageInfo::$CRLF;
			$xml .= '	<TransactionIdSet>' . PackageInfo::$CRLF;
			$xml .= '		<Id idDomain="' . $parse[0] . '">' . $parse[1] . '</Id>' . PackageInfo::$CRLF;
			$xml .= '	</TransactionIdSet>' . PackageInfo::$CRLF;
			$xml .= '</' . $resource . 'Query>';
		}

		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_DELETE, $xml);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if (count($return))
		{
			return $return[0];
		}

		return null;
	}
	*/

	/**
	 *
	 *
	 */
	protected function _guessResource(string $xml, $optype)
	{
		$tmp = explode('_', get_class($this));
		return end($tmp);
	}

	/**
	 *
	 *
	 */
	public function rawQuery(Context $Context, string $realmID, string $xml, $resource = null)
	{
		$IPP = $Context->IPP();

		if (!$resource)
		{
			$resource = $this->_guessResource($xml, IDS::OPTYPE_QUERY);
		}

		return $this->_findAll($Context, $realmID, $resource, null, null, null, null, $xml);
	}

	public function rawAdd(Context $Context, string $realmID, $xml, $resource = null)
	{
		$IPP = $Context->IPP();

		if (!$resource)
		{
			$resource = $this->_guessResource($xml, IDS::OPTYPE_ADD);
		}

		// Send the data to IPP
		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_ADD, $xml);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if ($IPP->errorCode() != IPP::ERROR_OK)
		{
			$this->_setError(
				$IPP->errorCode(),
				$IPP->errorText(),
				$IPP->errorDetail());

			return false;
		}

		return $return;
	}

	protected function _map(array $list, string $key, $value)
	{
		$map = [];
		foreach ($list as $Object)
		{
			$map[$Object->get($key)] = $Object->get($value);
		}

		return $map;
	}

	protected function _find()
	{

	}


	/**
	 *
	 * Returns null on error, and sets $IPP->errorCode, $IPP->errorText, and $IPP->errorDetail
	 *
	 * Added $options array in 09/2012:
	 *   Supported array keys for QuickBooks Desktop are:
	 *     ActiveOnly       => true/false (False by default. May not be used with DeletedObjects)
	 *     DeletedObjects   => true/false (False by default. May not be used with ActiveOnly)
	 *   Supported array keys for QuickBooks Online are:
	 *     (none yet)
	 */
	protected function _findAll(Context $Context, string $realmID, string $resource, $query = null, $sort = null, int $page = 1, int $size = 50, string $xml = '', array $options = []): ?array
	{
		$IPP = $Context->IPP();
		$flavor = $IPP->flavor();

		//print('flavor [' . $flavor . ']');
		//exit;

		if ($flavor == IDS::FLAVOR_DESKTOP)
		{
			if (!$xml)
			{
				$options_string = '';
				//$options_string = ' ErroredObjectsOnly="true" ';

				/*if (!empty($options['ActiveOnly']))
				{
					$options_string = 'ActiveOnly="false" ';
				}
				else if (!empty($options['DeletedObjects']))
				{
					$options_string = 'DeletedObjects="true" ';
				}*/

				$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PackageInfo::$CRLF;
				$xml .= '<' . $resource . 'Query ' . $options_string . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '">' . PackageInfo::$CRLF;

				if ($size)
				{
					$xml .= '	<StartPage>' . $page . '</StartPage>' . PackageInfo::$CRLF;
					$xml .= '	<ChunkSize>' . $size . '</ChunkSize>' . PackageInfo::$CRLF;
				}

				$xml .= $query;

				$xml .= '</' . $resource . 'Query>';
			}
		}
		else if ($flavor == IDS::FLAVOR_ONLINE)
		{
			if (!$xml)
			{
				$defaults = [
					'PageNum' => $page,
					'ResultsPerPage' => $size,
				];

				if (!is_array($query))
				{
					// Assume $query is a query string and convert it to an array
					$str = $query ?? '';
					$query = parse_str($str, $query);
				}

				if (!is_array($query))
				{
					throw new \Exeption('Failed to parse $query paramter in ' . __METHOD__);
				}

				$xml = http_build_query(array_merge($defaults, $query));
			}
		}

		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_QUERY, $xml);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if ($IPP->errorCode() != IPP::ERROR_OK)
		{
			$this->_setError(
				$IPP->errorCode(),
				$IPP->errorText(),
				$IPP->errorDetail());

			return null;
		}

		return $return;
	}

	/**
	 * Get an IDS object by Name (i.e. get a customer by the QuickBooks Name field)
	 */
	protected function _findByName(Context $Context, string $realmID, string $resource, string $name, ?string $xml = ''): ?BaseObject
	{
		$IPP = $Context->IPP();

		if ($IPP->flavor() == IDS::FLAVOR_DESKTOP)
		{
			if (!$xml)
			{
				$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PackageInfo::$CRLF;
				$xml .= '<' . $resource . 'Query xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '">' . PackageInfo::$CRLF;
				$xml .= '	<FirstLastInside>' . XML::encode($name) . '</FirstLastInside>' . PackageInfo::$CRLF;
				$xml .= '</' . $resource . 'Query>';
			}
		}
		else
		{
			$xml = http_build_query(['Filter' => 'Name :EQUALS: ' . $name]);
		}

		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_QUERY, $xml);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if (count($return))
		{
			return $return[0];
		}

		return null;
	}

	/**
	 * Add an IDS object via IPP
	 */
	protected function _add(Context $Context, string $realmID, string $resource, BaseObject $Object): ?string
	{
		$IPP = $Context->IPP();

		switch ($IPP->version())
		{
			case IDS::VERSION_2:
				throw new \Exception('IDS version "'. IDS::VERSION_2 .'" is unsupported and non-functional.  You should use "'. IDS::VERSION_LATEST .'" instead.');

			case IDS::VERSION_3:
				return $this->_add_v3($Context, $realmID, $resource, $Object);
		}
	}

	protected function _add_v3(Context $Context, string $realmID, string $resource, BaseObject $Object): ?string
	{
		$IPP = $Context->IPP();

		$unsets = [
			'Id',
		];

		foreach ($unsets as $unset)
		{
			$Object->remove($unset);
		}

		// Generate the XML
		$xml = $Object->asXML(0, null, null, null, IDS::VERSION_3);

		// Send the data to IPP
		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_ADD, $xml);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if ($IPP->errorCode() != IPP::ERROR_OK)
		{
			$this->_setError(
				$IPP->errorCode(),
				$IPP->errorText(),
				$IPP->errorDetail());

			return null;
		}

		return $return;
	}
/*
	protected function _add_v2($Context, $realmID, $resource, $Object)
	{
		$IPP = $Context->IPP();

		//

		//$Object->unsetAddress();
		//$Object->unsetPhone();
		//$Object->unsetDBAName();

		$unsets = [
			'Id',
			'SyncToken',
			'MetaData',
			'ExternalKey',
			'Synchronized',
			'PartyReferenceId',
			'SalesTaxCodeId', 		// @todo These are customer/vendor specific and probably shouldn't be here
			'SalesTaxCodeName',
			'OpenBalanceDate',
			'OpenBalance',
		];

		foreach ($unsets as $unset)
		{
			$Object->remove($unset);
		}

		if ($IPP->flavor() == IDS::FLAVOR_DESKTOP)
		{
			// Build the XML request
			$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PackageInfo::$CRLF;
			$xml .= '<Add xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '" ' . PackageInfo::$CRLF;
			$xml .= '	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' . PackageInfo::$CRLF;
			$xml .= '	RequestId="' . md5(mt_rand() . microtime()) . '" ' . PackageInfo::$CRLF;
			$xml .= '	xsi:schemaLocation="http://www.intuit.com/sb/cdm/' . $IPP->version() . ' ./RestDataFilter.xsd ">' . PackageInfo::$CRLF;
			$xml .= '	<OfferingId>ipp</OfferingId>' . PackageInfo::$CRLF;
			$xml .= '	<ExternalRealmId>' . $realmID . '</ExternalRealmId>' . PackageInfo::$CRLF;
			$xml .= '' . $Object->asIDSXML(1, null, IDS::OPTYPE_ADD);
			$xml .= '</Add>';
		}
		else
		{
			$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . PackageInfo::$CRLF;
			$xml .= $Object->asIDSXML(0, null, IDS::OPTYPE_ADD, $IPP->flavor());
		}

		// Send the data to IPP
		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_ADD, $xml);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if ($IPP->errorCode() != IPP::ERROR_OK)
		{
			$this->_setError(
				$IPP->errorCode(),
				$IPP->errorText(),
				$IPP->errorDetail());

			return false;
		}

		return $return;
	}
*/
	protected function _delete(Context $Context, string $realmID, $resource, $ID)
	{
		// v3 only
		$IPP = $Context->IPP();

		// Get the object first... because IDS is stupid and wants us to send
		//	the entire object even though we know the Id of the object that we
		//	want to delete... *sigh*
		$objects = $this->_query($Context, $realmID, "SELECT * FROM " . $resource . " WHERE Id = '" . IDS::usableIDType($ID) . "' ");

		if (isset($objects[0]) and
			is_object($objects[0]))
		{
			$Object = $objects[0];

			$unsets = [];

			foreach ($unsets as $unset)
			{
				$Object->remove($unset);
			}

			// Generate the XML
			$xml = $Object->asXML(0, null, null, null, IDS::VERSION_3);

			//die($xml);

			// Send the data to IPP
			$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_DELETE, $xml);
			$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
			$this->_setLastDebug($Context->lastDebug());

			//print('erro code: [' . $IPP->errorCode() . ']' . "\n");
			//print("\n\n\n\n\n" . $Context->lastResponse() . "\n\n\n\n\n");

			if ($IPP->errorCode() != IPP::ERROR_OK)
			{
				$this->_setError(
					$IPP->errorCode(),
					$IPP->errorText(),
					$IPP->errorDetail());

				return false;
			}

			return true;
		}

		$this->_setError(
			IPP::ERROR_INTERNAL,
			'Could not find ' . $resource . ' ' . IDS::usableIDType($ID) . ' to delete.',
			'Could not find ' . $resource . ' ' . IDS::usableIDType($ID) . ' to delete.');

		return false;
	}

	protected function _void(Context $Context, string $realmID, $resource, $ID)
	{
		// v3 only
		$IPP = $Context->IPP();

		// Get the object first... because IDS is stupid and wants us to send
		//	the entire object even though we know the Id of the object that we
		//	want to delete... *sigh*
		$objects = $this->_query($Context, $realmID, "SELECT * FROM " . $resource . " WHERE Id = '" . IDS::usableIDType((string) $ID) . "' ");

		if (isset($objects[0]) &&
			is_object($objects[0]))
		{
			$Object = $objects[0];

			$unsets = [];

			foreach ($unsets as $unset)
			{
				$Object->remove($unset);
			}

			// Generate the XML
			$xml = $Object->asXML(0, null, null, null, IDS::VERSION_3);

			//die($xml);

			// Send the data to IPP
			$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_VOID, $xml);
			$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
			$this->_setLastDebug($Context->lastDebug());

			//print('erro code: [' . $IPP->errorCode() . ']' . "\n");
			//print("\n\n\n\n\n" . $Context->lastResponse() . "\n\n\n\n\n");

			if ($IPP->errorCode() != IPP::ERROR_OK)
			{
				$this->_setError(
					$IPP->errorCode(),
					$IPP->errorText(),
					$IPP->errorDetail());

				return false;
			}

			return true;
		}

		$this->_setError(
			IPP::ERROR_INTERNAL,
			'Could not find ' . $resource . ' ' . IDS::usableIDType($ID) . ' to void.',
			'Could not find ' . $resource . ' ' . IDS::usableIDType($ID) . ' to void.');

		return false;
	}

	protected function _pdf(Context $Context, string $realmID, $resource, $ID)
	{
		// v3 only
		$IPP = $Context->IPP();
		$IPP->useIDSParser(false); // We want raw pdf output

		return $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_PDF, null, $ID);
	}

	protected function _download(Context $Context, string $realmID, $resource, $ID)
	{
		// v3 only
		$IPP = $Context->IPP();
		$IPP->useIDSParser(false); // We want raw pdf output

		return $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_DOWNLOAD, null, $ID);
	}

	protected function _send(Context $Context, string $realmID, $resource, $ID)
	{
		// v3 only
		$IPP = $Context->IPP();

		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_SEND, null, $ID);

	  $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
	  $this->_setLastDebug($Context->lastDebug());

	  if ($IPP->errorCode() != IPP::ERROR_OK)
	  {
		 $this->_setError(
			$IPP->errorCode(),
			$IPP->errorText(),
			$IPP->errorDetail());

		 return false;
	  }

	  return $return;
	}

	/**
	 * @deprecated 	Use _update() instead
	 */
	protected function _modify(Context $Context, string $realmID, string $resource, BaseObject $Object, $ID)
	{
		return $this->_update($Context, $realmID, $resource, $Object, $ID);
	}

	/**
	 * Update an object within IDS (QuickBooks)
	 *
	 * @param object $Context
	 * @param string $realmID
	 * @param string $resource
	 * @param object $Object
	 * @return boolean
	 */
	protected function _update(Context $Context, string $realmID, string $resource, BaseObject $Object, $ID)
	{
		$IPP = $Context->IPP();

		switch ($IPP->version())
		{
			case IDS::VERSION_2:
				throw new \Exception('IDS version "'. IDS::VERSION_2 .'" is unsupported and non-functional.  You should use "'. IDS::VERSION_LATEST .'" instead.');
				//return $this->_update_v2($Context, $realmID, $resource, $Object, $ID);
				return false;

			case IDS::VERSION_3:
				return $this->_update_v3($Context, $realmID, $resource, $Object, $ID);
		}

		return false;
	}

	protected function _update_v3(Context $Context, string $realmID, $resource, BaseObject $Object, $ID)
	{
		$IPP = $Context->IPP();

		$unsets = [];

		foreach ($unsets as $unset)
		{
			$Object->remove($unset);
		}

		// Generate the XML
		$xml = $Object->asXML(0, null, null, null, IDS::VERSION_3);

		//die($xml);

		// Send the data to IPP
		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_MOD, $xml);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if ($IPP->errorCode() != IPP::ERROR_OK)
		{
			$this->_setError(
				$IPP->errorCode(),
				$IPP->errorText(),
				$IPP->errorDetail());

			return false;
		}

		return $return;
	}

	/*
	protected function _update_v2($Context, $realmID, $resource, $Object, $ID)
	{
		$IPP = $Context->IPP();

		// Remove crap that we don't want to send to QuickBooks
		$unsets = array(
		//	'Id',
		//	'SyncToken',
			'MetaData',
			'ExternalKey',
			'Synchronized',
			'CustomField',
		//	'PartyReferenceId',
		//	'SalesTaxCodeId', 		// @todo These are customer/vendor specific and probably shouldn't be here
		//	'SalesTaxCodeName',
		//	'OpenBalanceDate',
		//	'OpenBalance',
			);

		foreach ($unsets as $unset)
		{
			$Object->remove($unset);
		}

		$Object->set('Synchronized', 'false');

		if ($IPP->flavor() == IDS::FLAVOR_DESKTOP)
		{
			// Build the XML request
			$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PackageInfo::$CRLF;
			$xml .= '<Mod xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '" ' . PackageInfo::$CRLF;
			$xml .= '	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' . PackageInfo::$CRLF;
			$xml .= '	RequestId="' . md5(mt_rand() . microtime()) . '" ' . PackageInfo::$CRLF;
			$xml .= '	xsi:schemaLocation="http://www.intuit.com/sb/cdm/' . $IPP->version() . ' ./RestDataFilter.xsd ">' . PackageInfo::$CRLF;
			//$xml .= '	<OfferingId>ipp</OfferingId>' . PackageInfo::$CRLF;
			$xml .= '	<ExternalRealmId>' . $realmID . '</ExternalRealmId>' . PackageInfo::$CRLF;
			$xml .= '' . $Object->asIDSXML(1, null, IDS::OPTYPE_MOD);
			$xml .= '</Mod>';
		}
		else
		{
			$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . PackageInfo::$CRLF;
			$xml .= $Object->asIDSXML(0, null, IDS::OPTYPE_MOD, $IPP->flavor());
		}

		// Send the data to IPP
		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_MOD, $xml, $ID);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		// Check for errors
		if ($IPP->errorCode() != IPP::ERROR_OK)
		{
			$this->_setError(
				$IPP->errorCode(),
				$IPP->errorText(),
				$IPP->errorDetail());

			return false;
		}

		return $return;
	}
	*/

	/**
	 * Find a record by ID number
	 *
	 *
	 */
	protected function _findById(Context $Context, string $realmID, string $resource, string $IDType, ?string $xml_or_IDType = '', ?string $query = null): ?BaseObject
	{
		$IPP = $Context->IPP();

		$flavor = $IPP->flavor();

		if (!$xml_or_IDType)
		{
			if ($flavor == IDS::FLAVOR_DESKTOP)
			{
				$parse = IDS::parseIDType($IDType);

				$xml_or_IDType  = '<?xml version="1.0" encoding="UTF-8"?>' . PackageInfo::$CRLF;
				$xml_or_IDType .= '<' . $resource . 'Query xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '">' . PackageInfo::$CRLF;

				if ($resource == IDS::RESOURCE_CUSTOMER)
				{
					$xml_or_IDType .= '<CustomFieldEnable>true</CustomFieldEnable>';
				}

				if ($query)
				{
					$xml_or_IDType .= $query;
				}

				$xml_or_IDType .= '	<' . IDS::resourceToKeyType($resource) . 'Set>' . PackageInfo::$CRLF;
				$xml_or_IDType .= '		<Id idDomain="' . $parse['domain'] . '">' . $parse['ID'] . '</Id>' . PackageInfo::$CRLF;
				$xml_or_IDType .= '	</' . IDS::resourceToKeyType($resource) . 'Set>' . PackageInfo::$CRLF;
				$xml_or_IDType .= '</' . $resource . 'Query>';
			}
			else if ($flavor == IDS::FLAVOR_ONLINE)
			{
				$xml_or_IDType = $IDType;
			}
		}

		$return = $IPP->IDS($Context, $realmID, $resource, IDS::OPTYPE_FINDBYID, $xml_or_IDType);
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		if (count($return))
		{
			return $return[0];
		}

		return null;
	}

	protected function _query(Context $Context, string $realmID, string $query)
	{
		$IPP = $Context->IPP();

		// Send the data to IPP
		//$return = $IPP->IDS($Context, $realmID, null, IDS::OPTYPE_QUERY, str_replace('=', '%3D', $query));
		$return = $IPP->IDS($Context, $realmID, null, IDS::OPTYPE_QUERY, urlencode($query));
		$this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
		$this->_setLastDebug($Context->lastDebug());

		$errorCode = $IPP->errorCode();
		if ($errorCode != IPP::ERROR_OK)
		{
			$this->_setError(
				$errorCode,
				$IPP->errorText(),
				$IPP->errorDetail());

			return null;
		}

		return $return;
	}

	/**
	 * Set the last XML request and XML response that was made by this service
	 */
	protected function _setLastRequestResponse(string $xmlRequest, string $xmlResponse): void
	{
		$this->_last_request = $xmlRequest;
		$this->_last_response = $xmlResponse;
	}

	protected function _setLastDebug($debug)
	{
		$this->_last_debug = $debug;
	}

	/**
	 * Get the last XML request that was made
	 */
	public function lastRequest(?Context $Context = null): string
	{
		if ($Context)
		{
			return $Context->lastRequest();
		}

		return $this->_last_request;
	}

	/**
	 * Get the last raw XML response that was returned
	 *
	 * @param object $Context		If you provide a specific context, this will return the last response using that particular context, otherwise it will return the last response from this service
	 * @return string				The last raw XML response
	 */
	public function lastResponse(Context $Context = null): string
	{
		if ($Context)
		{
			return $Context->lastResponse();
		}

		return $this->_last_response;
	}

	public function lastError(?Context $Context = null): string
	{
		if ($Context)
		{
			return $Context->lastError();
		}

		return $this->_errcode . ': [' . $this->_errtext . ', ' . $this->_errdetail . ']';
	}

	public function lastDebug(?Context $Context = null)
	{
		if ($Context)
		{
			return $Context->lastDebug();
		}

		return $this->_last_debug;
	}

	/**
	 * Get the error number of the last error that occurred
	 *
	 * @return mixed		The error number (or error code, some QuickBooks error codes are hex strings)
	 */
	public function errorCode()
	{
		return $this->_errcode;
	}

	/**
	 * Alias if ->errorCode()   (here for consistency with rest of framework)
	 */
	public function errorNumber()
	{
		return $this->errorCode();
	}

	/**
	 * Get the last error message that was reported
	 *
	 * Remember that issuing new commands may cause previous unchecked errors
	 * to be *cleared*, so make sure you check for errors if you expect an
	 * error might occur!
	 */
	public function errorText(): ?string
	{
		return $this->_errtext;
	}

	/**
	 * Alias of ->errorText()   (here for consistency with rest of framework)
	 */
	public function errorMessage(): ?string
	{
		return $this->errorText();
	}

	/**
	 *  Get the error detail message from the response
	 *
	 * The error detail node sometimes contains additional information about
	 * the error that occurred. You should make sure to also check the result
	 * of ->errorCode() and ->errorMessage() too.
	 *
	 * @return string
	 */
	public function errorDetail()
	{
		return $this->_errdetail;
	}

	/**
	 * Tell whether or not an error occurred
	 */
	public function hasErrors(): bool
	{
		return $this->_errcode != IPP::ERROR_OK;
	}

	/**
	 * Set an error message
	 *
	 * @param integer $errcode	The error number/code
	 * @param string $errmsg	The text error message
	 * @return void
	 */
	protected function _setError($errcode, string $errtext = '', string $errdetail = ''): void
	{
		$filtered = filter_var($errcode, FILTER_VALIDATE_INT, FILTER_FLAG_ALLOW_OCTAL | FILTER_FLAG_ALLOW_HEX);
		if (false !== $filtered)
		{
			$errorCode = $filtered;
		}

		$this->_errcode = $errcode;
		$this->_errtext = $errtext;
		$this->_errdetail = $errdetail;
	}
}
