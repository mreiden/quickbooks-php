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
 *
 */

namespace QuickBooksPhpDevKit\IPP;

use QuickBooksPhpDevKit\IPP;
use QuickBooksPhpDevKit\IPP\IDS;
use QuickBooksPhpDevKit\PackageInfo;

class User
{
	public const ANONYMOUS = 'anonymous';

	protected $_userid;

	protected $_email;

	protected $_firstname;

	protected $_lastname;

	protected $_login;

	protected $_screenname;

	protected $_is_verified;

	protected $_external_auth;

	protected $_authid;

	public function __construct($userid, string $email, string $firstname, string $lastname, $login, string $screenname, bool $is_verified, $external_auth, $authid)
	{
		$this->_userid = $userid;
		$this->_email = $email;
		$this->_firstname = $firstname;
		$this->_lastname = $lastname;
		$this->_login = $login;
		$this->_screenname = $screenname;
		$this->_is_verified = $is_verified;
		$this->_external_auth = $external_auth;
		$this->_authid = $authid;
	}

	public function getUserId()
	{
		return $this->_userid;
	}

	public function getEmail(): ?string
	{
		return $this->_email;
	}

	public function getScreenName(): ?string
	{
		return $this->_screenname;
	}

	public function getFirstName(): ?string
	{
		return $this->_firstname;
	}

	public function getLastName(): ?string
	{
		return $this->_lastname;
	}

	public function getLogin()
	{
		return $this->_login;
	}

	public function isVerified(): bool
	{
		return true === $this->_is_verified;
	}

	public function isAnonymous(): bool
	{
		return $this->_login === static::ANONYMOUS;
	}

	public function getExternalAuth()
	{
		return $this->_external_auth;
	}

	public function getAuthId()
	{
		return $this->_authid;
	}
}
