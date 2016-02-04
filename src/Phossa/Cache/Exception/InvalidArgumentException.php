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

namespace Phossa\Cache\Exception;

use Phossa\Shared\Exception\InvalidArgumentException as IAException;

/**
 * InvalidArgumentException for \Phossa\Cache
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Psr\Cache\InvalidArgumentException
 * @see     \Phossa\Cache\Exception\ExceptionInterface
 * @see     \Phossa\Shared\Exception\InvalidArgumentException
 * @version 1.0.8
 * @since   1.0.0 added
 */
class InvalidArgumentException extends IAException implements
    ExceptionInterface,
    \Psr\Cache\InvalidArgumentException
{
}
