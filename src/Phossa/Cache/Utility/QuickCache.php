<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Utility;

/**
 * QuickCache
 *
 * Provides a usable cache class
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class QuickCache extends \Phossa\Cache\CachePool
{
    /**
     * default ttl
     *
     * @var    int
     * @access protected
     */
    protected $ttl = 86400;

    /**
     * Complete cycle of get cached result of a callable
     *
     * 1. if first argument is int, it is a TTL
     * 2. otherwise the first argument is a callable
     * 3. the remaining are the arguments for the callable
     *
     * @param  void
     * @return mixed
     * @throws \Exception if any runtime thrown by the callable
     * @access public
     * @api
     */
    public function cachedCallable()
    {
        $args = func_get_args();
        if (is_int($args[0])) {
            $ttl = array_shift($args);
            $key = $this->generateKey($args);
        } else {
            $key = $this->generateKey($args);
            $ttl = $this->ttl;
        }

        // get
        $item = $this->getItem($key);

        if ($item->isHit()) {
            // return cached result
            return $item->get();
        } else {
            // execute callable
            $func = array_shift($args);
            $val  = call_user_func_array($func, $args);

            // cache result
            $item->set($val);
            $item->expiresAfter($ttl);
            $this->save($item);

            // return result
            return $val;
        }
    }

    /**
     * Generate key base on input
     *
     * @param  mixed $reference reference data
     * @return string
     * @access protected
     */
    protected function generateKey($reference)/*# : string */
    {
        if (is_array($reference)) {
            $reference = $this->flatReference($reference);
        }
        return md5(serialize($reference));
    }

    /**
     * flat the reference array to make it easy for serialize
     *
     * @param  array $reference reference data
     * @return array flattered array
     * @access protected
     */
    protected function flatReference(array $reference)/*# : array */
    {
        reset($reference);
        foreach($reference as $key => $value) {
            if (is_object($value)) {
                $reference[$key] = get_class($value);
            } else if (is_array($value)) {
                $reference[$key] = $this->_flatReference($value);
            }
        }
        ksort($reference);
        return $reference;
    }
}
