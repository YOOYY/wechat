<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Foundation\Application;

class Templatenotice extends Base
{
    public function __construct()
    {
        $app = app('wechat');
        $this->notice = $app->notice;
    }
    
    // 设置模板ID：template / templateId / uses
    // 设置接收者openId: to / receiver
    // 设置详情链接：url / link / linkTo
    // 设置模板数据：data / with
    public function sendNotice($userId,$templateId,$url,$data){
        try{
            $res = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($userId);

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'发送模板消息');
        }
    }
}
