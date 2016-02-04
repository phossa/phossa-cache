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

namespace Phossa\Cache\Message;

use Phossa\Shared\Message\MessageAbstract;

/**
 * Message class for Phossa\Cache
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.0 added
 */
class Message extends MessageAbstract
{
    /**#@+
     * @var   int
     */

    /**
     * %s
     */
    const CACHE_MESSAGE         = 1512220901;

    /**
     * Invalid extension "%s"
     */
    const CACHE_INVALID_EXT     = 1512220908;

    /**
     * Invalid cache driver "%s"
     */
    const CACHE_INVALID_DRIVER  = 1512220909;

    /**
     * Invalid cache item key "%s"
     */
    const CACHE_INVALID_KEY     = 1512220910;
    /**#@-*/

    /**
     * Serialize/unserialize failed for key "%s"
     */
    const CACHE_FAIL_SERIALIZE  = 1512220911;

    /**
     * Invalid extension "%s" method "%s"
     */
    const CACHE_INVALID_METHOD  = 1512220912;

    /**
     * Delete "%s" failed
     */
    const CACHE_FAIL_DELETE     = 1512220913;

    /**
     * Mkdir "%s" failed
     */
    const CACHE_FAIL_MKDIR      = 1512220914;

    /**
     * Fwrite "%s" failed
     */
    const CACHE_FAIL_WRITEFILE  = 1512220915;

    /**
     * Cache driver "%s" failed
     */
    const CACHE_FAIL_DRIVER     = 1512220916;

    /**
     * Encrypt/decrypt failed for key "%s"
     */
    const CACHE_FAIL_ENCRYPT    = 1512220917;

    /**
     * Unknown property "%s" for "%s"
     */
    const CACHE_UNKNOWN_PROP    = 1512220918;

    /**
     * Autocommit deferred to cache
     */
    const CACHE_COMMIT_DEFERRED = 1512220919;

    /**
     * Garbage collection at %s
     */
    const CACHE_GARBAGE_COLLECT = 1512220920;

    /**
     * Stampede protection triggered for "%s"
     */
    const CACHE_STAMPEDE_EXT    = 1512220921;


    /**
     * Cache driver "%s" fallbacks to "%s"
     */
    const CACHE_FALLBACK_DRIVER = 1512220922;

    /**
     * Bypass the cache
     */
    const CACHE_BYPASS_EXT      = 1512220923;

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected static $messages = [
        self::CACHE_MESSAGE         => '%s',
        self::CACHE_INVALID_EXT     => 'Invalid extension "%s"',
        self::CACHE_INVALID_DRIVER  => 'Invalid cache driver "%s"',
        self::CACHE_INVALID_KEY     => 'Invalid cache item key "%s"',
        self::CACHE_FAIL_SERIALIZE  => 'Serialize/unserialize failed for key "%s"',
        self::CACHE_INVALID_METHOD  => 'Invalid extension "%s" method "%s"',
        self::CACHE_FAIL_DELETE     => 'Delete "%s" failed',
        self::CACHE_FAIL_MKDIR      => 'Mkdir "%s" failed',
        self::CACHE_FAIL_WRITEFILE  => 'Fwrite "%s" failed',
        self::CACHE_FAIL_DRIVER     => 'Cache driver "%s" failed',
        self::CACHE_FAIL_ENCRYPT    => 'Encrypt/decrypt failed for key "%s"',
        self::CACHE_UNKNOWN_PROP    => 'Unknown property "%s" for "%s"',
        self::CACHE_COMMIT_DEFERRED => 'Autocommit deferred to cache',
        self::CACHE_GARBAGE_COLLECT => 'Garbage collection at %s',
        self::CACHE_STAMPEDE_EXT    => 'Stampede protection triggered for "%s"',
        self::CACHE_FALLBACK_DRIVER => 'Cache driver "%s" fallbacks to "%s"',
        self::CACHE_BYPASS_EXT      => 'Bypass the cache',
    ];
}
