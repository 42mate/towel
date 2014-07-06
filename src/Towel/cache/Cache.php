<?php
/**
 * Created by PhpStorm.
 * User: ezequiel
 * Date: 5/26/14
 * Time: 4:56 PM
 */

namespace Towel\cache;


class Cache
{
    const DEFAULT_KEY_PREFIX = 'towel';

    public function __construct(\Towel\cache\CacheInterface $cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    public function get($key)
    {
        return $this->cacheDriver->get($this->getKey($key));
    }

    public function set($key, $value, $expire = null)
    {
        $this->cacheDriver->set($this->getKey($key), $value, $expire);
    }

    public function clear()
    {
        $keys = $this->getDriverInstance()->getAllKeys();
        if (is_array($keys)) { //Bool will be returned in case of no keys, nothing to delete.
            $this->getDriverInstance()->deleteMulti($keys);
        }
    }

    public function delete($key)
    {
        $this->cacheDriver->delete($this->getKey($key));
    }

    public function getDriverInstance()
    {
        return $this->cacheDriver;
    }

    public function getKey($key) {
        $prefix = self::DEFAULT_KEY_PREFIX;
        $config = get_app()->config();
        if (!empty($config['cache']['prefix'])) {
            $prefix = $config['cache']['prefix'];
        }

        return $prefix . '_' . $key;
    }
}