<?php declare(strict_types=1);

/**
 * QuickBooks .QWC file generation class
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
 */

namespace QuickBooksPhpDevKit\WebConnector;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;

/**
 * QuickBooks .QWC file generation
 *
 */
class QWC
{
	protected $_name;
	protected $_descrip;
	protected $_appurl;
	protected $_appsupport;
	protected $_username;
	protected $_fileid;
	protected $_ownerid;
	protected $_qbtype;
	protected $_readonly;
	protected $_run_every_n_seconds;
	protected $_personaldata;
	protected $_unattendedmode;
	protected $_authflags;
	protected $_notify;
	protected $_appdisplayname;
	protected $_appuniquename;
	protected $_appid;

	public const PERSONALDATA_DEFAULT = '';
	public const PERSONALDATA_NOTNEEDED = 'pdpNotNeeded';
	public const PERSONALDATA_OPTIONAL = 'pdpOptional';
	public const PERSONALDATA_REQUIRED = 'pdpRequired';

	public const UNATTENDEDMODE_DEFAULT = '';
	public const UNATTENDEDMODE_REQUIRED = 'umpRequired';
	public const UNATTENDEDMODE_OPTIONAL = 'umpOptional';

	public const SUPPORTED_DEFAULT = null;
	public const SUPPORTED_ALL = '0x0';
	public const SUPPORTED_SIMPLESTART = '0x1';
	public const SUPPORTED_PRO = '0x2';
	public const SUPPORTED_PREMIER = '0x4';
	public const SUPPORTED_ENTERPRISE = '0x8';

	/**
	 * Generate a valid QuickBooks Web Connector *.QWC file
	 *
	 * @param string $name			The name of the QuickBooks Web Connector job (something descriptive, this gets displayed to the end-user)
	 * @param string $descrip		A short description of the QuickBooks Web Connector job (something descriptive, this gets displayed to the end-user)
	 * @param string $appurl		The absolute URL to the SOAP server (this *MUST* be a HTTPS:// link *UNLESS* it's running on localhost)
	 * @param string $appsupport	A URL where an end-user can go to get support for the application
	 * @param string $username		The username that QuickBooks Web Connector should use to connect
	 * @param string $fileid		A file-ID value... apparently you can just make this up, make it resemble this string: {57F3B9B6-86F1-4fcc-B1FF-966DE1813D20}
	 * @param string $ownerid		As above, apparently you can just make this up, make it resemble this string: {57F3B9B6-86F1-4fcc-B1FF-966DE1813D20}
	 * @param string $qbtype		Either PackageInfo::QbType['QBFS'] or PackageInfo::QbType['QBPOS']
	 * @param boolean $readonly		Whether or not to open the connection as read-only
	 * @param integer $run_every_n_seconds		If you want to schedule the job to run every once in a while automatically, you can pass in a number of seconds between runs here
	 * @param string $personaldata
	 * @param string $unattendedmode
	 * @param integer $authflags
	 * @param boolean $notify
	 * @param string $appdisplayname
	 * @param string $appuniquename
	 * @param string $appid
	 * @return string
	 */
	public function __construct(
		string $name,
		string $descrip,
		string $appurl,
		string $appsupport,
		string $username,
		string $fileid = null,
		string $ownerid = null,
		string $qbtype = PackageInfo::QbType['QBFS'],
		bool $readonly = false,
		$run_every_n_seconds = null,							// Can be number of seconds (int) or a string like '15 minutes'
		string $personaldata = self::PERSONALDATA_DEFAULT,
		string $unattendedmode = self::UNATTENDEDMODE_DEFAULT,
		?string $authflags = self::SUPPORTED_DEFAULT,
		bool $notify = false,
		string $appdisplayname = '',
		string $appuniquename = '',
		string $appid = '')
	{
		$this->_name = $name;
		$this->_descrip = $descrip;

		// Validate App URL
		$appurl = trim($appurl);
		$url = parse_url($appurl);
		if (false === $url || !in_array($url['scheme'], ['http','https']) || empty($url['host']))
		{
			throw new \Exception('App URL is not a URL: ' . $appurl);
		}
		$this->_appurl = $appurl;


		// Validate App URL
		$appsupport = trim($appsupport);
		$url = parse_url($appsupport);
		if (false === $url || !in_array($url['scheme'], ['http','https']) || empty($url['host']))
		{
			throw new \Exception('App Support URL is not a URL: ' . $appsupport);
		}
		$this->_appsupport = $appsupport;


		$this->_username = $username;
		$this->_fileid = $fileid ?? $this->fileID();
		$this->_ownerid = $ownerid ?? $this->ownerID();


		// Validate $qbtype
		$qbtype = trim($qbtype);
		if (!in_array(trim($qbtype), PackageInfo::QbType))
		{
			throw new \Exception('Invalid QbType.  Must be one of: '. implode(', ', PackageInfo::QbType));
		}
		$this->_qbtype = $qbtype;


		// Readonly permission
		$this->_readonly = $readonly;


		// Validate Run WebConnector every n seconds
		$this->_run_every_n_seconds = $run_every_n_seconds ?? 0;
		$seconds = filter_var($this->_run_every_n_seconds, FILTER_VALIDATE_INT);
		if (false === $seconds)
		{
			if (!is_string($this->_run_every_n_seconds))
			{
				throw new \Exception('Invalid $run_every_n_seconds.  Must be a time interval string (e.g. "15 minutes") or a number of seconds.');
			}

			$seconds = Utilities::intervalToSeconds($this->_run_every_n_seconds);
			if (null === $seconds)
			{
				throw new \Exception('Invalid $run_every_n_seconds time inverval string.  Must be a time interval string (e.g. "15 minutes") or a number of seconds.');
			}
		}
		$this->_run_every_n_seconds = $seconds;


		// Validate personal data access
		$valid_personaldata = [
			self::PERSONALDATA_DEFAULT,
			self::PERSONALDATA_NOTNEEDED,
			self::PERSONALDATA_OPTIONAL,
			self::PERSONALDATA_REQUIRED,
		];
		$personaldata = trim($personaldata);
		if (!in_array($personaldata, $valid_personaldata))
		{
			throw new \Exception('Invalid $personaldata (' . $personaldata . ').  Must be one of: "' . implode('", "', $valid_personaldata) . '"');
		}
		$this->_personaldata = $personaldata;


		// Validate unattended mode access
		$valid_unattendedmode = [
			self::UNATTENDEDMODE_DEFAULT,
			self::UNATTENDEDMODE_OPTIONAL,
			self::UNATTENDEDMODE_REQUIRED,
		];
		$unattendedmode = trim($unattendedmode);
		if (!in_array($unattendedmode, $valid_unattendedmode))
		{
			throw new \Exception('Invalid $unattendedmode (' . $unattendedmode . ').  Must be one of: "' . implode('", "', $valid_unattendedmode) . '"');
		}
		$this->_unattendedmode = $unattendedmode;


		// Validate authflags
		$valid_authflags = [
			self::SUPPORTED_DEFAULT,
			self::SUPPORTED_ALL,
			self::SUPPORTED_SIMPLESTART,
			self::SUPPORTED_PRO,
			self::SUPPORTED_PREMIER,
			self::SUPPORTED_ENTERPRISE,
		];
		if (!in_array($authflags, $valid_authflags, true))
		{
			throw new \Exception('Invalid $authflags (' . var_export($authflags, true) . ').  Must be one of: "' . implode('", "', $valid_authflags) . '"');
		}
		$this->_authflags = $authflags;


		$this->_notify = $notify;
		$this->_appdisplayname = $appdisplayname;
		$this->_appuniquename = $appuniquename;
		$this->_appid = $appid;
	}

	public function http($filename = 'quickbooks.qwc')
	{
		header('Content-Type: text/xml; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		print($this->generate());

		return true;
	}

	/**
	 *
	 */
	public function generate(): string
	{
		/*
		AppDisplayName
		AppUniqueName
		AuthFlags
			- 0x0 (all, default)
			- 0x0 (All, default)
			- 0x1 (SupportQBSimpleStart)
			- 0x2 (SupportQBPro)
			- 0x4 (SupportQBPremier)
			- 0x8 (SupportQBEnterprise)
		Notify			true or false
		PersonalDataPref
			- pdpNotNeeded
			- pdpOptional
			- pdpRequired
		Style	(do not support, package only supports default style)
		UnattendedModePref
			- umpRequired
			- umpOptional
		CertURL
		*/

		// Make sure the owner and file ids are wrapped in a single set of {}
		$this->_ownerid = '{' . trim($this->_ownerid, '{}') . '}';
		$this->_fileid = '{' . trim($this->_fileid, '{}') . '}';


		$xml = '<?xml version="1.0"?>' . PackageInfo::$CRLF;
		$xml .= '<QBWCXML>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<AppName>' . htmlspecialchars($this->_name) . '</AppName>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<AppID>' . htmlspecialchars($this->_appid) . '</AppID>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<AppURL>' . htmlspecialchars($this->_appurl) . '</AppURL>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<AppDescription>' . htmlspecialchars($this->_descrip) . '</AppDescription>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<AppSupport>' . htmlspecialchars($this->_appsupport) . '</AppSupport>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<UserName>' . htmlspecialchars($this->_username) . '</UserName>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<OwnerID>' . $this->_ownerid . '</OwnerID>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<FileID>' . $this->_fileid . '</FileID>' . PackageInfo::$CRLF;
		$xml .= "\t" . '<QBType>' . $this->_qbtype . '</QBType>' . PackageInfo::$CRLF;

		if ($this->_personaldata != self::PERSONALDATA_DEFAULT)
		{
			$xml .= "\t" . '<PersonalDataPref>' . $this->_personaldata . '</PersonalDataPref>' . PackageInfo::$CRLF;
		}

		if ($this->_unattendedmode != self::UNATTENDEDMODE_DEFAULT)
		{
			$xml .= "\t" . '<UnattendedModePref>' . $this->_unattendedmode . '</UnattendedModePref>' . PackageInfo::$CRLF;
		}

		if ($this->_authflags != self::SUPPORTED_DEFAULT)
		{
			$xml .= "\t" . '<AuthFlags>' . $this->_authflags . '</AuthFlags>' . PackageInfo::$CRLF;
		}

		$xml .= "\t" . '<Notify>' . ($this->_notify ? 'true' : 'false') .'</Notify>' . PackageInfo::$CRLF;

		if (strlen($this->_appdisplayname) > 0)
		{
			$xml .= "\t" . '<AppDisplayName>' . $this->_appdisplayname . '</AppDisplayName>' . PackageInfo::$CRLF;
		}

		if (strlen($this->_appuniquename) > 0)
		{
			$xml .= "\t" . '<AppUniqueName>' . $this->_appuniquename . '</AppUniqueName>' . PackageInfo::$CRLF;
		}

		if ($this->_run_every_n_seconds > 0 && $this->_run_every_n_seconds < 60)
		{
			$xml .= "\t" . '<Scheduler>' . PackageInfo::$CRLF;
			$xml .= "\t" . "\t" . '<RunEveryNSeconds>' . $this->_run_every_n_seconds . '</RunEveryNSeconds>' . PackageInfo::$CRLF;
			$xml .= "\t" . '</Scheduler>' . PackageInfo::$CRLF;
		}
		else if ($this->_run_every_n_seconds >= 60)
		{
			$xml .= "\t" . '<Scheduler>' . PackageInfo::$CRLF;
			$xml .= "\t" . "\t" . '<RunEveryNMinutes>' . floor($this->_run_every_n_seconds / 60) . '</RunEveryNMinutes>' . PackageInfo::$CRLF;
			$xml .= "\t" . '</Scheduler>' . PackageInfo::$CRLF;
		}

		$xml .= "\t" . '<IsReadOnly>' . ($this->_readonly ? 'true' : 'false') . '</IsReadOnly>' . PackageInfo::$CRLF;

		$xml .= '</QBWCXML>' . PackageInfo::$CRLF;

		return $xml;
	}

	/**
	 * Alias of QuickBooks_QWC::generate()
	 */
	public function __toString(): string
	{
		return $this->generate();
	}

	/**
	 * Generate a random File ID string
	 *
	 * *** WARNING *** I have no idea if it is OK or not to do it like this... do you know? E-mail me!
	 */
	static public function fileID(bool $surround = true): string
	{
		return Utilities::GUID($surround);
	}

	/**
	 * Generate a random Owner ID string
	 *
	 * *** WARNING *** I have no idea if it is OK or not to do it like this... do you know? E-mail me!
	 */
	static public function ownerID(bool $surround = true): string
	{
		return Utilities::GUID($surround);
	}

	/**
	 * Generate a random GUID string
	 */
	static public function GUID(bool $surround = true): string
	{
		return Utilities::GUID($surround);
	}
}
