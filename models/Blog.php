<?php
namespace models;
use PDO;
class Blog extends Base{
    public function delete($id){
        $stmt=self::$pdo->prepare('DELETE FROM mvc_blogs where id=? AND user_id=?');
        $ret=$stmt->execute([
            $id,
            $_SESSION['id'],
        ]);

    }

    // 取一条数据
    public function find($id){
        $stmt = self::$pdo->prepare('SELECT * FROM mvc_blogs WHERE id = ?');
        $stmt->execute([
            $id
        ]);
        return $stmt->fetch();
    }
    public function update($title,$content,$is_show,$id){

        $stmt = self::$pdo->prepare("UPDATE mvc_blogs SET title=?,content=?,is_show=? WHERE id=?" );
        $ret = $stmt->execute([
            $title,
            $content,
            $is_show,
            $id,
        ]);
    }
       //搜索日志
    public function search(){
        //取日志列表
        // $pdo = new PDO("mysql:host=127.0.0.1;dbname=mvc",'root','');
        // $pdo->exec('SET NAMES utf8');
        $where = 'user_id='.$_SESSION['id'];
        $value=[];
         // 如果有keword 并值不为空时
         if(isset($_GET['keyword']) && $_GET['keyword'])
         {
             $where .= " AND (title LIKE ? OR content LIKE ?)";
             $value[] = '%'.$_GET['keyword'].'%';
             $value[] = '%'.$_GET['keyword'].'%';
         }
 
         if(isset($_GET['start_date']) && $_GET['start_date'])
         {
             $where .= " AND created_at >= ?";
             $value[] = $_GET['start_date'];
         }
 
         if(isset($_GET['end_date']) && $_GET['end_date'])
         {
             $where .= " AND created_at <= ?";
             $value[] = $_GET['end_date'];
         }
 
         if(isset($_GET['is_show']) && ($_GET['is_show']==1 || $_GET['is_show']==='0'))
         {
             $where .= " AND is_show = ?";
             $value[] = $_GET['is_show'];
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
        $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM mvc_blogs WHERE $where");
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
        $stmt = self::$pdo->prepare("SELECT * FROM mvc_blogs WHERE $where ORDER BY $orderBy $orderyWay LIMIT $offset,$perpage "); 
        // var_dump($stmt);
        $stmt->execute($value);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data'=>$data,
            'pageBtn'=>$pageBtn
        ] ;
    }
    public function getNew()
    {
        $stmt = self::$pdo->query('SELECT * FROM mvc_blogs WHERE is_show=1 ORDER BY id DESC LIMIT 20');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function index2html(){
      $stmt = self::$pdo->query("SELECT * FROM mvc_blogs WHERE is_show=1 ORDER BY id DESC LIMIT 20");
      $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
      //开启一个缓冲区
      ob_start();
      view('index.index',[
          'blogs'=>$blogs,
      ]);
      //从缓冲区取出页面
      $str = ob_get_contents();
      file_put_contents(ROOT.'public/index.html', $str);
    //   ob_clean();
    }

    //从数据库中取出日志的浏览量
    public function getDisplay($id){
        $id = (int)$_GET['id'];
        //链接radis
        $redis = \libs\Redis::getInstance();
        //判断blog_display 这个hash中有没有blog-$id
        $key = "blog-{$id}";
        if($redis->hexists('blog_display',$key)){
           $newNum=$redis->hincrby('blog_display',$key,1);
           return $newNum;
        }else{
            $stmt =self::$pdo->prepare('SELECT display FROM mvc_blogs WHERE id=?');
            $stmt->execute([$id]);
            $display =$stmt->fetch(PDO::FETCH_COLUMN);
            $display++;
            $redis->hset('blog_display',$key,$display);
            return $display;
        }        
    }
    //吧内存中数据取出来  更新到数据库

    public function displayToDb(){
        $redis = \libs\Redis::getInstance();
        $data=$redis->hgetall('blog_display');
        // var_dump($data);
        foreach($data as $k=>$v){
             $id = str_replace('blog-','',$k);
            //  var_dump($id);
             $sql = "UPDATE mvc_blogs SET display={$v} WHERE id={$id}";
            //  var_dump($sql);
             self::$pdo->exec($sql);
        }
    }

    public function add($title,$content,$is_show){
        $stmt=self::$pdo->prepare("INSERT INTO mvc_blogs(title,content,is_show,user_id) VALUES(?,?,?,?)");
        // $stmt=self::$pdo->prepare("INSERT INTO mvc_blogs(title,content,is_show,user_id) VALUES(?,?,?,?)");
        $ret=$stmt->execute([
            $title,
            $content,
            $is_show,
            $_SESSION['id'],

        ]);
        var_dump($stmt);
        if(!$ret){
            echo '失败';
            $error = $stmt->errorInfo();
            echo '<pre>';
            var_dump($error);
            exit;
        }
        //返回新插入记录的id
        return self::$pdo->lastInsertId();
    }
}