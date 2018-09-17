<?php
namespace controllers;
class RedbagController{
    //  初始化
    public function init(){
        $redis = \libs\Redis::getInstance();
        // 初始化库存量
        $redis->set('redbag_stock',20);
        // 初始化空的集合
        $key = 'redbag_'.date('Ymd');
        $redis->sadd('$key','-1');
        // 设置过期时间
        $redis->expire($key,5900);

    }
}