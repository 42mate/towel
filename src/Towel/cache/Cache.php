<?php
/**
 * Created by PhpStorm.
 * User: ezequiel
 * Date: 5/26/14
 * Time: 4:56 PM
 */

namespace Towel\cache;


class Cache extends \Memcache implements Towel\cache\CacheInterface
{
    public function __construct(Towel\cache\CacheInterface $cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    public function get($key)
    {
        return $this->cacheDriver->get($key);
    }

    public function set($key, $value, $expire = null)
    {
        if (null === $expire) {
            $expire = time() + 3600 * 30; // 30 days by default (?)
        }
        $this->cacheDriver->add($key, $value, $expire);
    }

    public function clear()
    {
        $this->cacheDriver->clear();
    }
}