<?php

namespace App\Jobs;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Article;

use App\Jobs\Base;
use DB;
use Log;
class Material extends Base{

    public function __construct()
    {
        $app = app('wechat');
        $this->tag = $app->user_tag;
        // 永久素材
        $this->material = $app->material;
        // 临时素材
        $this->temporary = $app->material_temporary;
    }

    //获取永久素材列表
    public function getmaterial($type = '',$start = '',$limit = '')
    {
        if(!empty($type) && isset($start)){
            return DB::table('material')->where('type',$type)->skip($start)->take($limit)->get();
        }else if(empty($type) && isset($start)){
            return DB::table('material')->skip($start)->take($limit)->get();
        }else if(!empty($type) && !isset($start)){
            return DB::table('material')->where('type',$type)->get();
        }else{
            return DB::table('material')->get();
        }
    }

    //获取临时素材列表    
    public function gettemporary($type = '',$start = '',$limit = '')
    {
        if(!empty($type) && isset($start)){
            return DB::table('temporary')->where('type',$type)->skip($start)->take($limit)->get();
        }else if(empty($type) && isset($start)){
            return DB::table('temporary')->skip($start)->take($limit)->get();
        }else if(!empty($type) && !isset($start)){
            return DB::table('temporary')->where('type',$type)->get();
        }else{
            return DB::table('temporary')->get();
        }
    }

    //获取永久素材接口
    public function materialapi($type = '')
    {
        if(!empty($type)){
            return DB::table('material')->where('type',$type)->where('blocked',0)->get();
        }else{
            return DB::table('material')->where('blocked',0)->get();
        }
    }

    //获取临时素材接口
    public function temporaryapi($type = '')
    {
        if(!empty($type)){
            return DB::table('temporary')->where('type',$type)->get();
        }else{
            $time = time();
            return DB::table('temporary')->whereNotBetween('created_at',[$time,$time - 259200])->get();
        }
    }

    //获取消息列表
    public function message($type){
        return DB::table('message')->whereIn('type',$type)->lists('id', 'name');
    }

    //获取单一素材
    public function getopt($id,$timetype)
    {
        $res = DB::table($timetype)->where('id',$id)->first();
        $res = $this->object_to_array($res);
        $res['timetype'] = $timetype;
        return $res;
    }

    //更新素材
    //$timetype,$type,$id,$name,$mediaId,$article_id,$index = 1
    public function update($opt){
        DB::beginTransaction();
        if($opt['type'] == 'article'){
            $article = $this->getarticle($opt,'article');
            try{
                $res = $this->material->updateArticle($opt['media_id'],$article,0);
                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
                return $this->error($e,'更新素材');
            }
        }else{
            DB::table($opt['timetype'])->where('id',$opt['id'])->update(array('name'=>$opt['name'],'note'=>$opt['note']));
            $res['errcode'] = 0;
        }
        return $res;
    }

    //更新素材
    public function updateItem($opt){
        DB::beginTransaction();
        $data = DB::table('material')->where('id',$opt['id'])->first();
        $articleStr = $data->content;
        $blocked = $data->blocked;
        $article = array(
            'title'=>$opt['title'],
            'author'=>$opt['author'],
            'digest'=>$opt['digest'],
            'source_url'=>$opt['content_source_url'],
            'cover_pic'=>$opt['show_cover_pic'],
            'only_fans_can_comment'=>$opt['only_fans_can_comment'],
            'need_open_comment'=>$opt['need_open_comment'],
            'thumb_media_id'=>$opt['thumb_media_id'],
            'content'=>$opt['content']
        );
        $articleArr = json_decode($articleStr,true);
        $articleContent = array(
            'title'=>$opt['title'],
            'author'=>$opt['author'],
            'digest'=>$opt['digest'],
            'content_source_url'=>$opt['content_source_url'],
            'show_cover_pic'=>$opt['show_cover_pic'],
            'only_fans_can_comment'=>$opt['only_fans_can_comment'],
            'need_open_comment'=>$opt['need_open_comment'],
            'thumb_media_id'=>$opt['thumb_media_id'],
            'content'=>$opt['content'],
            'thumb_url'=>$opt['thumb_url']
        );
        $articleArr[$opt['index']] = $articleContent;
        $content = json_encode($articleArr);

        DB::table('material')->where('id',$opt['id'])->update(array('content'=>$content,'name'=>$opt['name'],'note'=>$opt['note']));
        try{
            if($blocked === 0){
                $res = $this->material->updateArticle($opt['media_id'],$article,$opt['index']);
            }
            DB::commit();
            $res['errcode'] = 0;
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'停用素材');
        }
    }

    public function updateArticle($id,$content){
        try{
            DB::table('material')->where('id',$id)->update(array('content'=>$content));
            $res['errcode'] = 0;
            return $res;
        }catch(\Exception $e){
            return $this->error($e,'更新素材');
        }
    }

    //停用素材
    public function blocked($type,$timetype,$mediaId){
        DB::beginTransaction();
        $result = DB::table('material')->where('media_id', $mediaId)->first();
        $block = isset($result)? $result->blocked:1;
        if($block === 1){
            $res['errcode'] = 0;
            DB::commit();
            return $res;
        }else{
            DB::table('material')->where('media_id', $mediaId)->update(['blocked' => 1]);
            DB::table('message')->where('media_id',$mediaId)->update(['state'=>1]);
        }
        try{
            if($timetype == 'temporary'){
                $res['errcode'] = 0;
            }else{
                if($type == 'articleimage'){
                    $res['errcode'] = 0;
                }else{
                    $res = $this->material->delete($mediaId);
                }
            }

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'停用素材');
        }
    }
  
    //删除素材
    //$path = 'upload/game3.jpg';
    public function delete($id,$type,$timetype,$path = '',$mediaId = ''){
        $res = $this->blocked($type,$timetype,$mediaId);
        if($res['errcode']==0){
            if($type != 'article' && $path != '' && $type != 'marticle'){
                unlink(public_path($path));
            }
            DB::table($timetype)->where('id', $id)->delete();
        }
        return $res;
    }

    public function updateMessage($omedia_id,$nmedia_id){
        DB::table('message')->where('media_id', $omedia_id)->update(array('media_id'=>$nmedia_id));
        $res = array('errcode'=>0);
        return $res;
    }

    //上传微信端
    //$type,$timetype,$path='',$title='默认标题',$description='默认描述',$name='',$newsid='',$id
    public function uploadwechat($opt){
        DB::beginTransaction();
        if($opt['timetype'] !='temporary'){
            $helper = $this->material;
        }else{
            $helper = $this->temporary;
        }
        
        switch ($opt['type']) {
            case 'image':
            $result = $helper->uploadImage($opt['path']);
            break;
            case 'voice':
            $result = $helper->uploadVoice($opt['path']);
            break;
            case 'video':
            $result = $helper->uploadVideo($opt['path'],$opt['title'],$opt['description']);
            break;
            case 'thumb':
            $result = $helper->uploadThumb($opt['path']);
            break;
            case 'articleimage':
            $result = $helper->uploadArticleImage($opt['path']);
            break;
            case 'article':
                $article = $this->getarticle($opt,'article');
                $result = $this->material->uploadArticle($article);
                break;
            case 'marticle':
                $article = $this->getarticle($opt,'marticle');
                $result = $this->material->uploadArticle($article);
                break;
            default:
            break;
        }
        foreach($result as $index => $val){
            $arr[$index] = $val;
        }
        if($opt['timetype'] == 'material'){
            $arr['blocked'] = 0;
            $arr['update_time'] = time();
        }
        unset($arr['item']);
        DB::table($opt['timetype'])->where('id', $opt['id'])->update($arr);

        //$res['errcode']=0;
        if(!isset($arr['errcode']) || $arr['errcode']==0){
            DB::commit();
            $arr['errcode']=0;
            return $arr;
        }else{
            DB::rollBack();
            return $this->error($arr);
        }
    }

    //上传服务器
    public function upload($opt){
        DB::beginTransaction();

        if($opt['type'] == 'article' || $opt['type'] == 'marticle'){
            $info = $opt;
            $info['id'] = DB::table('material')->insertGetId($opt);
            $opt['timetype'] = 'material';
        }else{
            Log::info("文件上传开始.");
            switch ($opt['type']) {
                case 'image':
                    if ((($_FILES["file"]["type"] != "image/gif") && ($_FILES["file"]["type"] != "image/jpeg") && ($_FILES["file"]["type"] != "image/png") && ($_FILES["file"]["type"] != "image/bmp")) && ($_FILES["file"]["type"] != "image/jpg") || ($_FILES["file"]["size"]/1024/1024 >= 5)){
                        Log::error("图片上传不符合规则".$_FILES["file"]["type"].$_FILES["file"]["size"]);
                        return false;
                    }
                    break;
                case 'voice':
                    if ((($_FILES["file"]["type"] != "audio/mp3") && ($_FILES["file"]["type"] != "audio/x-ms-wma") && ($_FILES["file"]["type"] != "audio/wav") && ($_FILES["file"]["type"] != "application/octet-stream")) || ($_FILES["file"]["size"]/1024/1024 >= 2)){
                        Log::error("音频上传不符合规则".$_FILES["file"]["type"].$_FILES["file"]["size"]);
                        return false;
                    }
                    break;
                case 'video':
                    if (($_FILES["file"]["type"] != "video/mp4") || ($_FILES["file"]["size"]/1024/1024 >= 10)){
                        Log::error("视频上传不符合规则".$_FILES["file"]["type"].$_FILES["file"]["size"]);
                        return false;
                    }
                    break;
                case 'thumb':
                    if (($_FILES["file"]["type"] != "image/jpg") && ($_FILES["file"]["type"] != "image/jpeg") || ($_FILES["file"]["size"]/1024 >= 64)){
                        Log::error("缩略图上传不符合规则".$_FILES["file"]["type"].$_FILES["file"]["size"]);
                        return false;
                    }
                    break;
                default:
                    break;
            }

            if ($_FILES["file"]["error"] > 0){
                Log::error("头像上传错误,错误码:" . $_FILES["file"]["error"]);
                return false;
            }else{
                //重命名文件防止重合
                $type = substr(strchr($_FILES["file"]["type"],"/"),1);
                switch ($type) {
                    case 'x-ms-wma':
                        $type = 'wma';
                        break;
                    case 'octet-stream':
                        $type = 'amr';
                        break;
                    case 'jpeg':
                        $type = 'jpg';
                        break;
                    default:
                        break;
                }
                $path = "upload/" . $this->uniqidStr().'.'.$type;
    
                $data = "上传文件名:".$_FILES["file"]["name"]."类型:".$_FILES["file"]["type"]."文件大小:".($_FILES["file"]["size"] / 1024)." Kb".'缓存文件名:'.$_FILES["file"]["tmp_name"].'储存文件名:'.$path;
                Log::info($data);
                //储存并返回储存名
                if (file_exists($path)){
                    Log::error($path . "已经存在");
                }else{
                    move_uploaded_file($_FILES["file"]["tmp_name"],$path);
                    if($opt['type'] == 'video'){
                        $info = array('name'=>$opt['name'],'type'=>$opt['type'],'path'=>$path,'title'=>$opt['title'],'description'=>$opt['description']);
                    }else{
                        $info = array('name'=>$opt['name'],'type'=>$opt['type'],'path'=>$path);
                    }

                    if($opt['timetype'] == 'temporary'){
                        $id = DB::table('temporary')->insertGetId($info);
                    }else{
                        $id = DB::table('material')->insertGetId($info);
                    }
                    $info['path'] = public_path($path);
                    $info['id'] = $id;
                }
            }
        }
        DB::commit();        
        $info['timetype'] = $opt['timetype'];
        return $info;
    }


    //文件重命名
    public function uniqidStr(){
        return md5(uniqid(md5(microtime(true)),true));
    }

    function getarticle($opt,$type){
        if($type == 'article'){
            $message = array(
                "title"=>$opt['title'],
                "thumb_media_id"=>$opt['thumb_media_id'],
                "author"=>$opt['author'],
                "digest"=>$opt['digest'],
                "show_cover"=>$opt['show_cover_pic'],
                "content"=>$opt['content'],
                "source_url"=>$opt['content_source_url'],
                "need_open_comment"=>$opt['need_open_comment'],
                "only_fans_can_comment"=>$opt['only_fans_can_comment']
            );
            $article = new Article($message);
        }else{
            $arr = array();
            $message = json_decode($opt['content'],true);
            foreach($message as $val){
                $val['show_cover'] = $val['show_cover_pic'];
                $val['source_url'] = $val['content_source_url'];
                $item = new Article($val);
                array_push($arr,$item);
            }
            $article = $arr;
        }
        return $article;
    }

    //页码函数
    public function page($limit = 10,$table,$type = 'image'){
        $num = DB::table($table)
            ->where('type',$type)
            ->count();
        return ceil($num/$limit);
    }
}
