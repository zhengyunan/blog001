<?php
namespace controllers;

use models\User;
class UserController{
    public function hello(){
        $user = new user;
        $name = $user->getName();
        //加载视图
        view('user.hello',[
            'name'=>$name
        ])
    }
}