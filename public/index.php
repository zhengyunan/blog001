<?php
define('ROOT',dirname(__FILE__) . '/../');

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
