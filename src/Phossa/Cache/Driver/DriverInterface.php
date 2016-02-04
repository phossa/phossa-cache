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

namespace Phossa\Cache\Driver;

use Phossa\Cache\CacheItemInterface;
use Phossa\Cache\Misc\ErrorAwareInterface;

/**
 * DriverInterface
 *
 * @interface
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.0 added
 * @since   1.0.8 added ping()/getFallback()/setFallback()
 */
interface DriverInterface extends ErrorAwareInterface
{
    /**
     * Get data from storage base on the key
     *
     * ALWAYS CALL has() before get() !!!
     *
     * @param  string $key the key
     * @return string
     * @access public
     * @api
     */
    public function get(/*# string */ $key)/*# : string */;

    /**
     * The expiration UNIX timestamp, 0 if not found
     *
     * @param  string $key the key
     * @return int
     * @access public
     * @api
     */
    public function has(/*# string */ $key)/*# : int */;

    /**
     * Clear the cache pool. return false on error
     *
     * @return bool
     * @access public
     * @api
     */
    public function clear()/*# : bool */;

    /**
     * Delete item from the pool. return false on error
     *
     * @param  string $key the key
     * @return bool
     * @access public
     * @api
     */
    public function delete(/*# string */ $key)/*# : bool */;

    /**
     * Save item to the pool. return false on error
     *
     * @param  CacheItemInterface $item
     * @return bool
     * @access public
     * @api
     */
    public function save(CacheItemInterface $item)/*# : bool */;

    /**
     * Save item (deferred) to the pool. return false on error
     *
     * @param  CacheItemInterface $item
     * @return bool
     * @access public
     * @api
     */
    public function saveDeferred(CacheItemInterface $item)/*# : bool */;

    /**
     * Commit deferred to the pool. return false on error.
     *
     * @return bool
     * @access public
     * @api
     */
    public function commit()/*# : bool */;

    /**
     * Purge items older than this seconds
     *
     * @param  int $maxlife max life in seconds, 0 for clear all
     * @return bool
     * @access public
     * @api
     */
    public function purge(/*# int */ $maxlife)/*# : bool */;

    /**
     * Ping driver, false means driver failed
     *
     * @return bool
     * @access public
     * @api
     */
    public function ping()/*# : bool */;

    /**
     * Get the fallback driver.
     *
     * If not set or fallback driver not responding, return NullDriver
     *
     * @return DriverInterface
     * @access public
     * @api
     */
    public function getFallback()/*# : DriverInterface */;

    /**
     * Set the fallback driver
     *
     * @param  DriverInterface $driver the fallback driver
     * @return void
     * @access public
     * @api
     */
    public function setFallback(DriverInterface $driver);
}
