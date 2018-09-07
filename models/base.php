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
}