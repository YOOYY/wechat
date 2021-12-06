<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Foundation\Application;
use DB;
class Usertag extends Base{

    protected $usertag;
    private $tag;
    
    public function __construct()
    {
        $app = app('wechat');
        $this->tag = $app->user_tag;
    }

    public function getList($start = 1,$limit){
        return DB::table('usertag')->skip($start)->take($limit)->get();
    }

    public function create($name){
        $res = $this->tag->create($name);
        if(isset($res->tag)){
            $res['errmsg'] = DB::table('usertag')->insert($res->tag);
            $res['errcode'] = 0;
            return $res;
        }else{
            return $this->error($res);
        }
    }

    public function update($tagId,$name){
        DB::beginTransaction();
        DB::table('usertag')->where('id', $tagId)->update(['name' => $name]);

        try{
            $res = $this->tag->update($tagId,$name);
            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'更新用户标签');
        }
    }

    public function delete($tagId){
        DB::beginTransaction();
        DB::table('usertag')->where('id', $tagId)->delete();

        try{
            $res = $this->tag->delete($tagId);
            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'删除用户标签');
        }
    }
}
