<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\CreditMemo;
use QuickBooksPhpDevKit\QBXML\Object\CreditMemo\CreditMemoLine;

final class CreditMemoTest extends XmlBaseTest
{
    protected $obj;

    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::CreditMemo(10001, '2019-07-04', 'My Customer', 'PO12345', 'SRep1', [
            'ClassName' => 'QuickBooksClass',
        ]);
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test CreditMemoAddRq
     */
    public function testCreditMemoAdd(): void
    {
        $expected = implode("\n", [
            '<CreditMemoAddRq>',
            '  <CreditMemoAdd>',
            '    <CustomerRef>',
            '      <FullName>My Customer</FullName>',
            '    </CustomerRef>',
            '    <ClassRef>',
            '      <FullName>QuickBooksClass</FullName>',
            '    </ClassRef>',
            '    <TxnDate>2019-07-04</TxnDate>',
            '    <RefNumber>10001</RefNumber>',
            '    <BillAddress>',
            '      <Addr1>My Customer</Addr1>',
            '      <Addr2>attn: John Doe</Addr2>',
            '      <Addr3>123 Billing Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>BillCity</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </BillAddress>',
            '    <ShipAddress>',
            '      <Addr1>My Customer</Addr1>',
            '      <Addr2>attn: John Doe</Addr2>',
            '      <Addr3>123 Ship Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>ShipCity</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </ShipAddress>',
            '    <PONumber>PO12345</PONumber>',
            '    <DueDate>2019-08-03</DueDate>',
            '    <SalesRepRef>',
            '      <FullName>SRep1</FullName>',
            '    </SalesRepRef>',
            '    <IsToBePrinted>false</IsToBePrinted>',
            '    <IsToBeEmailed>true</IsToBeEmailed>',
            '    <CustomerSalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </CustomerSalesTaxCodeRef>',
            '    <CreditMemoLineAdd>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem1</FullName>',
            '      </ItemRef>',
            '      <Desc>First ServiceItem</Desc>',
            '      <Quantity>99</Quantity>',
            '      <Rate>1.15</Rate>',
            '    </CreditMemoLineAdd>',
            '    <CreditMemoLineAdd>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem2</FullName>',
            '      </ItemRef>',
            '      <Desc>Second ServiceItem</Desc>',
            '      <Amount>15.67</Amount>',
            '    </CreditMemoLineAdd>',
            '  </CreditMemoAdd>',
            '</CreditMemoAddRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_CREDITMEMO']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test CreditMemoModRq
     */
    public function testCreditMemoMod(): void
    {
        $expected = implode("\n", [
            '<CreditMemoModRq>',
            '  <CreditMemoMod>',
            '    <TxnID>7777-1234567890</TxnID>',
            '    <EditSequence>9876543210</EditSequence>',
            '    <CustomerRef>',
            '      <FullName>My Customer</FullName>',
            '    </CustomerRef>',
            '    <ClassRef>',
            '      <FullName>QuickBooksClass</FullName>',
            '    </ClassRef>',
            '    <TxnDate>2019-07-04</TxnDate>',
            '    <RefNumber>10001</RefNumber>',
            '    <BillAddress>',
            '      <Addr1>My Customer</Addr1>',
            '      <Addr2>attn: John Doe</Addr2>',
            '      <Addr3>123 Billing Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>BillCity</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </BillAddress>',
            '    <ShipAddress>',
            '      <Addr1>My Customer</Addr1>',
            '      <Addr2>attn: John Doe</Addr2>',
            '      <Addr3>123 Ship Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>ShipCity</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </ShipAddress>',
            '    <PONumber>PO12345</PONumber>',
            '    <DueDate>2019-08-03</DueDate>',
            '    <SalesRepRef>',
            '      <FullName>SRep1</FullName>',
            '    </SalesRepRef>',
            '    <IsToBePrinted>false</IsToBePrinted>',
            '    <IsToBeEmailed>true</IsToBeEmailed>',
            '    <CustomerSalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </CustomerSalesTaxCodeRef>',
            '    <CreditMemoLineMod>',
            '      <TxnLineID>-1</TxnLineID>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem1</FullName>',
            '      </ItemRef>',
            '      <Desc>First ServiceItem</Desc>',
            '      <Quantity>99</Quantity>',
            '      <Rate>1.15</Rate>',
            '    </CreditMemoLineMod>',
            '    <CreditMemoLineMod>',
            '      <TxnLineID>-1</TxnLineID>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem2</FullName>',
            '      </ItemRef>',
            '      <Desc>Second ServiceItem</Desc>',
            '      <Amount>15.67</Amount>',
            '    </CreditMemoLineMod>',
            '  </CreditMemoMod>',
            '</CreditMemoModRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['MOD_CREDITMEMO']);

        $this->commonTests($expected, $qbXml);
    }
}
