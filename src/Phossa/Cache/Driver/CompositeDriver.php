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
 * CompositeDriver
 *
 * Consists of a front driver and a backend driver
 *
 * @package \Phossa\Shared
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
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
     * @param  array $configs object configs
     * @access public
     */
    public function __construct(array $configs = [])
    {
        // parent constructor
        parent::__construct($configs);

        // front
        if (isset($configs['front'])) {
            $front = $configs['front'];
            if (is_array($front)) {
                $class = $front['className'];
                $this->front = new $class($front);
            }
            if ($front instanceof DriverInterface) {
                $this->front = $front;
            }
        }
        if (!is_object($this->front)) {
            $this->front = new NullDriver();
        }

        // back
        if (isset($configs['back'])) {
            $back = $configs['back'];
            if (is_array($back)) {
                $class = $back['className'];
                $this->back = new $class($back);
            }
            if ($back instanceof DriverInterface) {
                $this->back = $back;
            }
        }
        if (!is_object($this->back)) {
            $this->back  = new NullDriver();
        }

        // default tester
        if (!is_callable($this->tester)) {
            $this->tester = function($item) { return true; };
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(
        /*# string */ $key
    )/*# : string */ {
        if ($this->front->has($key)) return $this->front->get($key);
        return $this->back->get($key);
    }

    /**
     * {inheritDoc}
     */
    public function has(
        /*# string */ $key
    )/*# : int */ {
        $res = $this->front->has($key);
        if ($res) return $res;
        return $this->back->has($key);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()/*# : bool */
    {
        if (!$this->front->clear()) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

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
    public function delete(
        /*# string */ $key
    )/*# : bool */ {
        if (!$this->front->delete($key)) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

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
    public function save(
        CacheItemInterface $item
    )/*# : bool */ {
        // write to both ?
        $both = $this->tester($item);

        // if $both, write to front also
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
    public function saveDeferred(
        CacheItemInterface $item
    )/*# : bool */ {
        // write to both ?
        $both = $this->tester($item);

        // if $both, write to front also
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
        if (!$this->front->commit()) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

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
    public function purge(
        /*# int */ $maxlife
    )/*# : bool */ {
        if (!$this->front->purge($maxlife)) {
            return $this->falseAndSetError(
                $this->front->getError(),
                $this->front->getErrorCode()
            );
        }

        if (!$this->back->purge($maxlife)) {
            return $this->falseAndSetError(
                $this->back->getError(),
                $this->back->getErrorCode()
            );
        }

        return true;
    }
}
