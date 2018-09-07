<?php
namespace libs;

//单例模式   三私一公
class Redis{
    private static $redis = null;
    private function __clone(){}
    private function __construct(){}
    
    public static function getInstance(){
        $config = config('redis');
        if(self::$redis===null){
            self::$redis = new \Predis\Client($config);
          }
        return self::$redis;
    }

}