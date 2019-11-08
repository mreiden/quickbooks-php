<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\ReceivePayment;
use QuickBooksPhpDevKit\QBXML\Object\ReceivePayment\AppliedToTxn;

final class ReceivePaymentTest extends XmlBaseTest
{
    protected $obj;

    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::ReceivePayment();
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test ReceivePaymentAddRq
     */
    public function testReceivePaymentAdd(): void
    {
        $expected = implode("\n", [
            '<ReceivePaymentAddRq>',
            '  <ReceivePaymentAdd>',
            '    <CustomerRef>',
            '      <ListID>80000196-1234567890</ListID>',
            '    </CustomerRef>',
            '    <ARAccountRef>',
            '      <FullName>Accounts Receivable</FullName>',
            '    </ARAccountRef>',
            '    <TxnDate>2019-07-02</TxnDate>',
            '    <RefNumber>1001</RefNumber>',
            '    <TotalAmount>543.21</TotalAmount>',
            '    <PaymentMethodRef>',
            '      <FullName>Check</FullName>',
            '    </PaymentMethodRef>',
            '    <Memo>Invoice ####</Memo>',
            '    <DepositToAccountRef>',
            '      <FullName>Undeposited Funds</FullName>',
            '    </DepositToAccountRef>',
            '    <IsAutoApply>false</IsAutoApply>',
            '    <AppliedToTxnAdd>',
            '      <TxnID>7625-1234567890</TxnID>',
            '      <PaymentAmount>543.21</PaymentAmount>',
            '    </AppliedToTxnAdd>',
            '  </ReceivePaymentAdd>',
            '</ReceivePaymentAddRq>',
        ]);

        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_RECEIVEPAYMENT']);

        $this->commonTests($qbXml, $expected);
    }

    /**
     * Test ReceivePaymentRq
     */
    public function testReceivePaymentMod(): void
    {
        $expected = implode("\n", [
            '<ReceivePaymentModRq>',
            '  <ReceivePaymentMod>',
            '    <CustomerRef>',
            '      <ListID>80000196-1234567890</ListID>',
            '    </CustomerRef>',
            '    <ARAccountRef>',
            '      <FullName>Accounts Receivable</FullName>',
            '    </ARAccountRef>',
            '    <TxnDate>2019-07-02</TxnDate>',
            '    <RefNumber>1001</RefNumber>',
            '    <TotalAmount>543.21</TotalAmount>',
            '    <PaymentMethodRef>',
            '      <FullName>Check</FullName>',
            '    </PaymentMethodRef>',
            '    <Memo>Invoice ####</Memo>',
            '    <DepositToAccountRef>',
            '      <FullName>Undeposited Funds</FullName>',
            '    </DepositToAccountRef>',
            '    <AppliedToTxnMod>',
            '      <TxnID>7625-1234567890</TxnID>',
            '      <PaymentAmount>543.21</PaymentAmount>',
            '    </AppliedToTxnMod>',
            '  </ReceivePaymentMod>',
            '</ReceivePaymentModRq>',
        ]);

        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['MOD_RECEIVEPAYMENT']);

        $this->commonTests($qbXml, $expected);
    }
}
