<?php declare(strict_types=1);

/**
 * Schema object for: UIExtensionSubscriptionAddRq
 *
 * @author "Keith Palmer Jr." <Keith@ConsoliByte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage QBXML
 */

namespace QuickBooksPhpDevKit\QBXML\Schema\Object;

use QuickBooksPhpDevKit\QBXML\Schema\AbstractSchemaObject;

/**
 * WARNING!!!  This file is auto-generated by QBXML\Schema\Generator and the data/qbxmlops130.xml schema
 */
class UIExtensionSubscriptionAddRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = '';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
			'UIExtensionSubscriptionAdd SubscriberID' => 'GUIDTYPE',
			'UIExtensionSubscriptionAdd COMCallbackInfo AppName' => 'STRTYPE',
			'UIExtensionSubscriptionAdd COMCallbackInfo ProgID' => 'STRTYPE',
			'UIExtensionSubscriptionAdd COMCallbackInfo CLSID' => 'GUIDTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription AddToMenu' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIf' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIfNot' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIf' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIfNot' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem MenuText' => 'STRTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem EventTag' => 'STRTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIf' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIfNot' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIf' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIfNot' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem MenuText' => 'STRTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem EventTag' => 'STRTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIf' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIfNot' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIf' => 'ENUMTYPE',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIfNot' => 'ENUMTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'UIExtensionSubscriptionAdd SubscriberID' => 0,
			'UIExtensionSubscriptionAdd COMCallbackInfo AppName' => 128,
			'UIExtensionSubscriptionAdd COMCallbackInfo ProgID' => 128,
			'UIExtensionSubscriptionAdd COMCallbackInfo CLSID' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription AddToMenu' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIf' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIfNot' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIf' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIfNot' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem MenuText' => 50,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem EventTag' => 50,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIf' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIfNot' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIf' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIfNot' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem MenuText' => 50,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem EventTag' => 50,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIf' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIfNot' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIf' => 0,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIfNot' => 0,
		];

		return $paths;
	}

	protected function &_isOptionalPaths(): array
	{
		// This seems broken when a parent is optional but an element is required if the parent is included (See HostQueryRq IncludeMaxCapacity).
		static $paths = []; //'_isOptionalPaths ';

		return $paths;
	}

	protected function &_sinceVersionPaths(): array
	{
		static $paths = [
			'UIExtensionSubscriptionAdd SubscriberID' => 999.99,
			'UIExtensionSubscriptionAdd COMCallbackInfo AppName' => 999.99,
			'UIExtensionSubscriptionAdd COMCallbackInfo ProgID' => 999.99,
			'UIExtensionSubscriptionAdd COMCallbackInfo CLSID' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription AddToMenu' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIf' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIfNot' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIf' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIfNot' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem MenuText' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem EventTag' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIf' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIfNot' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIf' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIfNot' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem MenuText' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem EventTag' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIf' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIfNot' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIf' => 999.99,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIfNot' => 999.99,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'UIExtensionSubscriptionAdd SubscriberID' => false,
			'UIExtensionSubscriptionAdd COMCallbackInfo AppName' => false,
			'UIExtensionSubscriptionAdd COMCallbackInfo ProgID' => false,
			'UIExtensionSubscriptionAdd COMCallbackInfo CLSID' => false,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription AddToMenu' => false,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIf' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIfNot' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIf' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIfNot' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem MenuText' => false,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem EventTag' => false,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIf' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIfNot' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIf' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIfNot' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem MenuText' => false,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem EventTag' => false,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIf' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIfNot' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIf' => true,
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIfNot' => true,
		];

		return $paths;
	}

	/*
	protected function &_inLocalePaths(): array
	{
		static $paths = [
			'FirstName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
			'LastName' => ['QBD', 'QBCA', 'QBUK', 'QBAU'],
		];

		return $paths;
	}
	*/

	protected function &_reorderPathsPaths(): array
	{
		static $paths = [
			'UIExtensionSubscriptionAdd',
			'UIExtensionSubscriptionAdd SubscriberID',
			'UIExtensionSubscriptionAdd COMCallbackInfo AppName',
			'UIExtensionSubscriptionAdd COMCallbackInfo ProgID',
			'UIExtensionSubscriptionAdd COMCallbackInfo CLSID',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription AddToMenu',
			'UIExtensionSubscriptionAdd',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIf',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition VisibleIfNot',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIf',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu DisplayCondition EnabledIfNot',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem MenuText',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem EventTag',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIf',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition VisibleIfNot',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIf',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription Submenu MenuItem DisplayCondition EnabledIfNot',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem MenuText',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem EventTag',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIf',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition VisibleIfNot',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIf',
			'UIExtensionSubscriptionAdd MenuExtensionSubscription MenuItem DisplayCondition EnabledIfNot',
		];

		return $paths;
	}
}
