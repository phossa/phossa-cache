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

use Phossa\Cache\Message\Message;
use Phossa\Cache\Exception;

/**
 * Implementation of ExtensionAwareInterface
 *
 * @trait
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAwareInterface
 * @version 1.0.8
 * @since   1.0.0 added
 * @since   1.0.8 removed setExtensions(), added addExtension()
 */
trait ExtensionAwareTrait
{
    use \Phossa\Cache\Misc\ErrorAwareTrait;

    /**
     * extensions array
     *
     * @var    array
     * @access protected
     */
    protected $extensions   = [];

    /**
     * sorted extensions array
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
     * Method signature is `functin (CachePoolInterface $cache, ...) {}`
     *
     * e.g.
     * <code>
     *     $cache = new \Phossa\Cache\CachePool();
     *
     *     // taggableExtension registers a 'clearByTag' method
     *     $cache->addExtension(new Extension\TaggableExtension());
     *
     *     // method
     *     $cache->clearByTag('bingo');
     * </code>
     *
     * @param  string $method method name
     * @param  array $arguments method arguments
     * @return mixed
     * @access public
     */
    public function __call(/*# string */ $method, array $arguments)
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
    public function addExtension(ExtensionInterface $extension)
    {
        // extension not loaded yet
        if (!isset($this->loaded[get_class($extension)])) {
            // stages handling
            $handles = $extension->stagesHandling();
            foreach ($handles as $stage => $priority) {
                $this->extensions[$stage][$priority][] = $extension;
            }

            // register extension methods if any
            $methods = $extension->registerMethods();
            foreach ($methods as $func) {
                if (method_exists($extension, $func) &&
                    !isset($this->methods[$func])
                ) {
                    $this->methods[$func] = [ $extension, $func ];
                } else {
                    throw new Exception\InvalidArgumentException(
                        Message::get(
                            Message::CACHE_INVALID_METHOD,
                            get_class($extension), $func
                        ),
                        Message::CACHE_INVALID_METHOD
                    );
                }
            }

            // mark this extension loaded
            $this->loaded[get_class($extension)] = true;

        // loaded twice
        } else {
            throw new Exception\DuplicationFoundException(
                Message::get(
                    Message::CACHE_INVALID_EXT,
                    get_class($extension)
                ),
                Message::CACHE_INVALID_EXT
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function runExtensions(
        /*# string */ $stage,
        $item = null
    )/*# : bool */ {
        // sort extensions by priority
        $this->sortExtensions($stage);

        // run each extensions in this stage
        foreach ($this->sorted[$stage] as $e) {
            /* @var $ex ExtensionAbstract */
            foreach ($e as $ex) {
                if (!$ex($this, $stage, $item)) {
                    // failed, retrieve extension's error
                    return $this->falseAndSetError(
                        $ex->getError(),
                        $ex->getErrorCode()
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
    public function clearExtensions()
    {
        $this->extensions = $this->sorted = [];
        $this->methods    = $this->loaded = [];

        // load MUST HAVE extension
        $this->addExtension(new SerializeExtension());
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
                foreach ($this->extensions[$stage] as $p => $e) {
                    $sorted[$stage][$p] = $e;
                }
            }

            // process the special STAGE_ALL
            $all = ExtensionStage::STAGE_ALL;
            if (isset($this->extensions[$all])) {
                foreach ($this->extensions[$all] as $p => $e) {
                    $sorted[$stage][$p] = isset($sorted[$stage][$p]) ?
                        (array_merge($sorted[$stage][$p], $e)) :
                        $e;
                }
            }

            // sort extensions by priority
            if (count($sorted[$stage])) {
                ksort($sorted[$stage]);
            }
        }
    }
}
