<?php declare(strict_types=1);

/**
 * AES Encryption (depends on mcrypt for now)
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author Keith Palmer <keith@ConsoliBYTE.com>
 *
 * @package QuickBooks
 */

namespace QuickBooksPhpDevKit\Encryption;

use QuickBooksPhpDevKit\Encryption;

/**
 *
 */
class Aes extends Encryption
{
	static function encrypt(string $key, string $plain, ?string $salt = null): string
	{
		if (is_null($salt))
		{
			$salt = static::salt();
		}

		$plain = json_encode([$plain, $salt]);

		$crypt = mcrypt_module_open('rijndael-256', '', 'ofb', '');

		if (false !== stripos(PHP_OS, 'win') &&
			version_compare(PHP_VERSION, '5.3.0') == -1)
		{
			$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($crypt), MCRYPT_RAND);
		}
		else
		{
			$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($crypt), MCRYPT_DEV_URANDOM);
		}

		$ks = mcrypt_enc_get_key_size($crypt);
		$key = substr(md5($key), 0, $ks);

		mcrypt_generic_init($crypt, $key, $iv);
		$encrypted = base64_encode($iv . mcrypt_generic($crypt, $plain));
		mcrypt_generic_deinit($crypt);
		mcrypt_module_close($crypt);

		return $encrypted;
	}

	static function decrypt(string $key, string $encrypted): string
	{
		$crypt = mcrypt_module_open('rijndael-256', '', 'ofb', '');
		$iv_size = mcrypt_enc_get_iv_size($crypt);
		$ks = mcrypt_enc_get_key_size($crypt);
		$key = substr(md5($key), 0, $ks);

		//print('before base64 [' . $encrypted . ']' . '<br />');

		$encrypted = base64_decode($encrypted);

		//print('given key was: ' . $key);
		//print('iv size: ' . $iv_size);

		//print('decrypting [' . $encrypted . ']' . '<br />');

		mcrypt_generic_init($crypt, $key, substr($encrypted, 0, $iv_size));
		$decrypted = trim(mdecrypt_generic($crypt, substr($encrypted, $iv_size)));
		mcrypt_generic_deinit($crypt);
		mcrypt_module_close($crypt);

		//print('decrypted: [[**(' . $salt . ')');
		//print_r($decrypted);
		//print('**]]');

		$tmp = json_decode($decrypted, true);
		$decrypted = current($tmp);

		return $decrypted;
	}
}
