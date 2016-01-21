# Introduction

Phossa-cache is a PSR-6 compliant caching package. It supports various drivers
and uses extension hook to be feature-rich.

More information about [PSR-6](http://www.php-fig.org/psr/psr-6/) and
[PSR-6 Meta](http://www.php-fig.org/psr/psr-6/meta/)

# Installation

Install via the `composer` utility.

```
composer require "phossa/cache=1.*"
```

# Features

- Fully PSR-6 compliant. Maybe the best PSR-6 caching package you will find
  at github at this time.

- Support PHP 5.4+.

- PHP7 ready for return type declarations and argument type declarations.

- Extensions:

  - **Bypass**: If sees a trigger in URL (e.g. '?nocache=true'), bypass the
    cache.

  - **Stampede**: Whenever cached object's lifetime is less than a configurable
    time, by certain percentage, the caching return false on 'isHit()' which
    will trigger re-generation of the object.

  - **Encrypt**: a simple extension to encrypt the serialized content

  - **GarbageCollect**: a simple extension to auto-clean the cache pool.

- Drivers

  - Filesystem

    The filesystem driver stores cached item in filesystem. It stores cached
    items in a md5-filename flat file. Configurable settings are

    - 'dir_root': the base directory for the filesystem cache
    - 'hash_level': hashed subdirectory level. default to 2
    - 'file_pref': cached item filename prefix
    - 'file_suff': cached item filename suffix

    ```php
    /*
     * construct the driver manually
     */
    $driver = new \Phossa\Cache\Driver\FilesystemDriver([
        'hash_level'    => 1,
        'file_pref'     => 'cache.',
        'dir_root'      => '/var/tmp/cache',
    ]);
    ```

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

- Use extensions

    ```php
    /*
     * SerializeExtension is the default ext, always used.
     * Second argument is an array of ExtensionInterface or config array
     */
    $cache = new \Phossa\Cache\CachePool(
        [],
        [
            [ 'className' => 'BypassExtension' ],
            [ 'className' => 'StampedeExtension', 'probability' => 80 ]
        ]
    ]);
    ```

# Version
1.0.0

# Dependencies

- PHP >= 5.4.0
- Phossa-shared 1.*
- Psr/Log 1.*

# License
[MIT License](http://spdx.org/licenses/MIT)