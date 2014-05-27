<?php
/**
 * Created by PhpStorm.
 * User: ezequiel
 * Date: 5/26/14
 * Time: 5:32 PM
 */

namespace Towel\cache;


class Memcached implements Towel\cache\CacheInterface
{
    public function __construct($appConfig)
    {
        $config = $appConfig['memcached'];

        // Adding hosts.
        if (!empty($config['hosts'])) {
            foreach ($config['hosts'] as $host => $port) {
                $this->addserver($host, $port, false);
            }
        } else {
            $this->addserver('127.0.0.1', '11211', false);
        }
    }

    public function get($key)
    {
        return $this->get($key);
    }

    public function set($key, $value, $expire = null)
    {
        if (null === $expire) {
            $expire = time() + 3600 * 30; // 30 days by default (?)
        }
        $this->add($key, $value, false, $expire);
    }

    public function clear()
    {
        $this->clear();
    }

} 