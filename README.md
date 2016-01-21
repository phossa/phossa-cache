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

```php
    use Phossa\Cache

    // use config array to set up the driver
    $driver = new Cache\Driver\FilesystemDriver([
        'hash_level'    => 1,
        'file_pref'     => 'cache.',
        'file_suff'     => '.txt'
    ]);

    /*
     * init cache with driver and extensions (objects or config arrays)
     */
    $cache = new Cache\CachePool(
        $driver,
        [
            [ 'className' => 'BypassExtension'],
            [ 'className' => 'StampedeExtension', 'probability' => 80 ]
        ]
    );

    $item = $cache->getItem('widget_list');
    if (!$item->isHit()) {
        $value = compute_expensive_widget_list();
        $item->set($value);
        $cache->save($item);
    }
    $widget_list = $item->get();

```

# Version
1.0.0

# Dependencies

- PHP >= 5.4.0
- Phossa-shared 1.*
- Psr/Log 1.*

# License
MIT License