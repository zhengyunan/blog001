<?php
// phpinfo();
ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=0');  // 设置 redis 服务器的地址、端口、使用的数据库    
ini_set('session.gc_maxlifetime', 600);   // 设置 SESSION 10分钟过期
session_start();

// 用户以post方式提交
// if($_SERVER['REQUEST_METHOD'] == 'POST')
// {
//     if(!isset($_POST['_token']))
//         die('违法操作！');

//     if($_POST['_token'] != $_SESSION['token'])
//         die('违法操作！');
// }

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

function e($content){
    return htmlspecialchars($content);
}

function hpe($content){
        static $purifier = null;
        if($purifier){
            // 1. 生成配置对象
            $config = \HTMLPurifier_Config::createDefault();

            // 2. 配置
            // 设置编码
            $config->set('Core.Encoding', 'utf-8');
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            // 设置缓存目录
            $config->set('Cache.SerializerPath', ROOT.'cache');
            // 设置允许的 HTML 标签
            $config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,ol[start],li,p[style],br,span[style],img[width|height|alt|src],*[style|class],pre,hr,code,h2,h3,h4,h5,h6,blockquote,del,table,thead,tbody,tr,th,td');
            // 设置允许的 CSS
            $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,margin,width,height,font-family,text-decoration,padding-left,color,background-color,text-align');
            // 设置是否自动添加 P 标签
            $config->set('AutoFormat.AutoParagraph', TRUE);
            // 设置是否删除空标签
            $config->set('AutoFormat.RemoveEmpty', TRUE);

            // 3. 过滤
            // 创建对象
            $purifier = new \HTMLPurifier($config);
        }
       
        // 过滤
        $clean_html = $purifier->purify($content);
        return $clean_html;
    }

    function csrf()
   {
        if(!isset($_SESSION['token']))
        {
            // 生成一个随机的字符串
            $token = md5( rand(1,99999) . microtime() );
            $_SESSION['token'] = $token;
        }
        return $token;
   }

   function csrf_field()
{
    $csrf = isset($_SESSION['token']) ? $_SESSION['token'] : csrf();
    echo "<input type='hidden' name='_token' value='{$csrf}'>";
}