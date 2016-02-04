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

namespace Phossa\Cache;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Cache interface
 *
 * @interface
 * @package Phossa\cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Psr\Cache\CacheItemPoolInterface
 * @see     \Phossa\Cache\Misc\LoggerAwareInterface
 * @see     \Phossa\Cache\Driver\DriverAwareInterface
 * @see     \Phossa\Cache\Extension\ExtensionAwareInterface
 * @version 1.0.8
 * @since   1.0.0 added
 */
interface CachePoolInterface extends
    CacheItemPoolInterface,
    Misc\LoggerAwareInterface,
    Driver\DriverAwareInterface,
    Extension\ExtensionAwareInterface
{
}
