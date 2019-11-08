<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\AbstractQbxmlObject;
use QuickBooksPhpDevKit\QBXML\Object\CreditMemo;
use QuickBooksPhpDevKit\QBXML\Object\Customer;
use QuickBooksPhpDevKit\qbXml\Object\ItemSalesTaxGroup;
use QuickBooksPhpDevKit\qbXml\Object\ItemService;
use QuickBooksPhpDevKit\qbXml\Object\Qbclass;
use QuickBooksPhpDevKit\QBXML\Object\SalesReceipt;
use QuickBooksPhpDevKit\QBXML\Object\Vendor;



final class ObjectFromQbxmlTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }


    /**
     * Test Creating a QBXML\Object\CreditMemo from a CreditMemoRet xml node in a CreditMemoQuery
     */
    public function testInvalidQbxml(): void
    {
        $invalid = [
            '',
            '<CreditMemoRet>',
            '<CreditMemoRet><CreditMemoRet>',
            '<CreditMemoRet></CreditMemoRe>',
        ];
        for ($i = 0; $i < count($invalid); $i++) {
            $this->assertNull(AbstractQbxmlObject::fromQBXML($invalid[0], PackageInfo::Actions['QUERY_CREDITMEMO']));
        }
    }



    /**
     * Test Creating a QBXML\Object\Class from a ClassRet xml node in a ClassAddRs response
     */
    public function testFromQbxmlClassRet(): void
    {
        $QBXML = implode("\n", [
            '<ClassRet>',
            '  <ListID>80000003-1564419254</ListID>',
            '  <TimeCreated>2019-07-29T11:54:14-06:00</TimeCreated>',
            '  <TimeModified>2019-07-29T11:54:14-06:00</TimeModified>',
            '  <EditSequence>1564419254</EditSequence>',
            '  <Name>QuickBooksClass</Name>',
            '  <FullName>QuickBooksClass</FullName>',
            '  <IsActive>true</IsActive>',
            '  <Sublevel>0</Sublevel>',
            '</ClassRet>',
        ]);

        // Convert the query response xml ClassRet into a Qbclass QBXML Object
        $objQBXML = AbstractQbxmlObject::fromQBXML($QBXML, PackageInfo::Actions['QUERY_CLASS']);
        $this->assertInstanceOf(Qbclass::class, $objQBXML);


        // Test that the object creates the expected QBXML
        $expected = implode("\n", [
            '<ClassAddRq>',
            '  <ClassAdd>',
            '    <Name>QuickBooksClass</Name>',
            '    <IsActive>true</IsActive>',
            '  </ClassAdd>',
            '</ClassAddRq>',
        ]);


        $qbXml = $objQBXML->asQBXML(PackageInfo::Actions['ADD_CLASS']);

        $this->commonTests($expected, $qbXml);
    }


    /**
     * Test Creating a QBXML\Object\CreditMemo from a CreditMemoRet xml node in a CreditMemoQueryRs response
     */
    public function testFromQbxmlCreditMemoRet(): void
    {
        $QBXML = implode("\n", [
            '<CreditMemoRet>',
            '  <TxnID>3FE6-1285088622</TxnID>',
            '  <TimeCreated>2011-11-11T11:11:11-07:00</TimeCreated>',
            '  <TimeModified>2011-11-11T11:11:11-07:00</TimeModified>',
            '  <EditSequence>1350060801</EditSequence>',
            '  <TxnNumber>5000</TxnNumber>',
            '  <CustomerRef>',
            '    <ListID>2E0000-1190037168</ListID>',
            '    <FullName>Company Name</FullName>',
            '  </CustomerRef>',
            '  <ARAccountRef>',
            '    <ListID>620000-1131452610</ListID>',
            '    <FullName>Accounts Receivable</FullName>',
            '  </ARAccountRef>',
            '  <TemplateRef>',
            '    <ListID>170000-1079026634</ListID>',
            '    <FullName>Custom Credit Memo</FullName>',
            '  </TemplateRef>',
            '  <TxnDate>2011-11-11</TxnDate>',
            '  <RefNumber>CM-5555</RefNumber>',
            '  <BillAddress>',
            '    <Addr1>Company Name</Addr1>',
            '    <Addr2>attn: Person Name</Addr2>',
            '    <Addr3>123 Main Street</Addr3>',
            '    <City>New York</City>',
            '    <State>NY</State>',
            '    <PostalCode>10001</PostalCode>',
            '  </BillAddress>',
            '  <BillAddressBlock>',
            '    <Addr1>Company Name</Addr1>',
            '    <Addr2>attn: Person Name</Addr2>',
            '    <Addr3>123 Main Street</Addr3>',
            '    <Addr4>New York, NY 10001</Addr4>',
            '  </BillAddressBlock>',
            '  <IsPending>false</IsPending>',
            '  <DueDate>2011-11-11</DueDate>',
            '  <ShipDate>2011-11-11</ShipDate>',
            '  <Subtotal>38.95</Subtotal>',
            '  <SalesTaxPercentage>0.00</SalesTaxPercentage>',
            '  <SalesTaxTotal>0.00</SalesTaxTotal>',
            '  <TotalAmount>38.95</TotalAmount>',
            '  <CreditRemaining>0.00</CreditRemaining>',
            '  <Memo>Refund 1 Product from invoice 1000</Memo>',
            '  <IsToBePrinted>false</IsToBePrinted>',
            '  <IsToBeEmailed>false</IsToBeEmailed>',
            '  <LinkedTxn>',
            '    <TxnID>3FE9-1285088647</TxnID>',
            '    <TxnType>Check</TxnType>',
            '    <TxnDate>2011-11-11</TxnDate>',
            '    <RefNumber>20232</RefNumber>',
            '    <LinkType>AMTTYPE</LinkType>',
            '    <Amount>-38.95</Amount>',
            '  </LinkedTxn>',
            '  <CreditMemoLineRet>',
            '    <TxnLineID>3FE8-1285088622</TxnLineID>',
            '    <ItemRef>',
            '      <ListID>8000000A-1266264548</ListID>',
            '      <FullName>Company Product 1</FullName>',
            '    </ItemRef>',
            '    <Quantity>1</Quantity>',
            '    <Rate>38.95</Rate>',
            '    <Amount>38.95</Amount>',
            '    <SalesTaxCodeRef>',
            '      <ListID>20000-1069184259</ListID>',
            '      <FullName>Non</FullName>',
            '    </SalesTaxCodeRef>',
            '  </CreditMemoLineRet>',
            '</CreditMemoRet>',
        ]);

        // Convert the query response xml CreditMemoRet into a CreditMemo QBXML Object
        $objQBXML = AbstractQbxmlObject::fromQBXML($QBXML, PackageInfo::Actions['QUERY_CREDITMEMO']);
        $this->assertInstanceOf(CreditMemo::class, $objQBXML);


        // Test that the object creates the expected QBXML
        $expected = implode("\n", [
            '<CreditMemoAddRq>',
            '  <CreditMemoAdd>',
            '    <CustomerRef>',
            '      <ListID>2E0000-1190037168</ListID>',
            '      <FullName>Company Name</FullName>',
            '    </CustomerRef>',
            '    <ARAccountRef>',
            '      <ListID>620000-1131452610</ListID>',
            '      <FullName>Accounts Receivable</FullName>',
            '    </ARAccountRef>',
            '    <TemplateRef>',
            '      <ListID>170000-1079026634</ListID>',
            '      <FullName>Custom Credit Memo</FullName>',
            '    </TemplateRef>',
            '    <TxnDate>2011-11-11</TxnDate>',
            '    <RefNumber>CM-5555</RefNumber>',
            '    <BillAddress>',
            '      <Addr1>Company Name</Addr1>',
            '      <Addr2>attn: Person Name</Addr2>',
            '      <Addr3>123 Main Street</Addr3>',
            '      <City>New York</City>',
            '      <State>NY</State>',
            '      <PostalCode>10001</PostalCode>',
            '    </BillAddress>',
            '    <IsPending>false</IsPending>',
            '    <DueDate>2011-11-11</DueDate>',
            '    <ShipDate>2011-11-11</ShipDate>',
            '    <Memo>Refund 1 Product from invoice 1000</Memo>',
            '    <IsToBePrinted>false</IsToBePrinted>',
            '    <IsToBeEmailed>false</IsToBeEmailed>',
            '    <CreditMemoLineAdd>',
            '      <ItemRef>',
            '        <ListID>8000000A-1266264548</ListID>',
            '        <FullName>Company Product 1</FullName>',
            '      </ItemRef>',
            '      <Quantity>1</Quantity>',
            '      <Rate>38.95</Rate>',
            '      <Amount>38.95</Amount>',
            '      <SalesTaxCodeRef>',
            '        <ListID>20000-1069184259</ListID>',
            '        <FullName>Non</FullName>',
            '      </SalesTaxCodeRef>',
            '    </CreditMemoLineAdd>',
            '  </CreditMemoAdd>',
            '</CreditMemoAddRq>',
        ]);


        $qbXml = $objQBXML->asQBXML(PackageInfo::Actions['ADD_CREDITMEMO']);

        $this->commonTests($expected, $qbXml);
    }


    /**
     * Test Creating a QBXML\Object\Customer from a CustomerRet xml node in a CustomerQueryRs response
     */
    public function testFromQbxmlCustomerRet(): void
    {
        $QBXML = implode("\n", [
            '<CustomerRet>',
            '  <ListID>10006-1211236622</ListID>',
            '  <TimeCreated>2008-05-19T18:37:02-05:00</TimeCreated>',
            '  <TimeModified>2008-06-10T23:35:56-05:00</TimeModified>',
            '  <EditSequence>1213155356</EditSequence>',
            '  <Name>Keith Palmer</Name>',
            '  <FullName>Keith Palmer</FullName>',
            '  <IsActive>true</IsActive>',
            '  <Sublevel>0</Sublevel>',
            '  <FirstName>Keith</FirstName>',
            '  <LastName>Palmer</LastName>',
            '  <BillAddress>',
            '    <Addr1>134 Stonemill Road</Addr1>',
            '    <Addr2>Suite D</Addr2>',
            '    <City>Storrs</City>',
            '    <State>CT</State>',
            '    <PostalCode>06268</PostalCode>',
            '    <Country>USA</Country>',
            '  </BillAddress>',
            '  <ShipAddress>',
            '    <Addr1>134 Stonemill Road</Addr1>',
            '    <Addr2>Suite D</Addr2>',
            '    <City>Storrs</City>',
            '    <State>CT</State>',
            '    <PostalCode>06268</PostalCode>',
            '    <Country>USA</Country>',
            '  </ShipAddress>',
            '  <Phone>(860) 634-1602</Phone>',
            '  <Fax>(860) 429-5182</Fax>',
            '  <Email>keith@uglyslug.com</Email>',
            '  <Balance>250.00</Balance>',
            '  <TotalBalance>250.00</TotalBalance>',
            '  <SalesTaxCodeRef>',
            '    <ListID>20000-1211065841</ListID>',
            '    <FullName>Non</FullName>',
            '  </SalesTaxCodeRef>',
            '  <ItemSalesTaxRef>',
            '    <ListID>10000-1211066051</ListID>',
            '    <FullName>Out of State</FullName>',
            '  </ItemSalesTaxRef>',
            '  <JobStatus>None</JobStatus>',
            '</CustomerRet>',
        ]);

        // Convert the query response xml CustomerRet into a Customer QBXML Object
        $objQBXML = AbstractQbxmlObject::fromQBXML($QBXML, PackageInfo::Actions['QUERY_CUSTOMER']);
        $this->assertInstanceOf(Customer::class, $objQBXML);


        // Test that the object creates the expected QBXML
        $expected = implode("\n", [
            '<CustomerAddRq>',
            '  <CustomerAdd>',
            '    <Name>Keith Palmer</Name>',
            '    <IsActive>true</IsActive>',
            '    <FirstName>Keith</FirstName>',
            '    <LastName>Palmer</LastName>',
            '    <BillAddress>',
            '      <Addr1>134 Stonemill Road</Addr1>',
            '      <Addr2>Suite D</Addr2>',
            '      <City>Storrs</City>',
            '      <State>CT</State>',
            '      <PostalCode>06268</PostalCode>',
            '      <Country>USA</Country>',
            '    </BillAddress>',
            '    <ShipAddress>',
            '      <Addr1>134 Stonemill Road</Addr1>',
            '      <Addr2>Suite D</Addr2>',
            '      <City>Storrs</City>',
            '      <State>CT</State>',
            '      <PostalCode>06268</PostalCode>',
            '      <Country>USA</Country>',
            '    </ShipAddress>',
            '    <Phone>(860) 634-1602</Phone>',
            '    <Fax>(860) 429-5182</Fax>',
            '    <Email>keith@uglyslug.com</Email>',
            '    <SalesTaxCodeRef>',
            '      <ListID>20000-1211065841</ListID>',
            '      <FullName>Non</FullName>',
            '    </SalesTaxCodeRef>',
            '    <ItemSalesTaxRef>',
            '      <ListID>10000-1211066051</ListID>',
            '      <FullName>Out of State</FullName>',
            '    </ItemSalesTaxRef>',
            '    <JobStatus>None</JobStatus>',
            '  </CustomerAdd>',
            '</CustomerAddRq>',
        ]);


        $qbXml = $objQBXML->asQBXML(PackageInfo::Actions['ADD_CUSTOMER']);

        $this->commonTests($expected, $qbXml);
    }


    /**
     * Test Creating a QBXML\Object\Customer from a CustomerAdd qbxml
     */
    public function testFromCustomerAdd(): void
    {
        $QBXML = implode("\n", [
            '<CustomerAdd>',
            '  <Name>20706 - Eastern XYZ University</Name>',
            '  <CompanyName>Eastern XYZ University</CompanyName>',
            '  <FirstName>Keith</FirstName>',
            '  <LastName>Palmer</LastName>',
            '  <BillAddress>',
            '    <Addr1>Eastern XYZ University</Addr1>',
            '    <Addr2>College of Engineering</Addr2>',
            '    <Addr3>123 XYZ Road</Addr3>',
            '    <City>Storrs-Mansfield</City>',
            '    <State>CT</State>',
            '    <PostalCode>06268</PostalCode>',
            '    <Country>United States</Country>',
            '  </BillAddress>',
            '  <Phone>860-634-1602</Phone>',
            '  <AltPhone>860-429-0021</AltPhone>',
            '  <Fax>860-429-5183</Fax>',
            '  <Email>keith@consolibyte.com</Email>',
            '  <Contact>Keith Palmer</Contact>',
            '</CustomerAdd>',
        ]);

        // Convert the xml CustomerAdd into a Customer QBXML Object
        $objQBXML = AbstractQbxmlObject::fromQBXML($QBXML, PackageInfo::Actions['ADD_CUSTOMER']);
        $this->assertInstanceOf(Customer::class, $objQBXML);


        // Test that the object creates the expected QBXML
        $expected = implode("\n", [
            '<CustomerAddRq>',
            '  <CustomerAdd>',
            '    <Name>20706 - Eastern XYZ University</Name>',
            '    <CompanyName>Eastern XYZ University</CompanyName>',
            '    <FirstName>Keith</FirstName>',
            '    <LastName>Palmer</LastName>',
            '    <BillAddress>',
            '      <Addr1>Eastern XYZ University</Addr1>',
            '      <Addr2>College of Engineering</Addr2>',
            '      <Addr3>123 XYZ Road</Addr3>',
            '      <City>Storrs-Mansfield</City>',
            '      <State>CT</State>',
            '      <PostalCode>06268</PostalCode>',
            '      <Country>United States</Country>',
            '    </BillAddress>',
            '    <Phone>860-634-1602</Phone>',
            '    <AltPhone>860-429-0021</AltPhone>',
            '    <Fax>860-429-5183</Fax>',
            '    <Email>keith@consolibyte.com</Email>',
            '    <Contact>Keith Palmer</Contact>',
            '  </CustomerAdd>',
            '</CustomerAddRq>',
        ]);


        $qbXml = $objQBXML->asQBXML(PackageInfo::Actions['ADD_CUSTOMER']);

        $this->commonTests($expected, $qbXml);
    }


    /**
     * Test Creating a QBXML\Object\ItemSalesTaxGroup from a ItemSalesTaxGroupRet xml node in a ItemSalesTaxGroupQueryRs response
     */
    public function testFromQbxmlItemSalesTaxGroupRet(): void
    {
        $QBXML = implode("\n", [
            '<ItemSalesTaxGroupRet>',
            '  <ListID>4E0000-1044396142</ListID>',
            '  <TimeCreated>2009-11-05T03:13:13</TimeCreated>',
            '  <TimeModified>2009-11-05T03:13:13</TimeModified>',
            '  <EditSequence>1044396142</EditSequence>',
            '  <Name>CO TAX</Name>',
            '  <IsActive>true</IsActive>',
            '  <ItemDesc>CO Combined Sales Tax</ItemDesc>',
            '  <ItemSalesTaxRef>',
            '    <ListID>CO Sales Tax</ListID>',
            '    <FullName>5F0000-1044396142</FullName>',
            '  </ItemSalesTaxRef>',
            '  <ItemSalesTaxRef>',
            '    <ListID>610000-1044396142</ListID>',
            '    <FullName>RTD</FullName>',
            '  </ItemSalesTaxRef>',
            '</ItemSalesTaxGroupRet>',
        ]);

        // Convert the query response xml ItemSalesTaxGroupRet into a ItemSalesTaxGroup QBXML Object
        $objQBXML = AbstractQbxmlObject::fromQBXML($QBXML, PackageInfo::Actions['QUERY_ITEMSALESTAXGROUP']);
        $this->assertInstanceOf(ItemSalesTaxGroup::class, $objQBXML);


        // Test that the object creates the expected QBXML
        $expected = implode("\n", [
            '<ItemSalesTaxGroupAddRq>',
            '  <ItemSalesTaxGroupAdd>',
            '    <Name>CO TAX</Name>',
            '    <IsActive>true</IsActive>',
            '    <ItemDesc>CO Combined Sales Tax</ItemDesc>',
            '    <ItemSalesTaxRef>',
            '      <ListID>CO Sales Tax</ListID>',
            '      <FullName>5F0000-1044396142</FullName>',
            '    </ItemSalesTaxRef>',
            '    <ItemSalesTaxRef>',
            '      <ListID>610000-1044396142</ListID>',
            '      <FullName>RTD</FullName>',
            '    </ItemSalesTaxRef>',
            '  </ItemSalesTaxGroupAdd>',
            '</ItemSalesTaxGroupAddRq>',
        ]);


        $qbXml = $objQBXML->asQBXML(PackageInfo::Actions['ADD_ITEMSALESTAXGROUP']);

        $this->commonTests($expected, $qbXml);
    }


    /**
     * Test Creating a QBXML\Object\ItemService from a ItemServiceRet xml node in a ItemServiceQueryRs response
     */
    public function testFromQbxmlItemServiceRet(): void
    {
        $QBXML = implode("\n", [
            '<ItemServiceRet>',
            '  <ListID>280001-1265079883</ListID>',
            '  <TimeCreated>2010-02-01T22:04:43-05:00</TimeCreated>',
            '  <TimeModified>2010-02-01T22:04:43-05:00</TimeModified>',
            '  <EditSequence>1265079883</EditSequence>',
            '  <Name>Crazy Horse</Name>',
            '  <FullName>Crazy Horse</FullName>',
            '  <IsActive>true</IsActive>',
            '  <Sublevel>0</Sublevel>',
            '  <SalesTaxCodeRef>',
            '    <ListID>10000-1211065841</ListID>',
            '    <FullName>Tax</FullName>',
            '  </SalesTaxCodeRef>',
            '  <SalesOrPurchase>',
            '    <Price>0.00</Price>',
            '    <AccountRef>',
            '      <ListID>440000-1265079854</ListID>',
            '      <FullName>Consulting Income</FullName>',
            '    </AccountRef>',
            '  </SalesOrPurchase>',
            '</ItemServiceRet>',
        ]);

        // Convert the query response xml ItemServiceRet into a ItemService QBXML Object
        $objQBXML = AbstractQbxmlObject::fromQBXML($QBXML, PackageInfo::Actions['QUERY_ITEMSERVICE']);
        $this->assertInstanceOf(ItemService::class, $objQBXML);


        // Test that the object creates the expected QBXML
        $expected = implode("\n", [
            '<ItemServiceAddRq>',
            '  <ItemServiceAdd>',
            '    <Name>Crazy Horse</Name>',
            '    <IsActive>true</IsActive>',
            '    <SalesTaxCodeRef>',
            '      <ListID>10000-1211065841</ListID>',
            '      <FullName>Tax</FullName>',
            '    </SalesTaxCodeRef>',
            '    <SalesOrPurchase>',
            '      <Price>0.00</Price>',
            '      <AccountRef>',
            '        <ListID>440000-1265079854</ListID>',
            '        <FullName>Consulting Income</FullName>',
            '      </AccountRef>',
            '    </SalesOrPurchase>',
            '  </ItemServiceAdd>',
            '</ItemServiceAddRq>',
        ]);


        $qbXml = $objQBXML->asQBXML(PackageInfo::Actions['ADD_ITEMSERVICE']);

        $this->commonTests($expected, $qbXml);
    }


    /**
     * Test Creating a QBXML\Object\SalesReceipt from a SalesReceiptRet xml node in a SalesReceiptQueryRs response
     */
    public function testFromQbxmlSalesReceiptRet(): void
    {
        $QBXML = implode("\n", [
            '<SalesReceiptRet>',
            '  <TxnID>141CA5-1231522949</TxnID>',
            '  <TimeCreated>2009-01-09T12:42:29-05:00</TimeCreated>',
            '  <TimeModified>2009-01-09T12:42:29-05:00</TimeModified>',
            '  <EditSequence>1231522949</EditSequence>',
            '  <TxnNumber>64951</TxnNumber>',
            '  <CustomerRef>',
            '    <ListID>80003579-1231522938</ListID>',
            '    <FullName>Test, Tom</FullName>',
            '  </CustomerRef>',
            '  <TemplateRef>',
            '    <ListID>E0000-1129903256</ListID>',
            '    <FullName>Custom Sales Receipt</FullName>',
            '  </TemplateRef>',
            '  <TxnDate>2009-01-09</TxnDate>',
            '  <RefNumber>16466</RefNumber>',
            '  <BillAddress>',
            '    <Addr1>Tom Test</Addr1>',
            '    <Addr2>123 Test St</Addr2>',
            '    <City>Concord</City>',
            '    <State>MA</State>',
            '    <PostalCode>01742</PostalCode>',
            '    <Country>USA</Country>',
            '  </BillAddress>',
            '  <BillAddressBlock>',
            '    <Addr1>Tom Test</Addr1>',
            '    <Addr3>123 Test St</Addr3>',
            '    <Addr4>Concord, Massachusetts 01742</Addr4>',
            '    <Addr5>United States</Addr5>',
            '  </BillAddressBlock>',
            '  <IsPending>true</IsPending>',
            '  <DueDate>2009-01-09</DueDate>',
            '  <ShipDate>2009-01-09</ShipDate>',
            '  <Subtotal>150.00</Subtotal>',
            '  <ItemSalesTaxRef>',
            '    <ListID>20C0000-1129494968</ListID>',
            '    <FullName>MA State Tax</FullName>',
            '  </ItemSalesTaxRef>',
            '  <SalesTaxPercentage>5.00</SalesTaxPercentage>',
            '  <SalesTaxTotal>0.00</SalesTaxTotal>',
            '  <TotalAmount>150.00</TotalAmount>',
            '  <IsToBePrinted>true</IsToBePrinted>',
            '  <IsToBeEmailed>false</IsToBeEmailed>',
            '  <CustomerSalesTaxCodeRef>',
            '    <ListID>10000-1128983215</ListID>',
            '    <FullName>Tax</FullName>',
            '  </CustomerSalesTaxCodeRef>',
            '  <DepositToAccountRef>',
            '    <ListID>960000-1129903123</ListID>',
            '    <FullName>*Undeposited Funds</FullName>',
            '  </DepositToAccountRef>',
            '  <SalesReceiptLineRet>',
            '    <TxnLineID>141CA7-1231522949</TxnLineID>',
            '    <ItemRef>',
            '      <ListID>200001-1143815300</ListID>',
            '      <FullName>gift certificate</FullName>',
            '    </ItemRef>',
            '    <Desc>$150.00 gift certificate</Desc>',
            '    <Quantity>1</Quantity>',
            '    <Rate>150.00</Rate>',
            '    <Amount>150.00</Amount>',
            '    <SalesTaxCodeRef>',
            '      <ListID>20000-1128983215</ListID>',
            '      <FullName>Non</FullName>',
            '    </SalesTaxCodeRef>',
            '  </SalesReceiptLineRet>',
            '  <SalesReceiptLineRet>',
            '    <TxnLineID>141CA9-1231522949</TxnLineID>',
            '    <ItemRef>',
            '      <ListID>80000857-1231503074</ListID>',
            '      <FullName>Handling Item - QuickBooks Inte</FullName>',
            '    </ItemRef>',
            '    <Desc>Handling Charge</Desc>',
            '    <Rate>0.00</Rate>',
            '    <Amount>0.00</Amount>',
            '    <SalesTaxCodeRef>',
            '      <ListID>20000-1128983215</ListID>',
            '      <FullName>Non</FullName>',
            '    </SalesTaxCodeRef>',
            '  </SalesReceiptLineRet>',
            '</SalesReceiptRet>',
        ]);

        // Convert the query response xml SalesReceiptRet into a SalesReceipt QBXML Object
        $objQBXML = AbstractQbxmlObject::fromQBXML($QBXML, PackageInfo::Actions['QUERY_SALESRECEIPT']);
        $this->assertInstanceOf(SalesReceipt::class, $objQBXML);


        // Test that the object creates the expected QBXML
        $expected = implode("\n", [
            '<SalesReceiptAddRq>',
            '  <SalesReceiptAdd>',
            '    <CustomerRef>',
            '      <ListID>80003579-1231522938</ListID>',
            '      <FullName>Test, Tom</FullName>',
            '    </CustomerRef>',
            '    <TemplateRef>',
            '      <ListID>E0000-1129903256</ListID>',
            '      <FullName>Custom Sales Receipt</FullName>',
            '    </TemplateRef>',
            '    <TxnDate>2009-01-09</TxnDate>',
            '    <RefNumber>16466</RefNumber>',
            '    <BillAddress>',
            '      <Addr1>Tom Test</Addr1>',
            '      <Addr2>123 Test St</Addr2>',
            '      <City>Concord</City>',
            '      <State>MA</State>',
            '      <PostalCode>01742</PostalCode>',
            '      <Country>USA</Country>',
            '    </BillAddress>',
            '    <IsPending>true</IsPending>',
            '    <DueDate>2009-01-09</DueDate>',
            '    <ShipDate>2009-01-09</ShipDate>',
            '    <ItemSalesTaxRef>',
            '      <ListID>20C0000-1129494968</ListID>',
            '      <FullName>MA State Tax</FullName>',
            '    </ItemSalesTaxRef>',
            '    <IsToBePrinted>true</IsToBePrinted>',
            '    <IsToBeEmailed>false</IsToBeEmailed>',
            '    <CustomerSalesTaxCodeRef>',
            '      <ListID>10000-1128983215</ListID>',
            '      <FullName>Tax</FullName>',
            '    </CustomerSalesTaxCodeRef>',
            '    <DepositToAccountRef>',
            '      <ListID>960000-1129903123</ListID>',
            '      <FullName>*Undeposited Funds</FullName>',
            '    </DepositToAccountRef>',
            '    <SalesReceiptLineAdd>',
            '      <ItemRef>',
            '        <ListID>200001-1143815300</ListID>',
            '        <FullName>gift certificate</FullName>',
            '      </ItemRef>',
            '      <Desc>$150.00 gift certificate</Desc>',
            '      <Quantity>1</Quantity>',
            '      <Rate>150.00</Rate>',
            '      <Amount>150.00</Amount>',
            '      <SalesTaxCodeRef>',
            '        <ListID>20000-1128983215</ListID>',
            '        <FullName>Non</FullName>',
            '      </SalesTaxCodeRef>',
            '    </SalesReceiptLineAdd>',
            '    <SalesReceiptLineAdd>',
            '      <ItemRef>',
            '        <ListID>80000857-1231503074</ListID>',
            '        <FullName>Handling Item - QuickBooks Inte</FullName>',
            '      </ItemRef>',
            '      <Desc>Handling Charge</Desc>',
            '      <Rate>0.00</Rate>',
            '      <Amount>0.00</Amount>',
            '      <SalesTaxCodeRef>',
            '        <ListID>20000-1128983215</ListID>',
            '        <FullName>Non</FullName>',
            '      </SalesTaxCodeRef>',
            '    </SalesReceiptLineAdd>',
            '  </SalesReceiptAdd>',
            '</SalesReceiptAddRq>',
        ]);


        $qbXml = $objQBXML->asQBXML(PackageInfo::Actions['ADD_SALESRECEIPT']);

        $this->commonTests($expected, $qbXml);
    }


    /**
     * Test Creating a QBXML\Object\Vendor from a VendorRet xml node in a QBOE (QuickBooks Online Edition) VendorQueryRs response
     */
    public function testFromQbxmlVendorRet_QBOE(): void
    {
        $QBXML = implode("\n", [
            '<VendorRet>',
              '<ListID>146</ListID>',
              '<TimeCreated>2009-10-24T00:34:07</TimeCreated>',
              '<TimeModified>2009-11-05T03:13:46</TimeModified>',
              '<EditSequence>19</EditSequence>',
              '<Name>Automotive Core Supply</Name>',
              '<CompanyName>Automotive Core Supply</CompanyName>',
              '<FirstName>Automotive</FirstName>',
              '<LastName>Core Supply</LastName>',
              '<VendorAddress>',
                '<City>Worcester</City>',
                '<State>MA</State>',
                '<PostalCode>03546</PostalCode>',
                '<Country>US</Country>',
              '</VendorAddress>',
              '<Phone>9884040132</Phone>',
              '<Fax>9884040132</Fax>',
              '<Email>baburaj@securenext.net</Email>',
              '<NameOnCheck>Automotive Core Supply</NameOnCheck>',
              '<AccountNumber>0014000000OvgNBAAZ</AccountNumber>',
              '<IsVendorEligibleFor1099>false</IsVendorEligibleFor1099>',
              '<Balance>0.00</Balance>',
            '</VendorRet>',
        ]);

        // Convert the query response xml VendorRet into a Vendor QBXML Object
        $objQBXML = AbstractQbxmlObject::fromQBXML($QBXML, PackageInfo::Actions['QUERY_VENDOR']);
        $this->assertInstanceOf(Vendor::class, $objQBXML);


        // Test that the object creates the expected QBXML
        $expected = implode("\n", [
            '<VendorAddRq>',
            '  <VendorAdd>',
            '    <Name>Automotive Core Supply</Name>',
            '    <CompanyName>Automotive Core Supply</CompanyName>',
            '    <FirstName>Automotive</FirstName>',
            '    <LastName>Core Supply</LastName>',
            '    <VendorAddress>',
            '      <City>Worcester</City>',
            '      <State>MA</State>',
            '      <PostalCode>03546</PostalCode>',
            '      <Country>US</Country>',
            '    </VendorAddress>',
            '    <Phone>9884040132</Phone>',
            '    <Fax>9884040132</Fax>',
            '    <Email>baburaj@securenext.net</Email>',
            '    <NameOnCheck>Automotive Core Supply</NameOnCheck>',
            '    <AccountNumber>0014000000OvgNBAAZ</AccountNumber>',
            '    <IsVendorEligibleFor1099>false</IsVendorEligibleFor1099>',
            '  </VendorAdd>',
            '</VendorAddRq>',
        ]);


        $qbXml = $objQBXML->asQBXML(PackageInfo::Actions['ADD_VENDOR']);

        $this->commonTests($expected, $qbXml);
    }
}
