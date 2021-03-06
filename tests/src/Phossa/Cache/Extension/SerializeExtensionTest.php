<?php
namespace Phossa\Cache\Extension;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-01-20 at 08:29:08.
 */
class SerializeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SerializeExtension
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SerializeExtension;

        // cache
        $this->cache  = new \Phossa\Cache\CachePool();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Phossa\Cache\Extension\SerializeExtension::stagesHandling
     */
    public function testStagesHandling()
    {
        $h = $this->object->stagesHandling();
        $this->assertArrayHasKey(ExtensionStage::STAGE_POST_GET, $h);
        $this->assertArrayHasKey(ExtensionStage::STAGE_PRE_SAVE, $h);
        $this->assertArrayHasKey(ExtensionStage::STAGE_PRE_DEFER,$h);
        $this->assertEquals(3, sizeof($h));
    }

    /**
     * @covers Phossa\Cache\Extension\SerializeExtension::__invoke
     */
    public function testInvoke()
    {
        $ext  = $this->object;
        $val  = 'wow';
        $item = new \Phossa\Cache\CacheItem('test', $this->cache);

        // set value
        $item->set($val);

        // PRE_SAVE
        $ext($this->cache, ExtensionStage::STAGE_PRE_SAVE, $item);
        $this->assertEquals(serialize($val), $item->get());

        // POST_GET
        $item->setHit(true); // simulate a successful get
        $ext($this->cache, ExtensionStage::STAGE_POST_GET, $item);
        $this->assertEquals($val, $item->get());

        // PRE_DEFER
        $ext($this->cache, ExtensionStage::STAGE_PRE_DEFER, $item);
        $this->assertEquals(serialize($val), $item->get());
    }
}
