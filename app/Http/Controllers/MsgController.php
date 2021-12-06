<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Message;

class MsgController extends Controller
{
    public function __construct(Message $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function lists(Request $request)
    {
        try{
            $name = $request->input('name');
            $type = $request->input('type','text');
            $current = $request->input('start',1);
            $limit = 10;
            $start = $limit*($current-1);
            $page = $this->service->page($limit,'message');
            $info = $this->service->getList($name,$type,$start,$limit);
            return view('msg',['info' => $info,'page' => $page,'current' => $current, 'type' => $type]);
        }
        //捕获异常
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取消息列表失败');
            echo '数据库错误'.$res['errmsg'];
        }
    }

    public function edit(Request $request)
    {
        try{
            return view('edit');
        }
        //捕获异常
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取消息列表失败');
            echo '数据库错误'.$res['errmsg'];
        }
    }

    //获取图文消息接口
    public function get(Request $request)
    {
        try{
            $type = $request->input('type');
            $res = $this->service->get($type);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取图文消息接口失败');
        }
        return response()->json($res);
    }

    //获取图文消息接口
    public function getnot(Request $request)
    {
        try{
            $type = $request->input('type');
            $res = $this->service->getNot($type);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取图文消息接口失败');
        }
        return response()->json($res);
    }

    public function update(Request $request)
    {
        try{
            $opt = $request->all();
            $res = $this->service->update($opt);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'更新消息失败');
        }
        return response()->json($res);
    }

    public function create(Request $request)
    {
        try{
            $opt = $request->all();
            $res = $this->service->create($opt);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'创建消息失败');
        }
        return response()->json($res);
    }

    public function delete(Request $request)
    {
        try{
            $id = $request->input('id');
            $res = $this->service->delete($id);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'删除消息失败');
        }
        return response()->json($res);
    }

    //获取当前设置的回复规则
    public function reply()
    {
        try{
            $res = $this->service->reply();
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取回复规则失败');
            echo '数据库错误'.$res['errmsg'];
        }
    }
}