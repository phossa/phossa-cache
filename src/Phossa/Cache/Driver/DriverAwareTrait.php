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
            // alway reset driver
            $this->driver = null;

            // driver config array found
            if (is_array($configs) && isset($configs['className'])) {
                // get driver class
                $class = $configs['className'];
                unset($configs['className']);

                // fix classname if namespace missing
                if (strpos($class, '\\') === false) {
                    $class = __NAMESPACE__ . '\\' . $class;
                }

                // remember fallback driver
                if (isset($configs['fallback'])) {
                    $_fallback = $configs['fallback'];
                    unset($configs['fallback']);
                }

                // construct driver instance
                if (is_a($class, '\Phossa\Cache\Driver\DriverAbstract', true)) {
                    $this->driver = new $class($configs);
                }

            // driver instance found
            } else if ($configs instanceof DriverInterface) {
                $this->driver = $configs;
            }

            // driver not set properly
            if (is_null($this->driver)) throw new \Exception(gettype($configs));

            // driver error found
            if ($this->driver->getErrorCode()) {
                // use fallback driver
                if ($fallback) {
                    // user-defined fallback driver
                    if (isset($_fallback)) {
                        $this->setDriver($_fallback);

                    // the default fallback NullDriver
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
