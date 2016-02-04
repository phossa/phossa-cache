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

use Psr\Log\LoggerInterface;

/**
 * LoggerAwareTrait
 *
 * @trait
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.0 added
 */
trait LoggerAwareTrait
{
    /**
     * Logger
     *
     * @var    LoggerInterface
     * @access protected
     */
    protected $logger;

    /**
     * {@inheritDoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function log(
        /*# string */ $level,
        /*# string */ $message
    ) {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->log($level, $message);
        } else {
            $skip = [ 'debug', 'info' ];
            if (!in_array($level, $skip)) {
                trigger_error($message, E_USER_NOTICE);
            }
        }
    }
}
