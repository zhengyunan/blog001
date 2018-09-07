<?php
namespace models;
use PDO;
class User{
    public function getName(){
        return "tom";
    }
    public $pdo;
    public function __construct()
    {
        // 取日志的数据
        $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=mvc', 'root', '');
        $this->pdo->exec('SET NAMES utf8');
    }
    public function add($email,$password){
       $stmt=$this->pdo->prepare("INSERT INTO users(email,password) VALUES(?,?)");
      return $stmt->execute([
           $email,
           $password,
       ]);
    }
} 
