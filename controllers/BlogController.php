<?php
namespace controllers;
use PDO;
use models\Blog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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


    public function makeExcel(){
         // 获取当前标签页
        $spreadsheet = new Spreadsheet();
        // 获取当前工作
        $sheet = $spreadsheet->getActiveSheet();

        // 设置第1行内容
        $sheet->setCellValue('A1', '标题');
        $sheet->setCellValue('B1', '内容');
        $sheet->setCellValue('C1', '发表时间');
        $sheet->setCellValue('D1', '是发公开');
        // 获取最新的十个日志
        $blog = new Blog;
        $data=$blog->getNew();
        $i=2;
        // var_dump($data)
        foreach($data as $v){
            $sheet->setCellValue('A'.$i, $v['title']);
            $sheet->setCellValue('B'.$i, $v['content']);
            $sheet->setCellValue('C'.$i, $v['created_at']);
            $sheet->setCellValue('D'.$i, $v['is_show']==1?'公开':'私有');
            $i++;
        }
        $date = date('Ymd');
         // 生成 Excel 文件
        $writer = new Xlsx($spreadsheet);
        
        $writer->save(ROOT . 'execl/'.$date.'.xlsx');

        // 调用 header 函数设置协议头，告诉浏览器开始下载文件

        // 下载文件路径
        $file = ROOT . 'excel/'.$date.'.xlsx';
        // 下载时文件名
        $fileName = '最新的20条日志-'.$date.'.xlsx';

        // 告诉浏览器这是一个二进程文件流    
        Header ( "Content-Type: application/octet-stream" ); 
        // 请求范围的度量单位  
        Header ( "Accept-Ranges: bytes" );  
        // 告诉浏览器文件尺寸    
        Header ( "Accept-Length: " . filesize ( $file ) );  
        // 开始下载，下载时的文件名
        Header ( "Content-Disposition: attachment; filename=" . $fileName );    

        // 读取服务器上的一个文件并以文件流的形式输出给浏览器
        readfile($file);
    }

    // 点赞
    public function agreements(){
        $id = $_GET['id'];
        // 判断登录
        if(!isset($_SESSION['id'])){
            echo json_encode([
                'status_code'=>'403',
                'message'=>'必须先登录'
            ]);
            exit;
        }

        // 判断是否已经点赞过这个日志
        $blog = new \models\Blog;
        $ret=$blog->agree($id);
        if($ret){
            echo json_encode([
                'status_code'=>'200',
            ]);
            exit;
        }else{
            echo json_encode([
                'status_code'=>'403',
                'message'=>'已经点赞过 不可以重复点赞',
            ]);
            exit;
        }
        // $blog->($id);
    }

    // 点赞列表
    public function agreements_list(){
          $id = $_GET['id'];
          // 获取这个日志所有点赞的用户
          $model = new \models\Blog;
          $data=$model->agreeList($id);
        //   var_dump($data);
          echo json_encode([
            'status_code'=>'200',
            'data'=>$data,
        ]);


    }

    
}