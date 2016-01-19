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
     * @var    DriverInterface
     * @access protected
     */
    protected $driver;

    /**
     * {@inheritDoc}
     */
    public function setDriver($configs)
    {
        try {
            $this->driver = null;
            if (is_array($configs) &&
                isset($configs['className']) &&
                is_a($configs['className'],
                    '\Phossa\Cache\Driver\DriverAbstract',
                    true)
            ) {
                $class = $configs['className'];
                $this->driver = new $class($configs);
            } else if ($configs instanceof DriverInterface) {
                $this->driver = $configs;
            }

            if (is_null($this->driver)) {
                throw new \Exception(gettype($configs));
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
    public function getDriver()/*# : DriverInterface */
    {
        return $this->driver;
    }
}
