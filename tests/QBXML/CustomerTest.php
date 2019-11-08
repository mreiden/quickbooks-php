<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\Customer;

final class CustomerTest extends XmlBaseTest
{
    protected $obj;

    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::Customer('My Customer Name -12345', 'Parent', 'SR');
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test CustomerAddRq
     */
    public function testCustomerAdd(): void
    {
        $expected = implode("\n", [
            '<CustomerAddRq>',
            '  <CustomerAdd>',
            '    <Name>My Customer Name -12345</Name>',
            // Class is only supported in QuickBooks Enterprise
            //'    <ClassRef>',
            //'      <FullName>QuickBooksClass</FullName>',
            //'    </ClassRef>',
            '    <ParentRef>',
            '      <FullName>Parent</FullName>',
            '    </ParentRef>',
            '    <CompanyName>My Company Name</CompanyName>',
            '    <FirstName>John</FirstName>',
            '    <MiddleName>Q.</MiddleName>',
            '    <LastName>Doe</LastName>',
            '    <BillAddress>',
            '      <Addr1>My Customer</Addr1>',
            '      <Addr2>attn: John Q. Doe</Addr2>',
            '      <Addr3>123 Billing Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>BillCity</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </BillAddress>',
            '    <ShipAddress>',
            '      <Addr1>My Customer</Addr1>',
            '      <Addr2>attn: John Q. Doe</Addr2>',
            '      <Addr3>123 Ship Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>ShipCity</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </ShipAddress>',
            '    <Phone>(111) 555-5550</Phone>',
            '    <AltPhone>1115555551</AltPhone>',
            '    <Fax>(111) 555-5555</Fax>',
            '    <Email>someone@example.com</Email>',
            '    <TermsRef>',
            '      <FullName>11% 12 Net 31</FullName>',
            '    </TermsRef>',
            '    <SalesRepRef>',
            '      <FullName>SR</FullName>',
            '    </SalesRepRef>',
            '    <SalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </SalesTaxCodeRef>',
            '    <AccountNumber>12345</AccountNumber>',
            '  </CustomerAdd>',
            '</CustomerAddRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbCustomer =& $this->obj;
        $qbXml = $qbCustomer->asQBXML(PackageInfo::Actions['ADD_CUSTOMER']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test CustomerAddRq with complete QBXML wrapper
     */
    public function testCustomerAddCompleteQbxml(): void
    {
        $expected = implode("\n", [
            '<?qbxml version="13.0"?>',
            '<QBXML>',
            '  <QBXMLMsgsRq onError="continueOnError">',
            '    <CustomerAddRq>',
            '      <CustomerAdd>',
            '        <Name>My Customer Name -12345</Name>',
            // Class is only supported in QuickBooks Enterprise
            //'        <ClassRef>',
            //'          <FullName>QuickBooksClass</FullName>',
            //'        </ClassRef>',
            '        <ParentRef>',
            '          <FullName>Parent</FullName>',
            '        </ParentRef>',
            '        <CompanyName>My Company Name</CompanyName>',
            '        <FirstName>John</FirstName>',
            '        <MiddleName>Q.</MiddleName>',
            '        <LastName>Doe</LastName>',
            '        <BillAddress>',
            '          <Addr1>My Customer</Addr1>',
            '          <Addr2>attn: John Q. Doe</Addr2>',
            '          <Addr3>123 Billing Street</Addr3>',
            '          <Addr4>APT 5555</Addr4>',
            '          <City>BillCity</City>',
            '          <State>NY</State>',
            '          <PostalCode>10019</PostalCode>',
            '        </BillAddress>',
            '        <ShipAddress>',
            '          <Addr1>My Customer</Addr1>',
            '          <Addr2>attn: John Q. Doe</Addr2>',
            '          <Addr3>123 Ship Street</Addr3>',
            '          <Addr4>APT 5555</Addr4>',
            '          <City>ShipCity</City>',
            '          <State>NY</State>',
            '          <PostalCode>10019</PostalCode>',
            '        </ShipAddress>',
            '        <Phone>(111) 555-5550</Phone>',
            '        <AltPhone>1115555551</AltPhone>',
            '        <Fax>(111) 555-5555</Fax>',
            '        <Email>someone@example.com</Email>',
            '        <TermsRef>',
            '          <FullName>11% 12 Net 31</FullName>',
            '        </TermsRef>',
            '        <SalesRepRef>',
            '          <FullName>SR</FullName>',
            '        </SalesRepRef>',
            '        <SalesTaxCodeRef>',
            '          <FullName>Non</FullName>',
            '        </SalesTaxCodeRef>',
            '        <AccountNumber>12345</AccountNumber>',
            '      </CustomerAdd>',
            '    </CustomerAddRq>',
            '  </QBXMLMsgsRq>',
            '</QBXML>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbCustomer =& $this->obj;
        $qbXml = $qbCustomer->asCompleteQBXML(PackageInfo::Actions['ADD_CUSTOMER']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test CustomerAddRq with UTF-8 Characters
     */
    public function testUtf8CustomerAdd(): void
    {
        $utf8_fields = [
            'CompanyName' => 'Freddy Krûegër’s — “Nîghtmåre ¾"',
            'Contact' => 'Test of some UTF8 chars— Á, Æ, Ë, ¾, Õ, ä, ß, ú, ñ',
            'BillAddress' => [
                'My &amp; &#8212; &Acirc; Customer',          // Address Line 1
                'attn: John Q. Doe',    // Address Line 2
                '123 Billing Street',   // Address Line 3
                'APT 5555',             // Address Line 4
                '',                     // Address Line 5
                'Here is the £ pound sign for ££££', // City
                'NY',                   // State
                '',                     // Province
                '10019',                // Postal Code
                '',                     // Country
                ''                      // Note for Address
            ],
        ];
        //$utf8_company_name = ;

        $utf8_customer = QbxmlTestdataGenerator::Customer($utf8_fields['CompanyName'], 'Parent', 'SR', $utf8_fields);
        //$utf8_customer->setCompanyName($utf8_company_name);

        // $utf8_customer->setContact();
        // $utf8_customer->setBillAddress(
        // );

        $expected = implode("\n", [
            '<?xml version="1.0" encoding="us-ascii"?>',
            '<CustomerAddRq>',
            '  <CustomerAdd>',
            '    <Name>Freddy Kr&#251;eg&#235;r&#8217;s &#8212; &#8220;N&#238;ghtm&#229;re &#190;"</Name>',
            // Class is only supported in QuickBooks Enterprise
            //'    <ClassRef>',
            //'      <FullName>QuickBooksClass</FullName>',
            //'    </ClassRef>',
            '    <ParentRef>',
            '      <FullName>Parent</FullName>',
            '    </ParentRef>',
            '    <CompanyName>Freddy Kr&#251;eg&#235;r&#8217;s &#8212; &#8220;N&#238;ghtm&#229;re &#190;"</CompanyName>',
            '    <FirstName>John</FirstName>',
            '    <MiddleName>Q.</MiddleName>',
            '    <LastName>Doe</LastName>',
            '    <BillAddress>',
            '      <Addr1>My &amp; &#8212; &#194; Customer</Addr1>',
            '      <Addr2>attn: John Q. Doe</Addr2>',
            '      <Addr3>123 Billing Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>Here is the &#163; pound sign for &#163;&#163;</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            //'      <Note>Here is the &#163; pound sign for you British </Note>',
            '    </BillAddress>',
            '    <ShipAddress>',
            '      <Addr1>My Customer</Addr1>',
            '      <Addr2>attn: John Q. Doe</Addr2>',
            '      <Addr3>123 Ship Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>ShipCity</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </ShipAddress>',
            '    <Phone>(111) 555-5550</Phone>',
            '    <AltPhone>1115555551</AltPhone>',
            '    <Fax>(111) 555-5555</Fax>',
            '    <Email>someone@example.com</Email>',
            '    <Contact>Test of some UTF8 chars&#8212; &#193;, &#198;, &#203;, &#190;, &#213;, &#228;</Contact>',
            '    <TermsRef>',
            '      <FullName>11% 12 Net 31</FullName>',
            '    </TermsRef>',
            '    <SalesRepRef>',
            '      <FullName>SR</FullName>',
            '    </SalesRepRef>',
            '    <SalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </SalesTaxCodeRef>',
            '    <AccountNumber>12345</AccountNumber>',
            '  </CustomerAdd>',
            '</CustomerAddRq>',
            '',
        ]);
        //fwrite(STDOUT, "$expected\n");

        $this->assertSame($utf8_fields['CompanyName'], $utf8_customer->getName());
        $this->assertSame($utf8_fields['CompanyName'], $utf8_customer->getCompanyName());

        // Contact should be truncated to 41 characters
        $this->assertSame('Test of some UTF8 chars— Á, Æ, Ë, ¾, Õ, ä', $utf8_customer->getContact());

        // PostalCode should be truncated to 13 characters
        $this->assertSame('Here is the £ pound sign for ££', $utf8_customer->getBillAddress('City'));

        $qbXml = $utf8_customer->asQBXML(PackageInfo::Actions['ADD_CUSTOMER']);

        $this->commonTests($expected, $qbXml, true);
    }

    /**
     * Test CustomerModRq
     */
    public function testCustomerMod(): void
    {
        $expected = implode("\n", [
            '<CustomerModRq>',
            '  <CustomerMod>',
            '    <ListID>70000077-1286207897</ListID>',
            '    <EditSequence>1191523643</EditSequence>',
            '    <Name>My Customer Name -12345</Name>',
            // Class is only supported in QuickBooks Enterprise
            //'    <ClassRef>',
            //'      <FullName>QuickBooksClass</FullName>',
            //'    </ClassRef>',
            '    <ParentRef>',
            '      <FullName>Parent</FullName>',
            '    </ParentRef>',
            '    <CompanyName>My Company Name</CompanyName>',
            '    <FirstName>John</FirstName>',
            '    <MiddleName>Q.</MiddleName>',
            '    <LastName>Doe</LastName>',
            '    <BillAddress>',
            '      <Addr1>My Customer</Addr1>',
            '      <Addr2>attn: John Q. Doe</Addr2>',
            '      <Addr3>123 Billing Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>BillCity</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </BillAddress>',
            '    <ShipAddress>',
            '      <Addr1>My Customer</Addr1>',
            '      <Addr2>attn: John Q. Doe</Addr2>',
            '      <Addr3>123 Ship Street</Addr3>',
            '      <Addr4>APT 5555</Addr4>',
            '      <City>ShipCity</City>',
            '      <State>NY</State>',
            '      <PostalCode>10019</PostalCode>',
            '    </ShipAddress>',
            '    <Phone>(111) 555-5550</Phone>',
            '    <AltPhone>1115555551</AltPhone>',
            '    <Fax>(111) 555-5555</Fax>',
            '    <Email>someone@example.com</Email>',
            '    <TermsRef>',
            '      <FullName>11% 12 Net 31</FullName>',
            '    </TermsRef>',
            '    <SalesRepRef>',
            '      <FullName>SR</FullName>',
            '    </SalesRepRef>',
            '    <SalesTaxCodeRef>',
            '      <FullName>Non</FullName>',
            '    </SalesTaxCodeRef>',
            '    <AccountNumber>12345</AccountNumber>',
            '  </CustomerMod>',
            '</CustomerModRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['MOD_CUSTOMER']);

        $this->commonTests($expected, $qbXml);
    }

    public function testAccountNumberRefNumDataTypes(): void
    {
        $RefNumber = 12345;
        $this->obj->setAccountNumber($RefNumber);
        $intResult = $this->obj->getAccountNumber();

        $this->obj->setAccountNumber("$RefNumber");
        $stringResult = $this->obj->getAccountNumber();

        $this->assertEquals($intResult, $stringResult);
    }
}
