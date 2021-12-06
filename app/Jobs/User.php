<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Foundation\Application;
use DB;

class User extends Base{

    protected $user;
    private $tag;
    private $group;

    public function __construct()
    {
        $app = app('wechat');
        $this->tag = $app->user_tag;
        $this->group = $app->user_group;
        $this->userService = $app->user;
    }

    public function myPage($limit = 10,$table,$tagId,$name){
        if(!empty($name)){
            if(!empty($tagId)){
                $num = DB::table('user')
                ->where('nickname', $name)
                ->where('tagid_list','like',$tagId.',%')
                ->orwhere('tagid_list','like','%,'.$tagId.',%')
                ->count();
            }else{
                $num = DB::table('user')
                ->where('nickname','=', $name)
                ->count();
            }
        }else{
            if(!empty($tagId)){
                $num = DB::table('user')
                ->where('tagid_list','like',$tagId.',%')
                ->orwhere('tagid_list','like','%,'.$tagId.',%')
                ->count();
            }else{
                $num = DB::table('user')
                ->count();
            }
        }
        return ceil($num/$limit);
    }

    public function getList($tagId,$name,$start = 1,$limit){
        if(!empty($name)){
            if(!empty($tagId)){
                $info = DB::table('user')
                ->where('nickname', $name)
                ->where('tagid_list','like',$tagId.',%')
                ->orwhere('tagid_list','like','%,'.$tagId.',%')
                ->skip($start)
                ->take($limit)
                ->get();
            }else{
                $info = DB::table('user')
                ->where('nickname','=', $name)
                ->skip($start)
                ->take($limit)
                ->get();
            }
        }else{
            if(!empty($tagId)){
                $info = DB::table('user')
                ->where('tagid_list','like',$tagId.',%')
                ->orwhere('tagid_list','like','%,'.$tagId.',%')
                ->skip($start)
                ->take($limit)
                ->get();
            }else{
                $info = DB::table('user')
                ->skip($start)
                ->take($limit)
                ->get();
            }
        }
        return $info;
    }

    public function getTag(){
        return DB::table('usertag')->lists('name', 'id');
    }

    public function batchtag($openIds, $tagId){
        DB::beginTransaction();

        $res = DB::table('user')->whereIn('openid', $openIds)->get();

        //添加标签粉丝量
        $count = count($openIds);

        foreach($res as $val){
            if(empty($val->tagid_list)){
                DB::table('usertag')->where('id',0)->decrement('count');
            }
            $tagIds = explode(',',$val->tagid_list);
            if(array_search($tagId,$tagIds) ===false){
                $tagIds = $val->tagid_list.$tagId.',';
                $res = DB::table('user')->where('openid', $val->openid)->update(['tagid_list'=>$tagIds]);
            }else{
                $count = $count-1;
            };
        }

        
        DB::table('usertag')->where('id',$tagId)->increment('count',$count); 

        try{
            $res = $this->tag->batchTagUsers($openIds, $tagId);

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'添加标签');
        }
    }

    public function batchUntag($openIds, $tagId){
        DB::beginTransaction();

        $res = DB::table('user')->whereIn('openid', $openIds)->get();

        //移除粉丝量
        $count = count($openIds);

        foreach($res as $val){
            $tagIds = explode(',',$val->tagid_list);
            $index = array_search($tagId,$tagIds);
            if($index !== false){
                array_splice($tagIds,$index,1);
                $tagIds = implode(',',$tagIds);
                $res = DB::table('user')->where('openid', $val->openid)->update(['tagid_list'=>$tagIds]);
            }else{
                $count = $count-1;
            };
            if(empty($tagIds)){
                DB::table('usertag')->where('id',0)->increment('count');
            }
        }

        DB::table('usertag')->where('id',$tagId)->decrement('count',$count);
        try{
            $res = $this->tag->batchUntagUsers($openIds, $tagId);

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'移除标签');
        }
    }

    public function remark($openId, $remark){
        DB::beginTransaction();
        
        DB::table('user')->where('openid', $openId)->update(['remark'=>$remark]);
        try{
            $res = $this->userService->remark($openId, $remark);
            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'用户改备注');
        }
    }

    public function addblack($openids){
        DB::beginTransaction();

        $res = DB::table('user')->whereIn('openid', $openids)->update(['groupid'=>1]);
        try{
            $res = $this->group->moveUsers($openids, 1);
            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'添加黑名单');
        }
    }

    public function removeblack($openids){
        DB::beginTransaction();

        $res = DB::table('user')->whereIn('openid', $openids)->update(['groupid'=>0]);
        try{
            $res = $this->group->moveUsers($openids, 0);
            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'移除黑名单');
        }
    }
}
