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

/**
 * Chinese zh_CN translation for Phossa\Cache\Message\Message
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Message\Message
 * @version 1.0.8
 * @since   1.0.8 added
 */
return [
    Message::CACHE_MESSAGE         => '%s',
    Message::CACHE_INVALID_EXT     => '未知扩展 "%s"',
    Message::CACHE_INVALID_DRIVER  => '未知缓存驱动 "%s"',
    Message::CACHE_INVALID_KEY     => '无效缓存文件钥匙 "%s"',
    Message::CACHE_FAIL_SERIALIZE  => '对钥匙为"%s"缓存 Serialize/Unserialize 失败',
    Message::CACHE_INVALID_METHOD  => '无效的扩展 "%s" 方法 "%s"',
    Message::CACHE_FAIL_DELETE     => '删除 "%s" 失败',
    Message::CACHE_FAIL_MKDIR      => '创建目录 "%s" 失败',
    Message::CACHE_FAIL_WRITEFILE  => '写入文件 "%s" 失败',
    Message::CACHE_FAIL_DRIVER     => '缓存驱动 "%s" 失败',
    Message::CACHE_FAIL_ENCRYPT    => '对钥匙为"%s"缓存 Encrypt/Decrypt 失败',
    Message::CACHE_UNKNOWN_PROP    => '未知的属性 "%s" （"%s"）',
    Message::CACHE_COMMIT_DEFERRED => '清空延迟写入',
    Message::CACHE_GARBAGE_COLLECT => '缓存垃圾清理 %s',
    Message::CACHE_STAMPEDE_EXT    => '写入竞争保护 "%s"',
    Message::CACHE_FALLBACK_DRIVER => '缓存驱动 "%s" 采用备用的 "%s"',
    Message::CACHE_BYPASS_EXT      => '回避了缓存',
];
