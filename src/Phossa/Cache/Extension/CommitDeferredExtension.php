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
 * Autocommit deferred saves to the stoage
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.0
 * @since   1.0.0 added
 */
class CommitDeferredExtension extends ExtensionAbstract
{
    /**
     * Probability, usually 1 - 200
     *
     * @var    int
     * @access protected
     */
    protected $probability  = 100;

    /**
     * Divisor, probability divisor, usually 1000
     *
     * @var    int
     * @type   int
     * @access protected
     */
    protected $divisor      = 1000;

    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        // only to be executed post saveDeferred()
        return [ ExtensionStage::STAGE_POST_DEFER => 90 ];
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
            $cache->log('notice', 'commit deferred in extension');
            $cache->getDriver()->commit();
        }
        return true;
    }
}
