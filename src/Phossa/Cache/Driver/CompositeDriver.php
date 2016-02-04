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
     * @var    DriverInterface
     * @access protected
     */
    protected $front;

    /**
     * Backend driver
     *
     * @var    DriverInterface
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

        // front driver, may use fallback
        if ($frontDriver->ping()) {
            $this->front = $frontDriver;
        } else {
            // set error
            $this->setError(
                $frontDriver->getError(),
                $frontDriver->getErrorCode()
            );
            $this->front = $frontDriver->getFallback();
        }

        // back driver, may use fallback
        if ($backDriver->ping()) {
            $this->back  = $backDriver;
        } else {
            // set error
            $this->setError(
                $backDriver->getError(),
                $backDriver->getErrorCode()
            );
            $this->back  = $backDriver->getFallback();
        }

        // default tester, will write item to both front/back cache
        if (!is_callable($this->tester)) {
            $this->tester = function () {
                return true;
            };
        }
    }

    /**
     * Either end get ok is ok
     *
     * {@inheritDoc}
     */
    public function get(/*# string */ $key)/*# : string */
    {
        // try front-end cache first
        if ($this->frontHas($key)) {
            return $this->front->get($key);
        }

        // get from backend cache
        return $this->back->get($key);
    }

    /**
     * Test front-end has
     *
     * @param  string $key the item key
     * @return int
     * @access public
     * @api
     */
    public function frontHas(/*# string */ $key)/*# : int */
    {
        return $this->front->has($key);
    }

    /**
     * Test backend has
     *
     * @param  string $key the item key
     * @return int
     * @access public
     * @api
     */
    public function backHas(/*# string */ $key)/*# : int */
    {
        return $this->back->has($key);
    }

    /**
     * Either end has is ok
     *
     * {inheritDoc}
     */
    public function has(/*# string */ $key)/*# : int */
    {
        // try front-end cache first
        if (($res = $this->frontHas($key))) {
            return $res;
        }

        // try backend cache
        return $this->backHas($key);
    }

    /**
     * Need both ends clear ok
     *
     * {@inheritDoc}
     */
    public function clear()/*# : bool */
    {
        $ends = [ $this->front, $this->back ];
        foreach ($ends as $end) {
            if (!$end->clear()) {
                return $this->falseAndSetError(
                    $end->getError(),
                    $end->getErrorCode()
                );
            }
        }
        return $this->trueAndFlushError();
    }

    /**
     * Need both ends delete ok
     *
     * {@inheritDoc}
     */
    public function delete(/*# string */ $key)/*# : bool */
    {
        $ends = [ $this->front, $this->back ];
        foreach ($ends as $end) {
            if (!$end->delete($key)) {
                return $this->falseAndSetError(
                    $end->getError(),
                    $end->getErrorCode()
                );
            }
        }
        return $this->trueAndFlushError();
    }

    /**
     * {@inheritDoc}
     */
    public function save(CacheItemInterface $item)/*# : bool */
    {
        return $this->protectedSave($item, 'save');
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item)/*# : bool */
    {
        return $this->protectedSave($item, 'saveDeferred');
    }

    /**
     * One end commit ok is ok
     *
     * {@inheritDoc}
     */
    public function commit()/*# : bool */
    {
        $ends = [ $this->front, $this->back ];
        $res  = false;

        foreach ($ends as $end) {
            // commit failed, set error
            if (!$end->commit()) {
                $this->setError(
                    $end->getError(),
                    $end->getErrorCode()
                );

            // one commit is ok, then all ok
            } else {
                $res = true;
            }
        }

        return $res;
    }

    /**
     * Need both ends purge ok
     *
     * {@inheritDoc}
     */
    public function purge(/*# int */ $maxlife)/*# : bool */
    {
        $ends = [ $this->front, $this->back ];
        foreach ($ends as $end) {
            if (!$end->purge($maxlife)) {
                return $this->falseAndSetError(
                    $end->getError(),
                    $end->getErrorCode()
                );
            }
        }
        return $this->trueAndFlushError();
    }

    /**
     * One end ping ok is ok
     *
     * {@inheritDoc}
     */
    public function ping()/*# : bool */
    {
        return $this->front->ping() || $this->back->ping();
    }

    /**
     * local save method, one end save ok is ok
     *
     * @param  CacheItemInterface $item
     * @param  string $function save or saveDeferred
     * @return bool
     * @access protected
     */
    protected function protectedSave(
        CacheItemInterface $item,
        $function = 'save'
    )/*# : bool */ {
        // write to both ?
        $func = $this->tester;
        $both = $func($item);

        // if $both is true, write to front
        $res1 = false;
        if ($both) {
            if ($this->front->$function($item)) {
                $res1 = true; // ok
            } else {
                $this->setError(
                    $this->front->getError(),
                    $this->front->getErrorCode()
                );
            }
        }

        // always write to backend
        $res2 = false;
        if ($this->back->$function($item)) {
            $res2 = true; // ok
        } else {
            $this->setError(
                $this->back->getError(),
                $this->back->getErrorCode()
            );
        }

        return $res1 || $res2 ? true : false;
    }
}
