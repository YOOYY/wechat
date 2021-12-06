<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Foundation\Application;
use DB;
class Admin extends Base{

    protected $admin;

    public function getList($start = 1,$limit){
        return DB::table('admin')->skip($start)->take($limit)->get();
    }

    public function create($name,$password,$note){
        $info['name'] = $name;
        $info['password'] = bcrypt($password);
        $info['note'] = $note;
        $info['created_at'] = time();
        DB::table('admin')->insert($info);
        $res['errcode'] = 0;
        return $res;
    }

    public function update($id,$name,$password,$note){
        $info['name'] = $name;
        $info['password'] = bcrypt($password);
        $info['note'] = $note;
        DB::table('admin')->where('id', $id)->update($info);
        $res['errcode'] = 0;
        return $res;
    }

    public function delete($id){
        DB::table('admin')->where('id', $id)->delete();
        $res['errcode'] = 0;
        return $res;
    }
}
