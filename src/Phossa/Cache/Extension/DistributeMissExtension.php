<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
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
 *     $cache->setExtensions([
 *         [
 *            'className'    => 'DistributeMissExtension',
 *            'distribution' => 30 // -3% to 3%
 *         ]
 *     ]);
 * </code>
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.0
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
        $d = $this->distribution;

        // expire ttl
        $ttl = $item->getExpiration()->getTimestamp() - time();

        // percentage
        $percent = (rand(0, $d * 2) - $d) * 0.001;

        // new expire ttl
        $new_ttl = (int) round($ttl + $ttl * $percent);
        $item->expiresAfter($new_ttl);

        return true;
    }
}
