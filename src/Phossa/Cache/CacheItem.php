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
     * default expiration time in seconds, max is 0x7fffffff
     *
     * @var    int
     * @access protected
     */
    protected $expire = 0;

    /**
     * default TTL in seconds
     *
     * @var    int
     * @access protected
     */
    protected $ttl    = 28800;

    /**
     * tags
     *
     * @var    string[]
     * @access protected
     */
    protected $tags   = [];

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
        // set configs
        if ($settings) {
            foreach($settings as $k => $v) {
                if (isset($this->$k)) $this->$k = $v;
            }
        }

        // set key
        $this->key    = $key;

        // set cache
        $this->cache  = $cache;
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

        // check pool has item
        $this->done = true;
        if ($this->isHit()) {
            // before get
            if (!$this->cache->runExtensions(ES::STAGE_PRE_GET, $this)) {
                $this->setHit(false);
                return $this->value;
            }

            $val = $this->cache->getDriver()->get($this->key);

            // set value
            $this->set($val);

            // after get
            if (!$this->cache->runExtensions(ES::STAGE_POST_GET, $this)) {
                $this->setHit(false);
                $this->set(null);
                return $this->value;
            }
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
                return $this->setHit(false);
            }

            $this->expire = $this->cache->getDriver()->has($this->key);
            $this->hit = $this->expire < time() ? false : true;

            // after has
            if (!$this->cache->runExtensions(ES::STAGE_POST_HAS, $this)) {
                return $this->setHit(false);
            }
        }

        return $this->hit;
    }

    /**
     * {@inheritDoc}
     */
    public function setHit(
        /*# bool */ $status
    )/*# : bool */ {
        $this->hit = (bool) $status;
        return $this->hit;
    }

    /**
     * {@inheritDoc}
     */
    public function set($value)/*# : CacheItemInterface */
    {
        // mark it gettable
        $this->done  = true;

        // set value
        $this->value = $value;

        // set default expire
        if ($this->expire === 0) $this->expiresAfter($this->ttl);

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
            var_dump($expiration);
            $this->expire = $expiration->getTimestamp();
            var_dump($expiration->getTimestamp());
            var_dump($this->expire);
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

    /**
     * {@inheritDoc}
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()/*# : array */
    {
        return $this->tags;
    }
}
