<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\Bill;
use QuickBooksPhpDevKit\QBXML\Object\Bill\ExpenseLine;
use QuickBooksPhpDevKit\QBXML\Object\Bill\ItemLine;

final class BillTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::Bill('Vendor Name', '70000009-1234567890', 'CustomerName');
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }


    /**
     * Test Invalid Bill parameters
     */
    public function testBillInvalid(): void
    {
        $this->expectException('\Exception');
        QbxmlTestdataGenerator::Bill(null);
    }

    /**
     * Test BillAddRq
     */
    public function testBillAdd(): void
    {
        $expected = implode("\n", [
            '<BillAddRq>',
            '  <BillAdd>',
            '    <VendorRef>',
            '      <ListID>70000009-1234567890</ListID>',
            '      <FullName>Vendor Name</FullName>',
            '    </VendorRef>',
            '    <TxnDate>2019-07-07</TxnDate>',
            '    <DueDate>2019-07-27</DueDate>',
            '    <RefNumber>Invoice 20002</RefNumber>',
            '    <Memo>Vendor performed a service.</Memo>',
            '    <ExpenseLineAdd>',
            '      <AccountRef>',
            '        <FullName>Taxes Owed:Licensing</FullName>',
            '      </AccountRef>',
            '      <Amount>1500.00</Amount>',
            '      <Memo>Licensing Tax and related fees</Memo>',
            '      <CustomerRef>',
            '        <FullName>CustomerName</FullName>',
            '      </CustomerRef>',
            '      <BillableStatus>Billable</BillableStatus>',
            '    </ExpenseLineAdd>',
            '    <ExpenseLineAdd>',
            '      <AccountRef>',
            '        <FullName>Taxes Owed:Business</FullName>',
            '      </AccountRef>',
            '      <Amount>542.24</Amount>',
            '      <Memo>Local Business Tax</Memo>',
            '      <CustomerRef>',
            '        <FullName>CustomerName</FullName>',
            '      </CustomerRef>',
            '      <BillableStatus>NotBillable</BillableStatus>',
            '    </ExpenseLineAdd>',
            '  </BillAdd>',
            '</BillAddRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_BILL']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test BillModRq
     */
    public function testBillMod(): void
    {
        $expected = implode("\n", [
            '<BillModRq>',
            '  <BillMod>',
            '    <TxnID>70000005-1234567890</TxnID>',
            '    <EditSequence>9876543210</EditSequence>',
            '    <VendorRef>',
            '      <ListID>70000009-1234567890</ListID>',
            '      <FullName>Vendor Name</FullName>',
            '    </VendorRef>',
            '    <TxnDate>2019-07-07</TxnDate>',
            '    <DueDate>2019-07-27</DueDate>',
            '    <RefNumber>Invoice 20002</RefNumber>',
            '    <Memo>Vendor performed a service.</Memo>',
            '    <ExpenseLineMod>',
            '      <TxnLineID>-1</TxnLineID>',
            '      <AccountRef>',
            '        <FullName>Taxes Owed:Licensing</FullName>',
            '      </AccountRef>',
            '      <Amount>1500.00</Amount>',
            '      <Memo>Licensing Tax and related fees</Memo>',
            '      <CustomerRef>',
            '        <FullName>CustomerName</FullName>',
            '      </CustomerRef>',
            '      <BillableStatus>Billable</BillableStatus>',
            '    </ExpenseLineMod>',
            '    <ExpenseLineMod>',
            '      <TxnLineID>-1</TxnLineID>',
            '      <AccountRef>',
            '        <FullName>Taxes Owed:Business</FullName>',
            '      </AccountRef>',
            '      <Amount>542.24</Amount>',
            '      <Memo>Local Business Tax</Memo>',
            '      <CustomerRef>',
            '        <FullName>CustomerName</FullName>',
            '      </CustomerRef>',
            '      <BillableStatus>NotBillable</BillableStatus>',
            '    </ExpenseLineMod>',
            '  </BillMod>',
            '</BillModRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['MOD_BILL']);

        $this->commonTests($expected, $qbXml);
    }
}
