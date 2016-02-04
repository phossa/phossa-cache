<?php
namespace Phossa\Cache\Extension;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-01-20 at 08:29:10.
 */
class CommitDeferredExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommitDeferredExtension
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CommitDeferredExtension([
            'probability' => 1000
        ]);

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
     * @covers Phossa\Cache\Extension\CommitDeferredExtension::stagesHandling
     */
    public function testStagesHandling()
    {
        $this->assertArrayHasKey(
            ExtensionStage::STAGE_POST_DEFER,
            $this->object->stagesHandling()
        );
    }

    /**
     * @covers Phossa\Cache\Extension\CommitDeferredExtension::__invoke
     */
    public function testInvoke()
    {
        // always true
        $ext = $this->object;

        // always true
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');
        $this->assertTrue($ext(
            $this->cache,
            ExtensionStage::STAGE_POST_DEFER
        ));
    }
}
