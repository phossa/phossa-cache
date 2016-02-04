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

namespace Phossa\Cache;

use Phossa\Cache\Message\Message;
use Phossa\Cache\Extension\ExtensionStage as ES;

/**
 * Cache class which implements CachePoolInterface
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\CachePoolInterface
 * @version 1.0.8
 * @since   1.0.0 added
 */
class CachePool implements CachePoolInterface
{
    use Misc\LoggerAwareTrait,
        Driver\DriverAwareTrait,
        Extension\ExtensionAwareTrait,
        \Phossa\Shared\Pattern\SetPropertiesTrait;

    /**
     * item factory method, signatures as follows
     *
     *     function(
     *          string $key,
     *          CachePoolInterface $pool,
     *          array $configs = []
     *     ): CacheItemInterface
     *
     * @var    callable
     * @access protected
     */
    protected $item_factory;

    /**
     * Item default configs
     *
     * @var    array
     * @access protected
     */
    protected $item_config = [];

    /**
     * Cache pool constructor
     *
     * @param  Driver\DriverInterface $driver the cache driver
     * @param  Extension\ExtensionInterface[] $extensions extensions
     * @param  array $configs (optional) cache pool configuration
     * @throws Exception\DuplicationFoundException
     *         if extension duplicated
     * @access public
     */
    public function __construct(
        Driver\DriverInterface $driver = null,
        array $extensions = [],
        array $configs    = []
    ) {
        // set configs
        if (count($configs)) $this->setProperties($configs);

        // driver, if not set, use the FilesystemDriver
        if (is_null($driver)) $driver = new Driver\FilesystemDriver();
        $this->setDriver($driver);

        // clear & load default extension
        $this->clearExtensions();

        // load exteneral extensions
        foreach($extensions as $ext) {
            $this->setExtension($ext);
        }

        // run extensions STAGE_INIT
        $this->runExtensions(ES::STAGE_INIT);
    }

    /**
     * Cache pool destructor
     *
     * @access public
     */
    public function __destruct()
    {
        // commit all deferred
        $this->commit();

        // run extensions
        $this->runExtensions(ES::STAGE_END);
    }

    /**
     * {@inheritDoc}
     */
    public function getItem(
        /*# string */ $key
    )/*# : CacheItemInterface */ {
        return $this->createItem($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getItems(
        array $keys = array()
    ) {
        $result = [];
        foreach($keys as $key) {
            $result[$key] = $this->getItem($key);
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function hasItem(/*# string */ $key)/*# : bool */
    {
        // create item object
        $item = $this->createItem($key);

        // check hit
        return $item->isHit();
    }

    /**
     * {@inheritDoc}
     */
    public function clear()/*# : bool */
    {
        // before clear
        if (!$this->runExtensions(ES::STAGE_PRE_CLEAR)) return false;

        // clear the pool
        if (!$this->driver->clear()) {
            return $this->falseAndSetError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after clear
        if (!$this->runExtensions(ES::STAGE_POST_CLEAR)) return false;

        return $this->trueAndFlushError();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteItem(/*# string */ $key)/*# : bool */
    {
        // create item object
        $item = $this->createItem($key);

        // before delete
        if (!$this->runExtensions(ES::STAGE_PRE_DEL, $item)) return false;

        // delete from pool
        if (!$this->driver->delete($item->getKey())) {
            return $this->falseAndSetError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after delete
        if (!$this->runExtensions(ES::STAGE_POST_DEL, $item)) return false;

        return $this->trueAndFlushError();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteItems(array $keys)/*# : bool */
    {
        foreach($keys as $key) {
            if (!$this->deleteItem($key)) return false;
        }
        return $this->trueAndFlushError();
    }

    /**
     * {@inheritDoc}
     */
    public function save(\Psr\Cache\CacheItemInterface $item)/*# : bool */
    {
        // extensions may change $item
        $clone = clone $item;

        // before save
        if (!$this->runExtensions(ES::STAGE_PRE_SAVE, $clone)) return false;

        // write to the pool
        if (!$this->driver->save($clone)) {
            return $this->falseAndSetError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after save
        if (!$this->runExtensions(ES::STAGE_POST_SAVE, $clone)) return false;

        return $this->trueAndFlushError();
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(
        \Psr\Cache\CacheItemInterface $item
    )/*# : bool */ {
        // extensions may change $item
        $clone = clone $item;

        // before deferred
        if (!$this->runExtensions(ES::STAGE_PRE_DEFER, $clone)) return false;

        // write to the pool
        if (!$this->driver->saveDeferred($clone)) {
            return $this->falseAndSetError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after deferred
        if (!$this->runExtensions(ES::STAGE_POST_DEFER, $clone)) return false;

        return $this->trueAndFlushError();
    }

    /**
     * {@inheritDoc}
     */
    public function commit()/*# : bool */
    {
        // before commit
        if (!$this->runExtensions(ES::STAGE_PRE_COMMIT)) return false;

        // commit to pool
        if (!$this->driver->commit()) {
            return $this->falseAndSetError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after commit
        if (!$this->runExtensions(ES::STAGE_POST_COMMIT)) return false;

        return $this->trueAndFlushError();
    }

    /**
     * Create item object
     *
     * @param  string $key item key
     * @return CacheItemInterface
     * @throws Exception\InvalidArgumentException
     * @access protected
     */
    protected function createItem(/*# string */ $key)/*# : CacheItemInterface */
    {
        // validate key first
        $this->validateKey($key);

        // use item factory
        if (is_callable($this->item_factory)) {
            $func = $this->item_factory;
            $item = $func($key, $this, $this->item_config);

        // default CacheItem class
        } else {
            $item = new CacheItem($key, $this, $this->item_config);
        }

        return $item;
    }

    /**
     * Validate key string
     *
     * @param  string &$key key to check
     * @return void
     * @throws Exception\InvalidArgumentException
     * @access protected
     */
    protected function validateKey(/*# string */ &$key)
    {
        // validate key
        if (is_string($key)) {
            $key = trim($key);
            return;
        }

        // throw exception
        throw new Exception\InvalidArgumentException(
            Message::get(Message::CACHE_INVALID_KEY, $key),
            Message::CACHE_INVALID_KEY
        );
    }
}
