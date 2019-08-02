<?php declare(strict_types=1);

/**
 * Schema object for: EmployeeAddRq
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
class EmployeeAddRq extends AbstractSchemaObject
{
	protected function &_qbxmlWrapper(): string
	{
		static $wrapper = 'EmployeeAdd';

		return $wrapper;
	}

	protected function &_dataTypePaths(): array
	{
		static $paths = [
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
			'SSN' => 'STRTYPE',
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
			'Gender' => 'ENUMTYPE',
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
			'AdditionalNotes Note' => 'STRTYPE',
			'BillingRateRef ListID' => 'IDTYPE',
			'BillingRateRef FullName' => 'STRTYPE',
			'EmployeePayrollInfo PayPeriod' => 'ENUMTYPE',
			'EmployeePayrollInfo ClassRef ListID' => 'IDTYPE',
			'EmployeePayrollInfo ClassRef FullName' => 'STRTYPE',
			'EmployeePayrollInfo ClearEarnings' => 'BOOLTYPE',
			'EmployeePayrollInfo Earnings PayrollItemWageRef ListID' => 'IDTYPE',
			'EmployeePayrollInfo Earnings PayrollItemWageRef FullName' => 'STRTYPE',
			'EmployeePayrollInfo Earnings Rate' => 'PRICETYPE',
			'EmployeePayrollInfo Earnings RatePercent' => 'PERCENTTYPE',
			'EmployeePayrollInfo UseTimeDataToCreatePaychecks' => 'ENUMTYPE',
			'EmployeePayrollInfo SickHours HoursAvailable' => 'TIMEINTERVALTYPE',
			'EmployeePayrollInfo SickHours AccrualPeriod' => 'ENUMTYPE',
			'EmployeePayrollInfo SickHours HoursAccrued' => 'TIMEINTERVALTYPE',
			'EmployeePayrollInfo SickHours MaximumHours' => 'TIMEINTERVALTYPE',
			'EmployeePayrollInfo SickHours IsResettingHoursEachNewYear' => 'BOOLTYPE',
			'EmployeePayrollInfo SickHours HoursUsed' => 'TIMEINTERVALTYPE',
			'EmployeePayrollInfo SickHours AccrualStartDate' => 'DATETYPE',
			'EmployeePayrollInfo VacationHours HoursAvailable' => 'TIMEINTERVALTYPE',
			'EmployeePayrollInfo VacationHours AccrualPeriod' => 'ENUMTYPE',
			'EmployeePayrollInfo VacationHours HoursAccrued' => 'TIMEINTERVALTYPE',
			'EmployeePayrollInfo VacationHours MaximumHours' => 'TIMEINTERVALTYPE',
			'EmployeePayrollInfo VacationHours IsResettingHoursEachNewYear' => 'BOOLTYPE',
			'EmployeePayrollInfo VacationHours HoursUsed' => 'TIMEINTERVALTYPE',
			'EmployeePayrollInfo VacationHours AccrualStartDate' => 'DATETYPE',
			'ExternalGUID' => 'GUIDTYPE',
			'IncludeRetElement' => 'STRTYPE',
		];

		return $paths;
	}

	protected function &_maxLengthPaths(): array
	{
		static $paths = [
			'Name' => 100,
			'IsActive' => 0,
			'Salutation' => 15,
			'FirstName' => 25,
			'MiddleName' => 5,
			'LastName' => 25,
			'Suffix' => 10,
			'JobTitle' => 41,
			'SupervisorRef ListID' => 0,
			'SupervisorRef FullName' => 209,
			'Department' => 31,
			'Description' => 64,
			'TargetBonus' => 0,
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
			'SSN' => 15,
			'Email' => 1023,
			'AdditionalContactRef ContactName' => 40,
			'AdditionalContactRef ContactValue' => 255,
			'EmergencyContacts PrimaryContact ContactName' => 40,
			'EmergencyContacts PrimaryContact ContactValue' => 255,
			'EmergencyContacts PrimaryContact Relation' => 0,
			'EmergencyContacts SecondaryContact ContactName' => 40,
			'EmergencyContacts SecondaryContact ContactValue' => 255,
			'EmergencyContacts SecondaryContact Relation' => 0,
			'EmployeeType' => 0,
			'PartOrFullTime' => 0,
			'Exempt' => 0,
			'KeyEmployee' => 0,
			'Gender' => 0,
			'HiredDate' => 0,
			'OriginalHireDate' => 0,
			'AdjustedServiceDate' => 0,
			'ReleasedDate' => 0,
			'BirthDate' => 0,
			'USCitizen' => 0,
			'Ethnicity' => 0,
			'Disabled' => 0,
			'DisabilityDesc' => 25,
			'OnFile' => 0,
			'WorkAuthExpireDate' => 0,
			'USVeteran' => 0,
			'MilitaryStatus' => 0,
			'AccountNumber' => 99,
			'Notes' => 4095,
			'AdditionalNotes Note' => 4095,
			'BillingRateRef ListID' => 0,
			'BillingRateRef FullName' => 209,
			'EmployeePayrollInfo PayPeriod' => 0,
			'EmployeePayrollInfo ClassRef ListID' => 0,
			'EmployeePayrollInfo ClassRef FullName' => 209,
			'EmployeePayrollInfo ClearEarnings' => 0,
			'EmployeePayrollInfo Earnings PayrollItemWageRef ListID' => 0,
			'EmployeePayrollInfo Earnings PayrollItemWageRef FullName' => 209,
			'EmployeePayrollInfo Earnings Rate' => 0,
			'EmployeePayrollInfo Earnings RatePercent' => 0,
			'EmployeePayrollInfo UseTimeDataToCreatePaychecks' => 0,
			'EmployeePayrollInfo SickHours HoursAvailable' => 0,
			'EmployeePayrollInfo SickHours AccrualPeriod' => 0,
			'EmployeePayrollInfo SickHours HoursAccrued' => 0,
			'EmployeePayrollInfo SickHours MaximumHours' => 0,
			'EmployeePayrollInfo SickHours IsResettingHoursEachNewYear' => 0,
			'EmployeePayrollInfo SickHours HoursUsed' => 0,
			'EmployeePayrollInfo SickHours AccrualStartDate' => 0,
			'EmployeePayrollInfo VacationHours HoursAvailable' => 0,
			'EmployeePayrollInfo VacationHours AccrualPeriod' => 0,
			'EmployeePayrollInfo VacationHours HoursAccrued' => 0,
			'EmployeePayrollInfo VacationHours MaximumHours' => 0,
			'EmployeePayrollInfo VacationHours IsResettingHoursEachNewYear' => 0,
			'EmployeePayrollInfo VacationHours HoursUsed' => 0,
			'EmployeePayrollInfo VacationHours AccrualStartDate' => 0,
			'ExternalGUID' => 0,
			'IncludeRetElement' => 50,
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
			'Name' => 999.99,
			'IsActive' => 999.99,
			'Salutation' => 999.99,
			'FirstName' => 999.99,
			'MiddleName' => 999.99,
			'LastName' => 999.99,
			'Suffix' => 999.99,
			'JobTitle' => 12.0,
			'SupervisorRef ListID' => 999.99,
			'SupervisorRef FullName' => 999.99,
			'Department' => 13.0,
			'Description' => 13.0,
			'TargetBonus' => 13.0,
			'EmployeeAddress Addr1' => 999.99,
			'EmployeeAddress Addr2' => 999.99,
			'EmployeeAddress Addr3' => 999.99,
			'EmployeeAddress Addr4' => 2.0,
			'EmployeeAddress City' => 999.99,
			'EmployeeAddress State' => 999.99,
			'EmployeeAddress PostalCode' => 999.99,
			'EmployeeAddress Country' => 999.99,
			'PrintAs' => 999.99,
			'Phone' => 999.99,
			'Mobile' => 2.1,
			'Pager' => 2.1,
			'PagerPIN' => 2.1,
			'AltPhone' => 999.99,
			'Fax' => 2.1,
			'SSN' => 999.99,
			'Email' => 999.99,
			'AdditionalContactRef ContactName' => 999.99,
			'AdditionalContactRef ContactValue' => 999.99,
			'EmergencyContacts PrimaryContact ContactName' => 999.99,
			'EmergencyContacts PrimaryContact ContactValue' => 999.99,
			'EmergencyContacts PrimaryContact Relation' => 999.99,
			'EmergencyContacts SecondaryContact ContactName' => 999.99,
			'EmergencyContacts SecondaryContact ContactValue' => 999.99,
			'EmergencyContacts SecondaryContact Relation' => 999.99,
			'EmployeeType' => 999.99,
			'PartOrFullTime' => 13.0,
			'Exempt' => 13.0,
			'KeyEmployee' => 13.0,
			'Gender' => 999.99,
			'HiredDate' => 999.99,
			'OriginalHireDate' => 13.0,
			'AdjustedServiceDate' => 13.0,
			'ReleasedDate' => 999.99,
			'BirthDate' => 2.0,
			'USCitizen' => 13.0,
			'Ethnicity' => 13.0,
			'Disabled' => 13.0,
			'DisabilityDesc' => 13.0,
			'OnFile' => 13.0,
			'WorkAuthExpireDate' => 13.0,
			'USVeteran' => 13.0,
			'MilitaryStatus' => 13.0,
			'AccountNumber' => 999.99,
			'Notes' => 3.0,
			'AdditionalNotes Note' => 999.99,
			'BillingRateRef ListID' => 999.99,
			'BillingRateRef FullName' => 999.99,
			'EmployeePayrollInfo PayPeriod' => 999.99,
			'EmployeePayrollInfo ClassRef ListID' => 999.99,
			'EmployeePayrollInfo ClassRef FullName' => 999.99,
			'EmployeePayrollInfo ClearEarnings' => 999.99,
			'EmployeePayrollInfo Earnings PayrollItemWageRef ListID' => 999.99,
			'EmployeePayrollInfo Earnings PayrollItemWageRef FullName' => 999.99,
			'EmployeePayrollInfo Earnings Rate' => 999.99,
			'EmployeePayrollInfo Earnings RatePercent' => 999.99,
			'EmployeePayrollInfo UseTimeDataToCreatePaychecks' => 3.0,
			'EmployeePayrollInfo SickHours HoursAvailable' => 999.99,
			'EmployeePayrollInfo SickHours AccrualPeriod' => 999.99,
			'EmployeePayrollInfo SickHours HoursAccrued' => 999.99,
			'EmployeePayrollInfo SickHours MaximumHours' => 999.99,
			'EmployeePayrollInfo SickHours IsResettingHoursEachNewYear' => 999.99,
			'EmployeePayrollInfo SickHours HoursUsed' => 5.0,
			'EmployeePayrollInfo SickHours AccrualStartDate' => 5.0,
			'EmployeePayrollInfo VacationHours HoursAvailable' => 999.99,
			'EmployeePayrollInfo VacationHours AccrualPeriod' => 999.99,
			'EmployeePayrollInfo VacationHours HoursAccrued' => 999.99,
			'EmployeePayrollInfo VacationHours MaximumHours' => 999.99,
			'EmployeePayrollInfo VacationHours IsResettingHoursEachNewYear' => 999.99,
			'EmployeePayrollInfo VacationHours HoursUsed' => 5.0,
			'EmployeePayrollInfo VacationHours AccrualStartDate' => 5.0,
			'ExternalGUID' => 8.0,
			'IncludeRetElement' => 4.0,
		];

		return $paths;
	}

	protected function &_isRepeatablePaths(): array
	{
		static $paths = [
			'Name' => false,
			'IsActive' => false,
			'Salutation' => false,
			'FirstName' => false,
			'MiddleName' => false,
			'LastName' => false,
			'Suffix' => false,
			'JobTitle' => false,
			'SupervisorRef ListID' => false,
			'SupervisorRef FullName' => false,
			'Department' => false,
			'Description' => false,
			'TargetBonus' => false,
			'EmployeeAddress Addr1' => false,
			'EmployeeAddress Addr2' => false,
			'EmployeeAddress Addr3' => false,
			'EmployeeAddress Addr4' => false,
			'EmployeeAddress City' => false,
			'EmployeeAddress State' => false,
			'EmployeeAddress PostalCode' => false,
			'EmployeeAddress Country' => false,
			'PrintAs' => false,
			'Phone' => false,
			'Mobile' => false,
			'Pager' => false,
			'PagerPIN' => false,
			'AltPhone' => false,
			'Fax' => false,
			'SSN' => false,
			'Email' => false,
			'AdditionalContactRef ContactName' => false,
			'AdditionalContactRef ContactValue' => false,
			'EmergencyContacts PrimaryContact ContactName' => false,
			'EmergencyContacts PrimaryContact ContactValue' => false,
			'EmergencyContacts PrimaryContact Relation' => false,
			'EmergencyContacts SecondaryContact ContactName' => false,
			'EmergencyContacts SecondaryContact ContactValue' => false,
			'EmergencyContacts SecondaryContact Relation' => false,
			'EmployeeType' => false,
			'PartOrFullTime' => false,
			'Exempt' => false,
			'KeyEmployee' => false,
			'Gender' => false,
			'HiredDate' => false,
			'OriginalHireDate' => false,
			'AdjustedServiceDate' => false,
			'ReleasedDate' => false,
			'BirthDate' => false,
			'USCitizen' => false,
			'Ethnicity' => false,
			'Disabled' => false,
			'DisabilityDesc' => false,
			'OnFile' => false,
			'WorkAuthExpireDate' => false,
			'USVeteran' => false,
			'MilitaryStatus' => false,
			'AccountNumber' => false,
			'Notes' => false,
			'AdditionalNotes Note' => false,
			'BillingRateRef ListID' => false,
			'BillingRateRef FullName' => false,
			'EmployeePayrollInfo PayPeriod' => false,
			'EmployeePayrollInfo ClassRef ListID' => false,
			'EmployeePayrollInfo ClassRef FullName' => false,
			'EmployeePayrollInfo ClearEarnings' => false,
			'EmployeePayrollInfo Earnings PayrollItemWageRef ListID' => false,
			'EmployeePayrollInfo Earnings PayrollItemWageRef FullName' => false,
			'EmployeePayrollInfo Earnings Rate' => false,
			'EmployeePayrollInfo Earnings RatePercent' => false,
			'EmployeePayrollInfo UseTimeDataToCreatePaychecks' => false,
			'EmployeePayrollInfo SickHours HoursAvailable' => false,
			'EmployeePayrollInfo SickHours AccrualPeriod' => false,
			'EmployeePayrollInfo SickHours HoursAccrued' => false,
			'EmployeePayrollInfo SickHours MaximumHours' => false,
			'EmployeePayrollInfo SickHours IsResettingHoursEachNewYear' => false,
			'EmployeePayrollInfo SickHours HoursUsed' => false,
			'EmployeePayrollInfo SickHours AccrualStartDate' => false,
			'EmployeePayrollInfo VacationHours HoursAvailable' => false,
			'EmployeePayrollInfo VacationHours AccrualPeriod' => false,
			'EmployeePayrollInfo VacationHours HoursAccrued' => false,
			'EmployeePayrollInfo VacationHours MaximumHours' => false,
			'EmployeePayrollInfo VacationHours IsResettingHoursEachNewYear' => false,
			'EmployeePayrollInfo VacationHours HoursUsed' => false,
			'EmployeePayrollInfo VacationHours AccrualStartDate' => false,
			'ExternalGUID' => false,
			'IncludeRetElement' => true,
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
			'SSN',
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
			'Gender',
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
			'AdditionalNotes',
			'AdditionalNotes Note',
			'BillingRateRef',
			'BillingRateRef ListID',
			'BillingRateRef FullName',
			'EmployeePayrollInfo',
			'EmployeePayrollInfo PayPeriod',
			'EmployeePayrollInfo ClassRef ListID',
			'EmployeePayrollInfo ClassRef FullName',
			'EmployeePayrollInfo ClearEarnings',
			'EmployeePayrollInfo',
			'EmployeePayrollInfo Earnings',
			'EmployeePayrollInfo Earnings PayrollItemWageRef',
			'EmployeePayrollInfo Earnings PayrollItemWageRef ListID',
			'EmployeePayrollInfo Earnings PayrollItemWageRef FullName',
			'EmployeePayrollInfo Earnings Rate',
			'EmployeePayrollInfo Earnings RatePercent',
			'EmployeePayrollInfo UseTimeDataToCreatePaychecks',
			'EmployeePayrollInfo SickHours HoursAvailable',
			'EmployeePayrollInfo SickHours AccrualPeriod',
			'EmployeePayrollInfo SickHours HoursAccrued',
			'EmployeePayrollInfo SickHours MaximumHours',
			'EmployeePayrollInfo SickHours IsResettingHoursEachNewYear',
			'EmployeePayrollInfo SickHours HoursUsed',
			'EmployeePayrollInfo SickHours AccrualStartDate',
			'EmployeePayrollInfo VacationHours HoursAvailable',
			'EmployeePayrollInfo VacationHours AccrualPeriod',
			'EmployeePayrollInfo VacationHours HoursAccrued',
			'EmployeePayrollInfo VacationHours MaximumHours',
			'EmployeePayrollInfo VacationHours IsResettingHoursEachNewYear',
			'EmployeePayrollInfo VacationHours HoursUsed',
			'EmployeePayrollInfo VacationHours AccrualStartDate',
			'ExternalGUID',
			'IncludeRetElement',
		];

		return $paths;
	}
}
