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

namespace Phossa\Cache\Extension;

use Phossa\Cache\Misc\ErrorAwareInterface;

/**
 * ExtensionAwareInterface
 *
 * @interface
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.0 added
 * @since   1.0.8 removed setExtensions(), added addExtension()
 */
interface ExtensionAwareInterface extends ErrorAwareInterface
{
    /**
     * Set extensions
     *
     * @param  ExtensionInterface $extension
     * @return void
     * @throws \Phossa\Cache\Exception\DuplicationFoundException
     *         if extension loaded twice
     * @access public
     * @api
     */
    public function addExtension(ExtensionInterface $extension);

    /**
     * Execute extensions at different extension stage.
     *
     * Return false and set error/errcode if one of the execution failed
     *
     * $item: string,               error message string
     *        null                  nothing
     *        CacheItemInterface    the item
     *
     * @param  string $stage caching stage
     * @param  null|string|\Phossa\Cache\CacheItemInterface $item
     *         error message or cache item
     * @return bool
     * @access public
     */
    public function runExtensions(
        /*# string */ $stage,
        $item = null
    )/*# : bool */;

    /**
     * Clear all extensions/methods
     *
     * @return void
     * @access public
     * @api
     */
    public function clearExtensions();
}
