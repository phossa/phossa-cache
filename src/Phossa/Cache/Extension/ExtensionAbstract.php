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
 * Abstract class implementing ExtensionInterface
 *
 * @abstract
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionInterface
 * @see     \Phossa\Cache\Misc\ErrorAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
abstract class ExtensionAbstract implements
    ExtensionInterface,
    \Phossa\Cache\Misc\ErrorAwareInterface
{
    use \Phossa\Cache\Misc\ErrorAwareTrait,
        \Phossa\Cache\Misc\SimpleConstructTrait;

    /**
     * {@inheritDoc}
     */
    public function registerMethods()/*# : array */
    {
        return [];
    }
}
