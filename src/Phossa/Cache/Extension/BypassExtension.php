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

use Phossa\Cache\CachePoolInterface;
use Phossa\Cache\CacheItemInterface;
use Phossa\Cache\Message\Message;

/**
 * Whenever sees a trigger in $_REQUEST(URL or cookie), bypass the cache
 *
 * Suppose the URL is 'http://example.com/test.php?nocache=1'. This will
 * trigger ByPassExtension and bypass the cache
 *
 * e.g.
 * <code>
 *     $bypass = new BypassExtension(
  *        // change trigger to 'mustbypass'
 *         'trigger' => 'mustbypass',
 *         // disable message
 *         'message' => ''
 *     );
 *
 *     // enable bypass extension with $cache
 *     $cache->setExtension($bypass);
 * </code>
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.8
 * @since   1.0.0 added
 */
class BypassExtension extends ExtensionAbstract
{
    /**
     * trigger in URL or cookie. set to '' to always bypass cache
     *
     * @var    string
     * @access protected
     */
    protected $trigger = 'nocache';

    /**
     * spit message for logging
     *
     * @var    bool
     * @access protected
     */
    protected $message = false;

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
        /*
         * 1. $this->trigger = '', always bypass the cache
         * 2. if sees $this->trigger in $_REQUEST, bypass the cache
         * 3. not to setError if $this->message == ''
         */
        if ($this->trigger === '' ||
            isset($_REQUEST[$this->trigger]) && $_REQUEST[$this->trigger]) {
            return $this->message ?
                $this->falseAndSetError(
                    Message::get(Message::CACHE_BYPASS_EXT),
                    Message::CACHE_BYPASS_EXT
                ) :
                false;

        // always return true if no trigger found
        } else {
            return true;
        }
    }
}
