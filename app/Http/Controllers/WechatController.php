<?php

namespace App\Http\Controllers;
use App\Jobs\Wechat;

class WechatController extends Controller
{
    public function __construct(Wechat $service)
    {
        $this->service = $service;
    }
    public function serve(){
        try{
           return $this->service->serve();
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'服务器错误');
            echo '';
        }
    }

    // public function test(){
    //     try{
    //         var_dump($this->service->textMessage('hehe'));
    //     }
    //     //捕获异常
    //     catch(Exception $e)
    //     {
    //         echo '数据库错误';
    //     }
    // }
}