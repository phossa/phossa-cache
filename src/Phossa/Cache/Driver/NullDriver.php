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

/**
 * NullDriver
 *
 * Basically this driver is a blackhole, doing nothing at all. It is the final
 * fallback driver for all other drivers.
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Driver\DriverAbstract
 * @version 1.0.8
 * @since   1.0.0 added
 * @since   1.0.8 added ping()
 */
class NullDriver extends DriverAbstract
{
    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $key)/*# : string */
    {
        return serialize(null);
    }

    /**
     * {inheritDoc}
     */
    public function has(/*# string */ $key)/*# : int */
    {
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(/*# string */ $key)/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function save(CacheItemInterface $item)/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item)/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function commit()/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function purge(/*# int */ $maxlife)/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function ping()/*# : bool */
    {
        return true;
    }
}
