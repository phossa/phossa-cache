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
 * CompositeDriver
 *
 * Consists of a front driver and a backend driver and a callable to test
 * if cache item needs to be save to the front driver.
 *
 * <code>
 *     $driver = new CompositeDriver(
 *         $frontDriver,
 *         $backDriver,
 *         [ 'tester'    => function($item) {
 *               // if size > 10k, stores at backend only
 *               if (strlen($item->get()) > 10240) return false;
 *               return true;
 *           }
 *         ]
 *     );
 * </code>
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.0 added
 */
class CompositeDriver extends DriverAbstract
{
    /**
     * Front end driver
     *
     * @var    DriverAbstract
     * @access protected
     */
    protected $front;

    /**
     * Backend driver
     *
     * @var    DriverAbstract
     * @access protected
     */
    protected $back;

    /**
     * callable to test wether write item to both engine.
     *
     * TRUE to write to front & back, FALSE to write to backend only
     *
     *     function(CacheItemInterface $item): bool
     *
     * @var    callable
     * @access protected
     */
    protected $tester;

    /**
     * Construct with configs/settings
     *
     * @param  DriverInterface $frontDriver
     * @param  DriverInterface $backDriver
     * @param  array $configs object configs
     * @access public
     */
    public function __construct(
        DriverInterface $frontDriver,
        DriverInterface $backDriver,
        array $configs = []
    ) {
        // set configs
        parent::__construct($configs);

        // front driver
        if ($frontDriver->ping()) {
            $this->front = $frontDriver;
        } else {
            $this->front = $frontDriver->getFallback();
        }

        // back driver
        if ($backDriver->ping()) {
            $this->back  = $backDriver;
        } else {
            $this->back  = $backDriver->getFallback();
        }

        // default tester, will write item to both front/back cache
        if (!is_callable($this->tester)) {
            $this->tester = function() { return true; };
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $key)/*# : string */
    {
        // try front-end cache first
        if ($this->front->has($key)) return $this->front->get($key);

        // get from backend cache
        return $this->back->get($key);
    }

    /**
     * {inheritDoc}
     */
    public function has(/*# string */ $key)/*# : int */
    {
        // try front-end cache first
        $res = $this->front->has($key);
        if ($res) return $res;

        // try backend cache
        return $this->back->has($key);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()/*# : bool */
    {
        // clear front-end cache
        if (!$this->front->clear()) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

        // clear backend cache
        if (!$this->back->clear()) {
            return $this->falseAndSetError(
                $this->back->getError(),
                $this->back->getErrorCode()
            );
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(/*# string */ $key)/*# : bool */
    {
        // delete from front-end cache
        if (!$this->front->delete($key)) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

        // delete from backend cache
        if (!$this->back->delete($key)) {
            return $this->falseAndSetError(
                $this->back->getError(),
                $this->back->getErrorCode()
            );
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function save(CacheItemInterface $item)/*# : bool */
    {
        // write to both ?
        $both = $this->tester($item);

        // if $both is true, write to front
        if ($both && !$this->front->save($item)) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

        // always write to backend
        if (!$this->back->save($item)) {
            return $this->falseAndSetError(
                $this->back->getError(),
                $this->back->getErrorCode()
            );
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item)/*# : bool */
    {
        // write to both ?
        $both = $this->tester($item);

        // if $both is true, write to front also
        if ($both && !$this->front->saveDeferred($item)) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

        // always write to backend
        if (!$this->back->saveDeferred($item)) {
            return $this->falseAndSetError(
                $this->back->getError(),
                $this->back->getErrorCode()
            );
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function commit()/*# : bool */
    {
        // commit to front-end cache
        if (!$this->front->commit()) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

        // commit to backend cache
        if (!$this->back->commit()) {
            return $this->falseAndSetError(
                $this->back->getError(),
                $this->back->getErrorCode()
            );
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function purge(/*# int */ $maxlife)/*# : bool */
    {
        // purge front-end cache
        if (!$this->front->purge($maxlife)) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

        // purge backend cache
        if (!$this->back->purge($maxlife)) {
            return $this->falseAndSetError(
                $this->back->getError(),
                $this->back->getErrorCode()
            );
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function ping()/*# : bool */
    {
        return $this->front->ping() && $this->back->ping();
    }
}
