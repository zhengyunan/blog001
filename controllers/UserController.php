<?php
namespace controllers;

use models\User;
class UserController{
    public function regist(){
        view('users.add');
    }
    public function store(){
        //接受表单

        // var_dump($_POST);?
        $email = $_POST['email'];
        $password = $_POST['password'];
        //插入到数据库
        $user = new User;
        $ret = $user->add($email,$password);
        if(!$ret){
            die('注册失败');
        }else{
            $name = explode('@',$email);
            $from = [$email,$name[0]];
            $message = [
                'title'=>'欢迎加入全栈一般',
                'content'=>"点击以下按钮进行激活<br><a href =''点击激活</a>",
                'from'=>$from,

            ];
            $message = JSON_encode($message);
            //放到队列里
            $redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
            // $redis->lpush('email',$message);
            // $mail = new \libs\Mail;
            // $content = "恭喜注册成功";
           
            // $mail->send('注册成功',$content,$from);
            echo "O98K";
        }

    }
    public function hello(){
        $user = new user;
        $name = $user->getName();
        //加载视图
        view('users.hello', [
            'name'=>$name
        ]);
    }
    public function word(){
        echo "word";
    }
}