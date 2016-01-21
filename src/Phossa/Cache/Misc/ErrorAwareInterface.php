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

/**
 * Dealing with error and error code
 *
 * @interface
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ErrorAwareInterface
{
    /**
     * Error happened?
     *
     * @param  void
     * @return bool
     * @access public
     * @api
     */
    public function hasError()/*# : bool */;
    
    /**
     * Get current error message. '' for no error
     *
     * @param  void
     * @return string
     * @access public
     * @api
     */
    public function getError()/*# : string */;

    /**
     * Get current error code. 0 for no error
     *
     * @param  void
     * @return int
     * @access public
     * @api
     */
    public function getErrorCode()/*# : int */;

    /**
     * Set the error message and (optional) error code.
     *
     * Flush current error/code with ''/0
     *
     * @param  string $message (optional) error message
     * @param  int $code (optional) error code
     * @return void
     * @access public
     * @api
     */
    public function setError(
        /*# string */ $message = '',
        /*# int */ $code = 0
    );
}
