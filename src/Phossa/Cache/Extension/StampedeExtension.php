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
 * Stampede protection for the cache
 *
 * If item expires in 600 seconds (configurable), and by 5% (configurable)
 * chance, this extension will mark the $item as a miss to force regenerating
 * the item.
 *
 * This extension will be executed in stage ExtensionStage::STAGE_POST_HAS
 * which is right after $item->isHit() called
 *
 * <code>
 *     $stampede = new Extension\StampedeExtension([
 *         'probability' => 60, // change property to 6%
 *         'time_left'   => 300 // change time left ot 5 minutes
 *     ]);
 * </code>
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.8
 * @since   1.0.0 added
 */
class StampedeExtension extends ExtensionAbstract
{
    /**
     * Probability, usually 1 - 100
     *
     * @var    int
     * @access protected
     */
    protected $probability  = 50;

    /**
     * Divisor, probability divisor, usually 1000
     *
     * @var    int
     * @access protected
     */
    protected $divisor      = 1000;

    /**
     * time left in seconds
     *
     * @var    int
     * @access protected
     */
    protected $time_left    = 600;

    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        return [ ExtensionStage::STAGE_POST_HAS => 50 ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        CachePoolInterface $cache,
        /*# string */ $stage,
        CacheItemInterface $item = null
    )/*# : bool */ {
        if ($item->isHit()) {
            // time left
            $left = $item->getExpiration()->getTimestamp() - time();

            if ($left < $this->time_left &&
                rand(1, $this->divisor) <= $this->probability
            ) {
                // log message
                $cache->log('notice', Message::get(
                    Message::CACHE_STAMPEDE_EXT, $item->getKey()
                ));

                // revert to miss
                return $item->setHit(false);
            }
        }
        return true;
    }
}
