<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\Generic;

final class GenericTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }



    /**
     * Test AbstractQbxmlObject get/setDate methods
     *
     * Note: Use a different key if you're going to test getting the value after setting it.
     *       If the setting fails, the get will get the previously set value
     */
    public function testDateMethods(): void
    {
        $qbObj = new Generic([]);

        // Test special 1969-12-31 date.  This is the date you get from date('Y-m-d', null|false)
        $this->assertSame(false, $qbObj->setDateType('MyDate', '1969-12-31'));
        $this->assertSame(true, $qbObj->setDateType('MyDate', '1969-12-31', false));

        // Test setting to null (clear value)
        $this->assertSame(true, $qbObj->setDateType('MyDate', null));
        $this->assertNull($qbObj->getDateType('MyDate', 'Y-m-d'));

        // Test empty dates
        $this->assertSame(false, $qbObj->setDateType('MyDate', ''));
        $this->assertSame(false, $qbObj->setDateType('MyDate', '0'));
        $this->assertSame(false, $qbObj->setDateType('MyDate', 0));

        // Test invalid datatypes
        $this->assertSame(false, $qbObj->setDateType('MyDate', false));
        $this->assertSame(false, $qbObj->setDateType('MyDate', []));
        $this->assertSame(false, $qbObj->setDateType('MyDate', new stdClass()));


        // Test invalid dates
        $this->assertSame(false, $qbObj->setDateType('MyDate2', '2019-99-50'));
        $this->assertNull($qbObj->getDateType('MyDate2', 'Y-m-d'));

        // Test setting a valid date and getting it in different formats
        $this->assertSame(true, $qbObj->setDateType('MyDate3', 20190704));
        $this->assertSame('2019-07-04', $qbObj->getDateType('MyDate3', 'Y-m-d'));
        $this->assertSame('20190704', $qbObj->getDateType('MyDate3', 'Ymd'));
        // Test default date format of 'Y-m-d'
        $this->assertSame('2019-07-04', $qbObj->getDateType('MyDate3'));
        $this->assertSame('2019-07-04', $qbObj->getDateType('MyDate3', null));
        $this->assertSame('2019-07-04', $qbObj->getDateType('MyDate3', ''));

        // Test SOAP Timestamp
        $this->assertSame(true, $qbObj->setDateType('MyDate4', '2019-07-07T12:12:12-07:00'));
        $this->assertSame('2019-07-07', $qbObj->getDateType('MyDate4', 'Y-m-d'));
    }

    /**
     * Test AbstractQbxmlObject get/setBoolean methods
     *
     * Note: Use a different key if you're going to test getting the value after setting it.
     *       If the setting fails, the get will get the previously set value
     */
    public function testBooleanMethods(): void
    {
        $qbObj = new Generic([]);

        // Test setting true values
        $this->assertSame(true, $qbObj->setBooleanType('MyBool', true));
        $this->assertSame(true, $qbObj->getBooleanType('MyBool'));

        $this->assertSame(true, $qbObj->setBooleanType('MyBool', 'true'));
        $this->assertSame(true, $qbObj->getBooleanType('MyBool'));

        $this->assertSame(true, $qbObj->setBooleanType('MyBool', 1));
        $this->assertSame(true, $qbObj->getBooleanType('MyBool'));

        $this->assertSame(true, $qbObj->setBooleanType('MyBool', '1'));
        $this->assertSame(true, $qbObj->getBooleanType('MyBool'));

        // Test setting false values
        $this->assertSame(true, $qbObj->setBooleanType('MyBool', false));
        $this->assertSame(false, $qbObj->getBooleanType('MyBool'));

        $this->assertSame(true, $qbObj->setBooleanType('MyBool', 'false'));
        $this->assertSame(false, $qbObj->getBooleanType('MyBool'));

        $this->assertSame(true, $qbObj->setBooleanType('MyBool', 0));
        $this->assertSame(false, $qbObj->getBooleanType('MyBool'));

        $this->assertSame(true, $qbObj->setBooleanType('MyBool', '0'));
        $this->assertSame(false, $qbObj->getBooleanType('MyBool'));

        // Test setting null value
        $this->assertSame(true, $qbObj->setBooleanType('MyBool', null));
        $this->assertNull($qbObj->getBooleanType('MyBool'));

        // Test setting invalid values
        $this->assertSame(false, $qbObj->setBooleanType('MyBool', 'null'));
        $this->assertSame(false, $qbObj->setBooleanType('MyBool', 'string'));
        $this->assertSame(false, $qbObj->setBooleanType('MyBool', []));
        $this->assertSame(false, $qbObj->setBooleanType('MyBool', 'null'));
    }

    /**
     * Test AbstractQbxmlObject get/setAmount methods
     *
     * Note: Use a different key if you're going to test getting the value after setting it.
     *       If the setting fails, the get will get the previously set value
     */
    public function testAmountMethods(): void
    {
        $qbObj = new Generic([]);

        // Test setting valid values
        $this->assertSame(true, $qbObj->setAmountType('MyAmount', 20.55));
        $this->assertSame('20.55', $qbObj->getAmountType('MyAmount'));

        $this->assertSame(true, $qbObj->setAmountType('MyAmount2', '20.55'));
        $this->assertSame('20.55', $qbObj->getAmountType('MyAmount2'));

        $this->assertSame(true, $qbObj->setAmountType('MyAmount3', 200));
        $this->assertSame('200.00', $qbObj->getAmountType('MyAmount3'));

        $this->expectException('\TypeError');
        $this->assertSame(false, $qbObj->setAmountType('MyAmount4', 'NaN'));
        $this->assertNull($qbObj->getAmountType('MyAmount4'));

        $this->assertSame(false, $qbObj->setAmountType('MyAmount5', []));
        $this->assertNull($qbObj->getAmountType('MyAmount5'));

        $this->assertSame(false, $qbObj->setAmountType('MyAmount6', new stdClass()));
        $this->assertNull($qbObj->getAmountType('MyAmount6'));
    }
}
