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
use Phossa\Cache\Message\Message;

/**
 * Autocommit deferred saves to the stoage if driver supports saveDeferred
 *
 * This extension will be executed at stage ExtensionStage::STAGE_POST_DEFER
 * which happens right after saveDeferred. By 100/1000 (10%) chance, it will
 * commit deferred save to the cache.
 *
 * e.g.
 * <code>
 *     $cache->setExtensions([
 *         // change percentage to 20% (200/1000)
 *         [ 'className' => 'CommitDeferredExtension', 'probability' => 200 ]
 *     ]);
 * </code>
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
        // 100/1000 (10%) chances to commit
        if (rand(1, $this->divisor) <= $this->probability) {
            // log message
            $cache->log('notice', Message::get(Message::CACHE_COMMIT_DEFERRED));

            // commit deferred
            $cache->getDriver()->commit();
        }
        // always return true
        return true;
    }
}
