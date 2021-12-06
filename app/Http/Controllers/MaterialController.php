<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Material;
use Log;
class MaterialController extends Controller
{
    public function __construct(Material $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function lists(Request $request)
    {
        try{
            $type = $request->input('type','image');
            $current = $request->input('start',1);
            $tcurrent = $request->input('tstart',1);

            $limit = 10;
            //永久素材页码
            $start = $limit*($current-1);
            $page = $this->service->page($limit,'material',$type);

            //临时素材页码
            $tstart = $limit*($tcurrent-1);
            $tpage = $this->service->page($limit,'temporary',$type);
            $info = $this->service->getmaterial($type,$start,$limit);
            $tlists = $this->service->gettemporary($type,$tstart,$limit);
            $tmptype = ['article','marticle'];
            $message = $this->service->message($tmptype);
            $tmptype = ['article'];
            $amessage = $this->service->message($tmptype);
            $articleThumb = $this->service->materialapi('thumb');
            return view('material',['info' => $info,'page' => $page,'current' => $current,'tlists'=>$tlists,'tpage' => $tpage,'tcurrent' => $tcurrent,'message' => $message,'amessage' => $amessage,'type' => $type,'articleThumb' => $articleThumb]);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取素材列表失败');
            echo '数据库错误'.$res['errmsg'];
        }
    }

    //获取素材接口
    public function getlist(Request $request)
    {
        try{
            $type = $request->input('type','');
            $ever = $this->service->materialapi($type);

            if($type != 'thumb'){
                $temp = $this->service->temporaryapi($type);
            }else{
                $temp = [];
            }

            $data = array('ever'=>$ever,'temp'=>$temp);
            $res = array('errmsg'=>$data,'errcode'=>0);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取素材API失败');
        }
        return response()->json($res);
    }

    // - title 标题
    // - author 作者
    // - content 具体内容
    // - thumb_media_id 图文消息的封面图片素材id（必须是永久mediaID）
    // - digest 图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
    // - source_url 来源 URL
    // - show_cover 是否显示封面，0 为 false，即不显示，1 为 true，即显示
    //修改永久图文消息

    //上传素材
    public function upload(Request $request)
    {
        try{
            $input = $request->all();
            $opt= $this->service->upload($input);
            if($opt['type'] != 'marticle'){
                $data = $this->service->uploadwechat($opt);
            }else{
                $data = $opt;
            }
            $res = array('errmsg'=>$data,'errcode'=>0);
        }
        catch(\Exception $e)
        {
            // $res = $this->service->error($e,'上传素材失败');
            Log::info(json_encode($e));
        }
        return response()->json($res);
    }

    //启用素材
    public function unlock(Request $request)
    {
        try{
            $id = $request->input('id');
            $timetype = $request->input('timetype');
            $opt = $this->service->getopt($id,$timetype);
            $res = $this->service->uploadwechat($opt);
            $res = $this->service->updateMessage($opt['media_id'],$res['media_id']);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'上传素材失败');
        }
        return response()->json($res);
    }

    //停用永久素材
    public function blocked(Request $request)
    {
        try{
            $media_id = $request->input('media_id');
            $res = $this->service->blocked('type','material',$media_id);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'停用素材失败');
        }
        return response()->json($res);
    }

    //删除素材
    public function delete(Request $request){
        try{
            $id = $request->input('id');
            $type = $request->input('type');
            $timetype = $request->input('timetype');
            $path = $request->input('path');
            $media_id = $request->input('media_id');
            $res = $this->service->delete($id,$type,$timetype,$path,$media_id);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'删除素材失败');
        }
        return response()->json($res);
    }

    //更新素材
    public function update(Request $request){
        try{
            //$input['timetype'],$input['type'],$input['id'],$input['name'],$input['media_id'],$input['article_id'],$input['index']
            $input = $request->all();
            if($input['type'] == 'marticle'){
                $res = $this->service->updateItem($input);
            }else{
                $res = $this->service->update($input);
            }
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'修改素材失败');
        }
        return response()->json($res);
    }

    //更新图文素材
    public function updateArticle(Request $request){
        try{
            $id = $request->input('id');
            $content = $request->input('content');
            $res = $this->service->updateArticle($id,$content);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'修改图文素材失败');
        }
        return response()->json($res);
    }
}