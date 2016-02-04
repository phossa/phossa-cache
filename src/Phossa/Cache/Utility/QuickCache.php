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

namespace Phossa\Cache\Utility;

/**
 * QuickCache
 *
 * Extends the Phossa\Cache\CachePool class with useful methods
 *
 * <code>
 *     // set default ttl to 24400
 *     $cache  = new \Phossa\Cache\Utility\QuichCache(
 *         [],[], ['ttl' => 24400]
 *     );
 *
 *     // cache callable results
 *     $result = $cache->cachedCallable(aCallable, $myParam0, $myParam1);
 * </code>
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.0 added
 */
class QuickCache extends \Phossa\Cache\CachePool
{
    /**
     * default ttl for the cached callable result
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
     * @return mixed
     * @throws \Phossa\Shared\Exception\RuntimeException
     *         any runtime thrown by the callable
     * @access public
     * @api
     */
    public function cachedCallable()
    {
        // get method arguments
        $args = func_get_args();

        // get ttl and uniq key
        if (is_int($args[0])) {
            $ttl = array_shift($args);
            $key = $this->generateKey($args);
        } else {
            $key = $this->generateKey($args);
            $ttl = $this->ttl;
        }

        // get item
        $item = $this->getItem($key);

        // found cached result
        if ($item->isHit()) {
            // return cached result
            return $item->get();

        // execute callable and save result
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
