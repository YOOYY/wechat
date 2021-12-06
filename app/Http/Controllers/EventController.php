<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Event;

class eventController extends Controller
{

    public function __construct(Event $service)
    {
        $this->service = $service;
        $this->middleware('auth');
    }

    //拉取用户信息
    public function lists(Request $request){
        try{
            $name = $request->input('name');
            $current = $request->input('start',1);
            $limit = 10;
            $start = $limit*($current-1);
            $page = $this->service->page($limit,'event');
            $info = $this->service->getList($name,$start,$limit);
            $message = $this->service->message();
            return view('event',['info' => $info,'page' => $page,'message' => $message,'current' => $current]);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取事件列表失败');
            echo '数据库错误'.$res['errmsg'];
        }
    }

    public function update(Request $request){
        try{
            $opt = $request->all();
            $res = $this->service->update($opt);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'事件更新失败');
        }
        return $res;
    }

    public function create(Request $request){
        try{
            $opt = $request->all();
            $res = $this->service->create($opt);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'事件创建失败');
        }
        return $res;
    }

    public function delete(Request $request){
        try{
            $id = $request->input('id');
            $res = $this->service->delete($id);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'事件删除失败');
        }
        return $res;
    }
}
