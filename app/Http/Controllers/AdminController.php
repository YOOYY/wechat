<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Admin;

class AdminController extends Controller
{
    public function __construct(Admin $service)
    {
        $this->service = $service;
        $this->middleware('auth');
    }

    //用户标签
    public function lists(Request $request){
        try{
            $current = $request->input('start',1);
            $limit = 10;
            $start = $limit*($current-1);
            $page = $this->service->page($limit,'admin');
            $info = $this->service->getList($start,$limit);
            return view('admin',['data' => $info,'page' => $page,'current' => $current]);
        }
        //捕获异常
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取账户列表失败');
            echo '数据库错误'.$res['errmsg'];
        }
    }

    //创建账户
    public function create(Request $request){
        try{
            $name = $request->input('name');
            $password = $request->input('password');
            $note = $request->input('note');
            $res = $this->service->create($name,$password,$note);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'创建账户失败');
        }
        return response()->json($res);
    }

    //修改
    public function update(Request $request){
        try{
            $id = $request->input('id');
            $name = $request->input('name');
            $password = $request->input('password');
            $note = $request->input('note');

            $res = $this->service->update($id,$name,$password,$note);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'修改账户失败');
        }
        return response()->json($res);
    }

    //删除
    public function delete(Request $request){
        try{
            $id = $request->input('id');
            $res = $this->service->delete($id);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'删除账户失败');
        }
        return response()->json($res);
    }
}