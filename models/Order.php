<?php
namespace models;
use PDO;
class Order extends Base
{
    // 下订单
    public function create($money)
    {
        $flake = new \libs\Snowflake(1023);
       
        
        $stmt = self::$pdo->prepare('INSERT INTO orders(user_id,money,sn) VALUES(?,?,?)');
        // var_dump($stmt);
        $stmt->execute([
            $_SESSION['id'],
            $money,
            $flake->nextId()
        ]); 

        // var_dump( $stmt->errorInfo() );
        // var_dump($flake->nextId(),$_SESSION['id'],$money);
        // die;
    }
    public function search(){
       $where ='user_id='.$_SESSION['id'];
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
       $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM orders WHERE $where");
       $stmt->execute();
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
       $stmt = self::$pdo->prepare("SELECT * FROM orders WHERE $where ORDER BY $orderBy $orderyWay LIMIT $offset,$perpage "); 
       // var_dump($stmt);
       $stmt->execute();
       $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

       return [
           'data'=>$data,
           'pageBtn'=>$pageBtn
       ] ;
    }

    public function findBySn($sn){
        $stmt = self::$pdo->prepare('SELECT * FROM orders WHERE sn=?');
        $stmt->execute([
            $sn
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 订单已经支付的状态
    public function setpaid($sn){
        $stmt = self::$pdo->prepare("UPDATE orders SET status=1,pay_time=now() WHERE sn=?");
        return $stmt->execute([
            $sn
        ]);
    }
    
}