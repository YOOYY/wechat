<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\User;

class userController extends Controller
{
    public function __construct(User $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    //
    //拉取用户信息
    public function lists(Request $request){
        try{
            $tagid = $request->input('tagid');
            $name = $request->input('name');
            $current = $request->input('start',1);
            $limit = 10;
            $start = $limit*($current-1);
            $page = $this->service->myPage($limit,'user',$tagid,$name);
            $info = $this->service->getList($tagid,$name,$start,$limit);
            $tags = $this->service->getTag();
            return view('user',['info' => $info,'tags' => $tags,'page' => $page,'current' => $current]);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'用户信息列表请求失败');
            echo '数据库错误'.$res['errmsg'];
        }
    }

    //批量为用户打标签
    public function batchtag(Request $request){
        try{
            //$openIds = [$openId1, $openId2, ...];
            $openIds = $request->input('openIds');
            $tagId = $request->input('tagId');

            $res = $this->service->batchtag($openIds, $tagId);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'打标签失败');
        }   
        return response()->json($res);
    }

    //批量为用户取消标签
    public function batchuntag(Request $request){
        try{
            $openIds = $request->input('openIds');
            $tagId = $request->input('tagId');

            $res = $this->service->batchUntag($openIds, $tagId);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'取消标签失败');
        }
        return response()->json($res);
    }

    //修改用户备注
    public function remark(Request $request){

        try{
            $openId = $request->input('openId');
            $remark = $request->input('remark');

            $res = $this->service->remark($openId, $remark);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'改备注失败');
        }
        return response()->json($res);
    }

    //添加黑名单
    public function addblack(Request $request){

        try{
            $openids = $request->input('openIds');
            $res= $this->service->addblack($openids);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'加入黑名单失败');
        }
        return response()->json($res);
    }

    //移除黑名单
    public function removeblack(Request $request){

        try{
            $openids = $request->input('openIds');
            $res= $this->service->removeblack($openids);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'移除黑名单失败');
        }
        return response()->json($res);        
    }
}