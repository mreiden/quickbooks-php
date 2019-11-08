<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\Vendor;

final class VendorTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::Vendor('Vendor1');
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test VendorAddRq
     */
    public function testVendorAdd(): void
    {
        $expected = implode("\n", [
            '<VendorAddRq>',
            '  <VendorAdd>',
            '    <Name>Vendor1</Name>',
            '    <IsActive>true</IsActive>',
            '    <CompanyName>Vendor Company Name</CompanyName>',
            '    <FirstName>John</FirstName>',
            '    <LastName>Doe</LastName>',
            '    <VendorAddress>',
            '      <Addr1>Vendor Company Name</Addr1>',
            '      <Addr2>attn: John Doe</Addr2>',
            '      <Addr3>123 Vendor Street</Addr3>',
            '      <Addr4>STE 5555</Addr4>',
            '      <City>City</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </VendorAddress>',
            '    <Phone>(555) 555-1234</Phone>',
            '    <AltPhone>(555) 555-4321</AltPhone>',
            '    <Fax>(555) 555-2222</Fax>',
            '    <Email>vendor@example.com</Email>',
            '    <VendorTypeRef>',
            '      <FullName>1099 Contractor</FullName>',
            '    </VendorTypeRef>',
            '  </VendorAdd>',
            '</VendorAddRq>',
        ]);


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_VENDOR']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test VendorModRq
     */
    public function testVendorMod(): void
    {
        $expected = implode("\n", [
            '<VendorModRq>',
            '  <VendorMod>',
            '    <ListID>70000007-1234567890</ListID>',
            '    <EditSequence>9876543210</EditSequence>',
            '    <Name>Vendor1</Name>',
            '    <IsActive>true</IsActive>',
            '    <CompanyName>Vendor Company Name</CompanyName>',
            '    <FirstName>John</FirstName>',
            '    <LastName>Doe</LastName>',
            '    <VendorAddress>',
            '      <Addr1>Vendor Company Name</Addr1>',
            '      <Addr2>attn: John Doe</Addr2>',
            '      <Addr3>123 Vendor Street</Addr3>',
            '      <Addr4>STE 5555</Addr4>',
            '      <City>City</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </VendorAddress>',
            '    <Phone>(555) 555-1234</Phone>',
            '    <AltPhone>(555) 555-4321</AltPhone>',
            '    <Fax>(555) 555-2222</Fax>',
            '    <Email>vendor@example.com</Email>',
            '    <VendorTypeRef>',
            '      <FullName>1099 Contractor</FullName>',
            '    </VendorTypeRef>',
            '  </VendorMod>',
            '</VendorModRq>',
        ]);


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['MOD_VENDOR']);

        $this->commonTests($expected, $qbXml);
    }
}
