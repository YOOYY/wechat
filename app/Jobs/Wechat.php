<?php

namespace App\Jobs;

use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Material;
use EasyWeChat\Foundation\Application;

use App\Jobs\Base;
use DB;
use Log;
use Rediss;

class Wechat extends Base{
    //服务器
    public function serve(){
        $server = app('wechat')->server;
        $server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    return $this->eventMessage($message);
                    break;
                case 'text':
                    return $this->textMessage($message);
                    break;
                default:
                    return $this->othersMessage($message);
                    break;
            }
        });
        
        $response = $server->serve();
        return $response;
    }

    //事件消息处理
    public function eventMessage($message){
        //关注与取关
        switch ($message->Event) {
            case 'subscribe':
                $userService = app('wechat')->user;
                $info = $userService->get($message->FromUserName);
                $info['tagid_list'] = '';

                DB::table('user')->insert($this->object_to_array($info));
                DB::table('usertag')->where('id',0)->increment('count');
                break;
            case 'unsubscribe':
                $info = DB::table('user')
                ->where('openid',$message->FromUserName)
                ->first();
                $tagid_list = empty($info->tagid_list)? array(0) : explode(",",$info->tagid_list);
                DB::table('usertag')
                ->whereIn('id',$tagid_list)->decrement('count');
                DB::table('user')
                ->where('openid',$message->FromUserName)
                ->delete();
                break;
            default:
                break;
        }

        if(is_null($message->EventKey)){
            $message->EventKey = '';
        }

        $res = DB::table('event')
        ->select('message.*')
        ->where('event.keyword',$message->EventKey)
        ->where('event.eventtype',$message->Event)
        ->leftJoin('message', 'event.message', '=', 'message.id')
        ->first();
        if(isset($res)){
            return $this->message($res,$message->FromUserName);
        }else{
            return '';
        }
    }

    //文字消息处理
    public function textMessage($message){
        $fromUser = $message->FromUserName;
        $res = DB::table('event')
            ->select('message.*')
            ->where('event.keyword',$message->Content)
            ->where('event.type','text')
            ->leftJoin('message', 'event.message', '=', 'message.id')
            ->first();

        if(!isset($res)){
            //默认文本回复
            $res = DB::table('event')
            ->select('message.*')
            ->where('event.eventtype','default')
            ->where('event.type','text')
            ->leftJoin('message', 'event.message', '=', 'message.id')
            ->first();
        }

        if(!isset($res)){return '';}

        return $this->message($res,$fromUser);
    }

    //其他消息处理
    public function othersMessage($message){
        $fromUser = $message->FromUserName;
        $type = $message->MsgType;
        $arr = array('image','voice','video','location','link','shortvideo');
        if(!in_array($type,$arr)){
            $type = 'other';
        }
        $res = DB::table('event')
        ->select('message.*')
        ->where('event.type',$type)
        ->leftJoin('message', 'event.message', '=', 'message.id')
        ->first();
        if(isset($res)){
            return $this->message($res,$fromUser);
        }else{
            return '';
        }
    }

    //返回消息处理
    public function message($message,$fromUser){
        switch ($message->type) {
            case 'text':
                return new Text(['content' => $message->content]);
                break;
            case 'image':
                return new Image(['media_id' => $message->media_id]);
                break;
            case 'video':
                return new Video([
                    'title' => $message->title,
                    'media_id' => $message->media_id,
                    'description' => $message->description,
                    'thumb_media_id' => $message->thumb_media_id
                ]);
                break;
            case 'voice':
                return new Voice(['media_id' => $message->media_id]);
                break;
            case 'link':
                    //暂不支持
                return;
                break;
            case 'location':
                    //暂不支持
                return;
                break;
            case 'news':
                return new News([
                    'title'       => $message->title,
                    'description' => $message->description,
                    'url'         => $message->url,
                    'image'       => $message->thumb_url
                ]);
                break;
            case 'article':
                return new Article([
                    'title'   => $message->title,
                    'author'  => $message->author,
                    'content' => $message->content,
                    'thumb_media_id' => $message->thumb_media_id,
                    'digest' => $message->digest,
                    'source_url' => $message->content_source_url,
                    'show_cover' => $message->show_cover_pic
                ]);
                break;
            //素材消息用于群发与客服消息时使用。
            case 'material':
                $staff = app('wechat')->staff;
                $staff->message(new Material('mpnews',$message->media_id))->by(env('STAFF_ACCOUNT',''))->to($fromUser)->send();
                break;
            case 'mnews':
                $result = array();
                $arr = json_decode($message->content,true);
                foreach($arr as $val){
                    $item = new News($val);
                    array_push($result,$item);
                }
                return $result;
                break;
            case 'transfer':
                if(empty($message->content)){
                    return new \EasyWeChat\Message\Transfer();
                }else{
                    $transfer = new \EasyWeChat\Message\Transfer();
                    return $transfer->account($message->content);
                }
                break;
            case 'method':
                return $this->giftcode($message->content,$fromUser);
                break;
            case 'sign':
                return $this->sign($fromUser);
                break;
            default:
                break;
        }
    }

    public function giftcode($type,$fromUser){
        $res = Rediss::hget($fromUser,$type);
        if(!$res){
            $code = Rediss::lpop($type);
            if($code){
                Rediss::hSet($fromUser,$type,$code);
                $res = $code;
            }else{
                $res = false;
            }
        }
        Log::error($res);
        if($res){
            $res = '终于等到您，您的专属迎新礼包码为：'.$res.'，您可进入游戏点击主界面下方【消息】，选择兑换中心，输入您的专属礼包码自行兑换20000金币奖励！！祝您游戏愉快！！';
        }else{
            $res = '礼包码已被抢光';
        }
        return new Text(['content' => $res]);
        // return new Article([
        //     'title'   => '迎新大礼包',
        //     'author'  => '牌缘',
        //     'content' => $res,
        //     'thumb_media_id' =>'Ddj88kHp9DUomY0dZIBRQ5XasbiWFX8VORsPULA3axA',
        //     'digest' => '欢迎萌新加入牌缘大家庭~',
        //     'show_cover' => 1
        // ]);
    }

    public function sign($fromUser){
        $unionid = app('wechat')->user->get($fromUser)['unionid'];
        $res = DB::table('sign')->where('unionid',$unionid)->first();
        if(isset($res)){
            $player = json_decode(json_encode($res),true);
            $offTime=date_diff(date_create($player['lastsign']),date_create())->format("%a");
            $playerid = $player['playerid'];
        }else{
            $baseUrl = env('APIURL');
            $result = $this->Get($baseUrl.'/wechat/binduser?unionid='.$unionid);
            if($result['error'] !== "0"){
                Log::error('绑定失败：'.$result['message']);
                return new Text(['content' => '绑定账号失败,请先下载游戏并用微信号登录']);
            }else{
                $playerid = $result['data']['playerid'];
                $num = DB::table('sign')
                ->insert(array('playerid'=>$playerid,'unionid'=>$unionid));
                $player = array('playerid'=>$playerid,'unionid'=>$unionid);
                $offTime=10;
            }
        }
        if($offTime>0){
            if($offTime>1){
                $signday = 0;
            }else{
                $signday = ($player['level'] ==7) ? 0 :$player['level'];
            }
            $level = $signday+1;
            $today = date('Y-m-d');
            $baseUrl = env('APIURL');
            $res = $this->Get($baseUrl.'/wechat/singon?playerid='.$playerid.'&singontype='.$level);
            if($res['error'] !== "0"){
                Log::error('签到失败：'.$res['message']);
                return new Text(['content' => '签到失败，请稍后重试']);
            }
            $num = DB::table('sign')
            ->where('playerid', $playerid)
            ->update(array('level'=>$level,'lastsign'=>$today));
            $moneylist = [588,888,1088,1688,1888,2888,3888];
            $money = $moneylist[$signday];
            return new Text(['content' => 
                "签到成功！获得金币{$money}。
                活动规则
                1、第1天：588金币；
                    第2天：888金币；
                    第3天：1088金币；
                    第4天：1688金币；
                    第5天：1888金币；
                    第6天：2888金币；
                    第7天：3888金币
                2、每个微信号每天仅限签到一次。
                3、连续签到7天后，签到天数重新计算。如果漏签，需从第一天开始重新签到。
                4、签到奖励将以游戏内邮件形式发放，请注意查收。
                5、如因系统延迟未收到奖励，请退出游戏后重新登陆领取。"]);
        }else{
            return new Text(['content' => '您今日已签到']);
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
