# Introduction

Phossa-cache is PSR-6 compliant caching package. It supports various drivers
and uses extension hooks to be feature-rich.

# Features

- Fully PSR-6 compliant. Maybe the best PSR-6 caching package you will find
  at github at this time.

- Support PHP 5.4+.

- PHP7 ready for return type declarations and argument type declarations.

- Extensions:

  - Bypass: If sees a trigger in URL (e.g. '?nocache=true'), bypass the cache.

  - Stampede: Whenever cached object's lifetime is less than a configurable
    time, by certain percentage, the caching return false on 'isHit()' which
    will trigger re-generation of the object.

  - Encrypt: a simple extension to encrypt the serialized content

  - GarbageCollect: a simple extension to auto-clean the cache pool.

- Drivers

  - Filesystem

  - Null

  - Fallback drivers

  - Composite driver

- Logging

- I18n

# Usage

- The simplest usage

    ```php
    /*
     * use the default FilesystemDriver which also set default cache
     * directory to sys_get_temp_dir() .'/cache'
     */
    $cache = new \Phossa\Cache\CachePool();

    $item = $cache->getItem('widget_list');
    if (!$item->isHit()) {
        $value = compute_expensive_widget_list();
        $item->set($value);
        $cache->save($item);
    }
    $widget_list = $item->get();
    ```
- Configure the driver

    ```php
    /*
     * the first argument is a DriverInterface or driver config array
     */
    $cache = new \Phossa\Cache\CachePool([
        'className'     => 'FilesystemDriver',
        'hash_level'    => 1, // subdirectory hash levels
        'file_pref'     => 'cache.', // cache file prefix
        'file_suff'     => '.txt',   // cache file suffix
        'dir_root'      => '/var/tmp/cache', // reset cache root
    ]);
    ```

# Version
1.0.0

# Dependencies

- PHP >= 5.4.0
- Phossa-shared 1.*
- Psr/Log 1.*

# License
MIT License