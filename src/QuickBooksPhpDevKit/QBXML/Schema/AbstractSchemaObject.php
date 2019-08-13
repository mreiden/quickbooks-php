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

/**
 *
 */
abstract class AbstractSchemaObject
{
	public const TYPE_STRTYPE = 'STRTYPE';
	public const TYPE_IDTYPE = 'IDTYPE';
	public const TYPE_BOOLTYPE = 'BOOLTYPE';
	public const TYPE_AMTTYPE = 'AMTTYPE';

	/**
	 * This QuickBooks Action is found only in the QuickBooks Point of Sale (QBPOS) sdk.
	 * @var bool
	 */
	protected $_isQbxmlPosOnly = false;


	public function qbxmlWrapper(): string
	{
		return $this->_qbxmlWrapper;
	}

	/**
	 *
	 */
	public function paths(?string $match = null): array
	{
		return array_keys($this->_dataTypePaths);
	}

	/**
	 *
	 */
	public function dataType(string $path, bool $case_doesnt_matter = true): ?string
	{
		/*
		static $paths = [
			'Name' => 'STRTYPE',
		];
		*/

		if (isset($this->_dataTypePaths[$path]))
		{
			return $this->_dataTypePaths[$path];
		}
		else if ($case_doesnt_matter)
		{
			$nocase = array_change_key_case($this->_dataTypePaths, CASE_LOWER);
			if (isset($nocase[strtolower($path)]))
			{
				return $nocase[strtolower($path)];
			}
		}

		return null;
	}

	/**
	 * Find the maximum length allowed for a path
	 */
	public function maxLength(string $path, bool $case_doesnt_matter = true, ?string $locale = null): int
	{
		if (isset($this->_maxLengthPaths[$path]))
		{
			return $this->_maxLengthPaths[$path];
		}
		else if ($case_doesnt_matter)
		{
			$nocase = array_change_key_case($this->_maxLengthPaths, CASE_LOWER);
			if (isset($nocase[strtolower($path)]))
			{
				return $nocase[strtolower($path)];
			}
		}

		return PHP_INT_MAX;
	}

	/**
	 * Tell whether the path is optional (may be ommitted from the QBXML request)
	 */
	public function isOptional(string $path): bool
	{
		/*
		static $paths = [
			'Name' => false,
			'FirstName' => true,
			'LastName' => true,
		];
		*/

		return $this->_isOptionalPaths[$path] ?? true;
	}

	/**
	 * Find the QBXML version the path became available
	 */
	public function sinceVersion(string $path): float
	{
		/*
		static $paths = [
			'FirstName' => '0.0',
			'LastName' => '0.0',
		];
		*/

		return $this->_sinceVersionPaths[$path] ?? 1;
	}

	/**
	 * Tell whether or not a specific element is repeatable
	 */
	public function isRepeatable(string $path): bool
	{
		/*
		static $paths = [
			'FirstName' => false,
			'LastName' => false,
		];
		*/

		return $this->_isRepeatablePaths[$path] ?? false;
	}

	/**
	 * Tell whether or not an element exists
	 */
	public function exists(string $path, bool $case_doesnt_matter = true, bool $is_end_element = false): bool
	{
		if (in_array($path, $this->_reorderPathsPaths))
		{
			return true;
		}
		else if ($case_doesnt_matter)
		{
			$nocase = array_map('strtolower', $this->_reorderPathsPaths);

			return in_array(strtolower($path), $nocase);
		}

		return false;
	}

	/**
	 * Returns the original
	 */
	public function unfold(string $path)
	{
		static $paths = null;

		if (null === $paths)
		{
			$paths = array_change_key_case(array_combine(array_values($this->_reorderPathsPaths), array_values($this->_reorderPathsPaths)), CASE_LOWER);
		}

		//print('unfolding: {' . $path . '}' . "\n");

		return $paths[strtolower($path)] ?? null;
	}

	/**
	 *
	 * @note WARNING! These are lists of UNSUPPORTED locales, NOT lists of supported ones!
	 *
	 */
	public function localePaths(): array
	{
		return $this->_localeExcludedPaths;
	}

	public function inLocale(string $path, string $locale): bool
	{
		if (!empty($this->_localeExcludedPaths[$path]))
		{
			return in_array($locale, $this->_localeExcludedPaths[$path]);
		}

		return false;
	}

	/**
	 * Re-order an array to match the schema order
	 */
	public function reorderPaths(array $unordered_paths, bool $allow_application_id = true, bool $allow_application_editsequence = true): array
	{
		/*
		static $ordered_paths = [
			'Name',
			'FirstName',
			'LastName',
		];
		*/

		$tmp = [];
		foreach ($this->_reorderPathsPaths as $key => $path)
		{
			if (in_array($path, $unordered_paths))
			{
				$tmp[$key] = $path;
			}
			/*else if (substr($path, -6) == 'ListID' and $allow_application_id)
			{
				// Modify and add:  (so that application IDs are supported and in the correct place)
				//	CustomerRef ListID tags
				// modified to:
				//	CustomerRef APIApplicationID tags

				$parent = trim(substr($path, 0, -7));

				$apppath = trim($parent . ' ' . PackageInfo::$API_APPLICATIONID);

				if (in_array($apppath, $unordered_paths))
				{
					$tmp[$key] = $apppath;
				}
			}
			else if (substr($path, -5) == 'TxnID' and $allow_application_id)
			{
				$parent = trim(substr($path, 0, -6));

				$apppath = $parent . ' ' . PackageInfo::$API_APPLICATIONID;

				if (in_array($apppath, $unordered_paths))
				{
					$tmp[$key] = $apppath;
				}
			}
			else if ($path == 'EditSequence' and $allow_application_editsequence)
			{
				$apppath = QUICKBOOKS_API_APPLICATIONEDITSEQUENCE;

				if (in_array($apppath, $unordered_paths))
				{
					$tmp[$key] = $apppath;
				}
			}*/

			/*else if ($path == PackageInfo::$API_APPLICATIONID)
			{
				print('HERE!');
			}*/
		}

		return $tmp;
	}

	/**
	 * This QuickBooks Action is found only in the QuickBooks Point of Sale (QBPOS) sdk.
	 */
	public function isOnlyInQBPOS(): bool
	{
		return $this->_isQbxmlPosOnly;
	}
}
