<?php
namespace models;
use PDO;
class User extends Base{
    public function setAvatar($path){
           $stmt=self::$pdo->prepare('UPDATE users SET avatar=? WHERE id=?');
           $stmt->execute([
            $path,
            $_SESSION['id'],
           ]);
    }
    public function add($email,$password){
       $stmt=self::$pdo->prepare("INSERT INTO users(email,password) VALUES(?,?)");
      return $stmt->execute([
           $email,
           $password,
       ]);
    }
    public function login($email,$password){
        $stmt = self::$pdo->prepare("SELECT * FROM users WHERE email=? AND password=?");
        $stmt->execute([
            $email,
            $password
        ]);
        // 取出数据
        $user = $stmt->fetch();
        if($user){
            $_SESSION['id']=$user['id'];
            $_SESSION['email']=$user['email'];
            $_SESSION['money'] = $user['money'];
            if($user['avatar']==''){
                $_SESSION['avatar']='/images/avatar.png';
            }else{
                $_SESSION['avatar'] = $user['avatar'];
            }
            
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function addMoney($money,$user_id){
        $stmt = self::$pdo->prepare("UPDATE users SET money=money+? WHERE id=?");
        return $stmt->execute([
            $money,
            $user_id,
        ]);
        
    }
    
    public function getMoney(){
        $id=$_SESSION['id'];
        //查询数据库
        $stmt = self::$pdo->prepare('SELECT money FROM users WHERE id=?');
        $stmt->execute([$id]);
        $money = $stmt->fetch(PDO::FETCH_COLUMN);
        //更新数据到session中
        $_SESSION['money'] = $money;
        return $money;
    }


    // 活跃用户
    public function computeActineUsers(){
        $stmt = self::$pdo->query(
            'SELECT user_id,COUNT(*)*5 fz FROM 
            mvc_blogs WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) GROUP BY user_id');
        $data1 = $stmt->fetchAll(PDO::FETCH_ASSOC); 

        $stmt = self::$pdo->query(
            'SELECT user_id,COUNT(*)*3 fz FROM 
            comments WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) GROUP BY user_id');
        $data2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = self::$pdo->query(
            'SELECT user_id,COUNT(*) fz FROM 
            blog_agree WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) GROUP BY user_id');
        $data3 = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // 合并数组
        $arr = [];
        // 合并第一个数组到空数组中
        foreach($data1 as $v){
            $arr[$v['user_id']]=$v['fz'];
        }
        foreach($data2 as $v){
            if(isset($arr['user_id'])){
                $arr[$v['user_id']]+=$v['fz'];
            }else{
                $arr[$v['user_id']]=$v['fz'];
            }
        }

        foreach($data3 as $v){
            if(isset($arr['user_id'])){
                $arr[$v['user_id']]+=$v['fz'];
            }else{
                $arr[$v['user_id']]=$v['fz'];
            }
        }
        // 倒序
        arsort($arr);

        // 取出前二十
        $data = array_slice($arr,0,20,TRUE);

        $userIds = array_keys($data);
        // echo "<pre>";
        // var_dump($userIds);
        $userIds = implode(',',$userIds);

        // 根据用户id取出用户头像和email
        $sql = "SELECT avatar,email FROM users WHERE id IN($userIds)";
        $stmt = self::$pdo->query($sql);
        $data=$stmt->fetchAll(PDO::FETCH_ASSOC);

        // 保存到redis中
        $redis = \libs\Redis::getInstance();
        $redis->set('active_users',json_encode($data));
        // var_dump($data);
    }
    public function getActiveUser(){
        $redis = \libs\Redis::getInstance();
        $data=$redis->get('active_users');
        return json_decode($data,true);
    }

    public function getAll()
        {
            $stmt = self::$pdo->query('SELECT * FROM users');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
} 
