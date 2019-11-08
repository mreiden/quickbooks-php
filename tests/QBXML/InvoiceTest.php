<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\Invoice;
use QuickBooksPhpDevKit\QBXML\Object\Invoice\InvoiceLine;

final class InvoiceTest extends XmlBaseTest
{
    protected $obj;

    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::Invoice(10001, '2019-07-04', 'My Customer', 'PO12345', 'SRep2', [
            'ClassName' => 'QuickBooksClass',
        ]);
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test InvoiceAddRq
     */
    public function testInvoiceAdd(): void
    {
        $expected = implode("\n", [
            '<InvoiceAddRq>',
            '  <InvoiceAdd>',
            '    <CustomerRef>',
            '      <FullName>My Customer</FullName>',
            '    </CustomerRef>',
            '    <ClassRef>',
            '      <FullName>QuickBooksClass</FullName>',
            '    </ClassRef>',
            '    <TemplateRef>',
            '      <FullName>Intuit Service Invoice</FullName>',
            '    </TemplateRef>',
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
            '    <TermsRef>',
            '      <FullName>11% 12 Net 31</FullName>',
            '    </TermsRef>',
            '    <DueDate>2019-08-03</DueDate>',
            '    <SalesRepRef>',
            '      <FullName>SRep2</FullName>',
            '    </SalesRepRef>',
            '    <IsToBePrinted>false</IsToBePrinted>',
            '    <IsToBeEmailed>true</IsToBeEmailed>',
            '    <CustomerSalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </CustomerSalesTaxCodeRef>',
            '    <InvoiceLineAdd>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem1</FullName>',
            '      </ItemRef>',
            '      <Desc>First ServiceItem</Desc>',
            '      <Quantity>111.00</Quantity>',
            '      <Rate>1.15</Rate>',
            '    </InvoiceLineAdd>',
            '    <InvoiceLineAdd>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem2</FullName>',
            '      </ItemRef>',
            '      <Desc>Second ServiceItem</Desc>',
            '      <Amount>45.67</Amount>',
            '    </InvoiceLineAdd>',
            '  </InvoiceAdd>',
            '</InvoiceAddRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_INVOICE']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test InvoiceModRq
     */
    public function testInvoiceMod(): void
    {
        $expected = implode("\n", [
            '<InvoiceModRq>',
            '  <InvoiceMod>',
            '    <TxnID>7623-1234567890</TxnID>',
            '    <EditSequence>9876543210</EditSequence>',
            '    <CustomerRef>',
            '      <FullName>My Customer</FullName>',
            '    </CustomerRef>',
            '    <ClassRef>',
            '      <FullName>QuickBooksClass</FullName>',
            '    </ClassRef>',
            '    <TemplateRef>',
            '      <FullName>Intuit Service Invoice</FullName>',
            '    </TemplateRef>',
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
            '    <TermsRef>',
            '      <FullName>11% 12 Net 31</FullName>',
            '    </TermsRef>',
            '    <DueDate>2019-08-03</DueDate>',
            '    <SalesRepRef>',
            '      <FullName>SRep2</FullName>',
            '    </SalesRepRef>',
            '    <IsToBePrinted>false</IsToBePrinted>',
            '    <IsToBeEmailed>true</IsToBeEmailed>',
            '    <CustomerSalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </CustomerSalesTaxCodeRef>',
            '    <InvoiceLineMod>',
            '      <TxnLineID>-1</TxnLineID>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem1</FullName>',
            '      </ItemRef>',
            '      <Desc>First ServiceItem</Desc>',
            '      <Quantity>111.00</Quantity>',
            '      <Rate>1.15</Rate>',
            '    </InvoiceLineMod>',
            '    <InvoiceLineMod>',
            '      <TxnLineID>-1</TxnLineID>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem2</FullName>',
            '      </ItemRef>',
            '      <Desc>Second ServiceItem</Desc>',
            '      <Amount>45.67</Amount>',
            '    </InvoiceLineMod>',
            '  </InvoiceMod>',
            '</InvoiceModRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['MOD_INVOICE']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test Integer and String RefNumbers
     */
    public function testRefNumDataTypes(): void
    {
        $RefNumber = 12345;
        $this->obj->setRefNumber($RefNumber);
        $intResult = $this->obj->getRefNumber();
        $this->obj->setRefNumber("$RefNumber");
        $stringResult = $this->obj->getRefNumber();
        $this->assertEquals($intResult, $stringResult);
    }

    /**
     * Test Integer and String PO Numbers
     */
    public function testPoNumberDataTypes(): void
    {
        $RefNumber = 54321;
        $this->obj->setPONumber($RefNumber);
        $intResult = $this->obj->getRefNumber();
        $this->obj->setPONumber("$RefNumber");
        $stringResult = $this->obj->getRefNumber();
        $this->assertEquals($intResult, $stringResult);
    }

    /**
     * Test Maximum Field Length
     */
    public function testMaximumFieldLength(): void
    {
        $value = 'Too Long Sales Rep Name';
        $this->obj->setSalesRepName($value);
        $this->assertSame('Too L', $this->obj->getSalesRepName());
    }

    /**
     * Test QBXML version restrictions
     */
    public function testSinceVersion(): void
    {
        $template = implode("\n", [
            '<InvoiceAddRq>',
            '  <InvoiceAdd>',
            '    <CustomerRef>',
            '      <FullName>My Customer</FullName>',
            '    </CustomerRef>',
            '    <ClassRef>',
            '      <FullName>QuickBooksClass</FullName>',
            '    </ClassRef>',
            '    <TemplateRef>',
            '      <FullName>Intuit Service Invoice</FullName>',
            '    </TemplateRef>',
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
            '    <TermsRef>',
            '      <FullName>11% 12 Net 31</FullName>',
            '    </TermsRef>',
            '    <DueDate>2019-08-03</DueDate>',
            '    <SalesRepRef>',
            '      <FullName>SRep2</FullName>',
            '    </SalesRepRef>',
            '    <IsToBePrinted>false</IsToBePrinted>{{*** EMAIL ***}}',
            '    <CustomerSalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </CustomerSalesTaxCodeRef>{{*** OTHER ***}}',
            '    <InvoiceLineAdd>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem1</FullName>',
            '      </ItemRef>',
            '      <Desc>First ServiceItem</Desc>',
            '      <Quantity>111.00</Quantity>',
            '      <Rate>1.15</Rate>',
            '    </InvoiceLineAdd>',
            '    <InvoiceLineAdd>',
            '      <ItemRef>',
            '        <FullName>Items:ServiceItem2</FullName>',
            '      </ItemRef>',
            '      <Desc>Second ServiceItem</Desc>',
            '      <Amount>45.67</Amount>',
            '    </InvoiceLineAdd>',
            '  </InvoiceAdd>',
            '</InvoiceAddRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");

        $value = 'Other is Available in v6.0+';
        $this->obj->setOther($value);

        $vars = [
            '{{*** OTHER ***}}',
            '{{*** EMAIL ***}}',
        ];
        $vals = [
            'NONE' => [
                "\n    <Other>Other is Available in v6.0+</Other>",
                "\n    <IsToBeEmailed>true</IsToBeEmailed>",
            ],
            'V5' => [
                '',
                '',
            ],
        ];

        $expected = str_replace($vars, $vals['NONE'], $template);
        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_INVOICE']);
        $this->commonTests($expected, $qbXml);

        $expected = str_replace($vars, $vals['V5'], $template);
        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_INVOICE'], 5.0);
        $this->commonTests($expected, $qbXml);
    }
}
