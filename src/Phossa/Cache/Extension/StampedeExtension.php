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
 * Stampede protected for the cache
 *
 * If expires in 600 seconds, and probability hits. mark $item as no hit to
 * force regenerate content.
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.0
 * @since   1.0.0 added
 */
class StampedeExtension extends ExtensionAbstract
{
    /**
     * Probability, usually 1 - 100
     *
     * @var    int
     * @access protected
     */
    protected $probability  = 50;

    /**
     * Divisor, probability divisor, usually 1000
     *
     * @var    int
     * @access protected
     */
    protected $divisor      = 1000;

    /**
     * time left in seconds
     *
     * @var    int
     * @access protected
     */
    protected $time_left    = 600;

    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        return [ ExtensionStage::STAGE_POST_HAS => 50 ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        CachePoolInterface $cache,
        /*# string */ $stage,
        CacheItemInterface $item = null
    )/*# : bool */ {
        if ($item->isHit()) {
            // time left
            $left = $item->getExpiration()->getTimestamp() - time();

            if ($left < $this->time_left &&
                rand(1, $this->divisor) <= $this->probability
            ) {
                // mark it as no hit
                $item->setHit(false);
            }
        }
        return true;
    }
}
