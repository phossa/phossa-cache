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
 * SerializeExtension
 *
 * Serialize before save, and unserialize after get
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.8
 * @since   1.0.0 added
 */
class SerializeExtension extends ExtensionAbstract
{
    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        return [
            ExtensionStage::STAGE_POST_GET     => 50,
            ExtensionStage::STAGE_PRE_SAVE     => 50,
            ExtensionStage::STAGE_PRE_DEFER    => 50
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        CachePoolInterface $cache,
        /*# string */ $stage,
        CacheItemInterface $item = null
    )/*# : bool */ {
        if ($stage === ExtensionStage::STAGE_POST_GET) {
            if ($item->isHit()) $res = @unserialize($item->get());
        } else {
            $res = @serialize($item->get());
        }

        if (isset($res)) {
            if ($res === false) {
                return $this->falseAndSetError(
                    Message::get(
                        Message::CACHE_FAIL_SERIALIZE, $item->getKey()
                    ),
                    Message::CACHE_FAIL_SERIALIZE
                );
            }
            $item->set($res);
        }
        return true;
    }
}
