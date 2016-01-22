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
     * default extensions, MUST LOAD
     *
     * @var    array
     * @access protected
     */
    protected $default_ext = [
        ['className' => 'SerializeExtension']
    ];

    /**
     * extensions array
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
     * methods registered from extensions
     *
     * @var    callable[]
     * @access protected
     */
    protected $methods      = [];

    /**
     * marker to prevent extension loaded twice
     *
     * @var    array
     * @access protected
     */
    protected $loaded       = [];

    /**
     * call extension methods if registered
     *
     * After registering extension method with CachePool. User may use these
     * methods via CachePool object
     *
     * e.g.
     * <code>
     *     $cache = new \Phossa\Cache\CachePool();
     *
     *     // taggableExtension registers a 'clearByTag' method
     *     $cache->setExtensions([
     *         [ 'className' => 'TaggableExtension' ]
     *     ]);
     *
     *     $cache->clearByTag('bingo');
     * </code>
     *
     * @param  string $method method name
     * @param  array $arguments method arguments
     * @return mixed
     * @access public
     */
    public function __call($method, array $arguments)
    {
        if (isset($this->methods[$method])) {
            // put $cache to the first of $arguments
            array_unshift($arguments, $this);
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
        // always reset sorted array
        $this->sorted = [];

        // load extension one by one
        foreach($extensions as $ext) {
            // extension config array found
            if (is_array($ext) && isset($ext['className'])) {
                // find extension class
                $class = $ext['className'];
                unset($ext['className']);

                // append namespace if missing
                if (strpos($class, '\\') === false) {
                    $class = __NAMESPACE__ . '\\' . $class;
                }

                // create extension instance
                if (is_a(
                    $class,
                    '\Phossa\Cache\Extension\ExtensionAbstract',
                    true
                )) $ext = new $class($ext);
            }

            // extension instance found and not loaded yet
            if ($ext instanceof ExtensionInterface &&
                !isset($this->loaded[get_class($ext)])
            ) {

                // stages handling
                $handles = $ext->stagesHandling();
                foreach($handles as $stage => $priority) {
                    $this->extensions[$stage][$priority][] = $ext;
                }

                // register extension methods with cache pool
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

                // mark this ext loaded
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

        // run each extensions in this stage
        foreach($this->sorted[$stage] as $e) {
            /* @var $ex ExtensionAbstract */
            foreach($e as $ex) {
                if (!$ex($this, $stage, $item)) {
                    // failed, get extension error
                    return $this->falseAndSetError(
                        $ex->getError(), $ex->getErrorCode()
                    );
                }
            }
        }

        // success and flush error
        return $this->trueAndFlushError();
    }

    /**
     * {@inheritDoc}
     */
    public function clearExtensions(
        /*# bool */ $loadDefaults = true
    ) {
        // flush all
        $this->extensions = $this->sorted = [];
        $this->methods = $this->loaded = [];

        // load defaults if wanted
        if ($loadDefaults && sizeof($this->default_ext)) {
            $this->setExtensions($this->default_ext);
        }
    }

    /**
     * Sort extensions by priority for $stage
     *
     * @param  string $stage extension stage
     * @return void
     * @access protected
     */
    protected function sortExtensions(/*# string */ $stage)
    {
        $sorted = &$this->sorted;

        // if not sorted yet
        if (!isset($sorted[$stage])) {
            $sorted[$stage] = [];

            // current stage
            if (isset($this->extensions[$stage])) {
                foreach($this->extensions[$stage] as $p => $e) {
                    $sorted[$stage][$p] = $e;
                }
            }

            // process the special STAGE_ALL
            $all = ExtensionStage::STAGE_ALL;
            if (isset($this->extensions[$all])) {
                foreach($this->extensions[$all] as $p => $e) {
                    $sorted[$stage][$p] = isset($sorted[$stage][$p]) ?
                        (array_merge($sorted[$stage][$p], $e)) :
                        $e;
                }
            }

            // sort extensions by priority
            if ($sorted[$stage]) ksort($sorted[$stage]);
        }
    }
}
