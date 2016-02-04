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
 * Abstract driver class implementing DriverInterface
 *
 * @abstract
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Driver\DriverInterface
 * @version 1.0.8
 * @since   1.0.0 added
 * @since   1.0.8 added getFallback()/setFallback()
 */
abstract class DriverAbstract implements DriverInterface
{
    use \Phossa\Cache\Misc\ErrorAwareTrait,
        \Phossa\Shared\Pattern\SetPropertiesTrait;

    /**
     * Constructor
     *
     * @param  array $configs config array
     * @access public
     * @api
     */
    public function __construct(array $configs = [])
    {
        $this->setProperties($configs);
    }

    /**
     * fallback driver
     *
     * @var    DriverInterface
     * @access protected
     */
    protected $fallback = null;

    /**
     * {@inheritDoc}
     */
    public function getFallback()/*# : DriverInterface */
    {
        if (is_null($this->fallback) || !$this->fallback->ping()) {
            $this->fallback = new NullDriver();
        }
        return $this->fallback;
    }

    /**
     * {@inheritDoc}
     */
    public function setFallback(DriverInterface $driver)
    {
        $this->fallback = $driver;
    }
}
