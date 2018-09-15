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
            $_SESSION['avatar'] = $user['avatar'];
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
} 
