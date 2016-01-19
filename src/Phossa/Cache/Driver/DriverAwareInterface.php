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

/**
 * DriverAwareInterface
 *
 * @interface
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface DriverAwareInterface
{
    /**
     * Set driver either with an driver object or driver config array
     *
     * @param  array|DriverInterface $configs driver configs or driver
     * @return void
     * @throws \Phossa\Cache\Exception\InvalidArgumentException
     *         if not the right driver or driver config
     * @access public
     * @api
     */
    public function setDriver($configs);

    /**
     * Get the driver, have to setDriver() first
     *
     * @param  void
     * @return DriverInterface
     * @access public
     * @api
     */
    public function getDriver()/*# : DriverInterface */;
}
