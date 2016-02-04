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

/**
 * DriverAwareInterface
 *
 * @interface
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.0 added
 */
interface DriverAwareInterface
{
    /**
     * Set cache driver.
     *
     * If $fallback is true and ping() is failed for $driver, allow fallback
     * driver or NullDriver
     *
     * @param  DriverInterface $driver the driver object
     * @param  bool $fallback allow fallback driver
     * @return void
     * @throws \Phossa\Cache\Exception\InvalidArgumentException
     *         if driver failed
     * @access public
     * @api
     */
    public function setDriver(
        DriverInterface $driver,
        /*# bool */ $fallback = true
    );

    /**
     * Get cache driver. Always setDriver() first
     *
     * @return DriverInterface
     * @access public
     * @api
     */
    public function getDriver()/*# : DriverInterface */;
}
