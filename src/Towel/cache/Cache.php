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
    public function __construct(\Towel\cache\CacheInterface $cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    public function get($key)
    {
        return $this->cacheDriver->get($key);
    }

    public function set($key, $value, $expire = null)
    {
        $this->cacheDriver->set($key, $value, $expire);
    }

    public function clear()
    {
        $this->cacheDriver->clear();
    }

    public function delete($key)
    {
        $this->cacheDriver->delete($key);
    }

    public function getDriverInstance()
    {
        return $this->cacheDriver;
    }
}