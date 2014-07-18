<?php

namespace Towel\Tests;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCache()
    {
        $this->assertInstanceOf('\Towel\cache\Cache', get_app()->getCache(), 'Is not an instance of Cache');
    }

    public function testCacheSetKey()
    {
        $cacheKey = 'testTowelCacheKey' . __METHOD__;
        $cachedVar = array($cacheKey => 123456);
        $cache = get_app()->getCache();
        $cache->set($cacheKey, $cachedVar, 60);
        $this->assertEquals($cachedVar, $cache->get($cacheKey), 'Stored Cache is different from cached value.');
    }

    public function testCacheGetKey()
    {
        $cache = get_app()->getCache();
        $cacheKey = 'testTowelCacheKey' . __METHOD__;
        $cachedVar = array($cacheKey => 123456);
        $cache->set($cacheKey, $cachedVar, 60);
        $this->assertEquals($cachedVar, $cache->get($cacheKey), 'Stored Cache is different from cached value.');

        $noExistantKey = 'SomeRandomKeyValue';
        $this->assertEmpty($cache->get($noExistantKey), 'Cache key: ' . $noExistantKey . ' is not empty.');
    }

    public function testCacheClear()
    {
        $cache = get_app()->getCache();
        $cache->clear();
        $this->assertEmpty($cache->getDriverInstance()->getAllKeys(), 'Cache keys found: ' . implode(',', $cache->getDriverInstance()->getAllKeys()));
    }
}