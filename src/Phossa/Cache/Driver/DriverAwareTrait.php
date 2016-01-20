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

use Phossa\Cache\Message\Message;
use Phossa\Cache\Exception\InvalidArgumentException;

/**
 * Trait implementing DriverAwareInterface
 *
 * @trait
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Driver\DriverAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait DriverAwareTrait
{
    /**
     * cache driver
     *
     * @var    DriverAbstract
     * @access protected
     */
    protected $driver;

    /**
     * {@inheritDoc}
     */
    public function setDriver($configs, $fallback = true)
    {
        try {
            $this->driver = null;
            if (is_array($configs) && isset($configs['className'])) {
                $class = $configs['className'];
                // append namespace if missing
                if (strpos($class, '\\') === false) {
                    $class = __NAMESPACE__ . '\\' . $class;
                }
                if (is_a($class, '\Phossa\Cache\Driver\DriverAbstract', true)) {
                    $this->driver = new $class($configs);
                }
            } else if ($configs instanceof DriverInterface) {
                $this->driver = $configs;
            }

            if (is_null($this->driver)) {
                throw new \Exception(gettype($configs));
            }

            // driver error
            if ($this->driver->getErrorCode()) {
                if ($fallback) {
                    // fallback to user-defined driver
                    if (isset($configs['fallback'])) {
                        $this->setDriver($configs['fallback']);

                    // fallback to NullDriver
                    } else {
                        $this->setDriver(new NullDriver());
                    }
                } else {
                    throw new \Exception(
                        Message::get(
                            Message::CACHE_FAIL_DRIVER,
                            gettype($this->driver)
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            throw new InvalidArgumentException(
                Message::get(Message::CACHE_INVALID_DRIVER, $e->getMessage()),
                Message::CACHE_INVALID_DRIVER
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
