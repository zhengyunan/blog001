<?php
namespace controllers;
use libs;
use Yansongda\Pay\Pay;
use Endroid\QrCode\QrCode;
class WxpayController
{   
    // 配置的账号
    protected $config = [
        'app_id' => 'wx426b3015555a46be', // 公众号 APPID
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',
        'notify_url' => 'http://aaa.tunnel.echomod.cn/wxpay/notify',
    ];
    

    // 用微信接口进行支付
    public function pay()
    {
        $sn = $_POST['sn'];
        $order = new \models\Order;
        $data = $order->findBySn($sn);
        
        if($data['status']==0){
             //调用接口
            $ret = Pay::wechat($this->config)->scan([
                'out_trade_no' =>$data['sn'],
                'total_fee' => $data['money']*100, // **单位：分**
                'body' => '微信支付'.$data['money']*100,
             ]);
             if($ret->return_code = 'SESSION' && $ret->result_code = 'SESSION'){
       
                
                view('users.wxpay',[
                    
                    'code'=>$ret->code_url,
                    'sn' => $sn,
                ]);
             }
        }else{
            die("订单状态不允许支付");
          
        }
        
        
        
        //打印返回值
        // echo $pay->return_code , '<hr>';
        // echo $pay->return_msg , '<hr>';
        // echo $pay->appid , '<hr>';
        // echo $pay->result_code , '<hr>';
        // echo $pay->code_url , '<hr>';  //支付玛
    }

    public function notify()
    {   
        $log = new \libs\Log('wxpay.log');
        $log->log('接收到微信的消息');
        $pay = Pay::wechat($this->config);
        
        try{
            $data = $pay->verify(); // 是的，验签就这么简单！
            $log->log('收到了数据是：'.file_get_contents('php://input'));
            if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS')
            {   $log->log('支付成功，金额'.$data->total_fee.'订单号'.$data->out_trade_no);
                echo '共支付了：'.$data->total_fee.'分';
                echo '订单ID：'.$data->out_trade_no;
                $order = new \models\Order;
                //判断如果订单状态为未支付 说明第一次收到消息  更新状态
                $orderInfo=$order->findBySn($data->out_trade_no);
                // var_dump($orderInfo);
                // die;
                 if($orderInfo['status']==0){
                     //开启事务
                       $order->startTrans();
                     //更新订单状态
                      $ret1=$order->setpaid($data->out_trade_no);
                      //更新用户余额
                      $user = new \models\User;
                      $ret2=$user->addMoney($orderInfo['money'],$orderInfo['user_id']);
                      if($ret1&&$ret2){
                          //提交事务
                          $order->commit();
                      }else{
                          //回滚
                          $order->rollback();
                      }
                    }
                //设置订单已支付状态
               
            }
            

        } catch (Exception $e) {
            $log->log('验证失败'.$e->getMessage());
            var_dump( $e->getMessage() );
        }
        
        $pay->success()->send();
    }
}