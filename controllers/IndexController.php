<?php
namespace controllers;
class IndexController {
    public function index(){
        // view('index.index');
        $blog = new \models\Blog;
        $blogs = $blog->getNew();
        $user = new \models\User;
        $users = $user->getActiveUser();
        view('index.index', [
            'blogs' => $blogs,
            'users'=>$users,
        ]);
    }
}