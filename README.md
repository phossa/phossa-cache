# phossa-cache
[![Build Status](https://travis-ci.org/phossa/phossa-cache.svg?branch=master)](https://travis-ci.org/phossa/phossa-cache.svg?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/phossa/phossa-cache.svg)](http://hhvm.h4cc.de/package/phossa/phossa-cache)
[![Latest Stable Version](https://poser.pugx.org/phossa/phossa-cache/v/stable)](https://packagist.org/packages/phossa/phossa-cache)
[![License](https://poser.pugx.org/phossa/phossa-cache/license)](https://packagist.org/packages/phossa/phossa-cache)

Introduction
---

Phossa-cache is a PSR-6 compliant caching library. It supports various drivers
and useful features like bypass, encrypt, stampede protection, garbage collect,
taggable item etc.

More information about [PSR-6](http://www.php-fig.org/psr/psr-6/) and
[PSR-6 Meta](http://www.php-fig.org/psr/psr-6/meta/)

Installation
---

Install via the `composer` utility.

```
composer require "phossa/phossa-cache=1.*"
```

or add the following lines to your `composer.json`

```json
{
    "require": {
       "phossa/phossa-cache": "^1.0.8"
    }
}
```

Features
---

- Fully PSR-6 compliant. Maybe the best PSR-6 caching package you will find
  at github at this time.

- Support all serializable PHP datatypes.

- **Extensions**:

  - **Bypass**: If sees a trigger in URL (e.g. '?nocache=true'), bypass the
    cache.

  - **Stampede**: Whenever cached object's lifetime is less than a configurable
    time, by certain percentage, the cache will return false on 'isHit()' which
    will trigger re-generation of the object.

  - **Encrypt**: A simple extension to encrypt the serialized content

  - **GarbageCollect**: A simple extension to auto-clean the cache pool.

  - **Taggable**: Item is taggable and can be cleared by tag.

  - **DistributeMiss**: Even out the spikes of item misses by alter expiration
    time a little bit.

- **Drivers**

  - **FilesystemDriver**

    The filesystem driver stores cached item in filesystem. It stores cached
    items in a md5-filename flat file. Configurable settings are

    - `dir_root`: the base directory for the filesystem cache
    - `hash_level`: hashed subdirectory level. default to 2
    - `file_pref`: cached item filename prefix
    - `file_suff`: cached item filename suffix

    ```php
    /*
     * construct the driver manually
     */
    $driver = new Driver\FilesystemDriver([
        'hash_level'    => 1,
        'file_pref'     => 'cache.',
        'dir_root'      => '/var/tmp/cache',
    ]);
    ```

  - **NullDriver**

    The blackhole driver. used as fallback driver for all other drivers.

  - **Fallback driver**

    User may configure a fallback driver if the desired driver is not ready.
    The `NullDriver` is the final fallback for all other drivers.

    ```php
    // default memcache driver
    $driver = new Driver\MemcacheDriver([
        'server' => [ '127.0.0.1', 11211 ]
    ]);

    // set a fallback filesystem driver
    $driver->setFallback(new Driver\FilesystemDriver([
        'dir_root' => '/var/tmp/cache'
    ]));

    $cache = new CachePool($driver);
    ```

  - **CompositeDriver**

    The `CompositeDriver` consists of two drivers, the front-end driver and
    the backend driver. User filters cachable objects by defining a `tester`
    callable which will determine which objects stores to both ends or backend
    only.

    ```php
    /*
     * set the composite driver
     */
    $driver = new Driver\CompositeDriver(
        // front-end driver
        new Driver\MemcacheDriver([
            'server' => [ '127.0.0.1', 11211 ]
        ]),

        // backend driver
        new Driver\FilesystemDriver([
            'dir_root' => '/var/tmp/cache'
        ]),

        // other settings
        [
            // if size > 10k, stores at backend only
            'tester' => function($item) {
                if (strlen($item->get()) > 10240) return false;
                return true;
            }
        ]
    );
    ```

- **Logging**

  The phossa-cache supports psr-3 compliant logger. Also provides a `log()`
  method for logging.

  ```php
  /*
   * set the logger
   */
  $cache->setLogger($psrLogger);
  $cache->log('info', 'this is an info');
  ```

  Or configure with logger at cache init

  ```php
  /*
   * the third argument is used for configuring CachePool
   */
  $cache = new CachePool($driver, [],
      'logger' => $psrLogger
  );
  $cache->log('info', 'this is an info');
  ```

- **Error**

  No exceptions thrown during caching process. So only errors will be used.

  ```php
  /*
   * create cache pool, exceptions may thrown here
   */
  $cache = new CachePool();
  $cache->setLogger($psrLogger);

  $item = $cache->getItem('widget_list');
  $val  = $item->get();
  if ($cache->hasError()) {
      $cache->log('error', $cache->getError());
      $widget_list = compute_expensive_widget_list();
      $item->set($widget_list);
      $item->expiresAfter(3600); // expires after an hour
      $cache->save($item);
      if ($cache->hasError()) $cache->log('error', $cache->getError());
  } else {
      $widget_list = $val;
  }
  ```

- I18n

  Messages are in `Message\Message.php`. I18n is possible. See phossa-shared
  package for detail.

- Support PHP 5.4+, PHP 7.0, HHVM.

- PHP7 ready for return type declarations and argument type declarations.

- PSR-1, PSR-2, PSR-3, PSR-4, PSR-6 compliant.

Usage
--

- The simple usage

    ```php
    /*
     * use the default FilesystemDriver which also set default cache
     * directory to sys_get_temp_dir() .'/cache'
     */
    $cache = new CachePool();

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
    $driver = new Driver\FilesystemDriver([
        'hash_level'    => 1, // subdirectory hash levels
        'file_pref'     => 'cache.', // cache file prefix
        'file_suff'     => '.txt',   // cache file suffix
        'dir_root'      => '/var/tmp/cache' // reset cache root
    ]);

    $cache = new CachePool($driver);
    ```

- Use extensions

    ```php
    /*
     * SerializeExtension is the default ext, always used.
     * Second argument is an array of ExtensionInterface or config array
     */
    $cache = new CachePool(
        $driver,
        [
            new Extension\BypassExtension(),
            new Extension\StampedeExtension(['probability' => 80 ])
        ]
    );
    ```

  or `addExtension()`

    ```php
    $cache = new CachePool($driver);
    $cache->addExtension(new Extension\BypassExtension());
    ```

- Hierarchal cache support

  Directory-style hierarchal structure is supported in `FilesystemDriver` and
  so other coming drivers.

    ```php
    // hierarchy key
    $item = $cache->getItem('mydomain/host1/newfile_xxx');

    // ending '/' means delete the hierarchy structure
    $cache->deleteItem('mydomain/host1/');
    ```

Dependencies
---

- PHP >= 5.4.0
- phossa/phossa-shared 1.0.6
- psr/cache 1.*
- psr/log 1.*

License
---

[MIT License](http://spdx.org/licenses/MIT)