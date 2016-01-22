<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache;

/**
 * CacheItemInterface
 *
 * @interface
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Psr\Cache\CacheItemInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface CacheItemInterface extends \Psr\Cache\CacheItemInterface
{
    /**
     * Returns the expiration time of a not-yet-expired cache item.
     *
     * If this cache item is a Cache Miss, this method MAY return the time at
     * which the item expired or the current time if that is not available.
     *
     * @return \DateTime The timestamp at which this cache item will expire.
     */
    public function getExpiration()/*# : \DateTime */;

    /**
     * Set hit status explicitly
     *
     * @param  bool $status hit status
     * @return bool current hit status
     * @access public
     * @api
     */
    public function setHit(/*# bool */ $status)/*# : bool */;

    /**
     * Set tags to this item
     *
     * @param  string[] $tags tags array
     * @return void
     * @access public
     * @api
     */
    public function setTags(array $tags);

    /**
     * Get item tags
     *
     * @param  void
     * @param  string[]
     * @access public
     * @api
     */
    public function getTags()/*# : array */;
}
