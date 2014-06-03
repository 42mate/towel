<?php

namespace Towel\cache;

class TowelMemcached extends \Memcached implements CacheInterface
{
    /**
     * Stablish Memcached options defined in config.php
     * @param $options
     */
    public function setOptions($options)
    {
        // Adding hosts.
        if (!empty($options['hosts'])) {
            foreach ($options['hosts'] as $host => $port) {
                $this->addserver($host, $port, false);
            }
        } else {
            $this->addserver('127.0.0.1', '11211', false);
        }
    }

    public function clear()
    {
        parent::flush();
    }
}