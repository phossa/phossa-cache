<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Message;

use Phossa\Shared\Message\MessageAbstract;

/**
 * Message class for Phossa\Cache
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
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
     * Driver "%s" failed, fallback to Null
     */
    const CACHE_FAIL_DRIVER     = 1512220916;
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
        self::CACHE_FAIL_DRIVER     => 'Driver "%s" failed, fallback to Null',
    ];
}
