<?php
$host = '127.0.0.1';   // 主机地址
$dbname = 'mvc';  // 数据库名
$user = 'root';       // 账号
$pass = '';   // 密码

// 连接数据库
$pdo = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);
$pdo->exec('SET NAMES utf8');
// for($i=0;$i<100;$i++){
//     $title = getChar(rand(20,100));
//     $content = getChar(rand(100,500));
//     $pdo->exec("INSERT INTO mvc_blogs(title,content) VALUES('$title','$content')");
// }
// // $pdo->exec("INSERT INTO mvc_blogs(id,title,content) VALUES(1,'美国总统','中国发财啦')");
// // $pdo->exec("UPDATE mvc_blogs SET title='中国最强',content='别国不行' WHERE id=1");
// // $pdo->exec("DELETE FROM mvc_blogs WHERE id=1"); //id还自增
// // $pdo->exec("TRUNCATE mvc_blogs");   //删除后id从1开始

// function getChar($num){
//     $b='';
//     for($i=0;$i<$num;$i++){
//         $a = chr(mt_rand(0xB0,0XDB)).chr(mt_rand(0xA1,0xf0));
//         $b.=iconv('GB2312','UTF-8',$a);
//         // $pdo->exec("INSERT INTO mvc_blogs(id,title,content) VALUES(1,$title,'中国发财啦')");
//     }
//     return $b;
// }

// $stmt = $pdo->query('SELECT * FROM mvc_blogs LIMIT 10');
$stmt = $pdo->prepare('INSERT INTO mvc_blogs(title,content) VALUES(?,?)');
$ret = $stmt->execute([
    '标题123',
    '内容000',
]);
if($ret){
    echo "添加成功的最后id为".$pdo->lastInsertId();
}else {
   $error= $stmt->errorInfo();
    var_dump($error);
}
//取一条
// $data = $stmt->fetch();
//取所有

// - PDO::FETCH_ASSOC  :  返回关联数组
// - PDO::FETCH_BOTH  ：返回混合数组
// - PDO::FETCH_NUM ：返回索引数组
// - PDO::FETCH_OBJ ：返回对象
// - PDO::FETCH__COLUMN ：返回某一列的值

// 1. 平时都使用 FETCH_ASSOC
// 2. 当只返回一个值时使用 FETCH_COLUMN



// $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
// var_dump($data);
