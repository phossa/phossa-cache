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

/**
 * TaggableExtension
 *
 * Make the Cachepool item taggable
 *
 * e.g.
 * <code>
 *     $taggable = new Extension\TaggableExtension();
 *     $cache->setExtension($taggable);
 *     $cache->clearByTag('bingo');
 * </code>
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.8
 * @since   1.0.0 added
 */
class TaggableExtension extends ExtensionAbstract
{
    /**
     * tag remember time in seconds
     *
     * @var    int
     * @access protected
     */
    protected $tag_life = 31536000; // one year

    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        // do tag things after save item
        return [
            ExtensionStage::STAGE_POST_SAVE     => 70,
            ExtensionStage::STAGE_POST_DEFER    => 70
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function registerMethods()/*# : array */
    {
        // register the method
        return [  'clearByTag' ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        CachePoolInterface $cache,
        /*# string */ $stage,
        CacheItemInterface $item = null
    )/*# : bool */ {
        // get item's tags
        $tags = $item->getTags();

        // record to a $tagItem (one tag one tagItem)
        if (count($tags)) {
            $key = $item->getKey();
            foreach($tags as $tag) {
                $tagKey  = $this->getTagKey($tag);
                $tagItem = $cache->getItem($tagKey);
                if ($tagItem->isHit()) {
                    $keyArray = $tagItem->get();
                    $keyArray[$key] = true;
                } else {
                    $keyArray = [ $key => true ];
                }

                $tagItem->set($keyArray);
                $tagItem->expiresAfter($this->tag_life); // one year
                $cache->save($tagItem);
            }
        }

        // always true
        return true;
    }

    /**
     * Get tag file name
     *
     * @param  string $tag tag
     * @return string
     * @throws void
     * @access protected
     */
    protected function getTagKey(/*# string */ $tag)/*# : string */
    {
        return 'THE_TAG_' . $tag;
    }

    /**
     * Clear by tags
     *
     * @param  CachePoolInterface $cache
     * @param  string $tag tag
     * @return bool
     * @access public
     */
    public function clearByTag(
        CachePoolInterface $cache,
        /*# string */ $tag
    )/*# : bool */ {
        // get tagItem for $tag
        $tagKey  = $this->getTagKey($tag);
        $tagItem = $cache->getItem($tagKey);

        // read array of keys from $tagItem
        if ($tagItem->isHit()) {
            $keyArray = $tagItem->get();
            foreach($keyArray as $k => $v) {
                if ($cache->deleteItem($k)) unset($keyArray[$k]);
            }

            // get error
            if ($keyArray) {
                $error = $cache->getError();
                $ecode = $cache->getErrorCode();
            }

            // update tagItem
            $tagItem->set($keyArray);
            $tagItem->expiresAfter($this->tag_life); // one year
            $cache->save($tagItem);

            if ($keyArray) return $this->falseAndSetError($error, $ecode);
        }

        return $this->trueAndFlushError();
    }
}
