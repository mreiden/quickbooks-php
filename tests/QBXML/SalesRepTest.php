<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\SalesRep;

final class SalesRepTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::SalesRep('SR', 'Sales Rep FullName', true, null);
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test ClassAddRq
     */
    public function testQbclassAdd(): void
    {
        $expected = implode("\n", [
            '<SalesRepAddRq>',
            '  <SalesRepAdd>',
            '    <Initial>SR</Initial>',
            '    <IsActive>true</IsActive>',
            '    <SalesRepEntityRef>',
            '      <FullName>Sales Rep FullName</FullName>',
            '    </SalesRepEntityRef>',
            '  </SalesRepAdd>',
            '</SalesRepAddRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_SALESREP']);

        $this->commonTests($expected, $qbXml);
    }
}
