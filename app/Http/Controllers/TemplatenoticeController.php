<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Templatenotice;

class templatenoticeController extends Controller
{
    public function __construct(Templatenotice $service)
    {
        $this->service = $service;
    }

    //发送模板消息
    // $userId = 'orsSv0aiekNxemy26YtmSsxkgTy4';
    // $templateId = 'ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY';
    // $url = 'http://overtrue.me';
    // $data = array(
    //          "first"  => "恭喜你购买成功！",
    //          "name"   => "巧克力",
    //          "price"  => "39.8元",
    //          "remark" => "欢迎再次购买！",
    //         );

    public function notice(Request $request){
        try{
            $userId = $request->input('userId');
            $templateId = $request->input('templateId');
            $url = $request->input('url');
            $data = $request->input('data');

            $res = $this->service->sendNotice($userId,$templateId,$url,$data);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'发送模板消息失败');
        }

        return $res;        
    }
}