<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Exception;

/**
 * InvalidArgumentException for \Phossa\Cache
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Psr\Cache\InvalidArgumentException
 * @see     \Phossa\Cache\Exception\ExceptionInterface
 * @see     \Phossa\Shared\Exception\InvalidArgumentException
 * @version 1.0.0
 * @since   1.0.0 added
 */
class InvalidArgumentException
    extends
        \Phossa\Shared\Exception\InvalidArgumentException
    implements
        ExceptionInterface,
        \Psr\Cache\InvalidArgumentException
{

}
