<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;
use QuickBooksPhpDevKit\WebConnector\QWC;

final class QwcTest extends XmlBaseTest
{
	protected $obj;

	public function setUp(): void
	{
	}

	public function tearDown(): void
	{
	}


	/**
	 * Test __toString()
	 */
	public function testToString(): void
	{
		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName'
		);

		$xml = (string) $qwc;
		$xml = $this->sanitizeXml($xml);
		$FileID = $xml->getElementsByTagName('FileID')->item(0)->nodeValue;
		$OwnerID = $xml->getElementsByTagName('OwnerID')->item(0)->nodeValue;

		$expected = implode("\n", [
			'<QBWCXML>',
			'    <AppName>My WebConnector</AppName>',
			'    <AppID></AppID>',
			'    <AppURL>https://example.com/MyWebConnectorUrl</AppURL>',
			'    <AppDescription>Short Descript of What It Does (e.g. Import our Invoices from website)</AppDescription>',
			'    <AppSupport>https://example.com/Support-Info-About-Url</AppSupport>',
			'    <UserName>WebConnectorUserName</UserName>',
			'    <OwnerID>' . $OwnerID . '</OwnerID>',
			'    <FileID>' . $FileID . '</FileID>',
			'    <QBType>QBFS</QBType>',
			'    <Notify>false</Notify>',
			'    <IsReadOnly>false</IsReadOnly>',
			'</QBWCXML>',
		]);

	   $this->commonTests($expected, $xml->saveXML());
	}

	/**
	 * Test ->http()
	 *
	 * @runInSeparateProcess  Required to avoid "Cannot modify header information - headers already sent by (output started at" errors
	 */
	public function testHttp(): void
	{
		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName'
		);

		ob_start();
		$xml = $qwc->http();
		$xml = ob_get_clean();

		$xml = $this->sanitizeXml($xml);
		$FileID = $xml->getElementsByTagName('FileID')->item(0)->nodeValue;
		$OwnerID = $xml->getElementsByTagName('OwnerID')->item(0)->nodeValue;

		$expected = implode("\n", [
			'<QBWCXML>',
			'    <AppName>My WebConnector</AppName>',
			'    <AppID></AppID>',
			'    <AppURL>https://example.com/MyWebConnectorUrl</AppURL>',
			'    <AppDescription>Short Descript of What It Does (e.g. Import our Invoices from website)</AppDescription>',
			'    <AppSupport>https://example.com/Support-Info-About-Url</AppSupport>',
			'    <UserName>WebConnectorUserName</UserName>',
			'    <OwnerID>' . $OwnerID . '</OwnerID>',
			'    <FileID>' . $FileID . '</FileID>',
			'    <QBType>QBFS</QBType>',
			'    <Notify>false</Notify>',
			'    <IsReadOnly>false</IsReadOnly>',
			'</QBWCXML>',
		]);

	   $this->commonTests($expected, $xml->saveXML());
	}

	/**
	 * Test Default Values
	 */
	public function testDefaultValues(): void
	{
		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName'
		);

		$xml = $qwc->generate();
		$xml = $this->sanitizeXml($xml);
		$FileID = $xml->getElementsByTagName('FileID')->item(0)->nodeValue;
		$OwnerID = $xml->getElementsByTagName('OwnerID')->item(0)->nodeValue;

		$expected = implode("\n", [
			'<QBWCXML>',
			'    <AppName>My WebConnector</AppName>',
			'    <AppID></AppID>',
			'    <AppURL>https://example.com/MyWebConnectorUrl</AppURL>',
			'    <AppDescription>Short Descript of What It Does (e.g. Import our Invoices from website)</AppDescription>',
			'    <AppSupport>https://example.com/Support-Info-About-Url</AppSupport>',
			'    <UserName>WebConnectorUserName</UserName>',
			'    <OwnerID>' . $OwnerID . '</OwnerID>',
			'    <FileID>' . $FileID . '</FileID>',
			'    <QBType>QBFS</QBType>',
			'    <Notify>false</Notify>',
			'    <IsReadOnly>false</IsReadOnly>',
			'</QBWCXML>',
		]);

	   $this->commonTests($expected, $xml->saveXML());
	}

	/**
	 * Test Invalid App Url
	 */
	public function testInvalidAppUrlException(): void
	{
		$this->expectException('\Exception');

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'htt://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName',
		);
		$qwc->generate();
	}

	/**
	 * Test Invalid App Support Url
	 */
	public function testInvalidAppSupportUrlException(): void
	{
		$this->expectException('\Exception');

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'blah:/example.com/Support-Info-About-Url',
			'WebConnectorUserName',
		);
		$qwc->generate();
	}

	/**
	 * Test Invalid QbType
	 */
	public function testInvalidQbTypeException(): void
	{
		$this->expectException('\Exception');

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName',
			'{FileID}',
			'{OwnerID}',
			'InvalidQbType',
		);
		$qwc->generate();
	}

	/**
	 * Test Invalid "Run Every n Seconds" Interval String
	 */
	public function testInvalidRunEveryIntervalStringException(): void
	{
		$this->expectException('\Exception');

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName',
			'{FileID}',
			'{OwnerID}',
			PackageInfo::QbType['QBFS'],
			false,
			'15 minuataetasutes',
		);
		$qwc->generate();
	}

	/**
	 * Test Invalid "Run Every n Seconds" Data Type
	 */
	public function testInvalidRunEveryDataTypeException(): void
	{
		$this->expectException('\Exception');

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName',
			'{FileID}',
			'{OwnerID}',
			PackageInfo::QbType['QBFS'],
			false,
			[],
		);
		$qwc->generate();
	}

	/**
	 * Test Invalid Personal Data
	 */
	public function testInvalidPersonalDataException(): void
	{
		$this->expectException('\Exception');

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName',
			'{FileID}',
			'{OwnerID}',
			PackageInfo::QbType['QBFS'],
			false,
			'15 minutes',
			'Invalid'
		);
		$qwc->generate();
	}

	/**
	 * Test Invalid Unattended Mode
	 */
	public function testInvalidUnattendedModeException(): void
	{
		$this->expectException('\Exception');

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName',
			'{FileID}',
			'{OwnerID}',
			PackageInfo::QbType['QBFS'],
			false,
			'15 minutes',
			QWC::PERSONALDATA_NOTNEEDED,
			'Invalid'
		);
		$qwc->generate();
	}

	/**
	 * Test Invalid Auth Flags
	 */
	public function testInvalidAuthFlagsException(): void
	{
		$this->expectException('\Exception');

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName',
			'{FileID}',
			'{OwnerID}',
			PackageInfo::QbType['QBFS'],
			false,
			'15 minutes',
			QWC::PERSONALDATA_NOTNEEDED,
			QWC::UNATTENDEDMODE_DEFAULT,
			'Invalid'
		);
		$qwc->generate();
	}

	/**
	 * Test run every that is less than a minute
	 */
	public function testRunEveryUnderOneMinute(): void
	{
		$FileID = QWC::fileID();
		$OwnerID = QWC::ownerID();
		$AppID = QWC::GUID();

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName',
			$FileID,
			$OwnerID,
			PackageInfo::QbType['QBFS'],
			false,
			'47 seconds',
		);

		$xml = $qwc->generate();
		$xml = $this->sanitizeXml($xml);

		$expected = implode("\n", [
			'<QBWCXML>',
			'    <AppName>My WebConnector</AppName>',
			'    <AppID></AppID>',
			'    <AppURL>https://example.com/MyWebConnectorUrl</AppURL>',
			'    <AppDescription>Short Descript of What It Does (e.g. Import our Invoices from website)</AppDescription>',
			'    <AppSupport>https://example.com/Support-Info-About-Url</AppSupport>',
			'    <UserName>WebConnectorUserName</UserName>',
			'    <OwnerID>' . $OwnerID . '</OwnerID>',
			'    <FileID>' . $FileID . '</FileID>',
			'    <QBType>QBFS</QBType>',
			'    <Notify>false</Notify>',
			'    <Scheduler>',
			'      <RunEveryNSeconds>47</RunEveryNSeconds>',
			'    </Scheduler>',
			'    ',
			'    ',
			'    <IsReadOnly>false</IsReadOnly>',
			'</QBWCXML>',
		]);

	   $this->commonTests($expected, $xml->saveXML());
	}

	/**
	 * Test Default Values
	 */
	public function testAllValues(): void
	{
		$FileID = QWC::fileID();
		$OwnerID = QWC::ownerID();
		$AppID = QWC::GUID();

		$qwc = new QWC(
			'My WebConnector',
			'Short Descript of What It Does (e.g. Import our Invoices from website)',
			'https://example.com/MyWebConnectorUrl',
			'https://example.com/Support-Info-About-Url',
			'WebConnectorUserName',
			$FileID,
			$OwnerID,
			PackageInfo::QbType['QBPOS'],
			false,
			'15 minutes',
			QWC::PERSONALDATA_OPTIONAL,
			QWC::UNATTENDEDMODE_OPTIONAL,
			QWC::SUPPORTED_ALL,
			true,
			'My App Display Name',
			'MyAppUniqueName0123456789',
			$AppID
		);

		$xml = $qwc->generate();
		$xml = $this->sanitizeXml($xml);

		$expected = implode("\n", [
			'<QBWCXML>',
			'    <AppName>My WebConnector</AppName>',
			'    <AppID>' . $AppID . '</AppID>',
			'    <AppURL>https://example.com/MyWebConnectorUrl</AppURL>',
			'    <AppDescription>Short Descript of What It Does (e.g. Import our Invoices from website)</AppDescription>',
			'    <AppSupport>https://example.com/Support-Info-About-Url</AppSupport>',
			'    <UserName>WebConnectorUserName</UserName>',
			'    <OwnerID>' . $OwnerID . '</OwnerID>',
			'    <FileID>' . $FileID . '</FileID>',
			'    <QBType>QBPOS</QBType>',
			'    <PersonalDataPref>pdpOptional</PersonalDataPref>',
			'    <UnattendedModePref>umpOptional</UnattendedModePref>',
			'    <AuthFlags>0x0</AuthFlags>',
			'    <Notify>true</Notify>',
			'    <AppDisplayName>My App Display Name</AppDisplayName>',
			'    <AppUniqueName>MyAppUniqueName0123456789</AppUniqueName>',
			'    <Scheduler>',
			'      <RunEveryNMinutes>15</RunEveryNMinutes>',
			'    </Scheduler>',
			'    ',
			'    ',
			'    <IsReadOnly>false</IsReadOnly>',
			'</QBWCXML>',
		]);

	   $this->commonTests($expected, $xml->saveXML());
	}
}
