<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Extension;

/**
 * ExtensionAwareInterface
 *
 * @interface
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ExtensionAwareInterface
{
    /**
     * Set extensions
     *
     * @param  ExtensionInterface[] $extensions array of extensions/definitions
     * @return void
     * @throws \Phossa\Cache\Exception\InvalidArgumentException
     *         if not the valid extension found
     * @access public
     * @api
     */
    public function setExtensions(array $extensions);

    /**
     * Execute extensions at different extension stage
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
        /*# string */ $stage, $item = null
    )/*# : bool */;
}
