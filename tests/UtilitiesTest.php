<?php declare(strict_types=1);

use QuickBooksPhpDevKit_UnitTesting\XmlBaseTest;
use QuickBooksPhpDevKit\PackageInfo;
use QuickBooksPhpDevKit\Utilities;

final class UtilitiesTest extends XmlBaseTest
{
    protected $obj;

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }



    /**
     * Test IP checks
     */
    public function testAllowedIp(): void
    {
        $this->assertEquals(Utilities::checkRemoteAddress('192.168.1.25', [], []), true);
        $this->assertEquals(Utilities::checkRemoteAddress('192.168.1.25', ['192.168.1.25'], []), true);
        $this->assertEquals(Utilities::checkRemoteAddress('192.168.1.25', [], ['192.168.1.25']), false);
        $this->assertEquals(Utilities::checkRemoteAddress('192.168.1.25', ['192.168.1.0/24'], []), true);

        $this->assertEquals(Utilities::checkRemoteAddress('192.168.1.25', ['192.168.1.25'], ['192.168.1.25']), false);
        $this->assertEquals(Utilities::checkRemoteAddress('192.168.1.25', ['192.168.1.0/24'], ['192.168.1.25']), false);
        $this->assertEquals(Utilities::checkRemoteAddress('192.168.1.25', ['192.168.1.0/24', '192.168.1.100'], ['192.168.1.25', '100.100.0.0/16']), false);

        $this->assertEquals(Utilities::checkRemoteAddress('::1', [], []), true);
        $this->assertEquals(Utilities::checkRemoteAddress('::1', ['::1'], []), true);
        $this->assertEquals(Utilities::checkRemoteAddress('::1', ['::1'], ['::1']), false);
        $this->assertEquals(Utilities::checkRemoteAddress('::1', [], ['::1']), false);
    }


    /**
     * Test Action To Object
     */
    public function testActionToObject(): void
    {
        $objects = Utilities::listObjects();
        //fwrite(STDERR, "Objects:\n".print_r($objects,true));

        $actions = Utilities::listActions(null, true);
        //fwrite(STDERR, "\nActions:\n".print_r($actions,true)."\n\n");

        foreach ($actions as $action) {
            $qbAction = PackageInfo::Actions[$action];

            $object = Utilities::actionToObject($qbAction);

            //fwrite(STDERR, "$action : $object\n");
            $this->assertNotNull($object);
        }
/*
$times = 1;
$tstart = microtime(true);
for ($i = 0; $i < $times; $i++) {
        foreach ($actions as $action) {
            $qbAction = PackageInfo::Actions[$action];

            //$object = Utilities::actionToObject_old($qbAction);
            $object = Utilities::actionToObject($qbAction);

            fwrite(STDERR, "$action : $object\n");
            //if ($object !== $object2)
            //{
            //    fwrite(STDERR, "\n$qbAction : $object : $object2\n");
            //}
            $this->assertNotNull($object);
        }
}
$tend_new = microtime(true) - $tstart;

$tstart = microtime(true);
for ($i = 0; $i < $times; $i++) {
        foreach ($actions as $action) {
            //fwrite(STDERR, "$action : ");
            $qbAction = PackageInfo::Actions[$action];

            $object = Utilities::actionToObject_old($qbAction);
            //$object2 = Utilities::actionToObject($qbAction);

            //if ($object !== $object2)
            //{
            //    fwrite(STDERR, "\n$qbAction : $object : $object2\n");
            //}
            //$this->assertNotNull($object);
        }
}
$tend_old = microtime(true) - $tstart;

fwrite(STDERR, "\n\nTime to Complete $times iterations:\nOld: $tend_old\nNew: $tend_new\n\n");
        $this->assertEquals(true, true);
*/
    }

    /**
     * Test ParseDSN invalid DSN
     */
    public function testParseDSN_invalidTypeException(): void
    {
        $this->expectException('\Exception');

        // Test DSN that is not a string or array
        $this->assertNull(Utilities::parseDSN(new StdClass()));
    }

    /**
     * Test ParseDSN invalid DSN
     */
    public function testParseDSN_invalid(): void
    {
        // Test no backend or database specified
        $dsn = [
            'backend' => 'sqlite3',
            'database' => '',
        ];
        $this->assertNull(Utilities::parseDSN($dsn));

        $dsn = [
            'backend' => '',
            'database' => 'quickbooks_database',
        ];
        $this->assertNull(Utilities::parseDSN($dsn));
    }

    /**
     * Test ParseDSN Partinvalid DSN
     */
    public function testParseDSN_part(): void
    {
        // Test retrieving a DSN part
        $dsn = [
            'backend' => 'mysqli',
            'database' => ':memory:',
            'username' => 'mysql_user',
        ];
        $this->assertSame('mysql_user', Utilities::parseDSN($dsn, [], 'username'));
        $this->assertSame('mysql_user', Utilities::parseDSN($dsn, [], 'user'));

        // Test retrieving a non-existent part
        $this->assertNull(Utilities::parseDSN($dsn, [], 'non-existent'));
        $this->assertNull(Utilities::parseDSN($dsn, [], 'host'));
    }
}
