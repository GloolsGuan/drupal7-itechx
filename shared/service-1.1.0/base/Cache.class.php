<?php
namespace service\base;
/**
 * ECache component is a startup engine for redis
 *
 * How can you use ECache?
 * $redis_cache = ECache::getCache();
 * $redis_cache->set();
 *
 * Reading more:  https://github.com/phpredis/phpredis#readme
 */



class Cache {


    public static $cache_redis = null;



    protected static function initStaticlly() {
        //$cache_conf = Lib_Gtools_System::loadConfig('global', 'cache');
        self::$cache_redis = new \Redis();

        $base = new Base();

        $config = $base->loadParams('servers/cache');

        $is_connected = self::$cache_redis->connect($config['host'], $config['port']);


        if (false==$is_connected) {
            throw new \Exception('Failed to connect to redis server.', '599');
        }

        self::$cache_redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        self::$cache_redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
    }

    public static function getCache() {

        self::initStaticlly();
        return self::$cache_redis;
    }
}