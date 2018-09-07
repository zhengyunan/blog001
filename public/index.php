<?php
// 动态的修改 php.ini 配置文件
// ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
// ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=15');  // 设置 redis 服务器的地址、端口、使用的数据库
// session_start();
define('ROOT',dirname(__FILE__) . '/../');
require(ROOT.'vendor/autoload.php');
function autoload($class){
    $path = str_replace('\\','/',$class);
    // echo ROOT .$path. 'php';
    require(ROOT . $path . '.php');
}
spl_autoload_register('autoload');

if(php_sapi_name() == 'cli')
{
    $controller = ucfirst($argv[1]) . 'Controller';
    $action = $argv[2];
}
else
{
if(isset($_SERVER['PATH_INFO'])){
    $pathInfo =explode('/' , $_SERVER['PATH_INFO']);
    // var_dump($_SERVER['PATH_INFO']);
    // var_dump($pathInfo);
    $controller =ucfirst($pathInfo[1]) . 'Controller';
    $action = $pathInfo[2];
    // var_dump($controller);

}else {
    $controller = 'IndexController';
    $action = 'index';
}
}
//为控制器添加命名空间
$fullController = 'controllers\\'.$controller;
$_C = new $fullController;
$_C->$action();
// $userController = new controllers\UserController;
// $userController->hello();

function view($viewFileName,$data=[]){
    extract($data);
    $path = str_replace('.','/',$viewFileName) .'.html';
    require(ROOT.'views/'.$path);
}

function config($name){
    //引入配置文件
    static $config = null;
    if($config===null){
        $config = require(ROOT.'config.php');
    }
    
    return $config[$name];
}