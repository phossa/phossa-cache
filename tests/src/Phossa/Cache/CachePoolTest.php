<?php
namespace Phossa\Cache;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-01-20 at 08:31:45.
 */
class CachePoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CachePool
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // driver
        $driver = new Driver\FilesystemDriver([
            'hash_level'    => 1,
            'file_pref'     => 'cache.',
            'file_suff'     => '.txt'
        ]);

        $this->object = new CachePool($driver);
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
     * @covers Phossa\Cache\CachePool::getItem
     */
    public function testGetItem()
    {
        $item = $this->object->getItem('test');
        $this->assertTrue($item instanceof CacheItem);
    }

    /**
     * @covers Phossa\Cache\CachePool::getItems
     */
    public function testGetItems()
    {
        $items = $this->object->getItems(['test', 'test2']);
        $this->assertTrue(is_array($items));
        $this->assertTrue($items['test'] instanceof CacheItem);
        $this->assertTrue($items['test2'] instanceof CacheItem);
        $this->assertEquals(2, sizeof($items));
    }

    /**
     * @covers Phossa\Cache\CachePool::hasItem
     */
    public function testHasItem()
    {
        $this->assertTrue($this->object->clear());
        $this->assertFalse($this->object->hasItem('test'));

        $item = $this->object->getItem('test');
        $item->set('wow');
        $this->object->save($item);
        $this->assertTrue($this->object->hasItem('test'));
    }

    /**
     * @covers Phossa\Cache\CachePool::clear
     */
    public function testClear()
    {
        $item = $this->object->getItem('test');
        $item->set('wow');
        $this->object->save($item);
        $this->assertTrue($this->object->hasItem('test'));
        $this->assertTrue($this->object->clear());
        $this->assertFalse($this->object->hasItem('test'));
    }

    /**
     * @covers Phossa\Cache\CachePool::deleteItem
     */
    public function testDeleteItem()
    {
        $item = $this->object->getItem('test');
        $item->set('wow');
        $this->object->save($item);
        $this->assertTrue($this->object->hasItem('test'));

        $this->assertTrue($this->object->deleteItem('test'));
        $this->assertFalse($this->object->hasItem('test'));
    }

    /**
     * @covers Phossa\Cache\CachePool::deleteItems
     */
    public function testDeleteItems()
    {
        $this->assertTrue($this->object->clear());

        $item = $this->object->getItem('test');
        $item->set('wow');
        $this->object->save($item);

        $item2= $this->object->getItem('test2');
        $item2->set('wow');
        $this->object->save($item2);

        $this->assertTrue($this->object->hasItem('test'));
        $this->assertTrue($this->object->hasItem('test2'));

        $this->assertTrue($this->object->deleteItems(['test', 'test2']));
        $this->assertFalse($this->object->hasItem('test'));
        $this->assertFalse($this->object->hasItem('test2'));

    }

    /**
     * @covers Phossa\Cache\CachePool::save
     */
    public function testSave()
    {
        $this->assertTrue($this->object->clear());
        $this->assertFalse($this->object->hasItem('test'));

        $item = $this->object->getItem('test');
        $item->set('wow');
        $this->object->save($item);
        $this->assertTrue($this->object->hasItem('test'));
    }

    /**
     * @covers Phossa\Cache\CachePool::saveDeferred
     */
    public function testSaveDeferred()
    {
        $this->assertTrue($this->object->clear());
        $this->assertFalse($this->object->hasItem('test'));

        $item = $this->object->getItem('test');
        $item->set('wow');
        $this->object->saveDeferred($item);
        $this->assertTrue($this->object->hasItem('test'));
    }

    /**
     * @covers Phossa\Cache\CachePool::commit
     */
    public function testCommit()
    {
        $this->assertTrue($this->object->commit());
    }

    /**
     * @covers Phossa\Cache\CachePool::getError
     */
    public function testGetError()
    {
        $this->object->setError('error', 10);
        $this->assertEquals('error', $this->object->getError());
    }

    /**
     * @covers Phossa\Cache\CachePool::getErrorCode
     */
    public function testGetErrorCode()
    {
        $this->object->setError('error', 10);
        $this->assertEquals(10, $this->object->getErrorCode());
    }

    /**
     * @covers Phossa\Cache\CachePool::setError
     */
    public function testSetError()
    {
        $this->object->setError('error2', 10);
        $this->assertEquals('error2', $this->object->getError());
    }

    /**
     * @covers Phossa\Cache\CachePool::setDriver
     */
    public function testSetDriver()
    {
        $this->object->setDriver(new Driver\NullDriver());
        $this->assertTrue($this->object->getDriver() instanceof Driver\NullDriver);
    }

    /**
     * @covers Phossa\Cache\CachePool::getDriver
     */
    public function testGetDriver()
    {
        $this->object->setDriver(new Driver\NullDriver());
        $this->assertTrue($this->object->getDriver() instanceof Driver\NullDriver);
    }

    /**
     * @covers Phossa\Cache\CachePool::addExtension
     */
    public function testAddExtension()
    {
        $this->object->addExtension(new Extension\CommitDeferredExtension);
    }

    /**
     * @covers Phossa\Cache\CachePool::runExtensions
     */
    public function testRunExtensions()
    {
        $this->object->addExtension(
            new Extension\BypassExtension([
                'message' => true
            ])
        );

        // fake to trigger bypass extension
        $_REQUEST['nocache'] = 1;

        $this->object->runExtensions(
            Extension\ExtensionStage::STAGE_PRE_HAS
        );

        $this->assertEquals(
            "Bypass the cache",
            $this->object->getError()
        );
        $this->assertEquals(
            Message\Message::CACHE_BYPASS_EXT,
            $this->object->getErrorCode()
        );
    }

    /**
     * test EncryptExtension
     *
     * @covers Phossa\Cache\CachePool::runExtensions
     */
    public function testRunExtensions2()
    {
        $cache = $this->object;
        // test encrypt extension
        $cache->addExtension(new Extension\EncryptExtension());

        $key = 'testEncrypt';

        // save item
        $item = $cache->getItem($key);
        $item->set('wow');
        $cache->save($item);

        // try get
        $item2 = $cache->getItem($key);
        $this->assertEquals('wow', $item2->get());

        // clear extensions
        $cache->clearExtensions();

        // failed to get
        $item3 = $cache->getItem($key);
        $this->assertFalse($cache->hasError());
        $item3->get();
        $this->assertTrue($cache->hasError());
    }

    /**
     * test TaggableExtension
     *
     * @covers Phossa\Cache\CachePool::runExtensions
     */
    public function testRunExtensions3()
    {
        $cache = $this->object;

        // test TaggableExtension
        $cache->addExtension(new Extension\TaggableExtension());

        $key = 'taggable';

        // save item
        $item = $cache->getItem($key);
        $item->set('wow');
        $item->setTags(['tagA', 'tagB']);
        $cache->save($item);

        // try get
        $item2 = $cache->getItem($key);
        $this->assertEquals('wow', $item2->get());

        // clear by tag
        $cache->clearByTag('tagA');
        $cache->clearByTag('tagB');

        // a miss
        $item3 = $cache->getItem($key);
        $this->assertFalse($item3->isHit());
    }
}
