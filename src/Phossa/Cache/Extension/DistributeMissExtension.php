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

/**
 * Change expiration time by -5% to 5% to evenly distribute cache miss
 *
 * This extension will be executed in stage ExtensionStage::STAGE_POST_SAVE
 *
 * <code>
 *     $distribute = new Extension\DistributeMissExtension([
 *            'distribution' => 30 // -3% to 3%
 *     ]);
 *
 *     // enable this ext
 *     $cache->setExtension($distribute);
 * </code>
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.8
 * @since   1.0.0 added
 */
class DistributeMissExtension extends ExtensionAbstract
{
    /**
     * item expiration time distribution 5% (50/1000)
     *
     * @var    int
     * @access protected
     */
    protected $distribution = 50;

    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        // before save
        return [
            ExtensionStage::STAGE_PRE_SAVE     => 20,
            ExtensionStage::STAGE_PRE_DEFER    => 20
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        CachePoolInterface $cache,
        /*# string */ $stage,
        CacheItemInterface $item = null
    )/*# : bool */ {
        // distribution
        $dis = $this->distribution;

        if ($item instanceof CacheItemInterface) {
            // expire ttl
            $ttl = $item->getExpiration()->getTimestamp() - time();

            // percentage
            $percent = (rand(0, $dis * 2) - $dis) * 0.001;

            // new expire ttl
            $new_ttl = (int) round($ttl + $ttl * $percent);
            $item->expiresAfter($new_ttl);
        }

        return true;
    }
}
