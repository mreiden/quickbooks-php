<?php declare(strict_types=1);

namespace QuickBooksPhpDevKit\IPP\Object;

use QuickBooksPhpDevKit\IPP\BaseObject;

class Employee extends BaseObject
{
	protected function _defaults(): array
	{
		return [
			'TypeOf' => 'Person',
		];
	}

	protected function _order(): array
	{
		return [
			'Id' => true,
			'MetaData' => true,
			'PartyReferenceId' => true,
			'TypeOf' => true,
			'Name' => true,
			'Address' => true,
			'Phone' => true,
			'WebSite' => true,
			'Email' => true,
			'Title' => true,
			'GivenName' => true,
			'MiddleName' => true,
			'FamilyName' => true,
			'Suffix' => true,
			'Gender' => true,
			'BirthDate' => true,
			'LegalName' => true,
			'DBAName' => true,
			'TaxIdentifier' => true,
			'Notes' => true,
			'Active' => true,
			'ShowAs' => true,
			'EmployeeType' => true,
			'EmployeeNumber' => true,
			'HiredDate' => true,
			'ReleasedDate' => true,
			'UseTimeEntry' => true,
		];
	}
}
