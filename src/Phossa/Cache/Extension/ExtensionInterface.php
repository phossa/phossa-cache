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
 * ExtensionInterface
 *
 * @interface
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ExtensionInterface
{
    /**
     * Register extension method with cache pool
     *
     * Returns method names to register with cache pool
     *
     * @param  void
     * @return string[]
     * @access public
     * @api
     */
    public function registerMethods()/*# : array */;

    /**
     * Returns stages (with priority) handling in the format of array
     *
     * priority 0 - 100, with 0 is the highest (executed first), 100 is the
     * lowest (executed last)
     *
     * e.g.
     * <code>
     *    returns [ ExtensionStage::STAGE_ALL => 20 ];
     * </code>
     *
     * @param  void
     * @return array
     * @access public
     * @api
     */
    public function stagesHandling()/*# : array */;

    /**
     * Make extension callable
     *
     * @param  CachePoolInterface $cache cache object
     * @param  string $stage stage name
     * @param  CacheItemInterface $item (optional) if any
     * @return bool
     * @access public
     * @api
     */
    public function __invoke(
        CachePoolInterface $cache,
        /*# string */ $stage,
        CacheItemInterface $item = null
    )/*# : bool */;
}
