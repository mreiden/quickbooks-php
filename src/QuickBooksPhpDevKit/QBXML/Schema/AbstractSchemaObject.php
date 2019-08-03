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


	abstract protected function &_qbxmlWrapper(): string;

	public function qbxmlWrapper(): string
	{
		return $this->_qbxmlWrapper();
	}

	abstract protected function &_dataTypePaths(): array;

	/**
	 *
	 */
	public function paths(?string $match = null): array
	{
		$paths = $this->_dataTypePaths();

		return array_keys($paths);
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

		$paths = $this->_dataTypePaths();

		if (isset($paths[$path]))
		{
			return $paths[$path];
		}
		else if ($case_doesnt_matter)
		{
			foreach ($paths as $dtpath => $datatype)
			{
				if (strtolower($dtpath) == strtolower($path))
				{
					return $datatype;
				}
			}
		}

		return null;
	}

	abstract protected function &_maxLengthPaths(): array;

	/**
	 *
	 */
	public function maxLength(string $path, bool $case_doesnt_matter = true, ?string $locale = null): int
	{
		/*
		static $paths = [
			'Name' => 40,
			'FirstName' => 41,
		];
		*/

		$paths = $this->_maxLengthPaths();

		if (isset($paths[$path]))
		{
			return $paths[$path];
		}
		else if ($case_doesnt_matter)
		{
			foreach ($paths as $mlpath => $maxlength)
			{
				if (strtolower($mlpath) == strtolower($path))
				{
					return $paths[$mlpath];
				}
			}
		}

		return 0;
	}

	abstract protected function &_isOptionalPaths(): array;

	public function isOptional(string $path): bool
	{
		/*
		static $paths = [
			'Name' => false,
			'FirstName' => true,
			'LastName' => true,
		];
		*/

		$paths = $this->_isOptionalPaths();

		return $paths[$path] ?? true;
	}

	abstract protected function &_sinceVersionPaths(): array;

	public function sinceVersion(string $path): float
	{
		/*
		static $paths = [
			'FirstName' => '0.0',
			'LastName' => '0.0',
		];
		*/

		$paths = $this->_sinceVersionPaths();

		return $paths[$path] ?? 999.99;
	}

	abstract protected function &_isRepeatablePaths(): array;

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

		$paths = $this->_isRepeatablePaths();

		return $paths[$path] ?? false;
	}

	/**
	 * Tell whether or not an element exists
	 */
	public function exists(string $path, bool $case_doesnt_matter = true, bool $is_end_element = false): bool
	{
		$ordered_paths = $this->_reorderPathsPaths();

		if (in_array($path, $ordered_paths))
		{
			return true;
		}
		else if ($case_doesnt_matter)
		{
			foreach ($ordered_paths as $ordered_path)
			{
				if (strtolower($path) == strtolower($ordered_path))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns the original
	 */
	public function unfold(string $path)
	{
		static $paths = null;

		if (is_null($paths))
		{
			$paths = $this->_reorderPathsPaths();
			$paths = array_change_key_case(array_combine(array_values($paths), array_values($paths)), CASE_LOWER);
		}

		//print('unfolding: {' . $path . '}' . "\n");

		return $paths[strtolower($path)] ?? null;
	}

	/**
	 *
	 * @note WARNING! These are lists of UNSUPPORTED locales, NOT lists of supported ones!
	 *
	 */
	protected function &_inLocalePaths(): array
	{
		$arr = [];

		return $arr;
	}

	/**
	 *
	 * @note WARNING! These are lists of UNSUPPORTED locales, NOT lists of supported ones!
	 *
	 */
	public function localePaths(): array
	{
		return $this->_inLocalePaths();
	}

	/*
	public function inLocale($path, $locale)
	{
		//static $paths = [
		//	'FirstName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
		//	'LastName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
		//];

		$paths = $this->_inLocalePaths();

		if (isset($paths[$path]))
		{
			return in_array($locale, $paths[$path]);
		}

		return false;
	}
	*/

	/**
	 * Return a list of paths in a specific schema order
	 */
	abstract protected function &_reorderPathsPaths(): array;

	/**
	 * Re-order an array to match the schema order
	 */
	public function reorderPaths(array $unordered_paths, bool $allow_application_id = true, bool $allow_application_editsequence = true): array
	{
		/*
		static $ordered_paths = array(
			0 => 'Name',
			1 => 'FirstName',
			2 => 'LastName',
			);
		*/

		$ordered_paths = $this->_reorderPathsPaths();
		$tmp = [];

		foreach ($ordered_paths as $key => $path)
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

		return array_merge($tmp);
	}
}
