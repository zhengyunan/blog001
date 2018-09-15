<?php
namespace controllers;
use models\User;
use models\Order;
class UserController{
    public function regist(){
        view('users.add');
    }
    public function store(){
        //1接受表单

        // var_dump($_POST);?
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        //2 生成激活码
        $code = md5(rand(1,99999));
        // var_dump($code);
        //保存到redis
        $redis = \libs\Redis::getInstance();
        $value = json_encode([
            'email'=>$email,
            'password'=>$password,
        ]);
        //键名
        $key = "temp_user:{$code}";
        // var_dump($key,$value);
        $redis->setex($key,300,$value);
        // //插入到数据库
        // $user = new User;
        // $ret = $user->add($email,$password);
        // if(!$ret){
        //     die('注册失败');
        // }else{
            $name = explode('@',$email);
            $from = [$email,$name[0]];
            $message = [
                'title'=>'治疗系统账号激活',
                'content'=>"点击以下按钮进行激活<br>点击激活:
                <a href='http://localhost:9999/user/active_user?code={$code}'>http://localhost:9999/user/active_user?code={$code}</a>
                <p>如果上面按钮不能点击请复制上面链接地址</p>",
                'from'=>$from,

            ];
            $message = JSON_encode($message);
            // var_dump($message);
            //放到队列里
            $redis = \libs\Redis::getInstance();
            $redis->lpush('email',$message);
            // $mail = new \libs\Mail;
            // $content = "恭喜注册成功";
           
            // $mail->send('注册成功',$content,$from);
            echo "O98K";
        // }

    }
    public function active_user(){
        //接受激活码
        $code = $_GET['code'];
        //到redis取激活码
        $redis = \libs\Redis::getInstance();
        //拼出名字
        $key = 'temp_user:'.$code;
        //取出数据
        $data = $redis->get($key);
        if($data){
            $redis->del($key);
            $data = json_decode($data,true);
            // var_dump($data);
            $user = new \models\User;
            $user->add($data['email'],$data['password']);
            header('Localhost:/user/login');
            //跳转登录页面
            
        }
    }

    public function login(){
        view('users.login');
    }
    public function dologin()
{
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    
    $user = new \models\User;
    if($user->login($email, $password))
    {
        message('登录成功！', 2, '/blog/index');
    }
    else
    {
        message('账号或者密码错误', 2, '/user/login');
    }
}

   public function logout(){
    //    echo "aaa";
       $_SESSION = [];
       message('退出成功',2,'/');
   }


//    充值
    public function charge()
    {  
        view('users.charge');
    }
    public function docharge()
    {
        // 生成订单
        $money = $_POST['money'];
        $model = new Order;
        $model->create($money);
        message('充值订单已生成，请立即支付！', 2, '/user/orders');
    }
    public function orders(){
        $order = new Order;
        $data=$order->search();

        // echo '<pre>';
        // var_dump($data);
        view('users.order',$data);
    }
    public function money()
    {
        $user = new User;
        echo $user->getMoney();
    }
    public function orderStatus(){
        $sn = $_GET['sn'];
        // 获取的次数
        $try = 10;
        $order = new \models\Order;
        do{
           $info=$order->findBySn($sn);
           if($info['status']==0){
               sleep(1);
               $try--;
           }else break;
           
        }while($try>0); //如果尝试的次数大于制定的次数
           echo $info['status'];
        
    }


    // 设置头像
    public function avatar(){
        view('users.avatar');
    }

    public function setavatar(){
        $upload = \libs\Uploader::make();
        $path=$upload->upload('avatar','avatar');
        // echo $path;
        // 保存到数据库中
        $model = new User;
        $model->setAvatar('uploads/'.$path);

        // 删除原图
        @unlink( ROOT . 'public'.$_SESSION['avatar'] );
        $_SESSION['avatar'] = '/uploads/'.$path;
        message('设置成功', 2, '/blog/index');
    }


    //上传多张图片
    public function album(){
        view('users.album');
    }
    public function uploadall(){
        // echo '<pre>';
        // var_dump($_FILES);

         //先创建目录
         $root = ROOT.'public/uploads/';
         //今天的日期目录
         $date = date('Ymd');
         if(!is_dir($root.$date)){
             mkdir($root.$date,0777);
             
         }
         // 生成唯一文件名
        // $name = md5(time().rand(1,9999));//32位字符串
        foreach($_FILES['image']['name'] as $k=>$v){
                // 生成唯一文件名
                $name = md5(time().rand(1,9999));//32位字符串
                
                // 补上文件后缀
                // 先取出原来后缀名字
                // $ext = strrchr( $_FILES['image']['name'] , '.'); 
                $str=strrchr($v,'.');
                // 全名
                $name = $name.$str;
                // var_dump($name);

                // 移动图片
                move_uploaded_file($_FILES['image']['tmp_name'][$k],$root.$date.'/'.$name);
                echo($root.$date.'/'.$name).'<hr>';
        }
    }

    public function uploadbig(){
        var_dump($_POST);
        // var_dump($_FILES);
        // die;
        $count = $_POST['count'];
        $i = $_POST['i'];
        $size = $_POST['size'];
        $name = 'big_img_'.$_POST['img_name']; //所有的分块名字
        $img = $_FILES['img'];
        // 保存每个分片
        move_uploaded_file( $img['tmp_name'] , ROOT.'tmp/'.$i);

        // 当最后一张图片上传完后合并

        // 全部分片都上传完毕后在合并
        // 把已经上传的先保存到redis中  
        $redis = \libs\Redis::getInstance();
        // 每上传一张图片+1
        $uploadedCount = $redis->incr($name);
        var_dump($uploadedCount);
        // 如果是最后一个分支就合并
        if($uploadedCount == $count){
            // alert("qweqwe");
            // 以追回的方式(a)创建并打开最终的大文件
            $fp = fopen(ROOT.'public/uploads/big'.$name.'.png','a');
            //循环所有分片
            for($i=0;$i<$count;$i++){
                // // 读取第 i 号文件并写到大文件中
                fwrite($fp,file_get_contents(ROOT.'tmp/'.$i));
                // 删除第 i 号临时文件
                unlink(ROOT.'tmp/'.$i);
            }
            // 关闭文件
            fclose($fp);
              // 从 redis 中删除这个文件对应的编号这个变量
              $redis->del($name);
        }

    }
}