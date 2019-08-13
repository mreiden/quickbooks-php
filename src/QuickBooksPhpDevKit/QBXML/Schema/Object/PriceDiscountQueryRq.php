<?php declare(strict_types=1);

/**
 * Schema object for: PriceDiscountQueryRq
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
 * WARNING!!!: This file is generated by QuickBooksPhpDevKit\QBXML\Schema\Generator using the /data/qbposxmlops30.xml file in this package.
 */
final class PriceDiscountQueryRq extends AbstractSchemaObject
{
	/**
	 * This QuickBooks Action is found only in the QuickBooks Point of Sale (QBPOS) sdk.
	 * @var bool
	 */
	protected $_isQbxmlPosOnly = true;

	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = '';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'MaxReturned' => 'INTTYPE',
		'OwnerID' => 'GUIDTYPE',
		'TxnID' => 'IDTYPE',
		'TimeCreatedFilter MatchNumericCriterion' => 'ENUMTYPE',
		'TimeCreatedFilter TimeCreated' => 'DATETIMETYPE',
		'TimeCreatedRangeFilter FromTimeCreated' => 'DATETIMETYPE',
		'TimeCreatedRangeFilter ToTimeCreated' => 'DATETIMETYPE',
		'TimeModifiedFilter MatchNumericCriterion' => 'ENUMTYPE',
		'TimeModifiedFilter TimeModified' => 'DATETIMETYPE',
		'TimeModifiedRangeFilter FromTimeModified' => 'DATETIMETYPE',
		'TimeModifiedRangeFilter ToTimeModified' => 'DATETIMETYPE',
		'PriceDiscountNameFilter MatchStringCriterion' => 'ENUMTYPE',
		'PriceDiscountNameFilter PriceDiscountName' => 'STRTYPE',
		'PriceDiscountNameRangeFilter FromPriceDiscountName' => 'STRTYPE',
		'PriceDiscountNameRangeFilter ToPriceDiscountName' => 'STRTYPE',
		'PriceDiscountReasonFilter MatchStringCriterion' => 'ENUMTYPE',
		'PriceDiscountReasonFilter PriceDiscountReason' => 'STRTYPE',
		'PriceDiscountReasonRangeFilter FromPriceDiscountReason' => 'STRTYPE',
		'PriceDiscountReasonRangeFilter ToPriceDiscountReason' => 'STRTYPE',
		'CommentsFilter MatchStringCriterion' => 'ENUMTYPE',
		'CommentsFilter Comments' => 'STRTYPE',
		'CommentsRangeFilter FromComments' => 'STRTYPE',
		'CommentsRangeFilter ToComments' => 'STRTYPE',
		'AssociateFilter MatchStringCriterion' => 'ENUMTYPE',
		'AssociateFilter Associate' => 'STRTYPE',
		'AssociateRangeFilter FromAssociate' => 'STRTYPE',
		'AssociateRangeFilter ToAssociate' => 'STRTYPE',
		'LastAssociateFilter MatchStringCriterion' => 'ENUMTYPE',
		'LastAssociateFilter LastAssociate' => 'STRTYPE',
		'LastAssociateRangeFilter FromLastAssociate' => 'STRTYPE',
		'LastAssociateRangeFilter ToLastAssociate' => 'STRTYPE',
		'ItemsCountFilter MatchNumericCriterion' => 'ENUMTYPE',
		'ItemsCountFilter ItemsCount' => 'INTTYPE',
		'ItemsCountRangeFilter FromItemsCount' => 'INTTYPE',
		'ItemsCountRangeFilter ToItemsCount' => 'INTTYPE',
		'PriceDiscountType' => 'ENUMTYPE',
		'IsInactive' => 'BOOLTYPE',
		'PriceDiscountXValueFilter MatchNumericCriterion' => 'ENUMTYPE',
		'PriceDiscountXValueFilter PriceDiscountXValue' => 'INTTYPE',
		'PriceDiscountXValueRangeFilter FromPriceDiscountXValue' => 'INTTYPE',
		'PriceDiscountXValueRangeFilter ToPriceDiscountXValue' => 'INTTYPE',
		'PriceDiscountYValueFilter MatchNumericCriterion' => 'ENUMTYPE',
		'PriceDiscountYValueFilter PriceDiscountYValue' => 'FLOATTYPE',
		'PriceDiscountYValueRangeFilter FromPriceDiscountYValue' => 'FLOATTYPE',
		'PriceDiscountYValueRangeFilter ToPriceDiscountYValue' => 'FLOATTYPE',
		'IsApplicableOverXValue' => 'BOOLTYPE',
		'StartDateFilter MatchNumericCriterion' => 'ENUMTYPE',
		'StartDateFilter StartDate' => 'DATETIMETYPE',
		'StartDateRangeFilter FromStartDate' => 'DATETIMETYPE',
		'StartDateRangeFilter ToStartDate' => 'DATETIMETYPE',
		'StopDateFilter MatchNumericCriterion' => 'ENUMTYPE',
		'StopDateFilter StopDate' => 'DATETIMETYPE',
		'StopDateRangeFilter FromStopDate' => 'DATETIMETYPE',
		'StopDateRangeFilter ToStopDate' => 'DATETIMETYPE',
		'StoreExchangeStatus' => 'ENUMTYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'PriceDiscountNameFilter PriceDiscountName' => 200,
		'PriceDiscountNameRangeFilter FromPriceDiscountName' => 200,
		'PriceDiscountNameRangeFilter ToPriceDiscountName' => 200,
		'PriceDiscountReasonFilter PriceDiscountReason' => 30,
		'PriceDiscountReasonRangeFilter FromPriceDiscountReason' => 30,
		'PriceDiscountReasonRangeFilter ToPriceDiscountReason' => 30,
		'CommentsFilter Comments' => 300,
		'CommentsRangeFilter FromComments' => 300,
		'CommentsRangeFilter ToComments' => 300,
		'AssociateFilter Associate' => 40,
		'AssociateRangeFilter FromAssociate' => 40,
		'AssociateRangeFilter ToAssociate' => 40,
		'LastAssociateFilter LastAssociate' => 40,
		'LastAssociateRangeFilter FromLastAssociate' => 40,
		'LastAssociateRangeFilter ToLastAssociate' => 40,
		'IncludeRetElement' => 50,
	];

	/**
	 * Field is optional (may be ommitted)
	 * @var bool[]
	 */
	protected $_isOptionalPaths = []; //'_isOptionalPaths ';

	/**
	 * Field Available Since QBXML Version #
	 * @var float[]
	 */
	protected $_sinceVersionPaths = [
	];

	/**
	 * Field May Be Included Multiple Times
	 * @var bool[]
	 */
	protected $_isRepeatablePaths = [
		'OwnerID' => true,
		'IncludeRetElement' => true,
	];

	/**
	 * Field Is Excluded From These Locales
	 *
	 * QuickBooks labels are QBD, QBCA, QBUK, QBAU, and QBOE but these are mapped to
	 * US, CA, UK, AU, and OE to match what is in the PackageInfo::Locale array.
	 * @var string[][]
	 */
	protected $_localeExcludedPaths = [
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'MaxReturned',
		'OwnerID',
		'TxnID',
		'TimeCreatedFilter',
		'TimeCreatedFilter MatchNumericCriterion',
		'TimeCreatedFilter TimeCreated',
		'TimeCreatedRangeFilter',
		'TimeCreatedRangeFilter FromTimeCreated',
		'TimeCreatedRangeFilter ToTimeCreated',
		'TimeModifiedFilter',
		'TimeModifiedFilter MatchNumericCriterion',
		'TimeModifiedFilter TimeModified',
		'TimeModifiedRangeFilter',
		'TimeModifiedRangeFilter FromTimeModified',
		'TimeModifiedRangeFilter ToTimeModified',
		'PriceDiscountNameFilter',
		'PriceDiscountNameFilter MatchStringCriterion',
		'PriceDiscountNameFilter PriceDiscountName',
		'PriceDiscountNameRangeFilter',
		'PriceDiscountNameRangeFilter FromPriceDiscountName',
		'PriceDiscountNameRangeFilter ToPriceDiscountName',
		'PriceDiscountReasonFilter',
		'PriceDiscountReasonFilter MatchStringCriterion',
		'PriceDiscountReasonFilter PriceDiscountReason',
		'PriceDiscountReasonRangeFilter',
		'PriceDiscountReasonRangeFilter FromPriceDiscountReason',
		'PriceDiscountReasonRangeFilter ToPriceDiscountReason',
		'CommentsFilter',
		'CommentsFilter MatchStringCriterion',
		'CommentsFilter Comments',
		'CommentsRangeFilter',
		'CommentsRangeFilter FromComments',
		'CommentsRangeFilter ToComments',
		'AssociateFilter',
		'AssociateFilter MatchStringCriterion',
		'AssociateFilter Associate',
		'AssociateRangeFilter',
		'AssociateRangeFilter FromAssociate',
		'AssociateRangeFilter ToAssociate',
		'LastAssociateFilter',
		'LastAssociateFilter MatchStringCriterion',
		'LastAssociateFilter LastAssociate',
		'LastAssociateRangeFilter',
		'LastAssociateRangeFilter FromLastAssociate',
		'LastAssociateRangeFilter ToLastAssociate',
		'ItemsCountFilter',
		'ItemsCountFilter MatchNumericCriterion',
		'ItemsCountFilter ItemsCount',
		'ItemsCountRangeFilter',
		'ItemsCountRangeFilter FromItemsCount',
		'ItemsCountRangeFilter ToItemsCount',
		'PriceDiscountType',
		'IsInactive',
		'PriceDiscountXValueFilter',
		'PriceDiscountXValueFilter MatchNumericCriterion',
		'PriceDiscountXValueFilter PriceDiscountXValue',
		'PriceDiscountXValueRangeFilter',
		'PriceDiscountXValueRangeFilter FromPriceDiscountXValue',
		'PriceDiscountXValueRangeFilter ToPriceDiscountXValue',
		'PriceDiscountYValueFilter',
		'PriceDiscountYValueFilter MatchNumericCriterion',
		'PriceDiscountYValueFilter PriceDiscountYValue',
		'PriceDiscountYValueRangeFilter',
		'PriceDiscountYValueRangeFilter FromPriceDiscountYValue',
		'PriceDiscountYValueRangeFilter ToPriceDiscountYValue',
		'IsApplicableOverXValue',
		'StartDateFilter',
		'StartDateFilter MatchNumericCriterion',
		'StartDateFilter StartDate',
		'StartDateRangeFilter',
		'StartDateRangeFilter FromStartDate',
		'StartDateRangeFilter ToStartDate',
		'StopDateFilter',
		'StopDateFilter MatchNumericCriterion',
		'StopDateFilter StopDate',
		'StopDateRangeFilter',
		'StopDateRangeFilter FromStopDate',
		'StopDateRangeFilter ToStopDate',
		'StoreExchangeStatus',
		'IncludeRetElement',
	];
}