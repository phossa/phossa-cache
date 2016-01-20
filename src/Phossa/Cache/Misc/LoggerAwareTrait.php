<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Misc;

use Psr\Log\LoggerInterface;

/**
 * LoggerAwareTrait
 *
 * @trait
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
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
        if ($this->logger) $this->logger->log($level, $message);
    }
}
