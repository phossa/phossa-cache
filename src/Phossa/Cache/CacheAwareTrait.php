<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Library
 * @package   Phossa\Cache
 * @copyright 2015 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Implementation of CacheAwareInterface
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     CacheAwareInterface
 * @version 1.0.11
 * @since   1.0.11 added
 */
trait CacheAwareTrait
{
    /**
     * the cache pool
     *
     * @var    CacheItemPoolInterface
     * @access protected
     */
    protected $cache;

    /**
     * Sets a cache pool instance on the object
     *
     * @param  CacheItemPoolInterface $cache
     * @return null
     */
    public function setCachePool(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }
}
