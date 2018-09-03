<?php
namespace controllers;
// use PDO;
use models\Blog;
class BlogController{
    public function index(){
        $blog = new Blog;
        $data=$blog->search();
        // echo '<pre>';
        // var_dump($data);
        view('blogs.index',$data);
    }


    public function content_to_html(){
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=mvc",'root','');
        $pdo->exec('SET NAMES utf8');
        $stmt = $pdo->query('SELECT * FROM mvc_blogs');
        $blog = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ob_start();
        foreach($blogs as $v){
            view('blogs.content',[
                'blog'=>$v
            ]);
           $str = ob_get_contents();
        //    var_dump($str);
        //    die;
           file_put_contents(ROOT.'public/contents/'.$v['id'].'.html',$str);
           ob_clean();
        }
        // echo "avvv";
    }
    public function index2html(){
        $blog =new Blog;
        $blog->index2html();
    }
}