<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Driver;

use Phossa\Cache\CacheItemInterface;

/**
 * DriverInterface
 *
 * @interface
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface DriverInterface
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
    public function get(
        /*# string */ $key
    )/*# : string */;

    /**
     * The expiration UNIX timestamp, 0 if not found
     *
     * @param  string $key the key
     * @return int
     * @access public
     * @api
     */
    public function has(
        /*# string */ $key
    )/*# : int */;

    /**
     * Clear the cache pool. return false on error
     *
     * @param  void
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
    public function delete(
        /*# string */ $key
    )/*# : bool */;

    /**
     * Save item to the pool. return false on error
     *
     * @param  CacheItemInterface $item
     * @return bool
     * @access public
     * @api
     */
    public function save(
        CacheItemInterface $item
    )/*# : bool */;

    /**
     * Save item (deferred) to the pool. return false on error
     *
     * @param  CacheItemInterface $item
     * @return bool
     * @access public
     * @api
     */
    public function saveDeferred(
        CacheItemInterface $item
    )/*# : bool */;

    /**
     * Commit deferred to the pool. return false on error.
     *
     * @param  void
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
    public function purge(
        /*# int */ $maxlife
    )/*# : bool */;
}
