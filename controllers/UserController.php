<?php
namespace controllers;

use models\User;
class UserController{
    public function regist(){
        view('users.add');
    }
    public function store(){
        //1接受表单

        // var_dump($_POST);?
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        //2 生成激活码
        $code = md5(rand(1,99999));
        // var_dump($code);
        //保存到redis
        $redis = \libs\Redis::getInstance();
        $value = json_encode([
            'email'=>$email,
            'password'=>$password,
        ]);
        //键名
        $key = "temp_user:{$code}";
        // var_dump($key,$value);
        $redis->setex($key,300,$value);
        // //插入到数据库
        // $user = new User;
        // $ret = $user->add($email,$password);
        // if(!$ret){
        //     die('注册失败');
        // }else{
            $name = explode('@',$email);
            $from = [$email,$name[0]];
            $message = [
                'title'=>'治疗系统账号激活',
                'content'=>"点击以下按钮进行激活<br>激活码是:{$code}",
                'from'=>$from,

            ];
            $message = JSON_encode($message);
            var_dump($message);
            //放到队列里
            $redis = \libs\Redis::getInstance();
            $redis->lpush('email',$message);
            // $mail = new \libs\Mail;
            // $content = "恭喜注册成功";
           
            // $mail->send('注册成功',$content,$from);
            echo "O98K";
        // }

    }
    public function activeUser(){
        //接受激活码
        $code = $_GET['code'];
        //到redis取激活码
        $redis = \libs\Redis::getInstance();
        //拼出名字
        $key = 'temp_user:'.$code;
        //
    }
}