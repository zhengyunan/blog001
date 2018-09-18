<?php
namespace controllers;

class CommentController
{
    // 发表评论
    public function comments(){
        // 接受原始数据
        $data = file_get_contents('php://input');
        $_POST = json_decode($data,TRUE);
         // 1检查是否登录
         if(!isset($_SESSION['id'])){
            echo json_encode([
                'status_code' => '401',
                'message' => '必须先登录',
            ]);
            exit;
        }
        // 2接受表单的数据
        $content = e($_POST['content']);
        $blog_id = $_POST['blog_id'];
        // 3插入数据
        $model = new \models\Comment;
        $model->add($content,$blog_id);
        //4 返回新发布的评论数据  过路后的
        echo json_encode([
            'status_code' => '200',
            'message' => '发表成功',
            'data'=>[
                'content'=>$content,
                'avatar'=>$_SESSION['avatar'],
                'email'=>$_SESSION['email'],
                'created_at'=>date('Y-m-d H:i:s')
            ]
        ]);
    }


    // 获取评论列表
    public function comment_list(){
        $blogId = $_GET['id'];

        // 获取日志评论列表
        $model = new \models\comment;
        $data = $model->getComments($blogId);
        
        // 获取的数据  转成json发给前端
        echo json_encode([
            'status_code'=>200,
            'data'=>$data
        ]);


    }
   
   
}