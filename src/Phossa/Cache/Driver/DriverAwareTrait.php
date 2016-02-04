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

use Phossa\Cache\Message\Message;
use Phossa\Cache\Exception\InvalidArgumentException;

/**
 * Trait implementing DriverAwareInterface
 *
 * @trait
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Driver\DriverAwareInterface
 * @version 1.0.8
 * @since   1.0.0 added
 */
trait DriverAwareTrait
{
    /**
     * cache driver
     *
     * @var    DriverInterface
     * @access protected
     */
    protected $driver;

    /**
     * {@inheritDoc}
     */
    public function setDriver(
        DriverInterface $driver,
        /*# bool */ $fallback = true
    ) {
        // ping first
        if ($driver->ping()) {
            $this->driver = $driver;
            return;
        }

        // fallback
        if ($fallback) {
            // set to fallback driver
            $this->driver = $driver->getFallback();

            // issue warning
            trigger_error(
                Message::get(
                    Message::CACHE_FALLBACK_DRIVER,
                    get_class($driver),
                    get_class($this->driver)
                ),
                E_USER_WARNING
            );
        } else {
            throw new InvalidArgumentException(
                Message::get(Message::CACHE_FAIL_DRIVER, get_class($driver)),
                Message::CACHE_FAIL_DRIVER
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDriver()/*# : DriverAbstract */
    {
        return $this->driver;
    }
}
