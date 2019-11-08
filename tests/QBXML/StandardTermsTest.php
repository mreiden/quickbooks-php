<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\StandardTerms;

final class StandardTermsTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::StandardTerms('11% 12 Net 31', 31, 12, 4.44);
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test StandardTermsAddRq
     */
    public function testStandardTermsAdd(): void
    {
        $expected = implode("\n", [
            '<StandardTermsAddRq>',
            '  <StandardTermsAdd>',
            '    <Name>11% 12 Net 31</Name>',
            '    <IsActive>true</IsActive>',
            '    <StdDueDays>31</StdDueDays>',
            '    <StdDiscountDays>12</StdDiscountDays>',
            '    <DiscountPct>4.44</DiscountPct>',
            '  </StandardTermsAdd>',
            '</StandardTermsAddRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_STANDARDTERMS']);

        $this->commonTests($expected, $qbXml);
    }
}
