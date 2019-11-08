<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\Account;

final class AccountTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
        $this->obj = QbxmlTestdataGenerator::Account('Bank', 'Checking:Operating Account', 'Checking account for operating expenses', 10001.01);
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test AccountAddRq
     */
    public function testAccountAdd(): void
    {
        $expected = implode("\n", [
            '<AccountAddRq>',
            '  <AccountAdd>',
            '    <Name>Operating Account</Name>',
            '    <IsActive>true</IsActive>',
            '    <ParentRef>',
            '      <FullName>Checking</FullName>',
            '    </ParentRef>',
            '    <AccountType>Bank</AccountType>',
            '    <BankNumber>0011 123 9999</BankNumber>',
            '    <Desc>Checking account for operating expenses</Desc>',
            '    <OpenBalance>10001.01</OpenBalance>',
            '    <OpenBalanceDate>2019-07-07</OpenBalanceDate>',
            '  </AccountAdd>',
            '</AccountAddRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['ADD_ACCOUNT']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test AccountModRq
     */
    public function testAccountMod(): void
    {
        $expected = implode("\n", [
            '<AccountModRq>',
            '  <AccountMod>',
            '    <ListID>70000007-1234567890</ListID>',
            '    <EditSequence>9876543210</EditSequence>',
            '    <Name>Operating Account</Name>',
            '    <IsActive>true</IsActive>',
            '    <ParentRef>',
            '      <FullName>Checking</FullName>',
            '    </ParentRef>',
            '    <AccountType>Bank</AccountType>',
            '    <BankNumber>0011 123 9999</BankNumber>',
            '    <Desc>Checking account for operating expenses</Desc>',
            '    <OpenBalance>10001.01</OpenBalance>',
            '    <OpenBalanceDate>2019-07-07</OpenBalanceDate>',
            '  </AccountMod>',
            '</AccountModRq>',
        ]);
        //fwrite(STDOUT, "$expected\n");


        $qbXml = $this->obj->asQBXML(PackageInfo::Actions['MOD_ACCOUNT']);

        $this->commonTests($expected, $qbXml);
    }

    /**
     * Test setting an invalid AccountType
     */
    public function testInvalidAccountType(): void
    {
        $this->expectException('\Exception');

        $this->obj->setAccountType('NotValidOption');
    }
}
