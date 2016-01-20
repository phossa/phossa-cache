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

use Phossa\Cache\Message\Message;
use Phossa\Cache\Exception;

/**
 * Implementation of ExtensionAwareInterface
 *
 * @trait
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait ExtensionAwareTrait
{
    use \Phossa\Cache\Misc\ErrorAwareTrait;

    /**
     * extensions
     *
     * @var    array
     * @access protected
     */
    protected $extensions   = [];

    /**
     * sorted extensions
     *
     * @var    array
     * @access protected
     */
    protected $sorted       = [];

    /**
     * methods from extensions
     *
     * @var    callable[]
     * @access protected
     */
    protected $methods      = [];

    /**
     * loaded extensions
     *
     * @var    array
     * @access protected
     */
    protected $loaded       = [];

    /**
     * call extension methods if any
     *
     * @param  string $method method name
     * @param  array $arguments arguments
     * @return mixed
     * @access public
     */
    public function __call($method, array $arguments)
    {
        if (isset($this->methods[$method])) {
            return call_user_func_array($this->methods[$method], $arguments);
        } else {
            throw new Exception\BadMethodCallException(
                Message::get(
                    Message::CACHE_INVALID_METHOD,
                    get_class($this), $method
                ),
                Message::CACHE_INVALID_METHOD
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setExtensions(array $extensions)
    {
        // reset sorted array
        $this->sorted = [];

        foreach($extensions as $ext) {
            // construct extension on the fly
            if (is_array($ext) && isset($ext['className'])) {
                $class = $ext['className'];
                // append namespace if missing
                if (strpos($class, '\\') === false) {
                    $class = __NAMESPACE__ . '\\' . $class;
                }
                if (is_a(
                    $class,
                    '\Phossa\Cache\Extension\ExtensionAbstract',
                    true
                )) $ext = new $class($ext);
            }

            if ($ext instanceof ExtensionInterface &&
                !isset($this->loaded[get_class($ext)])) {

                // stages handling
                $handles = $ext->stagesHandling();
                foreach($handles as $stage => $priority) {
                    $this->extensions[$stage][$priority][] = $ext;
                }

                // register methods with cache pool
                $methods = $ext->registerMethods();
                foreach($methods as $func) {
                    if (method_exists($ext, $func) &&
                        !isset($this->methods[$func])
                    ) {
                        $this->methods[$func] = [ $ext, $func ];
                    } else {
                        throw new Exception\InvalidArgumentException(
                            Message::get(
                                Message::CACHE_INVALID_METHOD,
                                get_class($ext), $func
                            ),
                            Message::CACHE_INVALID_METHOD
                        );
                    }
                }

                $this->loaded[get_class($ext)] = true;
            } else {
                throw new Exception\InvalidArgumentException(
                    Message::get(
                        Message::CACHE_INVALID_EXT,
                        is_object($ext) ? get_class($ext) :
                            var_export($ext, true)
                    ),
                    Message::CACHE_INVALID_EXT
                );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function runExtensions(
        /*# string */ $stage, $item = null
    )/*# : bool */ {
        // sort extensions for this stage by priority
        $this->sortExtensions($stage);

        // run each extensions
        foreach($this->sorted[$stage] as $e) {
            /* @var $ex ExtensionAbstract */
            foreach($e as $ex) {
                if (!$ex($this, $stage, $item)) {
                    return $this->falseAndSetError(
                        $ex->getError(), $ex->getErrorCode()
                    );
                }
            }
        }

        return $this->trueAndFlushError();
    }

    /**
     * Sort extensions
     *
     * @param  string $stage extension stage
     * @return void
     * @access protected
     */
    protected function sortExtensions(/*# string */ $stage)
    {
        $sorted = &$this->sorted;
        if (!isset($sorted[$stage])) {
            $sorted[$stage] = [];

            // current stage
            if (isset($this->extensions[$stage])) {
                foreach($this->extensions[$stage] as $p => $e) {
                    $sorted[$stage][$p] = $e;
                }
            }

            // STAGE_ALL
            $all = ExtensionStage::STAGE_ALL;
            if (isset($this->extensions[$all])) {
                foreach($this->extensions[$all] as $p => $e) {
                    $sorted[$stage][$p] = isset($sorted[$stage][$p]) ?
                        (array_merge($sorted[$stage][$p], $e)) :
                        $e;
                }
            }

            // sort extensions by priority
            if ($sorted[$stage]) asort($sorted[$stage]);
        }
    }
}
