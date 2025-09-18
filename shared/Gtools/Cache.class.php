<?php
namespace Gtools;
/* 
 * PHP-REDIS DOCS https://github.com/phpredis/phpredis/
 */

class Cache{
    
    static $redis_servers = [];
    
    public static function getServer($ip='127.0.0.1', $port=6379){
        
        $skey = sprintf('%s:%d', $ip, $port);
        
        if (array_key_exists($skey, self::$redis_servers) && self::$redis_servers[$skey] instanceof \Redis){
            try{
                $redis_client = self::$redis_servers[$skey];
                $str_ping = $redis_client->ping();
                
                return $redis_client;
            }catch(\RedisException $re){
                return self::connect($ip, $port);
            }
        }
        
        return self::connect($ip, $port);
    }
    
    
    
    protected static function connect($ip, $port){
        $redis_client = new \Redis();
        $skey = sprintf('%s:%d', $ip, $port);
        
        $is_connected = $redis_client->connect($ip, $port);
        
        if (true==$is_connected){
            self::$redis_servers[$skey] = $redis_client;
            return $redis_client;
        }
        
        throw new \RedisException(sprintf('Failed to conenct to redis server with parameter "%s".', $skey));
        
        return false;
    }   
    
}
