<?php
namespace controllers;
class RedbagController{
    //  初始化
    public function rob(){
        if(!isset($_SESSION['id'])){
            echo json_encode([
                'status_code'=>'401',
                'message'=>'未登录'
            ]);
           
            exit;
        }
        
        // 2. 判断当前是否是9~10点之间
        if(date('H')>9){
            echo json_encode([
                'status_code'=>'403',
                'message'=>'不是抢红包的时间'
            ]);
            exit;
        }
        // 判断今天是否已经抢过
        // redis中集合是否有今天抢过的记录
        $key = 'redbag_'.date('Ymd');
        $redis = \libs\Redis::getInstance();
        $exists = $redis->sismember($key,$_SESSION['id']);
        if($exists){
            echo json_encode([
                'status_code'=>'403',
                'message'=>'今天已经抢过啦  明天再来吧'
            ]);
            exit;
        }

        // 若上面的都通过证明可以抢红包
        // 1  先从redis中减少库存量
        $stock = $redis->decr('redbag_stock');
        // 2  判断是否还有库存
        if($stock<0){
            echo json_encode([
                'status_code'=>'402',
                'message'=>'手慢了 红包被抢没啦',
            ]);
            exit;
        }

        // 3如果还有库存就下单 放到数列中
        $redis->lpush('redbag_orders',$_SESSION['id']);
        // 4把id放到集合中证明已经抢过啦
        $redis->sadd($key,$_SESSION['id']);
        echo json_encode([
            'status_code'=>'200',
            'message'=>'恭喜已经抢到啦',
        ]);
        // if()
    }
    public function init(){
        $redis = \libs\Redis::getInstance();
        // 初始化库存量
        $redis->set('redbag_stock',20);
        // 初始化空的集合
        $key = 'redbag_'.date('Ymd');
        $redis->sadd($key,'-1');
        // 设置过期时间
        $redis->expire($key,5900);

    }
    
    // 当有新的数据时候生成订单
    public function makeOrder(){
        $redis = \libs\Redis::getInstance();
        $model = new \models\Redbag;
        // 设置 socket 永不超时
        ini_set('default_socket_timeout', -1); 

        echo "开始监听红包队列... \r\n";

        // 循环监听一个列表
        while(true)
        {
                // 从队列中取数据，设置为永久不超时
                $data = $redis->brpop('redbag_orders', 0);
                /*
                返回的数据是一个数组用户的ID：[用户ID]
                */
                // 处理数据
                $userId = $data[1];
                // 下订单
                $model->create($userId);

                echo "========有人抢了红包！\r\n";
            }
        }

        public function rob_view(){
            view('redbag.rob');
        }
    }
