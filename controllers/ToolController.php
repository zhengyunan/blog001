<?php
namespace controllers;
class ToolController
{
    public function users()
    {
        $model = new \models\User;
        $data = $model->getAll();
        echo json_encode([
            'status_code' => 200,
            'data' => $data,
        ]);
    }

    public function login(){
        $email = $_GET['email'];
        // 退出
        $_SESSION=[];
        $user = new \models\User;
        $user->login($email,md5('123123'));
    }
}