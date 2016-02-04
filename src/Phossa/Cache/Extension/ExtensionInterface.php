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
 * ExtensionInterface
 *
 * @interface
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.0 added
 */
interface ExtensionInterface
{
    /**
     * Register extension method with cache pool
     *
     * Returns method names to register with cache pool. Method signature is
     * the following: `functin (CachePoolInterface $cache, ...) {}`
     *
     * <code>
     *     class TaggableExtension extends ExtensionAbstract
     *     {
     *         ...
     *         public function registerMethods() {
     *             // extension class method names
     *             return [ 'clearByTag' ];
     *         }
     *     }
     *
     *     // cache
     *     $cache = new CachePool();
     *
     *     // load extension TaggableExtension
     *     $cache->setExtensions([ new TaggableExtension() ]);
     *
     *     // now we can use 'clearByTag'
     *     $cache->clearByTag('sports');
     * </code>
     *
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
     *    public function stagesHandling()
     *    {
     *        returns [ ExtensionStage::STAGE_ALL => 20 ];
     *    }
     * </code>
     *
     * @return array
     * @access public
     * @api
     */
    public function stagesHandling()/*# : array */;

    /**
     * Make extension callable
     *
     * @param  CachePoolInterface $cache cache pool object
     * @param  string $stage stage name (see ExtensionStage)
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
