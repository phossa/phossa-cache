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
 * ExtensionStage
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class ExtensionStage
{
    /**#@+
     * Extension stages
     *
     * @const
     */

    const STAGE_ALL         = 'all';

    const STAGE_INIT        = 'init';

    const STAGE_END         = 'end';

    const STAGE_PRE_HAS      = 'pre_has';
    const STAGE_POST_HAS     = 'post_has';

    const STAGE_PRE_GET      = 'pre_get';
    const STAGE_POST_GET     = 'post_get';

    const STAGE_PRE_SAVE     = 'pre_save';
    const STAGE_POST_SAVE    = 'post_save';

    const STAGE_PRE_CLEAR    = 'pre_clear';
    const STAGE_POST_CLEAR   = 'post_clear';

    const STAGE_PRE_DEL      = 'pre_delete';
    const STAGE_POST_DEL     = 'post_delete';

    const STAGE_PRE_DEFER    = 'pre_defer';
    const STAGE_POST_DEFER   = 'post_defer';

    const STAGE_PRE_COMMIT   = 'pre_commit';
    const STAGE_POST_COMMIT  = 'post_commit';

    /**#@-*/
}
