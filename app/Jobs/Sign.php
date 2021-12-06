<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Foundation\Application;
use DB;
use Log;

class Sign extends Base{

    protected $user;
    private $tag;
    private $group;

    public function __construct()
    {
        $app = app('wechat');
    }

    public function lists($player){
        $playerid = $player['playerid'];
        if(isset($player['lastsign'])){
            $offTime=date_diff(date_create($player['lastsign']),date_create())->format("%a");
        }else{
            $offTime=10;
        }
        Log::error('距离上次签到的时间：'.$offTime);
        if($offTime>0){
            $used = 0;
            if($offTime>1){
                $signday = 0;
            }else{
                $signday = ($player['level'] ==7) ? 0 :$player['level'];
            }
        }else{
            $used = 1;
            $signday = $player['level'];
        }
        return array('used'=>$used,'signday'=>$signday,'playerid'=>$playerid);
    }

    // public function bind($playerid,$unionid){
    //     $count = DB::table('sign')
    //     ->where('playerid', $playerid)
    //     ->count();
    //     if($count == 0){
    //         $res = $this->post($url,array('unionid'=>$unionid,'playerid'=>$playerid));
    //         if(!$res){
    //             Log::error('绑定失败：'.$res['msg']);
    //             return array('error' => 1,'errmsg' => '绑定账号失败');
    //         }
    //         $num = DB::table('sign')
    //         ->insert(array('playerid'=>$playerid,'unionid'=>$unionid));
    //         return array('error' => 0);
    //     }else{
    //         return array('error' => 1,'errmsg' => '该账号已被绑定');
    //     }
    // }

    public function signed($playerid,$level){
        $today = date('Y-m-d');
        $baseUrl = env('APIURL');
        $res = $this->Get($baseUrl.'/wechat/singon?playerid='.$playerid.'&singontype='.$level);
        if($res['error'] !== "0"){
            Log::error('签到失败：'.$res['message']);
            return array('error' => 1,'errmsg' => '签到失败:'.$res['message']);
        }
        $num = DB::table('sign')
        ->where('playerid', $playerid)
        ->update(array('level'=>$level,'lastsign'=>$today));
        return array('error'=>0);
    }

    public function unbind($playerid){
        $res = DB::table('sign')
        ->where('playerid', $playerid)
        ->delete();
        return array('error' => "0");
    }

    public function Get($url) {
        $headerArray =array("Content-type:application/json;","Accept:application/json");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output,true);
        return $output;
    }
}
