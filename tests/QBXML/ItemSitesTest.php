<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\ItemSites;

final class ItemSitesTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
        $ListIDs = [
            '70000100-1111111111',
            '70000100-1111111112',
            '70000100-1111111113',
        ];
        $this->obj = QbxmlTestdataGenerator::ItemSites($ListIDs);
        //fwrite(STDERR, "ItemSites Object: \n". print_r($this->obj,true) ."\n");
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test ItemSitesQueryRq
     */
    public function testItemSitesQuery(): void
    {
        $expected = implode("\n", [
            '<ItemSitesQueryRq>',
            '  <ListID>70000100-1111111111</ListID>',
            '  <ListID>70000100-1111111112</ListID>',
            '  <ListID>70000100-1111111113</ListID>',
            '</ItemSitesQueryRq>',
        ]);


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['QUERY_ITEMSITES']);

        $this->commonTests($expected, $qbXml);
    }
}
