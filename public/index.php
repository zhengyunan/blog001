<?php
// phpinfo();
ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=0');  // 设置 redis 服务器的地址、端口、使用的数据库    
ini_set('session.gc_maxlifetime', 600);   // 设置 SESSION 10分钟过期
session_start();
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

// 跳转任意
function redirect($url){
    header('Location:'.$url);
    exit;
}
// 让网页跳回上一个页面
function back(){
    redirect($_SERVER['HTTP_REFERER']);
}

//操作成功

function message($message,$type,$url,$seconds=5){
     if($type==0){
        echo "<script>alert('{$message}');location.href='{$url}';</script>";
        exit;
     }else if($type == 1)
     {
         // 加载消息页面
         view('common.success', [
             'message' => $message,
             'url' => $url,
             'seconds' => $seconds
         ]);
     }
     else if($type==2)
     {
         // 把消息保存到 SESSION
         $_SESSION['_MESS_'] = $message;
         // 跳转到下一个页面
         redirect($url);
     }
}