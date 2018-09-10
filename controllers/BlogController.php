<?php
namespace controllers;
use PDO;
use models\Blog;
class BlogController{
    public function delete(){
        $id = $_GET['id'];
        $blog = new Blog;
        $blog->delete($id);
        message('删除成功',2,'/blog/index');
    }
    
    public function create(){
        view('blogs.create');
    }

    public function store(){
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $blog = new Blog;
        $blog->add($title,$content,$is_show);
        //跳转
        message('发表日志成功',2,'/blog/index');

    }
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
        foreach($blog as $v){
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
    public function edit(){
        $id = $_GET['id'];
        $blog =new Blog;
        $data=$blog->find($id);
        view('blogs.edit',[
            'data'=>$data,
        ]);
    }

    public function update(){
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $id = $_POST['id'];
        $blog = new Blog;
        $blog->update($title,$content,$is_show,$id);
        message('修改成功',0,'/blog/index');

    }
    public function updated_display(){
        $id = (int)$_GET['id'];
        //链接radis
           $blog = new Blog;
           echo $blog->getDisplay($id);
           
    }
    public function displayToDb(){
        $blog = new Blog;
        $blog->displayToDb();
    }
}