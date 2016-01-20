<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Driver;

use Phossa\Cache\Message\Message;
use Phossa\Cache\CacheItemInterface;

/**
 * FilesystemDriver
 *
 * @package \Phossa\Shared
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class FilesystemDriver extends DriverAbstract
{
    /**
     * root directory. if empty, default to system temp dir + '/cache'
     *
     * @var    string
     * @access protected
     */
    protected $dir_root     = '';

    /**
     * subdirectory hash level 0 - 5
     *
     * @var    int
     * @access protected
     */
    protected $hash_level   = 2;

    /**
     * filename prefix
     *
     * @var    string
     * @access protected
     */
    protected $file_pref    = '';

    /**
     * filename suffix
     *
     * @var    string
     * @access protected
     */
    protected $file_suff    = '';

    /**
     * Construct with configs/settings
     *
     * @param  array $configs object configs
     * @access public
     */
    public function __construct(array $configs = [])
    {
        // parent constructor
        parent::__construct($configs);

        // default cache directory
        if (empty($this->dir_root)) {
            $this->dir_root = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cache';
        }

        // clean up
        $this->dir_root = rtrim($this->dir_root, " \t\r\n\0\x0B\\/");

        // error in root dir
        if (!is_dir($this->dir_root) && !mkdir($this->dir_root, 0777, true)) {
            // set error
            $this->falseAndSetError(
                Message::get(Message::CACHE_FAIL_MKDIR, $this->dir_root),
                Message::CACHE_FAIL_MKDIR
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(
        /*# string */ $key
    )/*# : string */ {
        $file = $this->getPath($key);
        return file_get_contents($file);
    }

    /**
     * {inheritDoc}
     */
    public function has(
        /*# string */ $key
    )/*# : int */ {
        $file = $this->getPath($key);
        if (file_exists($file)) return filemtime($file);
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()/*# : bool */
    {
        return $this->deleteFromDir($this->dir_root);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(
        /*# string */ $key
    )/*# : bool */ {
        $file = $this->getPath($key);

        // directory
        if (is_dir($file)) return $this->deleteFromDir($file, 0, true);

        // file
        if (file_exists($file) && !unlink($file)) {
            return $this->falseAndSetError(
                Message::get(Message::CACHE_FAIL_DELETE, $key),
                Message::CACHE_FAIL_DELETE
            );
        }

        // success
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function save(
        CacheItemInterface $item
    )/*# : bool */ {
        $key  = $item->getKey();
        $file = $this->getPath($key);

        // make sure directory exits
        $dir  = dirname($file);
        if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
            return $this->falseAndSetError(
                Message::get(Message::CACHE_FAIL_MKDIR, $dir),
                Message::CACHE_FAIL_MKDIR
            );
        }

        // write to file
        if (($tmp = tempnam($dir, 'temp_')) === false ||
            file_put_contents($tmp, $item->get()) === false) {
            return $this->falseAndSetError(
                Message::get(Message::CACHE_FAIL_WRITEFILE, $key),
                Message::CACHE_FAIL_WRITEFILE
            );
        }
        chmod($tmp, 0640);

        // rename to $file
        if (rename($tmp, $file) === false) {
            return $this->falseAndSetError(
                Message::get(Message::CACHE_FAIL_WRITEFILE, $file),
                Message::CACHE_FAIL_WRITEFILE
            );
        }

        // set expire time
        touch($file, $item->getExpiration()->getTimestamp());

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(
        CacheItemInterface $item
    )/*# : bool */ {
        return $this->save($item);
    }

    /**
     * {@inheritDoc}
     */
    public function commit()/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function purge(
        /*# int */ $maxlife
    )/*# : bool */ {
        return $this->deleteFromDir($this->dir_root, $maxlife, false);
    }

    /**
     * Get path base on key.
     *
     * $key may contain '/' to signal a path
     *
     * @param  string $key item key
     * @return string
     * @access protected
     */
    protected function getPath(/*# string */ $key)/*# : string */
    {
        if (($pos = strrpos($key, '/')) !== false) {
            $dir  = $this->dir_root . DIRECTORY_SEPARATOR .
                    substr($key, 0, $pos) . DIRECTORY_SEPARATOR;
            $key  = substr($key, $pos + 1);
        } else {
            $dir  = $this->dir_root . DIRECTORY_SEPARATOR;
        }
        $file = $key ? $this->hashIt($key) : '';

        return $dir . $file;
    }

    /**
     * Get hashed filename
     *
     * @param  string $key the key
     * @return string
     * @access protected
     */
    protected function hashIt(/*# string */ $key)/*# : string */
    {
        $md5  = md5($key);
        $hash = $this->file_pref . $md5 . $this->file_suff;

        // no hash
        if (!$this->hash_level) return $hash;

        // hash
        $pref = '';
        for($i = 0; $i < $this->hash_level; $i++) {
            $pref .= $md5[$i] . '/';
        }
        return $pref . $hash;
    }

    /**
     * Remove all contents under one directory
     *
     * @param  string $dir directory
     * @param  int $maxlife delete those older than $maxlife seconds
     * @param  bool $removeDir remove the directory also
     * @return bool
     * @access protected
     */
    protected function deleteFromDir(
        /*# string */ $dir,
        /*# int */ $maxlife = 0,
        /*# bool */ $removeDir = false
    )/*# : bool */ {
        $now = time();
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file == "." || $file == "..") continue;
                $sub = $dir . DIRECTORY_SEPARATOR . $file;
                $res = true;
                if (is_dir($sub)) {
                    $res = $this->deleteFromDir($sub, $maxlife, true);
                } else {
                    if (!$maxlife || $now - filectime($sub) > $maxlife) {
                        $res = unlink($sub);
                    }
                }
                if ($res === false) {
                    return $this->falseAndSetError(
                        Message::get(Message::CACHE_FAIL_DELETE, $sub),
                        Message::CACHE_FAIL_DELETE
                    );
                }
            }
            if ($removeDir) @rmdir($dir);
        }
        return true;
    }
}
