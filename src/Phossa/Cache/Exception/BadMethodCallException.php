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

use \Phossa\Shared\Exception\BadMethodCallException as BMException;

/**
 * BadMethodCallException for \Phossa\Cache
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Exception\ExceptionInterface
 * @see     \Phossa\Shared\Exception\BadMethodCallException
 * @version 1.0.8
 * @since   1.0.0 added
 */
class BadMethodCallException extends BMException implements ExceptionInterface
{
}
