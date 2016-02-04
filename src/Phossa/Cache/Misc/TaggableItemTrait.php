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
 * TaggableItemTrait
 *
 * @trait
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.8 added
 */
trait TaggableItemTrait
{
    /**
     * tags
     *
     * @var    string[]
     * @access protected
     */
    protected $tags   = [];

    /**
     * {@inheritDoc}
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()/*# : array */
    {
        return $this->tags;
    }
}
