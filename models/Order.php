<?php
namespace models;
class Order extends Base{
    public function create($money){
    $flake = new \libs\Snowflake(1023);

    $stmt = self::$pdo->prepare('INSERT INTO orders(user_id,money,sn) VALUES(?,?,?)');
    $data=$stmt->execute([
        $_SESSION['id'],
        $money,
        $flake->nextId(),
   
    ]);
    // var_dump($data);
    // die;
    // var_dump($stmt);
    }
}