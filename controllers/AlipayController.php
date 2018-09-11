<?php
namespace controllers;

use Yansongda\Pay\Pay;

class AlipayController
{
    public $config = [
        'app_id' => '2016091700532099',

         // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAks0who4aQ9XweSEt807taiRqdRw4D1ymsZ6t9VqnEaSlCWQ36n0+iU3CsZdgvXdqa63g72qHNKUbV+/9knOQLLVKflc57B7EHMyAUMD3bAI57cds1BxTHqfWdZ3cahLAwNK6ZMC1FN2Iwa7nw86hXy+hG2FO8MedDJ14QfAT6GG6xVMWVseZ9vNo98apeZrpCJkAwJ4+dBJkpWj20JHyq4rk+gDV3brjU3BtoOGQU9E4/IXx16rShSKLwEBkfM0gKbXeD8vM7J35g29OnXk8j2vk2BdtlHQF/J7D4rwaK8pQyqKKg+rn7+Kyds1ArGaydmxJftQHeTj/L6gU5gQhJQIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEowIBAAKCAQEApUgGxew1NqsaCcp81a0zHeemTjP5NAi33SWQkCFSeroyhpvukp2CflUmwLej9EBntLVzrmSs4cYp9+Hljy0MKz2MHam/s5vpSyOWL1eZEafldH43npLbsOgXYJY8hZHO3O2E4yjriyy2/IfJ4JK9fU03KtO/P7jyBsJCqNcDbDLYL9CVgllNTG7z1mACItG4qyFmY9bzV857f72RE6WRTUA1Gobx6yN53IHzscEV2v8Mz6G7OrfAfArN/NWLxXV+++UqV7xSTGqeZP4j2LncKRJK2TwpqViYVS3E6dtdR+3zvGaMC0SOP5l1b6fRLn9cfoLv702BOIBufrrNg9r81wIDAQABAoIBAQCgF/w3imyg32sBQNlaP8HbnZ5A8abY9/jghJpagWxa5DA8op2b9mWH6QL4eOieliPdrkS3D11F86SDLpQk8wVufNdThDDj0IlQ9s9qW/cwWuiuxfMp7iZOXQEH1X4aAvnUlVy6i9Bbppw4T28D0B8rV7ewDBqbppEIavIWiO4PxkDQx9wH2cVwm9+9acGx8taQYkmoXr5AN0DcHM4QqCemaRoj6UpBkXoeaS94UznCKjyxUrs1Co/zSiMfgfC5GFlgKuTvVYgPY2fRiKL6tyHZXDHsPDefHzMtDGTsOy7Sc+WVb9IS+yBK4DufIfcV2uJrZHnl/N8E24Wy2uuJaaRBAoGBANrOy9thJP2DMsseeBJbyA5YRxxT/Ikeo92xainSluG3E+whxjLNw36IpOhtqmfuQwCDazhjU9oZnydcgK6lDaABlD2G3BhJ7n38JFRGn18HXR1dUiCR4EfmmRoiRkWmwynRbPTbRoMGjU53hc78Wk3WPIHL4Kl0eZ/mPrqXBofjAoGBAMFgFIQfwuvUEdyr3b7SJmBl6oqLD4YiXgFzOhneFZa34kYD/6IAtgrQWYUU6RWoc6gLf1c5UJZJR6cahSCCivZVX/iM477gqQGk0wR/1ae/eji1o360uGjm2w4cuMMMO/O5ZgaLiAQJVGSoMm/rjBn0vaZUPiOr6qHCAOdSTkF9AoGAFdXFKiNLmbDaBMMJoGtgT24nyn/nF5fjKmBmA75sKCIo7suAB9nYxGvGaRtoge6Y6Rg0PvBKkd00sKzeeo8mCO2faXRnylS2ZAQha/eQN994P1HsvCtSglpUtPLuqEy39RhdEyI+qxygRVBQHhO4v0O9zgYWaVKiQH6ti9k+YZkCgYA1MaTzI3mPPesb9PsuvshnxCFcsxw2HcYWSAg3jWwK5dKMyBTOD7wEBYRao4Hqv0fxdhg1ekK81LOtOBJe+woW8S1RRhBwNYTIOvsBRdkUKAwLBYxQEo6X7MldfHEm1nq3dMyoCEIGOjrI5avaNcV4bmatFodxcJ0+mgt9lpqIUQKBgG98RgfBu+tdtmqpucPjFBiYGZSXol5LNJqlJ82Eq31b8IO2wRZygSjkqsr/2CGNwDia/O5ZiEy2yWdp8qesBoQxzYzWpnHUi+D+m/6tujDMj2Zkpgl8WQCdocTzi/UoAuyN0WugKdTxHxuEm2iWK/SK7NbaPaJhsyosSHg8boL/',
        

        // 通知地址
        'notify_url' => 'http://requestbin.fullcontact.com/pkqexppk',
        // 跳回地址
        'return_url' => 'http://localhost:9999/alipay/return',
        
        // 沙箱模式（可选）
        'mode' => 'dev',
        
    ];
    // 发起支付
    public function pay()
    {   
        //生成订单
        $order = [
            'out_trade_no' => time(),    // 本地订单ID
            'total_amount' => '0.01',    // 支付金额
            'subject' => 'test subject', // 支付标题
        ];
        
        //跳转到支付宝
        $alipay = Pay::alipay($this->config)->web($order);
        $alipay->send();
    }
    // 支付完成跳回
    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！
        echo '<h1>支付成功！</h1> <hr>';
        var_dump( $data->all() );
    }
    // 接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！
            // 这里需要对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            echo '订单ID：'.$data->out_trade_no ."\r\n";
            echo '支付总金额：'.$data->total_amount ."\r\n";
            echo '支付状态：'.$data->trade_status ."\r\n";
            echo '商户ID：'.$data->seller_id ."\r\n";
            echo 'app_id：'.$data->app_id ."\r\n";
        } catch (\Exception $e) {
            echo '失败：';
            var_dump($e->getMessage()) ;
        }
        // 返回响应
        $alipay->success()->send();
    }


    // 退款
    public function refund()
    {
        // 生成唯一退款订单号
        $refundNo = md5( rand(1,99999) . microtime() );
        try{
            // 退款
            $ret = Pay::alipay($this->config)->refund([
                'out_trade_no' => '1536671404',    // 之前的订单流水号
                'refund_amount' => 0.01,              // 退款金额，单位元
                'out_request_no' => $refundNo,     // 退款订单号
            ]);
            if($ret->code == 10000)
            {
                echo '退款成功！';
            }else{
                echo '失败';
                var_dump($ret);
            }
        }
        catch(\Exception $e)
        {
            var_dump( $e->getMessage() );
        }
    }
}