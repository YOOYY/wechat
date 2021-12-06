<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Usertag;

class usertagController extends Controller
{
    public function __construct(Usertag $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    //用户标签
    public function lists(Request $request){
        try{
            $current = $request->input('start',1);
            $limit = 10;
            $start = $limit*($current-1);
            $page = $this->service->page($limit,'usertag');
            $info = $this->service->getList($start,$limit);
            return view('usertag',['data' => $info,'page' => $page,'current' => $current]);
        }
        //捕获异常
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取用户标签失败');
            echo '数据库错误'.$res['errmsg'];
        }
    }

    //创建标签
    public function create(Request $request){
        try{
            $name = $request->input('name');
            $res = $this->service->create($name);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'创建标签失败');
        }
        return response()->json($res);
    }

    //修改标签
    public function update(Request $request){
        try{
            $tagId = $request->input('tagId');
            $name = $request->input('name');

            $res = $this->service->update($tagId, $name);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'修改用户标签失败');
        }
        return response()->json($res);
    }

    //删除标签
    public function delete(Request $request){
        try{
            $tagId = $request->input('tagId');
            $res = $this->service->delete($tagId);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'删除用户标签失败');
        }
        return response()->json($res);
    }
}