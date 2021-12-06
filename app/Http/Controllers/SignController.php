<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Sign;
use Log;

class signController extends Controller
{
    public function __construct(Sign $service)
    {
        $this->service = $service;
    }

    //签到页面
    public function lists(Request $request){
        try{
            $player = session('player');
            if(!$player){
                header('location:/oauth/oauth');
            }else{
                $res = $this->service->lists($player);
                // $res['playerid'] = '';
                // $res['used'] = 0;
                return view('sign',$res);
            }
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'签到渲染失败');
            echo $res['errmsg'];
        }
    }

    // public function login(Request $request){
    //     try{
    //         return view('signlogin');
    //     }
    //     catch(\Exception $e)
    //     {
    //         $res = $this->service->error($e,'绑定账号页面渲染失败');
    //         echo $res['errmsg'];
    //     }
    // }

    // public function bind(Request $request){
    //     try{
    //         $playerid = $request->input('playerid');
    //         $player = session('player');
    //         if(!$player){
    //             $res = array('error' => 1,'errmsg'=>'身份信息失效请重新点击菜单进入网页');
    //         }else{
    //             $unionid = $player['unionid'];
    //             $res = $this->service->bind($playerid,$unionid);
    //             $request->session()->put('player', array('playerid'=>$playerid,'unionid'=>$unionid));
    //         }
    //     }
    //     catch(\Exception $e)
    //     {
    //         $res = $this->service->error($e,'绑定失败');
    //     }
    //     return response()->json($res);
    // }

    public function signed(Request $request){
        try{
            $level = $request->input('level');
            $player = session('player');
            if(!$player){
                $res = array('error' => 1,'errmsg'=>'身份信息失效请重新点击菜单进入网页');
            }else{
                $playerid = $player['playerid'];
                $res = $this->service->signed($playerid,$level);
            }
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'绑定失败');
        }
        return response()->json($res);
    }

    public function unbind(Request $request){
        try{
            $playerid = $request->input('playerid');
            $res = $this->service->unbind($playerid);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'解绑失败');
        }
        return response()->json($res);
    }
}