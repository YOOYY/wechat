<?php

namespace App\Jobs;

use App\Jobs\Base;
use DB;
class Event extends Base{

    protected $event;

    public function getList($type,$start = 0,$limit){
        if(isset($type)){
            $res = DB::table('event')
            ->select('event.*', 'message.name')
            ->where('type',$type)->skip($start)->take($limit)
            ->leftJoin('message', 'event.message', '=', 'message.id')
            ->get();
        }else{
            $res = DB::table('event')
            ->where('event.type', '!=', 'event')
            ->orWhere('event.id', '1')
            ->select('event.*', 'message.name')
            ->skip($start)->take($limit)
            ->leftJoin('message', 'event.message', '=', 'message.id')
            ->get();
        }
        return $res;
    }

    //获取消息
    public function message(){
        $type = ['article','marticle'];
        return DB::table('message')->whereNotIn('type',$type)->lists('id', 'name');
    }

    //更新
    public function update($opt){
        if($opt['id']<8){
            $res['message'] = DB::table('event')->where('id',$opt['id'])->update($opt);
        }else{
            $res['message'] = DB::table('event')->where('id',$opt['id'])->update($opt);
        }
        $res['errcode'] = 0;
        return $res;
    }

    //删除
    public function delete($id){
        //$arr = array('subscribe','unsubscribe','SCAN','LOCATION','CLICK','VIEW');
        if($id>8){
            $res['message'] = DB::table('event')->where('id',$id)->delete();
        }
        $res['errcode'] =0;
        return $res;
    }

    //新建
    public function create($opt){
        $res['message'] = DB::table('event')->insert($opt);
        $res['errcode'] = 0;
        return $res;
    }

    //分页
    public function page($limit = 10,$table){
        $num = DB::table($table)
            ->where('event.type', '!=', 'event')
            ->orWhere('event.id', '1')
            ->count();
        return ceil($num/$limit);
    }
}
