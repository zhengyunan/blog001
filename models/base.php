<?php

// 所有其他模型中的父模型

namespace models;
use PDO;
class Base{

    public static $pdo=null;
    public function __construct()
    {   
        if(self::$pdo===null){
            // 取日志的数据
            $config = config('db');
       self::$pdo = new PDO('mysql:host='.$config['host'].';dbname='.$config['dbname'], $config['user'], $config['pass']);
       self::$pdo->exec('SET NAMES '.$config['charset']);
        }
        
    }

    // 开启事务
    public function startTrans()
    {
        self::$pdo->exec('start transaction');
    }

    // 提交事务
    public function commit()
    {
        self::$pdo->exec('commit');
    }

    // 回滚事务
    public function rollback()
    {
        self::$pdo->exec('rollback');
    }
}