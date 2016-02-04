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
 * Implementation of ErrorAwareInterface
 *
 * @trait
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Misc\ErrorAwareInterface
 * @version 1.0.8
 * @since   1.0.0 added
 */
trait ErrorAwareTrait
{
    /**
     * error message
     *
     * @var    string
     * @access protected
     */
    protected $error    = '';

    /**
     * error code
     *
     * @var    int
     * @access protected
     */
    protected $err_code = 0;

    /**
     * {@inheritDoc}
     */
    public function hasError()/*# : bool */
    {
        return $this->err_code > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getError()/*# : string */
    {
        return $this->error;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorCode()/*# : int */
    {
        return $this->err_code;
    }

    /**
     * {@inheritDoc}
     */
    public function setError(
        /*# string */ $message = '',
        /*# int */ $code = 0
    ) {
        $this->error = $message;
        $this->err_code = $code;
    }

    /**
     * helper method, flush error message and return true
     *
     * @return bool
     * @access protected
     */
    protected function trueAndFlushError()/*# : bool */
    {
        $this->setError();
        return true;
    }

    /**
     * helper method, set error and return false
     *
     * @param  string $message error message
     * @param  int $code (optional) error code
     * @return bool
     * @access protected
     */
    protected function falseAndSetError(
        /*# string */ $message,
        /*# int */ $code = 0
    )/*# : bool */ {
        $this->setError($message, $code);
        return false;
    }
}
