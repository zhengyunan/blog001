<?php
namespace controllers;
use PDO;
use models\Blog;
class BlogController{
    public function delete(){
        $id = $_POST['id'];
        $blog = new Blog;
        $blog->delete($id);
        $blog->deleteHtml($id);
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
        //返回新日志id
        $id=$blog->add($title,$content,$is_show);
        var_dump($id);
        //跳转
        if($is_show==1){
            $blog->makeHtml($id);
        }
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
        if($is_show==1){
            $blog->makeHtml($id);
        }else{
            $blog->deleteHtml($id);
        }
        message('修改成功',0,'/blog/index');

    }
    public function updated_display(){
        $id = (int)$_GET['id'];
        //链接radis
           $blog = new Blog;
           $display= $blog->getDisplay($id);
        //    返回多个数据用json
        echo json_encode([
            'display'=>$display,
            'email'=>isset($_SESSION['email']) ? $_SESSION['email']:'',
        ]);
           
    }
    public function displayToDb(){
        $blog = new Blog;
        $blog->displayToDb();
    }
    // 查看私有日志
    public function content(){
         $id = $_GET['id'];
         $model = new blog;
         $blog = $model->find($id);
         //判断这个日志是不是我的日志
         if($_SESSION['id']!=$blog['user_id'])
         die('无权访问');
         view('blogs.content',[
             'blog'=>$blog,
         ]);
    }
}