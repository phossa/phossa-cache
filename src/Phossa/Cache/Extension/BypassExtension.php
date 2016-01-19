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

use Phossa\Cache\CachePoolInterface;
use Phossa\Cache\CacheItemInterface;
use Phossa\Cache\Message\Message;

/**
 * Whenever sees a trigger in URL or cookie, bypass the cache
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.0
 * @since   1.0.0 added
 */
class BypassExtension extends ExtensionAbstract
{
    /**
     * trigger in URL or cookie. set trigger to '' to always bypass cache
     *
     * @var    string
     * @access protected
     */
    protected $trigger = 'nocache';

    /**
     * message for logging
     *
     * @var    string
     * @access protected
     */
    protected $message = 'bypass cache';

    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        // highest priority for all stages
        return [ ExtensionStage::STAGE_ALL => 0 ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        CachePoolInterface $cache,
        /*# string */ $stage,
        CacheItemInterface $item = null
    )/*# : bool */ {
        // whenever sees trigger, return false
        if ($this->trigger === '' ||
            isset($_REQUEST[$this->trigger]) && $_REQUEST[$this->trigger]) {
            return $this->message ?
                $this->falseAndSetError($this->message, Message::CACHE_MESSAGE):
                false;

        // always return true if no trigger found
        } else {
            return true;
        }
    }
}
