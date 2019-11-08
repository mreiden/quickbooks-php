<?php declare(strict_types=1);

use QuickBooksPhpDevKit\QBXML\QbxmlTestdataGenerator;
use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\QBXML\Object\Department;

final class DepartmentTest extends XmlBaseTest
{
    protected $obj;


    public function setUp(): void
    {
        $departmentName = 'Department Name';
        $departmentCode = 'ABCDEFG';           // This should be truncated to 3 characters (ABC)
        $this->obj = QbxmlTestdataGenerator::Department($departmentName, $departmentCode);
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test ClassAddRq
     */
    public function testDepartmentAddCompleteQbxml(): void
    {
        $expected = implode("\n", [
            '<?qbxmlpos version="3.0"?>',
            '<QBXMLPOS>',
            '  <QBXMLPOSMsgsRq onError="continueOnError">',
            '    <DepartmentAddRq>',
            '      <DepartmentAdd>',
            '        <DepartmentCode>ABC</DepartmentCode>',               // ABCDEFG is correctly truncated to ABC (maxlength 3)
            '        <DepartmentName>Department Name</DepartmentName>',
            '      </DepartmentAdd>',
            '    </DepartmentAddRq>',
            '  </QBXMLPOSMsgsRq>',
            '</QBXMLPOS>',
        ]);
        //fwrite(STDOUT, "$expected\n");

        // asCompleteQBXML() should be wrapped in QBXMLPOS tags because Department is only in the QB POS sdk
        $qbXml = $this->obj->asCompleteQBXML(PackageInfo::Actions['ADD_DEPARTMENT']);

        $this->commonTests($expected, $qbXml);
    }
}
