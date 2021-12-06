<?php

namespace App\Jobs;
use EasyWeChat\Foundation\Application;
use App\Jobs\Base;
use DB;
class Message extends Base{

    protected $message;

    public function __construct()
    {
        $app = app('wechat');
        $this->reply = $app->reply;
    }
    
    public function getList($name,$type,$start = 0,$limit){
        switch ($type) {
            case 'text':
                $query = DB::table('message')
                            ->select('id','name', 'type','content','note')
                            ->where('type',$type)
                            ->skip($start)->take($limit);
                if(!empty($name)){
                    $res = $query->where('name',$name)->get();
                }else{
                    $res = $query->get();
                }
                break;
            case 'image':
                $query =  DB::table('message')
                ->leftJoin('temporary', function ($join) {
                    $join->on('message.media_id', '=', 'temporary.media_id')
                         ->where('temporary.type', '=', 'image');
                })
                ->leftJoin('material', function ($join) {
                    $join->on('message.media_id', '=', 'material.media_id')
                         ->where('material.type', '=', 'image');
                })
                ->select(
                    'message.id','message.name', 'message.type','message.media_id', 'material.url as pic',
                    'temporary.name as temporaryname','material.name as materialname',
                    'temporary.path as temporarypath','material.path as materialpath',
                    'temporary.created_at as created_at','message.note','message.state'
                )
                ->where('message.type',$type)
                ->skip($start)->take($limit)
                ->distinct();
                if(!empty($name)){
                    $res = $query->where('message.name',$name)->get();
                }else{
                    $res = $query->get();
                }
                break;
            case 'video':
                $query = DB::table('message')
                    ->leftJoin('material', 'message.media_id', '=', 'material.media_id')
                    ->leftJoin('temporary', 'message.media_id', '=', 'temporary.media_id')
                    ->select('message.id','message.name', 'message.type','message.title','message.description','message.media_id','temporary.name as temporaryname',
                            'material.name as materialname','temporary.created_at as created_at','message.note','message.state')
                    ->where('message.type',$type)
                    ->skip($start)->take($limit)
                    ->distinct();
                if(!empty($name)){
                    $res = $query->where('message.name',$name)->get();
                }else{
                    $res = $query->get();
                }
                break;
            case 'voice':
            case 'material':
                $query = DB::table('message')
                    ->leftJoin('material', 'message.media_id', '=', 'material.media_id')
                    ->leftJoin('temporary', 'message.media_id', '=', 'temporary.media_id')
                    ->select('message.id','message.name', 'message.type','message.media_id','material.name as materialname','temporary.name as temporaryname',
                    'temporary.created_at as created_at','message.note','message.state')
                    ->where('message.type',$type)
                    ->skip($start)->take($limit)
                    ->distinct();
                if(!empty($name)){
                    $res = $query->where('message.name',$name)->get();
                }else{
                    $res = $query->get();
                }
                break;
            case 'news':
                $query = DB::table('message')
                            ->select('id','name', 'type','title','description','thumb_url as image','url','note')
                            ->where('type',$type)
                            ->skip($start)->take($limit);
                if(!empty($name)){
                    $res = $query->where('message.name',$name)->get();
                }else{
                    $res = $query->get();
                }
                break;
            case 'mnews':
                $query = DB::table('message')
                    ->select('id','name', 'type','content','note')
                    ->where('type',$type)
                    ->skip($start)->take($limit);
                if(!empty($name)){
                    $res = $query->where('message.name',$name)->get();
                }else{
                    $res = $query->get();
                }
                break;
            case 'transfer':
                $query = DB::table('message')->
                    select('id','name', 'type','content','note')
                    ->where('type','transfer')
                    ->skip($start)->take($limit);
                if(!empty($name)){
                    $res = $query->where('message.name',$name)->get();
                }else{
                    $res = $query->get();
                }
                break;
            default:
                $query = DB::table('message')
                    ->select('id','name', 'type','content','note')
                    ->where('type',$type)
                    ->skip($start)->take($limit);
                if(!empty($name)){
                    $res = $query->where('name',$name)->get();
                }else{
                    $res = $query->get();
                }
                break;
        }
        return $res;
    }

    //更新
    public function update($opt){
        $arr = ['type'=>'','content'=>'','media_id'=>'','title'=>'','description'=>'','url'=>''];
        $opt = array_merge($arr,$opt);
        $res['errmsg'] = DB::table('message')->where('id',$opt['id'])->update($opt);
        $res['errcode'] =0;
        return $res;
    }

    //删除
    public function delete($id){
        $res['errmsg'] = DB::table('message')->where('id',$id)->delete();
        $res['errcode'] =0;
        return $res;
    }

    //新建
    public function create($opt){
        $res['errmsg'] = DB::table('message')->insert($opt);
        $res['errcode'] = 0;
        return $res;
    }

    // //查询当前回复规则
    // public function reply(){
    //     return $this->reply->current();
    // }
    
    //获取图文消息
    public function get($type){
        if(is_array($type)){
            $res['errmsg'] = DB::table('message')->whereIn('type',$type)->lists('id', 'name');
        }else{
            $res['errmsg'] = DB::table('message')->where('type',$type)->lists('id', 'name');
        }
        $res['errcode'] = 0;
        return $res;
    }

    //获取图文消息
    public function getNot($type){
        if(is_array($type)){
            $res['errmsg'] = DB::table('message')->whereNotIn('type',$type)->lists('id', 'name');
        }else{
            $res['errmsg'] = DB::table('message')->where('type', '<>', $type)->lists('id', 'name');
        }
        $res['errcode'] = 0;
        return $res;
    }

    //页码函数
    public function page($limit = 10,$table,$type = 'text'){
        $num = DB::table($table)
            ->where('type',$type)
            ->count();
        return ceil($num/$limit);
    }
}
