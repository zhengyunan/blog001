<?php
namespace controllers;
use libs\Mail;
class MailController{
    public function send(){
        $redis = \libs\Redis::getInstance();

        $mailer = new Mail;
        ini_set('default_socket_timeout',-1);
        echo "发邮件队列启动成功..\r\n";
        //循环从队列中取消息并发送 
        while(true){
            $data=$redis->brpop('email',0);
            $message = json_decode($data[1], TRUE);
             
            //发邮件
            $mailer->send($message['title'],$message['content'],$message['from']);
            echo "发送邮件成功 继续等待下一个  \r\n";
        }
        
    }
}