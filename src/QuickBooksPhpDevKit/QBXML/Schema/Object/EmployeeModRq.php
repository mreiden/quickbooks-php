<?php declare(strict_types=1);

/**
 * Schema object for: EmployeeModRq
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
 * WARNING!!!: This file is generated by QuickBooksPhpDevKit\QBXML\Schema\Generator using the /data/qbxmlops130.xml file in this package.
 */
final class EmployeeModRq extends AbstractSchemaObject
{
	/**
	 * Object's QBXML wrapping tag type
	 * @var string
	 */
	protected $_qbxmlWrapper = 'EmployeeMod';

	/**
	 * Field Datatype
	 * @var string[]
	 */
	protected $_dataTypePaths = [
		'ListID' => 'IDTYPE',
		'EditSequence' => 'STRTYPE',
		'Name' => 'STRTYPE',
		'IsActive' => 'BOOLTYPE',
		'Salutation' => 'STRTYPE',
		'FirstName' => 'STRTYPE',
		'MiddleName' => 'STRTYPE',
		'LastName' => 'STRTYPE',
		'Suffix' => 'STRTYPE',
		'JobTitle' => 'STRTYPE',
		'SupervisorRef ListID' => 'IDTYPE',
		'SupervisorRef FullName' => 'STRTYPE',
		'Department' => 'STRTYPE',
		'Description' => 'STRTYPE',
		'TargetBonus' => 'AMTTYPE',
		'EmployeeAddress Addr1' => 'STRTYPE',
		'EmployeeAddress Addr2' => 'STRTYPE',
		'EmployeeAddress Addr3' => 'STRTYPE',
		'EmployeeAddress Addr4' => 'STRTYPE',
		'EmployeeAddress City' => 'STRTYPE',
		'EmployeeAddress State' => 'STRTYPE',
		'EmployeeAddress PostalCode' => 'STRTYPE',
		'EmployeeAddress Country' => 'STRTYPE',
		'PrintAs' => 'STRTYPE',
		'Phone' => 'STRTYPE',
		'Mobile' => 'STRTYPE',
		'Pager' => 'STRTYPE',
		'PagerPIN' => 'STRTYPE',
		'AltPhone' => 'STRTYPE',
		'Fax' => 'STRTYPE',
		'Email' => 'STRTYPE',
		'AdditionalContactRef ContactName' => 'STRTYPE',
		'AdditionalContactRef ContactValue' => 'STRTYPE',
		'EmergencyContacts PrimaryContact ContactName' => 'STRTYPE',
		'EmergencyContacts PrimaryContact ContactValue' => 'STRTYPE',
		'EmergencyContacts PrimaryContact Relation' => 'ENUMTYPE',
		'EmergencyContacts SecondaryContact ContactName' => 'STRTYPE',
		'EmergencyContacts SecondaryContact ContactValue' => 'STRTYPE',
		'EmergencyContacts SecondaryContact Relation' => 'ENUMTYPE',
		'EmployeeType' => 'ENUMTYPE',
		'PartOrFullTime' => 'ENUMTYPE',
		'Exempt' => 'ENUMTYPE',
		'KeyEmployee' => 'ENUMTYPE',
		'HiredDate' => 'DATETYPE',
		'OriginalHireDate' => 'DATETYPE',
		'AdjustedServiceDate' => 'DATETYPE',
		'ReleasedDate' => 'DATETYPE',
		'BirthDate' => 'DATETYPE',
		'USCitizen' => 'ENUMTYPE',
		'Ethnicity' => 'ENUMTYPE',
		'Disabled' => 'ENUMTYPE',
		'DisabilityDesc' => 'STRTYPE',
		'OnFile' => 'ENUMTYPE',
		'WorkAuthExpireDate' => 'DATETYPE',
		'USVeteran' => 'ENUMTYPE',
		'MilitaryStatus' => 'ENUMTYPE',
		'AccountNumber' => 'STRTYPE',
		'Notes' => 'STRTYPE',
		'AdditionalNotesMod NoteID' => 'INTTYPE',
		'AdditionalNotesMod Note' => 'STRTYPE',
		'BillingRateRef ListID' => 'IDTYPE',
		'BillingRateRef FullName' => 'STRTYPE',
		'EmployeePayrollInfoMod PayPeriod' => 'ENUMTYPE',
		'EmployeePayrollInfoMod ClassRef ListID' => 'IDTYPE',
		'EmployeePayrollInfoMod ClassRef FullName' => 'STRTYPE',
		'EmployeePayrollInfoMod ClearEarnings' => 'BOOLTYPE',
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef ListID' => 'IDTYPE',
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef FullName' => 'STRTYPE',
		'EmployeePayrollInfoMod Earnings Rate' => 'PRICETYPE',
		'EmployeePayrollInfoMod Earnings RatePercent' => 'PERCENTTYPE',
		'EmployeePayrollInfoMod UseTimeDataToCreatePaychecks' => 'ENUMTYPE',
		'EmployeePayrollInfoMod SickHours HoursAvailable' => 'TIMEINTERVALTYPE',
		'EmployeePayrollInfoMod SickHours AccrualPeriod' => 'ENUMTYPE',
		'EmployeePayrollInfoMod SickHours HoursAccrued' => 'TIMEINTERVALTYPE',
		'EmployeePayrollInfoMod SickHours MaximumHours' => 'TIMEINTERVALTYPE',
		'EmployeePayrollInfoMod SickHours IsResettingHoursEachNewYear' => 'BOOLTYPE',
		'EmployeePayrollInfoMod SickHours HoursUsed' => 'TIMEINTERVALTYPE',
		'EmployeePayrollInfoMod SickHours AccrualStartDate' => 'DATETYPE',
		'EmployeePayrollInfoMod VacationHours HoursAvailable' => 'TIMEINTERVALTYPE',
		'EmployeePayrollInfoMod VacationHours AccrualPeriod' => 'ENUMTYPE',
		'EmployeePayrollInfoMod VacationHours HoursAccrued' => 'TIMEINTERVALTYPE',
		'EmployeePayrollInfoMod VacationHours MaximumHours' => 'TIMEINTERVALTYPE',
		'EmployeePayrollInfoMod VacationHours IsResettingHoursEachNewYear' => 'BOOLTYPE',
		'EmployeePayrollInfoMod VacationHours HoursUsed' => 'TIMEINTERVALTYPE',
		'EmployeePayrollInfoMod VacationHours AccrualStartDate' => 'DATETYPE',
		'IncludeRetElement' => 'STRTYPE',
	];

	/**
	 * Field Maximum Length
	 * @var int[]
	 */
	protected $_maxLengthPaths = [
		'EditSequence' => 16,
		'Name' => 100,
		'Salutation' => 15,
		'FirstName' => 25,
		'MiddleName' => 5,
		'LastName' => 25,
		'Suffix' => 10,
		'JobTitle' => 41,
		'SupervisorRef FullName' => 209,
		'Department' => 31,
		'Description' => 64,
		'EmployeeAddress Addr1' => 41,
		'EmployeeAddress Addr2' => 41,
		'EmployeeAddress Addr3' => 500,
		'EmployeeAddress Addr4' => 500,
		'EmployeeAddress City' => 31,
		'EmployeeAddress State' => 21,
		'EmployeeAddress PostalCode' => 13,
		'EmployeeAddress Country' => 255,
		'PrintAs' => 41,
		'Phone' => 21,
		'Mobile' => 21,
		'Pager' => 21,
		'PagerPIN' => 10,
		'AltPhone' => 21,
		'Fax' => 21,
		'Email' => 1023,
		'AdditionalContactRef ContactName' => 40,
		'AdditionalContactRef ContactValue' => 255,
		'EmergencyContacts PrimaryContact ContactName' => 40,
		'EmergencyContacts PrimaryContact ContactValue' => 255,
		'EmergencyContacts SecondaryContact ContactName' => 40,
		'EmergencyContacts SecondaryContact ContactValue' => 255,
		'DisabilityDesc' => 25,
		'AccountNumber' => 99,
		'Notes' => 4095,
		'AdditionalNotesMod Note' => 4095,
		'BillingRateRef FullName' => 31,
		'EmployeePayrollInfoMod ClassRef FullName' => 159,
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef FullName' => 31,
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
		'IsActive' => 3.0,
		'JobTitle' => 12.0,
		'SupervisorRef ListID' => 13.0,
		'SupervisorRef FullName' => 13.0,
		'Department' => 13.0,
		'Description' => 13.0,
		'TargetBonus' => 13.0,
		'EmployeeAddress Addr4' => 2.0,
		'Mobile' => 2.1,
		'Pager' => 2.1,
		'PagerPIN' => 2.1,
		'Fax' => 2.1,
		'AdditionalContactRef ContactName' => 12.0,
		'AdditionalContactRef ContactValue' => 12.0,
		'EmergencyContacts PrimaryContact ContactName' => 13.0,
		'EmergencyContacts PrimaryContact ContactValue' => 13.0,
		'EmergencyContacts PrimaryContact Relation' => 13.0,
		'EmergencyContacts SecondaryContact ContactName' => 13.0,
		'EmergencyContacts SecondaryContact ContactValue' => 13.0,
		'EmergencyContacts SecondaryContact Relation' => 13.0,
		'EmployeeType' => 5.0,
		'PartOrFullTime' => 13.0,
		'Exempt' => 13.0,
		'KeyEmployee' => 13.0,
		'HiredDate' => 5.0,
		'OriginalHireDate' => 13.0,
		'AdjustedServiceDate' => 13.0,
		'ReleasedDate' => 5.0,
		'BirthDate' => 2.0,
		'USCitizen' => 13.0,
		'Ethnicity' => 13.0,
		'Disabled' => 13.0,
		'DisabilityDesc' => 13.0,
		'OnFile' => 13.0,
		'WorkAuthExpireDate' => 13.0,
		'USVeteran' => 13.0,
		'MilitaryStatus' => 13.0,
		'Notes' => 3.0,
		'AdditionalNotesMod NoteID' => 12.0,
		'AdditionalNotesMod Note' => 12.0,
		'BillingRateRef ListID' => 6.0,
		'BillingRateRef FullName' => 6.0,
		'EmployeePayrollInfoMod PayPeriod' => 2.0,
		'EmployeePayrollInfoMod ClassRef ListID' => 2.0,
		'EmployeePayrollInfoMod ClassRef FullName' => 2.0,
		'EmployeePayrollInfoMod ClearEarnings' => 2.0,
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef ListID' => 2.0,
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef FullName' => 2.0,
		'EmployeePayrollInfoMod Earnings Rate' => 2.0,
		'EmployeePayrollInfoMod Earnings RatePercent' => 2.0,
		'EmployeePayrollInfoMod UseTimeDataToCreatePaychecks' => 2.0,
		'EmployeePayrollInfoMod SickHours HoursAvailable' => 2.0,
		'EmployeePayrollInfoMod SickHours AccrualPeriod' => 2.0,
		'EmployeePayrollInfoMod SickHours HoursAccrued' => 2.0,
		'EmployeePayrollInfoMod SickHours MaximumHours' => 2.0,
		'EmployeePayrollInfoMod SickHours IsResettingHoursEachNewYear' => 2.0,
		'EmployeePayrollInfoMod SickHours HoursUsed' => 2.0,
		'EmployeePayrollInfoMod SickHours AccrualStartDate' => 2.0,
		'EmployeePayrollInfoMod VacationHours HoursAvailable' => 2.0,
		'EmployeePayrollInfoMod VacationHours AccrualPeriod' => 2.0,
		'EmployeePayrollInfoMod VacationHours HoursAccrued' => 2.0,
		'EmployeePayrollInfoMod VacationHours MaximumHours' => 2.0,
		'EmployeePayrollInfoMod VacationHours IsResettingHoursEachNewYear' => 2.0,
		'EmployeePayrollInfoMod VacationHours HoursUsed' => 2.0,
		'EmployeePayrollInfoMod VacationHours AccrualStartDate' => 2.0,
		'IncludeRetElement' => 4.0,
	];

	/**
	 * Field May Be Included Multiple Times
	 * @var bool[]
	 */
	protected $_isRepeatablePaths = [
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
		'Name' => ['US','CA','UK','AU'],
		'IsActive' => ['OE'],
		'Suffix' => ['US','CA','UK','AU'],
		'SupervisorRef ListID' => ['CA','UK','AU','OE'],
		'SupervisorRef FullName' => ['CA','UK','AU','OE'],
		'Department' => ['CA','UK','AU','OE'],
		'Description' => ['CA','UK','AU','OE'],
		'EmployeeAddress Addr3' => ['US','CA','UK','AU'],
		'EmployeeAddress Addr4' => ['US','CA','UK','AU'],
		'EmployeeAddress Country' => ['US','CA','UK','AU'],
		'Pager' => ['OE'],
		'PagerPIN' => ['OE'],
		'Fax' => ['OE'],
		'EmergencyContacts PrimaryContact ContactName' => ['CA','UK','AU','OE'],
		'EmergencyContacts PrimaryContact ContactValue' => ['CA','UK','AU','OE'],
		'EmergencyContacts PrimaryContact Relation' => ['CA','UK','AU','OE'],
		'EmergencyContacts SecondaryContact ContactName' => ['CA','UK','AU','OE'],
		'EmergencyContacts SecondaryContact ContactValue' => ['CA','UK','AU','OE'],
		'EmergencyContacts SecondaryContact Relation' => ['CA','UK','AU','OE'],
		'EmployeeType' => ['OE'],
		'PartOrFullTime' => ['CA','UK','AU','OE'],
		'Exempt' => ['CA','UK','AU','OE'],
		'KeyEmployee' => ['CA','UK','AU','OE'],
		'HiredDate' => ['OE'],
		'OriginalHireDate' => ['CA','UK','AU','OE'],
		'AdjustedServiceDate' => ['CA','UK','AU','OE'],
		'ReleasedDate' => ['OE'],
		'BirthDate' => ['OE'],
		'USCitizen' => ['CA','UK','AU','OE'],
		'Ethnicity' => ['CA','UK','AU','OE'],
		'Disabled' => ['CA','UK','AU','OE'],
		'DisabilityDesc' => ['CA','UK','AU','OE'],
		'OnFile' => ['CA','UK','AU','OE'],
		'WorkAuthExpireDate' => ['CA','UK','AU','OE'],
		'USVeteran' => ['CA','UK','AU','OE'],
		'MilitaryStatus' => ['CA','UK','AU','OE'],
		'AccountNumber' => ['OE'],
		'Notes' => ['OE'],
		'BillingRateRef ListID' => ['OE'],
		'BillingRateRef FullName' => ['OE'],
		'EmployeePayrollInfoMod PayPeriod' => ['OE'],
		'EmployeePayrollInfoMod ClassRef ListID' => ['OE'],
		'EmployeePayrollInfoMod ClassRef FullName' => ['OE'],
		'EmployeePayrollInfoMod ClearEarnings' => ['OE'],
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef ListID' => ['OE'],
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef FullName' => ['OE'],
		'EmployeePayrollInfoMod Earnings Rate' => ['OE'],
		'EmployeePayrollInfoMod Earnings RatePercent' => ['OE'],
		'EmployeePayrollInfoMod UseTimeDataToCreatePaychecks' => ['OE'],
		'EmployeePayrollInfoMod SickHours HoursAvailable' => ['OE'],
		'EmployeePayrollInfoMod SickHours AccrualPeriod' => ['OE'],
		'EmployeePayrollInfoMod SickHours HoursAccrued' => ['OE'],
		'EmployeePayrollInfoMod SickHours MaximumHours' => ['OE'],
		'EmployeePayrollInfoMod SickHours IsResettingHoursEachNewYear' => ['OE'],
		'EmployeePayrollInfoMod SickHours HoursUsed' => ['OE'],
		'EmployeePayrollInfoMod SickHours AccrualStartDate' => ['OE'],
		'EmployeePayrollInfoMod VacationHours HoursAvailable' => ['OE'],
		'EmployeePayrollInfoMod VacationHours AccrualPeriod' => ['OE'],
		'EmployeePayrollInfoMod VacationHours HoursAccrued' => ['OE'],
		'EmployeePayrollInfoMod VacationHours MaximumHours' => ['OE'],
		'EmployeePayrollInfoMod VacationHours IsResettingHoursEachNewYear' => ['OE'],
		'EmployeePayrollInfoMod VacationHours HoursUsed' => ['OE'],
		'EmployeePayrollInfoMod VacationHours AccrualStartDate' => ['OE'],
		'IncludeRetElement' => ['OE'],
	];

	/**
	 * Fields In Order They Must Be Included In The QBXML Request
	 * @var string[]
	 */
	protected $_reorderPathsPaths = [
		'ListID',
		'EditSequence',
		'Name',
		'IsActive',
		'Salutation',
		'FirstName',
		'MiddleName',
		'LastName',
		'Suffix',
		'JobTitle',
		'SupervisorRef',
		'SupervisorRef ListID',
		'SupervisorRef FullName',
		'Department',
		'Description',
		'TargetBonus',
		'EmployeeAddress',
		'EmployeeAddress Addr1',
		'EmployeeAddress Addr2',
		'EmployeeAddress Addr3',
		'EmployeeAddress Addr4',
		'EmployeeAddress City',
		'EmployeeAddress State',
		'EmployeeAddress PostalCode',
		'EmployeeAddress Country',
		'PrintAs',
		'Phone',
		'Mobile',
		'Pager',
		'PagerPIN',
		'AltPhone',
		'Fax',
		'Email',
		'AdditionalContactRef',
		'AdditionalContactRef ContactName',
		'AdditionalContactRef ContactValue',
		'EmergencyContacts',
		'EmergencyContacts PrimaryContact ContactName',
		'EmergencyContacts PrimaryContact ContactValue',
		'EmergencyContacts PrimaryContact Relation',
		'EmergencyContacts SecondaryContact ContactName',
		'EmergencyContacts SecondaryContact ContactValue',
		'EmergencyContacts SecondaryContact Relation',
		'EmployeeType',
		'PartOrFullTime',
		'Exempt',
		'KeyEmployee',
		'HiredDate',
		'OriginalHireDate',
		'AdjustedServiceDate',
		'ReleasedDate',
		'BirthDate',
		'USCitizen',
		'Ethnicity',
		'Disabled',
		'DisabilityDesc',
		'OnFile',
		'WorkAuthExpireDate',
		'USVeteran',
		'MilitaryStatus',
		'AccountNumber',
		'Notes',
		'AdditionalNotesMod',
		'AdditionalNotesMod NoteID',
		'AdditionalNotesMod Note',
		'BillingRateRef',
		'BillingRateRef ListID',
		'BillingRateRef FullName',
		'EmployeePayrollInfoMod',
		'EmployeePayrollInfoMod PayPeriod',
		'EmployeePayrollInfoMod ClassRef ListID',
		'EmployeePayrollInfoMod ClassRef FullName',
		'EmployeePayrollInfoMod ClearEarnings',
		'EmployeePayrollInfoMod',
		'EmployeePayrollInfoMod Earnings',
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef',
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef ListID',
		'EmployeePayrollInfoMod Earnings PayrollItemWageRef FullName',
		'EmployeePayrollInfoMod Earnings Rate',
		'EmployeePayrollInfoMod Earnings RatePercent',
		'EmployeePayrollInfoMod UseTimeDataToCreatePaychecks',
		'EmployeePayrollInfoMod SickHours HoursAvailable',
		'EmployeePayrollInfoMod SickHours AccrualPeriod',
		'EmployeePayrollInfoMod SickHours HoursAccrued',
		'EmployeePayrollInfoMod SickHours MaximumHours',
		'EmployeePayrollInfoMod SickHours IsResettingHoursEachNewYear',
		'EmployeePayrollInfoMod SickHours HoursUsed',
		'EmployeePayrollInfoMod SickHours AccrualStartDate',
		'EmployeePayrollInfoMod VacationHours HoursAvailable',
		'EmployeePayrollInfoMod VacationHours AccrualPeriod',
		'EmployeePayrollInfoMod VacationHours HoursAccrued',
		'EmployeePayrollInfoMod VacationHours MaximumHours',
		'EmployeePayrollInfoMod VacationHours IsResettingHoursEachNewYear',
		'EmployeePayrollInfoMod VacationHours HoursUsed',
		'EmployeePayrollInfoMod VacationHours AccrualStartDate',
		'IncludeRetElement',
	];
}
