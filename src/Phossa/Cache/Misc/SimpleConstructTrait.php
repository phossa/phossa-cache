<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Misc;

/**
 * Create an object with configs/settings
 *
 * @trait
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait SimpleConstructTrait
{
    /**
     * Construct with configs/settings
     *
     * @param  array $configs object configs
     * @access public
     */
    public function __construct(array $configs = [])
    {
        // siliently ignores unknown config
        foreach($configs as $name => $value) {
            if (isset($this->$name)) {
                $this->$name = $value;
            }
        }
    }
}
