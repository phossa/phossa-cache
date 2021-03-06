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

namespace Phossa\Cache\Misc;

/**
 * LoggerAwareInterface
 *
 * @interface
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.0 added
 */
interface LoggerAwareInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * Log messages
     *
     * @param  string $level
     *     debug|info|notice|warning|error|critical|alert|emergency
     * @param  string $message message to log
     * @return void
     * @access public
     * @api
     */
    public function log(
        /*# string */ $level,
        /*# string */ $message
    );
}
