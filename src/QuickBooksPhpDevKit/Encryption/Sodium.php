<?php declare(strict_types=1);

/**
 * libSodium crypto_secretbox Ecryption
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
 * libSodium crypto_secretbox symetric-key encryption
 */
final class Sodium extends Encryption
{
	private static $block_size = 16;

	static function encrypt(string $base64_encoded_secret_key, string $plain, $not_used_salt = null): string
	{
		$padded = sodium_pad($plain, self::$block_size);

		$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
		$encrypted = sodium_crypto_secretbox($padded, $nonce, base64_decode($base64_encoded_secret_key));

		//sodium_memzero($base64_encoded_secret_key);
		//sodium_memzero($plain);
		//sodium_memzero($padded);

		return base64_encode($nonce . $encrypted);
	}

	static function decrypt(string $base64_encoded_secret_key, string $encrypted): ?string
	{
		$encrypted = base64_decode($encrypted);

		if (mb_strlen($encrypted, '8bit') < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES)
		{
			return null;
		}

		$nonce = mb_substr($encrypted, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
		$encrypted = mb_substr($encrypted, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

		$decrypted = sodium_crypto_secretbox_open($encrypted, $nonce, base64_decode($base64_encoded_secret_key));
		$decrypted = sodium_unpad($decrypted, self::$block_size);

		//sodium_memzero($encrypted);
		//sodium_memzero($nonce);
		//sodium_memzero($base64_encoded_secret_key);

		return $decrypted;
	}
}
