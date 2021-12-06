<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Material;

use DB;
use Log;
class Staff extends Base{

    protected $staff;
    public function __construct()
    {
        $app = app('wechat');
        $this->staff = $app->staff;
        $this->session = $app->staff_session;
    }

    //获取所有客服账号列表
    public function getList(){
        try{
            $res = DB::table('staff')->get();
            $arr = $this->onlines();
            if(empty($arr)){
                $arr = array();
            }else{
                $arr = $arr->kf_online_list;
            }
            foreach($res as &$val){
                $val->online='未在线';            
                foreach($arr as $value){
                    if($val->kf_id == $value['kf_id']){
                        $val->online='在线';
                        break;
                    }
                }
            }
            return $res;
        }catch(\Exception $e){
            return $this->error($e,'获取所有客服账号列表');
        }
    }
    
    //获取所有在线的客服账号列表
    public function onlines(){
        try{
            return $this->staff->onlines();
        }catch(\Exception $e){
            return $this->error($e,'获取所有在线的客服账号列表');
        }
    }

    //添加客服帐号
    public function create($kf_account,$kf_nick){
        DB::beginTransaction();
        try{
            $res = $this->staff->create($kf_account,$kf_nick);
            $opt = array('kf_account'=>$kf_account,'kf_nick'=>$kf_nick);
            DB::table('staff')->insert($opt);

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'添加客服帐号');
        }
    }
    
    //修改客服帐号
    public function update($kf_account,$kf_nick){
        DB::beginTransaction();

        try{
            $res = $this->staff->update($kf_account,$kf_nick);
            $opt = array('kf_account'=>$kf_account,'kf_nick'=>$kf_nick);
            DB::table('staff')->where('kf_account',$kf_account)->update($opt);

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'修改客服帐号');
        }
    }

    //删除客服帐号
    public function delete($kf_account){
        DB::beginTransaction();

        try{
            $res = $this->staff->delete($kf_account);
            DB::table('staff')->where('kf_account',$kf_account)->delete();

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'删除客服帐号');
        }
    }
    
    //设置客服帐号的头像
    public function avatar($kf_account,$avatarPath){
        DB::beginTransaction();

        try{
            DB::table('staff')->where('kf_account',$kf_account)->update(['kf_headimgurl'=>$avatarPath]);
            $res = $this->staff->avatar($kf_account, public_path($avatarPath));

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'设置客服帐号的头像');
        }
    }

    //获取客服聊天记录
    public function records($startTime, $endTime, $pageIndex, $pageSize){
        $res = $this->staff->records($startTime, $endTime, $pageIndex, $pageSize)->recordlist;
        foreach($res as &$val){
            $val['time'] = date('Y-m-d G:i:s', $val['time']);
        }
        return $res;
    }
    
    //主动发送消息给用户
    public function send($kf_account,$message,$openid){
        $res = DB::table('message')->where('id', $message)->first();
        $message = $this->message($res);
        if(empty($kf_account)){
            return $this->staff->message($message)->to($openid)->send();
        }else{
            return $this->staff->message($message)->by($kf_account)->to($openid)->send();
        }
    }

    //创建会话
    public function sessioncreate($kf_account,$openid){
        return $this->session->create($kf_account, $openid);
    }
    
    //关闭会话
    public function close($kf_account,$openid){
        return $this->session->close($kf_account,$openid);
    }

    //获取客户会话状态
    public function get($openid){
        return $this->session->get($openid);
    }
    
    //获取客服会话列表
    public function sessionlists($kf_account){
        $res = $this->session->lists($kf_account)['sessionlist'];
        $arr = [];
        foreach($res as $val){
            array_push($arr,$val['openid']);
        }
        $list = DB::table('user')->select('nickname','openid')->whereIn('openid',$arr)->get();
        foreach($res as &$value){
            $value['nickname'] = $value['openid'];
            foreach($list as $v){
                if($v->openid == $value['openid']){
                    $value['nickname'] = $v->nickname;
                }
            }
        }
        return $res;
    }

    //获取未接入会话列表
    public function waiters(){
        $res = $this->session->waiters()['waitcaselist'];
        return $res;
    }

    //获取消息列表
    public function msglist(){
        $type = ['article','marticle','transfer'];
        return DB::table('message')->whereNotIn('type',$type)->lists('name', 'id');
    }

    public function upload(){
        Log::info("文件上传开始.");

        if ((($_FILES["file"]["type"] != "image/gif") && ($_FILES["file"]["type"] != "image/jpeg") && ($_FILES["file"]["type"] != "image/png") && ($_FILES["file"]["type"] != "image/bmp")) || ($_FILES["file"]["size"]/1024/1024 >= 5)){
            Log::error("头像上传不符合规则");
            return false;
        }

        if ($_FILES["file"]["error"] > 0){
            Log::error("头像上传错误,错误码:" . $_FILES["file"]["error"]);
            return false;
        }else{
            //重命名文件防止重合
            $type = substr(strchr($_FILES["file"]["type"],"/"),1);
            $path = $this->uniqidStr().'.'.$type;

            $data = "上传文件名:".$_FILES["file"]["name"]."类型:".$_FILES["file"]["type"]."文件大小:".($_FILES["file"]["size"] / 1024)." Kb".'缓存文件名:'.$_FILES["file"]["tmp_name"].'储存文件名:'.$path;
            Log::info($data);
            //储存并返回储存名
            if (file_exists("upload/" . $path)){
                Log::error($path . "已经存在");
            }else{
                move_uploaded_file($_FILES["file"]["tmp_name"],"upload/".$path );
                return '/upload/'.$path;
            }
        }
    }

    public function uniqidStr(){
        return md5(uniqid(md5(microtime(true)),true));
    }

    //返回消息处理
    public function message($message){
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
                    'source_url' => $message->source_url,
                    'show_cover' => $message->show_cover,
                ]);
                break;
                //素材消息用于群发与客服消息时使用。
            case 'material':
                return new Material('mpnews', $message->media_id);
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
                return new \EasyWeChat\Message\Transfer();                   
            case 'atransfer':
                $transfer = new \EasyWeChat\Message\Transfer();
                return $transfer->account($message->content);
            default:
                break;
        }
    }
}
