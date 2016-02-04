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

use Phossa\Shared\Exception\DuplicationFoundException as DFException;

/**
 * DuplicationFoundException for \Phossa\Cache
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Exception\ExceptionInterface
 * @see     \Phossa\Shared\Exception\DuplicationFoundException
 * @version 1.0.8
 * @since   1.0.8 added
 */
class DuplicationFoundException extends DFException implements
    ExceptionInterface
{
}
