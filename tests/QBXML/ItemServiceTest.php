<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\ItemService;

final class ItemServiceTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::ItemService('Items:Item1', 'Item Income:Item1');
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test ItemServiceAddRq
     */
    public function testItemServiceAdd(): void
    {
        $expected = implode("\n", [
            '<ItemServiceAddRq>',
            '  <ItemServiceAdd>',
            '    <Name>Item1</Name>',
            '    <IsActive>true</IsActive>',
            '    <ParentRef>',
            '      <FullName>Items</FullName>',
            '    </ParentRef>',
            '    <SalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </SalesTaxCodeRef>',
            '    <SalesOrPurchase>',
            '      <Desc>Items:Item1 for 51.51</Desc>',
            '      <Price>51.51</Price>',
            '      <AccountRef>',
            '        <FullName>Item Income:Item1</FullName>',
            '      </AccountRef>',
            '    </SalesOrPurchase>',
            '  </ItemServiceAdd>',
            '</ItemServiceAddRq>',
        ]);


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_SERVICEITEM']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test ItemServiceModRq
     */
    public function testItemServiceMod(): void
    {
        $expected = implode("\n", [
            '<ItemServiceModRq>',
            '  <ItemServiceMod>',
            '    <ListID>70000010-1234567890</ListID>',
            '    <EditSequence>9876543210</EditSequence>',
            '    <Name>Item1</Name>',
            '    <IsActive>true</IsActive>',
            '    <ParentRef>',
            '      <FullName>Items</FullName>',
            '    </ParentRef>',
            '    <SalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </SalesTaxCodeRef>',
            '    <SalesOrPurchaseMod>',
            '      <Desc>Items:Item1 for 51.51</Desc>',
            '      <Price>51.51</Price>',
            '      <AccountRef>',
            '        <FullName>Item Income:Item1</FullName>',
            '      </AccountRef>',
            '    </SalesOrPurchaseMod>',
            '  </ItemServiceMod>',
            '</ItemServiceModRq>',
        ]);


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['MOD_SERVICEITEM']);

        $this->commonTests($expected, $qbXml);
    }
}
