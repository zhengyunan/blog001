<?php
namespace controllers;
class IndexController {
    public function index(){
        // view('index.index');
        $blog = new \models\Blog;
        $blogs = $blog->getNew();
        view('index.index', [
            'blogs' => $blogs
        ]);
    }
}