<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache;

use Phossa\Cache\Message\Message;
use Phossa\Cache\Extension\ExtensionStage as ES;

/**
 * Cache class which implements CachePoolInterface
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Psr\Cache\CacheItemPoolInterface
 * @see     \Phossa\Cache\CacheItemInterface
 * @see     \Phossa\Cache\Misc\ErrorAwareInterface
 * @see     \Phossa\Cache\Driver\DriverAwareInterface
 * @see     \Phossa\Cache\Extension\ExtensionAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class CachePool implements CachePoolInterface
{
    use Driver\DriverAwareTrait,
        Extension\ExtensionAwareTrait;

    /**
     * item factory method, signatures as follows
     *
     *     function(
     *          string $key,
     *          CachePoolInterface $pool,
     *          array $configs = []
     *     ): CacheItem
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
     * default extensions
     *
     * @var    array
     * @access protected
     */
    protected $default_ext = [
        ['className' => '\Phossa\Cache\Extension\SerializeExtension']
    ];

    /**
     * Cache pool constructor
     *
     * @param  array|Driver\DriverInterface $driver driver or driver settings
     * @param  Extension\ExtensionInterface[] $extensions extensions
     * @param  array $configs (optional) cache pool configuration
     * @throws Exception\InvalidArgumentException
     *         if not valid driver or extensions found
     * @access public
     */
    public function __construct(
        $driver,
        array $extensions = [],
        array $configs = []
    ) {
        // set configs
        if ($configs) {
            foreach($configs as $name => $value) {
                if (isset($this->$name)) {
                    $this->$name = $value;
                }
            }
        }

        // set driver
        $this->setDriver($driver);

        // load default extensions
        $this->setExtensions($this->default_ext);

        // load user-defined extensions
        if ($extensions) $this->setExtensions($extensions);

        // run extensions STAGE_INIT
        $this->runExtensions(ES::STAGE_INIT);
    }

    /**
     * Cache pool destructor
     *
     * @param  void
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
        if (!($res = $this->driver->clear())) {
            $this->setError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after clear
        $this->runExtensions(ES::STAGE_POST_CLEAR);

        return $res;
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
        $res = $this->driver->delete($item->getKey());
        if (!$res) {
            $this->setError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after delete
        $this->runExtensions(ES::STAGE_POST_DEL, $item);

        return $res;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteItems(array $keys)/*# : bool */
    {
        foreach($keys as $key) {
            if (!$this->deleteItem($key)) return false;
        }
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function save(CacheItemInterface $item)/*# : bool */
    {
        // extension may change $item
        $clone = clone $item;

        // before save
        if (!$this->runExtensions(ES::STAGE_PRE_SAVE, $clone)) return false;

        // write to the pool
        if (!($res = $this->driver->save($clone))) {
            $this->setError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after save
        $this->runExtensions(ES::STAGE_POST_SAVE, $clone);

        return $res;
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item)/*# : bool */
    {
        // extensions may change $item
        $clone = clone $item;

        // before deferred
        if (!$this->runExtensions(ES::STAGE_PRE_DEFER, $clone)) return false;

        // write to the pool
        if (!($res = $this->driver->saveDeferred($clone))) {
            $this->setError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after deferred
        $this->runExtensions(ES::STAGE_POST_DEFER, $clone);

        return $res;
    }

    /**
     * {@inheritDoc}
     */
    public function commit()/*# : bool */
    {
        // before commit
        if (!$this->runExtensions(ES::STAGE_PRE_COMMIT)) return false;

        // commit to pool
        if (!($res = $this->driver->commit())) {
            $this->setError(
                $this->driver->getError(),
                $this->driver->getErrorCode()
            );
        }

        // after commit
        $this->runExtensions(ES::STAGE_POST_COMMIT);

        return $res;
    }

    /**
     * Create item object
     *
     * @param  string $key item key
     * @return CacheItemInterface
     * @throws Exception\InvalidArgumentException
     *         if $throwException is true and $key not a valid key
     * @access protected
     */
    protected function createItem(
        /*# string */ $key
    )/*# : CacheItemInterface */ {
        // validate key first
        $this->validateKey($key);

        // use item factory
        if (is_callable($this->item_factory)) {
            $func = $this->item_factory;
            $item = $func($key, $this, $this->item_config);

        // default
        } else {
            $item = new CacheItem($key, $this, $this->item_config);
        }

        return $item;
    }

    /**
     * Validate key string
     *
     * @param  string $key key to check
     * @return void
     * @throws Exception\InvalidArgumentException
     *         if $throwException is true and $key not a valid key
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
            Message::get(Message::CACHE_INVALID_KEY, (string) $key),
            Message::CACHE_INVALID_KEY
        );
    }
}