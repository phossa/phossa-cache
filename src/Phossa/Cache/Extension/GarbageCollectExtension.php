<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Package
 * @package   Phossa\Cache
 * @author    Hong Zhang <phossa@126.com>
 * @copyright 2015 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Extension;

use Phossa\Cache\CachePoolInterface;
use Phossa\Cache\CacheItemInterface;
use Phossa\Cache\Message\Message;

/**
 * Garbage collection for the cache
 *
 * This extension will remove garbages (stale cached items) from the cache
 * automatically in ExtensionStage::STAGE_POST_GET stage by 0.3% chance.
 *
 * e.g.
 * <code>
 *     $garbage = new Extension\GarbageCollectExtension([
 *            'probability'  => 10,  // change to 1% (10/1000)
 *            'max_lifetime' => 3600 // older than 1 hour is stale
 *     ]);
 * </code>
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.8
 * @since   1.0.0 added
 */
class GarbageCollectExtension extends ExtensionAbstract
{
    /**
     * Probability, usually 1 - 10
     *
     * @var    int
     * @access protected
     */
    protected $probability  = 3;

    /**
     * Divisor, probability divisor, usually 1000
     *
     * @var    int
     * @access protected
     */
    protected $divisor      = 1000;

    /**
     * Max lifetime in second, anything older is considered stale
     *
     * @var    int
     * @access protected
     */
    protected $max_lifetime = 86400;

    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        // lowest priority
        return [ ExtensionStage::STAGE_POST_GET => 95 ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        CachePoolInterface $cache,
        /*# string */ $stage,
        CacheItemInterface $item = null
    )/*# : bool */ {
        if (rand(1, $this->divisor) <= $this->probability) {
            // log message
            $cache->log('info', Message::get(
                Message::CACHE_GARBAGE_COLLECT,
                date("Y-m-d H:i:s")
            ));

            // purge those staled
            $cache->getDriver()->purge($this->max_lifetime);
        }
        
        // always true
        return true;
    }
}
