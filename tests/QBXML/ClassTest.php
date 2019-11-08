<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\Qbclass;

final class ClassTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::Qbclass('QuickBooksClass', true);
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
            '<ClassAddRq>',
            '  <ClassAdd>',
            '    <Name>QuickBooksClass</Name>',
            '    <IsActive>true</IsActive>',
            '  </ClassAdd>',
            '</ClassAddRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_CLASS']);

        $this->commonTests($expected, $qbXml);
    }
}
