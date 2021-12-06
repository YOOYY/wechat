<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Message\Text;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Material;
use EasyWeChat\Foundation\Application;
use DB;
use Rediss;
use Log;

class Schedule extends Base{

    public function __construct()
    {
        $app = app('wechat');
        $this->group = $app->user_group;
        $this->userService = $app->user;
        $this->material = $app->material;
        $this->staff = $app->staff;
        $this->menu = $app->menu;

        $this->openid = [];
    }
    
    public function updateTag()
    {
        DB::transaction(function () {
            echo '开始更新用户标签<br>';
            DB::table('usertag')->delete();
            $res = $this->group->lists()->toArray();
            DB::table('usertag')->insert($res['groups']);
            echo '用户标签完毕<br>';
        });
    }

    //获取用户信息列表
    public function updateUser(){
        echo '开始更新用户列表<br>';
        $i = 0;
        $nextOpenId = null;
        $this->userlists = [];
        do {
            $lists = $this->userService->lists($nextOpenId);
            $openids = $lists->data['openid'];
            array_push($this->userlists,$openids);
            $total = $lists->total;
            $page = ceil($total/10000);
            $count = $lists->count;
            $j = 0;
            do{
                $opage = ceil($count/100);
                if($j == ($opage-1)){
                    $ocount = $count%100;
                    $this->openid = array_slice($openids,($j*100));
                }else{
                    $ocount = 100;
                    $this->openid = array_slice($openids,($j*100),100);
                }

                DB::transaction(function () {
                    $res = $this->userService->batchGet($this->openid)->toArray()['user_info_list'];
                    DB::table('user')->whereIn('openid', $this->openid)->delete();
                    foreach($res as &$val){
                        $val['tagid_list'] = implode(',',$val['tagid_list']);
                    }
                    DB::table('user')->insert($res);
                });

                $j++;
            } while ($j<$opage);
            $nextOpenId = $lists->nextOpenId;
            $i++;

        } while ($i<$page);
        DB::transaction(function () {
            $id = DB::table('user')->lists('openid');
            $userlistArr = $this->object_to_array($this->userlists)[0];
            $data = array_diff($id,$userlistArr);
            if($data){
                DB::table('user')->where('openid',$data)->delete();
            }
        });
        echo '更新用户列表结束<br>';
    }

    public function updateMaterial()
    {
        $stats = $this->material->stats();

        //更新图片
        echo '开始更新图片素材<br>';
        $i = 1;
        $image_count = $stats['image_count'];
        $page = ceil($image_count/20);
        do{
            $lists = $this->material->lists('image', 20*($i-1))['item'];
            $arr = DB::table('material')->where('type','image')->where('blocked',0)->lists('media_id');
            $this->updateArr = [];
            $this->insertArr = [];
            foreach($lists as &$v){
                $v['type'] = 'image';
                unset($v['tags']);
                if(array_search($v['media_id'],$arr) === false){
                    array_push($this->insertArr,$v);
                }else{
                    array_push($this->updateArr,$v);
                }
            }
            DB::transaction(function () {
                $this->updateBatch('material',$this->updateArr,"image");
                if(count($this->insertArr) != 0){
                    DB::table('material')->insert($this->insertArr);
                }
            });
            $i++;
        } while ($i<=$page);
        echo '更新图片素材结束<br>';

        //更新音频
        echo '开始更新音频素材<br>';
        $voice_count = $stats['voice_count'];
        $i = 1; 
        $page = ceil($voice_count/20);
        do{
            $lists = $this->material->lists('voice', 20*($i-1))['item'];

            $arr = DB::table('material')->where('type','voice')->where('blocked',0)->lists('media_id');
            $this->updateArr = [];
            $this->insertArr = [];
            foreach($lists as $v){
                $v['type'] = 'voice';
                unset($v['tags']);
                if(array_search($v['media_id'],$arr) === false){
                    array_push($this->insertArr,$v);
                }else{
                    array_push($this->updateArr,$v);
                }
            }
            DB::transaction(function () {
                $this->updateBatch('material',$this->updateArr,"voice");
                if(count($this->insertArr) != 0){
                    DB::table('material')->insert($this->insertArr);
                }
            });
            $i++;
        } while ($i<=$page);
        echo '更新音频素材结束<br>';
        
        //更新视频
        echo '开始更新视频素材<br>';
        $video_count = $stats['video_count'];
        $i = 1; 
        $page = ceil($video_count/20);
        do{
            $lists = $this->material->lists('video', 20*($i-1))['item'];
            foreach($lists as &$val){
                if(isset($val)){
                    $resource = $this->material->get($val['media_id']);
                    $val['title'] = $resource['title'];
                    $val['description'] = $resource['description'];
                }
            }
            $arr = DB::table('material')->where('type','video')->where('blocked',0)->lists('media_id');
            $this->updateArr = [];
            $this->insertArr = [];
            foreach($lists as $v){
                $v['type'] = 'video';
                unset($v['tags']);
                if(array_search($v['media_id'],$arr) === false){
                    array_push($this->insertArr,$v);
                }else{
                    array_push($this->updateArr,$v);
                }
            }
            DB::transaction(function () {
                $this->updateBatch('material',$this->updateArr,"video");
                if(count($this->insertArr) != 0){
                    DB::table('material')->insert($this->insertArr);
                }
            });
            $i++;
        } while ($i<=$page);
        echo '更新视频素材结束<br>';

        //更新文章
        echo '开始更新图文素材<br>';
        $stats = $this->material->stats();
        $news_count = $stats['news_count'];
        $i = 1; 
        $page = ceil($news_count/20);
        do{
            $lists = $this->material->lists('news', 20*($i-1))['item'];
            $arr = DB::table('material')->whereIn('type',['article','marticle'])->where('blocked',0)->lists('media_id');
            $this->updateArr = [];
            $this->insertArr = [];
            foreach($lists as $v){
                unset($v['tags']);
                if(array_search($v['media_id'],$arr) === false){
                    array_push($this->insertArr,$v);
                }else{
                    array_push($this->updateArr,$v);
                }
            }
            //更新
            foreach($this->updateArr as $this->v){
                DB::transaction(function () {
                    if(count($this->v['content']['news_item']) == 1){
                        $arr = $this->v['content']['news_item'][0];
                        $arr['type'] = 'article';
                        $arr['update_time'] = $this->v['update_time'];
                        DB::table('material')->where('media_id',$this->v['media_id'])->update($arr);
                    }else{
                        $arr = $this->v['content']['news_item'];
                        $content = json_encode($arr);
                        DB::table('material')->where('media_id',$this->v['media_id'])->update(["content"=>$content,"type"=>'marticle',"update_time"=>$this->v['update_time']]);
                    }
                });
            }
            //追加
            foreach($this->insertArr as $this->v){
                DB::transaction(function () {
                    if(count($this->v['content']['news_item']) == 1){
                        $new = $this->v['content']['news_item'][0];
                        $new["media_id"] = $this->v['media_id'];
                        $new["name"] = $this->v['media_id'];
                        $new["update_time"] = $this->v['update_time'];
                        $new["type"] = 'article';
                        DB::table('material')->insert($new);
                    }else{
                        $arr = $this->v['content']['news_item'];
                        $content = json_encode($arr);
                        $new["media_id"] = $this->v['media_id'];
                        $new["name"] = $this->v['media_id'];
                        $new["type"] = 'marticle';
                        $new["update_time"] = $this->v['update_time'];
                        $new["content"] = $content;
                        DB::table('material')->insert($new);
                    }
                });
            }            
            $i++;
        } while ($i <= $page);
        echo '更新图文素材结束<br>';

        // 更新缩略图
        echo '开始更新缩略图<br>';
        DB::transaction(function () {
            $a1 = DB::table('material')->where('type','thumb')->where('blocked',0)->lists('url','media_id');
            $a2 = DB::table('material')->where('type','article')->where('blocked',0)->lists('thumb_url','thumb_media_id');
            $marticleArr = DB::table('material')->where('type','marticle')->where('blocked',0)->lists('content');
            foreach ($marticleArr as $marticleItem){
                $k = json_decode($marticleItem,true);
                foreach ($k as $i){
                    $a1[$i['thumb_media_id']] = $i['thumb_url'];
                }
            }
            $result=array_diff_key($a2,$a1);
            foreach($result as $index => $value){
                $arr = array('media_id'=>$index,'url'=>$value,'name'=>$index,'type'=>'thumb');
                DB::table('material')->insert($arr);
            }
        });
        echo '更新缩略图结束<br>';

        //更新文章图片
        echo '开始更新文章图片<br>';
        //单图
        $oarticles = DB::table('material')->where('type','article')->where('blocked',0)->lists('content');
        //多图
        $marticles = array();
        $marticleArr = DB::table('material')->where('type','marticle')->where('blocked',0)->lists('content');
        foreach ($marticleArr as $marticleItem){
            $k = json_decode($marticleItem,true);
            foreach ($k as $i){
                array_push($marticles,$i['content']);
            }
        }
        $articles = array_merge($marticles,$oarticles);
        $arr = array();
        foreach($articles as $val){
            preg_match_all("/<img(.*)data-src(.*)>/U", $val, $pat_array);
            preg_match_all("/url\((&quot;)?(http(s)?:\/\/mmbiz.qpic.cn(.*))(&quot;)?\)(;)?/U", $val, $bg_array);
            foreach($pat_array[0] as $v){
                preg_match("/data-src=\"(.*)\"/U", $v, $src_array);
                if(empty($src_array)){
                    continue;
                }else{
                    $src = $src_array[1];
                }
                preg_match("/title=\"(.*)\"/U", $v, $title_array);
                if(empty($title_array)){
                    $title = '追加素材';
                }else{
                    $title = $title_array[1];
                }

                if(array_key_exists($src,$arr)){
                    if($arr[$src] == '追加素材' && $title != '追加素材'){
                        $arr[$src] = $title;
                    };
                }else{
                    $arr[$src] = $title;
                };
            }
            $bg_array = array_flip($bg_array[2]);
            $arr = array_merge($bg_array,$arr);
        }
        $articleimage = DB::table('material')->where('type','articleimage')->where('blocked',0)->lists('id','url');
        $result=array_diff_key($arr,$articleimage);
        foreach($result as $index => $item){
            DB::table('material')->insert(array('name'=>$item,'url'=>$index,'type'=>'articleimage'));
        }
        echo '更新文章图片素材结束<br>';
    }

    public function updateStaff()
    {
        DB::transaction(function () {
            echo '开始更新客服列表<br>';
            $res = $this->staff->lists()->toArray()['kf_list'];
            //var_dump($res);
            $old = DB::table('staff')->get();
            DB::table('staff')->delete();
            foreach($res as &$val){
                foreach($old as $v){
                    if($val['kf_account'] == $v->kf_account){
                        $val['kf_headimgurl'] = $v->kf_headimgurl;
                        break;
                    }
                }
                DB::table('staff')->insert($val);
            }
            echo '更新客服列表结束<br>';
        });
    }

    public function updateFile()
    {
        echo '开始更新本地素材文件<br>';
        $arr = DB::table('material')->select('media_id','name','type')
                ->whereNull('path')
                ->whereNotNull('media_id')
                ->where('blocked',0)
                ->whereNotIn('type',['article','articleimage','marticle'])
                ->get();
        $app = app('wechat');
        $material = $app->material;
        foreach($arr as &$val){
            $type = substr($val->name,strrpos($val->name,'.'));
            switch ($val->type) {
                case 'image':
                    $typeArr = array('jpg','jpeg','png','gif','bmp');
                    $type = in_array($type,$typeArr)?$type:'jpg';
                    break;
                case 'voice':
                    $typeArr = array('mp3','wma','wav','amr');
                    $type = in_array($type,$typeArr)?$type:'mp3';
                    break;
                case 'video':
                    $type = 'mp4';
                    break;
                case 'thumb':
                    $type = 'jpg';
                    break;
                default:
                    $type = 'jpg';
                    break;
            }
            $pathname = "upload/" . $this->uniqidStr().'.'.$type;
            $path = env('ROOTDIR','').'public/'.$pathname;
            if($val->type != 'video'){
                $file = $material->get($val->media_id);
                file_put_contents($path, $file);
            }else{
                $url = $material->get($val->media_id)['down_url'];
                $this->downfile($url,$path);
            }
            DB::table('material')
                ->where('media_id', $val->media_id)
                ->update(['path' => $pathname]);
        }
        echo '更新本地素材文件结束<br>';
    }

    // public function updateMenu()
    // {
    //     DB::transaction(function () {
    //         try{
    //             $menus = $this->menu->all();
    //         }catch(\Exception $e)
    //         {
    //             return;
    //         }
    //         foreach($menus as &$v){
    //             $v['button'] = json_encode($v['button']);
    //             $v['matchrule'] = json_encode($v['matchrule']);
    //         }
    //         DB::table('menu')->delete();
    //         DB::table('menu')->insert($menus);
    //     });
    // }

    //批量更新
    public function updateBatch($tableName,$multipleData = [],$type = '')
    {
        try {
            if (empty($multipleData)) {
                throw new \Exception("数据不能为空");
            }
            //$tableName = DB::getTablePrefix() . $this->getTable(); // 表名
            $firstRow  = current($multipleData);

            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
            unset($updateColumn[0]);
            // 拼接sql语句
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets      = [];
            $bindings  = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = "`" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= "ELSE `" . $uColumn . "` END ";
                $sets[] = $setSql;
            }
            $updateSql .= implode(', ', $sets);
            $whereIn   = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings  = array_merge($bindings, $whereIn);
            $whereIn   = rtrim(str_repeat('?,', count($whereIn)), ',');
            if($type){
                $type = " AND type = $type";
            }
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")" . $type;
            // 传入预处理sql语句和对应绑定数据
            // echo '更新';
            // echo $updateSql;
            return DB::update($updateSql, $bindings);
        } catch (\Exception $e) {
            return $e;
        }
    }

    //文件重命名
    public function uniqidStr(){
        return md5(uniqid(md5(microtime(true)),true));
    }

    function downfile($video,$path)
    {
        ob_clean();
        ob_start();
        readfile($video);		//读取图片
        $video = ob_get_contents();	//得到缓冲区中保存的图片
        ob_end_clean();		//清空缓冲区
        $fp = fopen($path,'w');	//写入图片
        if(fwrite($fp,$video))
        {
            fclose($fp);
            echo "图片保存成功";
        }
        return true;
    }

    function initGiftCode($name){
        Rediss::del($name);
        $file = __DIR__."/../../".$name.".txt";
        $intptr = fopen($file, "r");

        if ($intptr) {
            while (!feof($intptr)) {
                $value = fgets($intptr);
                Rediss::rpush($name, $value);
            }
            fclose($intptr);
            $length = Rediss::Llen($name);
            Log::error($length);
        } else {
            echo " $file is error \n";
        }
    }
}
