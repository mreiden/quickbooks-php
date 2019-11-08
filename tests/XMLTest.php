<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;

use QuickBooksPhpDevKit\{
    Cast,
    PackageInfo,
    XML,
};
use QuickBooksPhpDevKit\QBXML\{
    Object\Invoice,
    QbxmlTestdataGenerator,
};

final class XMLTest extends XmlBaseTest
{
    protected $obj;

    private $testStrings = [
        'Keith Palmer, Shannon Daniels, Kurtis & Karli' => 'Keith Palmer, Shannon Daniels, Kurtis &amp; Karli',
        'Test of some UTF8 chars- Á, Æ, Ë, ¾, Õ, ä, ß, ú, ñ' => 'Test of some UTF8 chars- &#193;, &#198;, &#203;, &#190;, &#213;, &#228;, &#223;, &#250;, &#241;',
        'Test & Then Some' => 'Test &amp; Then Some',
        'Test of already encoded &amp; data.' => 'Test of already encoded &amp; data.',
        'Tapio Törmänen' => 'Tapio T&#246;rm&#228;nen',
        'Here is the £ pound sign for you British gents...' => 'Here is the &#163; pound sign for you British gents...',
        'Quotes " in \'String\'' => "Quotes &quot; in 'String'",
        'Freddy Krûegër’s — “Nîghtmåre ¾"' => 'Freddy Kr&#251;eg&#235;r&#8217;s &#8212; &#8220;N&#238;ghtm&#229;re &#190;&quot;',


        'CableÃ‚Â Raceway/Wire Chase,Ã‚Â 1.25" x 6\', White' => 'Cable&#195;&#8218;&#194;&#160;Raceway/Wire Chase,&#195;&#8218;&#194;&#160;1.25&quot; x 6\', White',
        "Cable&Atilde;‚&Acirc;&nbsp;Raceway/Wire Chase,&Atilde;‚&Acirc;&nbsp;1.25&quot; x 6', White" => 'Cable&#195;&#8218;&#194;&#160;Raceway/Wire Chase,&#195;&#8218;&#194;&#160;1.25&quot; x 6\', White',
        ' Ã â ' => ' &#195; &#226; ',
        'Zugängliche' => 'Zug&#228;ngliche',
        'investigación' => 'investigaci&#243;n',
        'desempeños artísticos' => 'desempe&#241;os art&#237;sticos',
        'Zugängliche investigación' => 'Zug&#228;ngliche investigaci&#243;n',
    ];


    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
        unset($this->obj);
    }



    /**
     * Test XML::encode and XML::decode
     */
    public function testXmlEncodeDecode(): void
    {
        foreach ($this->testStrings as $string_test => $expected)
        {
            $encoded = XML::encode($string_test, true, false);
            //fwrite(STDERR, "Encoding String: $string_test\n");
            //fwrite(STDERR, "                 $encoded\n");
            //fwrite(STDERR, "                 " . htmlspecialchars($encoded) ."\n");
            $this->assertEquals($expected, $encoded, "Unexpected encoding of string: $encoded");
        }
    }

    /**
     * Test XML::encode of null and ''
     */
    public function testXmlEncodeEmpty(): void
    {
        $encoded = XML::encode(null, true, false);
        $this->assertEquals('', $encoded);

        $encoded = XML::encode('', true, false);
        $this->assertEquals('', $encoded);
    }

    /**
     * Test XML::cleanXML of invalid XML
     */
    public function testXmlInvalidCleanXML(): void
    {
        $this->expectException('\Exception');

        $invalidXML = '<Parent><Child><Value>3</Child></Parent>';
        $encoded = XML::cleanXML($invalidXML);
        $this->assertNull($encoded);
    }
}
