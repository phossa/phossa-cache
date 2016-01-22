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
 * NullDriver
 *
 * Basically this driver is a blackhole, doing nothing at all. It is the final
 * fallback driver for all other drivers.
 *
 * <code>
 *     $cache = new \Phossa\Cache\CachePool([
 *         'className'     => 'MemcacheDriver',
 *         'server'        => [ '127.0.0.1', 11211 ],
 *         'fallback'      => [ 'className' => 'NullDriver' ]
 *     ]);
 * </code>
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class NullDriver extends DriverAbstract
{
    /**
     * {@inheritDoc}
     */
    public function get(
        /*# string */ $key
    )/*# : string */ {
        return serialize(NULL);
    }

    /**
     * {inheritDoc}
     */
    public function has(
        /*# string */ $key
    )/*# : int */ {
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
    public function delete(
        /*# string */ $key
    )/*# : bool */ {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function save(
        CacheItemInterface $item
    )/*# : bool */ {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(
        CacheItemInterface $item
    )/*# : bool */ {
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
    public function purge(
        /*# int */ $maxlife
    )/*# : bool */ {
        return true;
    }
}
