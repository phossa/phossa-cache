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

use Phossa\Cache\CachePoolInterface;
use Phossa\Cache\Extension\ExtensionStage as ES;

/**
 * Implementation of CacheItemInterface
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\CacheItemInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class CacheItem implements CacheItemInterface
{
    /**
     * cache pool
     *
     * @var    CachePoolInterface
     * @access protected
     */
    protected $cache;

    /**
     * item key
     *
     * @var    string
     * @access protected
     */
    protected $key;

    /**
     * is Hit ?
     *
     * @var    bool
     * @access protected
     */
    protected $hit;

    /**
     * is GET processed?
     *
     * @var    bool
     * @access protected
     */
    protected $done;

    /**
     * value
     *
     * @var    mixed
     * @access protected
     */
    protected $value;

    /**
     * default expiration time in seconds
     *
     * @var    int
     * @access protected
     */
    protected $expire = 0x7fffffff;

    /**
     * default TTL in seconds
     *
     * @var    int
     * @access protected
     */
    protected $ttl    = 3600;

    /**
     * Constructor
     *
     * @param  string $key item key
     * @param  CacheItemPoolInterface $cache the cache pool
     * @param  array $settings (optional) item settings
     * @access public
     */
    public function __construct(
        /*# string */ $key,
        CachePoolInterface $cache,
        array $settings = []
    ) {
        // set key
        $this->key    = $key;

        // set cache
        $this->cache  = $cache;

        // set configs
        if ($settings) {
            foreach($settings as $k => $v) {
                if (isset($this->$k)) $this->$k = $v;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()/*# : string */
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function get()
    {
        // get/set already ?
        if ($this->done) return $this->value;
        $this->done = true;

        // check pool has item
        if ($this->isHit()) {
            // before get
            if (!$this->cache->runExtensions(ES::STAGE_PRE_GET, $this)) {
                return $this->value;
            }

            $val = $this->cache->getDriver()->get($this->key);

            // set value
            $this->set($val);

            // after get
            $this->cache->runExtensions(ES::STAGE_POST_GET, $this);
        }

        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function isHit()/*# : bool */
    {
        if (!is_bool($this->hit)) {
            // before has
            if (!$this->cache->runExtensions(ES::STAGE_PRE_HAS, $this)) {
                $this->hit = false;
                return $this->hit;
            }

            $this->expire = $this->cache->getDriver()->has($this->key);
            $this->hit = $this->expire < time() ? false : true;

            // after has
            $this->cache->runExtensions(ES::STAGE_POST_HAS, $this);
        }

        return $this->hit;
    }

    /**
     * {@inheritDoc}
     */
    public function setHit(/*# bool */ $status)
    {
        $this->hit = (bool) $status;
    }

    /**
     * {@inheritDoc}
     */
    public function set($value)/*# : CacheItemInterface */
    {
        $this->done  = true;
        $this->value = $value;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function expiresAt($expiration)/*# : CacheItemInterface */
    {
        if ($expiration === null) {
            $this->expire = time() + $this->ttl;
        } else if ($expiration instanceof \DateTimeInterface) {
            /* @var $expiration \DateTimeInterface */
            $this->expire = $expiration->getTimestamp();
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function expiresAfter($time)/*# : CacheItemInterface */
    {
        if (is_int($time)) {
        } else if ($time instanceof \DateInterval) {
            $time = (int) $time->format("%s");
        } else {
            $time = $this->ttl;
        }
        $this->expire = time() + $time;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiration()/*# : \DateTime */
    {
        return new \DateTime('@' . $this->expire);
    }
}
