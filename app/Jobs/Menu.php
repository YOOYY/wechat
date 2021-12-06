<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Foundation\Application;
use DB;
use Log;
class Menu extends Base{

    public function __construct()
    {
        $app = app('wechat');
        $this->menu = $app->menu;
    }

    public function getList(){
        $res = DB::table('menu')->get();
        foreach($res as &$val){
            $val->buttons = json_decode($val->buttons,TRUE);
            $val->matchrule = json_decode($val->matchrule,TRUE);
        }
        return $res;
    }

    public function getEvent(){
        $res = DB::table('event')->select('eventtype','keyword')->where('type','event')->where('eventtype','!=','subscribe')->get();
        return $res;
    }

    public function getEventList(){
        $res = DB::table('event')->select('event.*','message.name')
                ->where('event.type','event')->where('event.eventtype','!=','subscribe')
                ->leftJoin('message', 'event.message', '=', 'message.id')
                ->get();
        return $res;
    }
    
    //获取消息
    public function message(){
        $type = ['article','marticle'];
        return DB::table('message')->whereNotIn('type',$type)->lists('id', 'name');
    }

    public function create($buttons, $matchRule = []){
        DB::beginTransaction();
        Log::error("创建菜单");
        Log::error($buttons);
        Log::error($matchRule);
        $menusdata = [];
        try{
            $res = $this->menu->add($buttons, $matchRule);
            $menus = $this->menu->all()->toArray();
            Log::error($menus);
            if(isset($menus['conditionalmenu'])){
                    $data['buttons'] = json_encode($menus['menu']['button']);
                    $data['menuid'] = json_encode($menus['menu']['menuid']);
                    array_push($menusdata,$data);
                    foreach($menus['conditionalmenu'] as $val){
                        $data = [];
                        $data['buttons'] = json_encode($val['button']);
                        $data['matchrule'] = json_encode($val['matchrule']);
                        $data['menuid'] = json_encode($val['menuid']);
                        array_push($menusdata,$data);
                    }
            }else{
                    $data['buttons'] = json_encode($menus['menu']['button']);
                    array_push($menusdata,$data);
            }

            DB::table('menu')->delete();
            DB::table('menu')->insert($menusdata);

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'创建菜单');
        }
    }

    public function delete($id){
        DB::beginTransaction();
        try{
            $menuId = DB::table('menu')->select('menuid')->where('id', $id)->first();
            DB::table('menu')->where('id', $id)->delete();
            if(isset($menuid)){
                $res = $this->menu->destroy($menuId);
            }else{
                $res = $this->menu->destroy();
            }

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'删除菜单');
        }
    }

    public function test($userId){
        try{
            $res['errmsg'] = $this->menu->test($userId);
            $res['errcode'] = 0;
            return $res;
        }catch(\Exception $e){
            return $this->error($e,'测试菜单');
        }

    }

    //分页
    public function page($limit = 10,$table){
        $num = DB::table($table)
            ->where('event.type', 'event')
            ->where('event.id','!=', '1')
            ->count();
        return ceil($num/$limit);
    }
}
