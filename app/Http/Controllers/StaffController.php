<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Staff;

class staffController extends Controller
{
    public function __construct(Staff $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    //获取所有客服账号列表
    public function lists(Request $request){
        try{
            $current = $request->input('start',1);
            $limit = 10;
            $start = $limit*($current-1);
            $page = $this->service->page($limit,'staff');
            $info = $this->service->getList($start,$limit);
            $message = $this->service->msglist();
            return view('staff',['data' => $info,'page' => $page,'message' => $message,'current'=>$current]);
        }
        //捕获异常
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取客服账号列表出错');
            echo '没有开通新版客服功能或者没有邀请客服人员<br>'.$res['errmsg'];
        }
    }

    //添加客服帐号
    public function create(Request $request){
        try{
            $kf_account = $request->input('kf_account');
            $kf_nick = $request->input('kf_nick');
            $res = $this->service->create($kf_account,$kf_nick);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'添加客服账号出错');
        }
        return response()->json($res);
    }
    
    //修改客服帐号
    public function update(Request $request){
        try{
            $kf_account = $request->input('kf_account');
            $kf_nick = $request->input('kf_nick');
            $res = $this->service->update($kf_account,$kf_nick);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'修改客服失败');
        }
        return response()->json($res);
    }

    //删除客服帐号
    public function delete(Request $request){
        try{
            $kf_account = $request->input('kf_account');
            $res = $this->service->delete($kf_account);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'删除客服失败');
        }
        return response()->json($res);
    }
    
    //设置客服帐号的头像
    public function avatar(Request $request){
        try{
            $kf_account = $request->input('kf_account');
            $avatarPath = $this->service->upload();
            if($avatarPath){
                $res = $this->service->avatar($kf_account, $avatarPath);
            }else{
                $res['errmsg'] = '上传失败';
            }
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'设置客服账号头像失败');
        }
        return response()->json($res);
    }

    //获取客服聊天记录
    public function records(Request $request){
        try{
            $startTime = $request->input('starttime');
            $endTime = $request->input('endtime');
            $pageIndex = $request->input('pageindex');
            $pageSize = $request->input('pagesize');
            $res['errmsg'] = $this->service->records($startTime, $endTime, $pageIndex, $pageSize);
            $res['errcode'] = 0;
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取聊天记录失败');
        }
        return response()->json($res);
    }

    //主动发送消息给用户
    public function send(Request $request){
        try{
            $kf_account = $request->input('kf_account');
            $message = $request->input('message');
            $openid = $request->input('openId');
            $res = $this->service->send($kf_account,$message,$openid);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'主动发送消息失败');
        }
        return response()->json($res);
    }

    //获取客服会话列表
    public function sessionlists(Request $request){
        try{
            $kf_account = $request->input('kf_account');
            $waiter = $this->service->waiters();
            $info = $this->service->sessionlists($kf_account);

            return view('session',['data' => $info,'kf_account' => $kf_account,'waiter' => $waiter]);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取客服会话列表失败');
            echo '获取客服会话列表失败<br>'.$res['errmsg'];
        }
    }

    //创建会话
    public function sessioncreate(Request $request){
        try{
            $kf_account = $request->input('kf_account');
            $openid = $request->input('openid');
            $res = $this->service->sessioncreate($kf_account, $openid);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'创建会话失败');
        }
        return response()->json($res);
    }
    
    //关闭会话
    public function close(Request $request){
        try{
            $kf_account = $request->input('kf_account');
            $openid = $request->input('openid');
            $res = $this->service->close($kf_account, $openid);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'关闭会话失败');
        }
        return response()->json($res);
    }

    //获取客户会话状态
    public function get(Request $request){
        try{
            $openid = $request->input('openid');
            $res['errmsg'] = $this->service->get($openid);
            $res['errcode'] = 0;
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取会话状态失败');
        }
        return response()->json($res);
    }
}