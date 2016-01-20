<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Cache interface
 *
 * @interface
 * @package \Phossa\cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Psr\Cache\CacheItemPoolInterface
 * @see     \Phossa\Cache\Misc\ErrorAwareInterface
 * @see     \Phossa\Cache\Misc\LoggerAwareInterface
 * @see     \Phossa\Cache\Driver\DriverAwareInterface
 * @see     \Phossa\Cache\Extension\ExtensionAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface CachePoolInterface extends
    CacheItemPoolInterface,
    Misc\ErrorAwareInterface,
    Misc\LoggerAwareInterface,
    Driver\DriverAwareInterface,
    Extension\ExtensionAwareInterface
{

}
