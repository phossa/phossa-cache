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

namespace Phossa\Cache;

/**
 * CacheItemInterface
 *
 * @interface
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Psr\Cache\CacheItemInterface
 * @see     \Phossa\Cache\Misc\TaggableItemInterface
 * @version 1.0.8
 * @since   1.0.0 added
 * @since   1.0.8 extends TaggableItemInterface
 */
interface CacheItemInterface extends
    Misc\TaggableItemInterface,
    \Psr\Cache\CacheItemInterface
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
}
