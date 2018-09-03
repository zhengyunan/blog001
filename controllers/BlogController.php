<?php
namespace controllers;
use PDO;
class BlogController{
    public function index(){
        //取日志列表
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=mvc",'root','');
        $pdo->exec('SET NAMES utf8');
        $where = 1;
        $value=[];
        if(isset($_GET['keyword'])&&$_GET['keyword']){
            $where = "title like '%".$_GET['keyword']."%' OR content like '%".$_GET['keyword']."%'";
        }
        // if(isset($_GET['keyword'])&&$_GET['keyword']){
        //    $where.=" AND (title LIKE ? OR content LIKE ?)";
        //    $value[] = '%'.$_GET['keyword'].'%';
        //    $value[] = '%'.$_GET['keyword'].'%';
        // }
        if(isset($_GET['start_date']) && $_GET['start_date'])
        {
        $where .= " AND created_at >= '{$_GET['start_date']}'";
        }
        if(isset($_GET['end_date']) && $_GET['end_date'])
        {
        $where .= " AND created_at <= '{$_GET['end_date']}'";
        }
        if(isset($_GET['is_show']) && ($_GET['is_show']==1 || $_GET['is_show']==='0' ))
        {
        $where .= " AND is_show='{$_GET['is_show']}'";
        }

        /************排序***** */
        $orderBy = 'created_at';
        $orderyWay = 'desc';

        // 设置排序字段
        if(isset($_GET['order_by']) && $_GET['order_by'] == 'display')
        {
            $orderBy = 'display';
        }
        // 设置排序方式
        if(isset($_GET['order_way']) && $_GET['order_way'] == 'asc')
        {
            $orderyWay = 'asc';
        }
        /*分页 */
        $perpage = 15;
        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        $offset = ($page-1)*$perpage;
        /*================ 翻面按钮 ******************************************/
        // 取总的记录数
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM mvc_blogs WHERE $where");
        $stmt->execute($value);
        $recordCount = $stmt->fetch(PDO::FETCH_COLUMN);
        // echo($recordCount);
        // 总的页数
        $pageCount = ceil($recordCount/$perpage);
        // echo($pageCount);
        // 制作按钮
        $pageBtn = '';
        for($i=1; $i<=$pageCount; $i++)
        {
            // if($page==$i){
            //     $pageBtn .= "<a class='active' href='?page={$i}'> {$i} </a>";
            // }else{
            //     $pageBtn .= "<a href='?page={$i}'> {$i} </a>";
            // }
            $active = $page==$i ? 'active' : '';
            $pageBtn .= "<a class='$active' href='?page={$i}'> {$i} </a>";
             
        }
        $stmt = $pdo->query("SELECT * FROM mvc_blogs WHERE $where ORDER BY $orderBy $orderyWay LIMIT $offset,$perpage "); 
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // echo '<pre>';
        // var_dump($data);
        view('blogs.index',[
            'data'=>$data,
            'pageBtn'=>$pageBtn
        ]);
    }
}