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
 * TaggableItemInterface
 *
 * @interface
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.8
 * @since   1.0.8 added
 */
interface TaggableItemInterface
{
    /**
     * Set tags to this item
     *
     * @param  string[] $tags tags array
     * @return void
     * @access public
     * @api
     */
    public function setTags(array $tags);

    /**
     * Get item tags
     *
     * @param  string[]
     * @access public
     * @api
     */
    public function getTags()/*# : array */;
}
