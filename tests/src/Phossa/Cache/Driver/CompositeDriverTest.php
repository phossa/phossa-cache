<?php
namespace Phossa\Cache\Driver;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-04 at 11:42:07.
 */
class CompositeDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompositeDriver
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $front = new FilesystemDriver([
            'hash_level'    => 1,
            'file_pref'     => 'b.',
            'dir_root'      => 'C:\\Temp\\Cache',
        ]);
        $back  = new FilesystemDriver();

        $this->object = new CompositeDriver($front, $back, [
            'tester' => function(\Phossa\Cache\CacheItem $item) {
                $val = $item->get();
                // stores at back only
                if (strlen($val) > 10) return false;
                return true;
            }
        ]);

        $cache  = new \Phossa\Cache\CachePool($this->object);
        $this->cache = $cache;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object->clear();
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::get
     */
    public function testGet()
    {
        $cache  = $this->cache;
        $driver = $this->object;

        // save
        $val  = 'wow';
        $item = $cache->getItem('test');
        $item->set($val);
        $cache->save($item);

        // serialized
        $this->assertEquals(serialize($val), $driver->get('test'));

        // thru item
        $item2 = $cache->getItem('test');
        $this->assertEquals($val, $item2->get('test'));
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::frontHas
     * @covers Phossa\Cache\Driver\CompositeDriver::backHas
     */
    public function testFrontHas()
    {
        $cache  = $this->cache;
        $driver = $this->object;

        // save
        $val  = 'wowwwwwwwwwwwwwwwwwwwwwww';
        $item = $cache->getItem('testX');
        $item->set($val);
        $cache->save($item);

        // strlen > 10, front NO
        $this->assertFalse($driver->frontHas('testX') > 0);

        // back YES
        $this->assertTrue($driver->backHas('testX') > 0);
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::has
     */
    public function testHas()
    {
        $cache  = $this->cache;
        $driver = $this->object;

        // save
        $val  = 'wow';
        $item = $cache->getItem('test2');
        $item->set($val);
        $item->expiresAfter(300);
        $cache->save($item);

        // not found
        $this->assertEquals(0, $driver->has('test1'));

        // found and return time-stamp
        $has = $driver->has('test2');
        $this->assertTrue(is_int($has));
        $this->assertGreaterThan(time(), $has);
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::clear
     */
    public function testClear()
    {
        $this->assertTrue($this->object->clear());
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::delete
     */
    public function testDelete()
    {
        $cache  = $this->cache;
        $driver = $this->object;

        // save
        $val  = 'wow';
        $item = $cache->getItem('hz/test4');
        $item->set($val);
        $cache->save($item);

        $this->assertTrue($driver->frontHas('hz/test4') > 0);
        $this->assertTrue($driver->backHas('hz/test4') > 0);

        // delete
        $driver->delete('hz/test4');

        $this->assertFalse($driver->frontHas('hz/test4') > 0);
        $this->assertFalse($driver->backHas('hz/test4') > 0);
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::save
     */
    public function testSave()
    {
        $cache  = $this->cache;
        $driver = $this->object;

        // save
        $val  = 'wow3wwwwwwwwwwwwwww';
        $item = $cache->getItem('bingo/test5');
        $item->set($val);
        $item->expiresAfter(1);
        $driver->save($item);

        // front NO
        $this->assertFalse($driver->frontHas('bingo/test5') > 0);

        // get from back
        $this->assertEquals($val, $driver->get('bingo/test5'));
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::saveDeferred
     */
    public function testSaveDeferred()
    {
        $cache  = $this->cache;
        $driver = $this->object;

        // save
        $val  = 'wow3wwwwwwwwwwwwwww';
        $item = $cache->getItem('bingo/test8');
        $item->set($val);
        $item->expiresAfter(1);
        $driver->saveDeferred($item);

        // front NO
        $this->assertFalse($driver->frontHas('bingo/test8') > 0);

        // get from back
        $this->assertEquals($val, $driver->get('bingo/test8'));
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::commit
     */
    public function testCommit()
    {
        $this->assertTrue($this->object->commit());
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::purge
     */
    public function testPurge()
    {
        $cache  = $this->cache;
        $driver = $this->object;

        // save
        $val  = 'wow3wwwwwwwwwwwwwww';
        $item = $cache->getItem('wow/test5');
        $item->set($val);
        $item->expiresAfter(1);
        $driver->save($item);

        sleep(3);
        $this->assertTrue($driver->purge(1));
    }

    /**
     * @covers Phossa\Cache\Driver\CompositeDriver::ping
     */
    public function testPing()
    {
        $this->assertTrue($this->object->ping());
    }
}
