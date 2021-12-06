<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Foundation\Application;
use Log;
use DB;
class Oauth extends Base{

    public function __construct()
    {
        $app = app('wechat');
        $this->oauth = $app->oauth;
        $this->url = $app->url;
    }

    public function oauth()
    {
        return $this->oauth->redirect();
    }

    public function user()
    {
        return $this->oauth->user()->toArray();
    }

    public function bind($unionid)
    {
        $baseUrl = env('APIURL');
        $result = $this->Get($baseUrl.'/wechat/binduser?unionid='.$unionid);
        if($result['error'] !== "0"){
            Log::error('绑定失败：'.$result['message']);
            return array('error' => "1",'errmsg' => '绑定账号失败:'.$result['message']);
        }else{
            $playerid = $result['data']['playerid'];
            $num = DB::table('sign')
            ->insert(array('playerid'=>$playerid,'unionid'=>$unionid));
            return array('error' => "0",'playerid'=>$playerid);
        }
    }

    public function shortUrl($url){
        try{
            $shortUrl= $this->url->shorten($url);
            $res['errmsg'] = $shortUrl;
            $res['errcode']=0;
            return $res;
        }catch(\Exception $e){
            return $this->error($e,'转短链');
        }
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
