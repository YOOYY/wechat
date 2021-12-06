<?php

namespace App\Http\Controllers;

use EasyWeChat\Message\Text;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Material;
use EasyWeChat\Foundation\Application;

use Illuminate\Http\Request;
use Log;

class ApiController extends Controller
{
    public function __construct()
    {
        $app = app('wechat');
        $this->userService = $app->user;
        $this->tag = $app->user_tag;
        $this->group = $app->user_group;
        $this->menu = $app->menu;
        $this->material = $app->material;
        $this->temporary = $app->material_temporary;
        $this->luckyMoney = $app->lucky_money;
        $this->reply = $app->reply;
        $this->staff = $app->staff;
        $this->session = $app->staff_session;

    }

    //获取用户openID列表
    public function userlists(Request $request){
        $next_openid = $request->input('next_openid');

        $res = $this->userService->lists($next_openid = null);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //获取单用户信息
    public function userinfo(Request $request){
        $openid = $request->input('openid');

        $res = $this->userService->get($openid);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //获取多用户信息
    public function usersinfo(Request $request){
        $openids = $request->input('openids');
        //$openids = ['orsSv0aiekNxemy26YtmSsxkgTy4'];
        $res = $this->userService->batchGet($openids);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //获取用户信息列表
    public function userinfolists(Request $request){
        $next_openid = $request->input('next_openid');

        $openids = $this->userService->lists($next_openid = null)->data['openid'];
        $res = $this->userService->batchGet($openids);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //修改备注
    public function userremark(Request $request){
        $openId = $request->input('openId');
        $remark = $request->input('remark');

        $res = $this->userService->remark($openId, $remark);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //获取用户所属用户组ID
    public function usergroup(Request $request){
        $openId = $request->input('openId');

        $res = $this->userService->group($openId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

//----------------- 标签 ---------------

    //用户标签
    public function taglists(){
        $res = $this->tag->lists()->toArray();

        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //创建标签
    public function tagcreate(Request $request){
        $name = $request->input('name');

        $res = $this->tag->create($name);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //修改标签
    public function tagupdate(Request $request){
        $tagId = $request->input('tagId');
        $name = $request->input('name');

        $res = $this->tag->update($tagId, $name);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //删除标签
    public function tagdelete(Request $request){
        $tagId = $request->input('tagId');

        $res = $this->tag->delete($tagId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }
    
    //获取指定 openid 用户身上的标签
    public function usertags(Request $request){
        $openId = $request->input('openId');

        $res = $this->tag->userTags($openId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }
    
    //批量为用户打标签
    public function batchtagusers(Request $request){
        $tagId = $request->input('tagId');
        $openIds = $request->input('openIds');

        $res = $this->tag->batchTagUsers($tagId, $openIds);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //批量为用户取消标签
    public function batchuntagusers(Request $request){
        $tagId = $request->input('tagId');
        $openIds = $request->input('openIds');

        $res = $this->tag->batchUntagUsers($tagId, $openIds);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //----------------- 分组 ---------------

    //用户分组
    public function grouplists(){
        $res = $this->group->lists();
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //创建分组
    public function groupcreate(Request $request){
        $name = $request->input('name');

        $res = $this->group->create($name);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //修改分组
    public function groupupdate(Request $request){
        $groupId = $request->input('groupId');
        $name = $request->input('name');

        $res = $this->group->update($groupId, $name);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //删除分组
    public function groupdelete(Request $request){
        $groupId = $request->input('groupId');

        $res = $this->group->delete($groupId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }
    
    //移动单个用户到指定分组
    public function groupmoveuser(Request $request){
        $openId = $request->input('openId');
        $groupId = $request->input('groupId');
        $res = $this->group->moveUser($openId, $groupId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }
    //批量移动用户到指定分组
    public function groupmoveusers(Request $request){
        $openIds = $request->input('openIds');
        $groupId = $request->input('groupId');
        //$openIds = [$openId1, $openId2, $openId3 ...];
        $res = $this->group->moveUsers($openIds, $groupId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //--------------菜单--------------
    //查询菜单
    public function menulists()
    {
        $res = $this->menu->all();
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //查询自定义菜单
    public function mymenulists()
    {
        $res = $this->menu->current();
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //添加菜单
    public function menuadd(Request $request)
    {
        $buttons = $request->input('buttons');

        // $buttons = [
        //     [
        //         "type" => "click",
        //         "name" => "今日歌曲",
        //         "key"  => "V1001_TODAY_MUSIC"
        //     ],
        //     [
        //         "name"       => "菜单",
        //         "sub_button" => [
        //             [
        //                 "type" => "view",
        //                 "name" => "搜索",
        //                 "url"  => "http://www.soso.com/"
        //             ],
        //             [
        //                 "type" => "view",
        //                 "name" => "视频",
        //                 "url"  => "http://v.qq.com/"
        //             ],
        //             [
        //                 "type" => "click",
        //                 "name" => "赞一下我们",
        //                 "key" => "V1001_GOOD"
        //             ],
        //         ],
        //     ],
        // ];

        $res = $this->menu->add($buttons);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //添加个性化菜单
    public function addmy(Request $request)
    {
        $buttons = $request->input('buttons');
        $matchRule = $request->input('matchRule');

        // $buttons = [
        //     [
        //         "type" => "click",
        //         "name" => "今日歌曲",
        //         "key"  => "V1001_TODAY_MUSIC"
        //     ],
        //     [
        //         "name"       => "菜单",
        //         "sub_button" => [
        //             [
        //                 "type" => "view",
        //                 "name" => "搜索",
        //                 "url"  => "http://www.soso.com/"
        //             ],
        //             [
        //                 "type" => "view",
        //                 "name" => "视频",
        //                 "url"  => "http://v.qq.com/"
        //             ],
        //             [
        //                 "type" => "click",
        //                 "name" => "赞一下我们",
        //                 "key" => "V1001_GOOD"
        //             ],
        //         ],
        //     ],
        // ];
        // $matchRule = [
        //     "tag_id" => "2",
        //     "sex" => "1",
        //     "country" => "中国",
        //     "province" => "广东",
        //     "city" => "广州",
        //     "client_platform_type" => "2",
        //     "language" => "zh_CN"
        // ];
        $res = $this->menu->add($buttons, $matchRule);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //删除菜单
    public function menudelete(Request $request)
    {
        $menuId = $request->input('menuId');

        $res = $this->menu->destroy($menuId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //删除菜单
    public function menudeleteall()
    {
        $res = $this->menu->destroy(); // 全部
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //测试个性化菜单
    public function menutest(Request $request)
    {
        $userId = $request->input('userId');

        $res = $this->menu->test($userId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //--------------------素材管理--------------

    // - title 标题
    // - author 作者
    // - content 具体内容
    // - thumb_media_id 图文消息的封面图片素材id（必须是永久mediaID）
    // - digest 图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
    // - source_url 来源 URL
    // - show_cover 是否显示封面，0 为 false，即不显示，1 为 true，即显示
    //上传永久图文消息
    public function uploadarticle(Request $request)
    {
        $title = $request->input('title');
        $author = $request->input('author');
        $content = $request->input('content');
        $digest = $request->input('digest');
        $source_url = $request->input('source_url');
        $show_cover = $request->input('show_cover');
        $thumb_media_id = $request->input('thumb_media_id');
        $article = new Article([
            'title' => $title,
            'author' => $author,
            'content' => $content,
            'digest' => $digest,
            'source_url' => $source_url,
            'show_cover' => $show_cover,
            'thumb_media_id' => $mediaId,
            //...
        ]);
        $res = $material->uploadArticle($article);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
        // 或者多篇图文
        //$this->material->uploadArticle([$article, $article2]);
    }

    //修改永久图文消息
    public function updatearticle(Request $request)
    {
        $title = $request->input('title');
        $author = $request->input('author');
        $content = $request->input('content');
        $digest = $request->input('digest');
        $source_url = $request->input('source_url');
        $show_cover = $request->input('show_cover');
        $thumb_media_id = $request->input('thumb_media_id');
        $index = $request->input('index');
        $article = new Article([
            'title' => $title,
            'author' => $author,
            'content' => $content,
            'digest' => $digest,
            'source_url' => $source_url,
            'show_cover' => $show_cover,
            'thumb_media_id' => $mediaId,
            //...
        ]);
        $res = $this->material->updateArticle($mediaId, $article,$index);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //获取永久素材
    public function materialget(Request $request)
    {
        $mediaId = $request->input('mediaId');

        $res = $this->material->get($mediaId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //获取永久素材列表
    public function materiallists(Request $request)
    {
        $type = $request->input('type');
        $offset = $request->input('offset');
        $count = $request->input('count');

        $res = $this->material->lists($type, $offset, $count);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //获取素材计数
    public function materialstats()
    {
        $res = $this->material->stats();
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //删除永久素材
    public function materialdelete(Request $request)
    {
        $mediaId = $request->input('mediaId');

        $res = $this->material->delete($mediaId);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //获取临时素材内容
    public function gettemporary(Request $request)
    {
        $mediaId = $request->input('mediaId');
        $path = $request->input('path');

        $content = $this->temporary->getStream($mediaId);
        $res = file_put_contents($path, $content);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //下载临时素材到本地
    public function download(Request $request)
    {
        $mediaId = $request->input('mediaId');
        $path = $request->input('path');
        $name = $request->input('name');
        $res = $this->temporary->download($mediaId, $path, $name);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //红包查询
    public function luckyquery(Request $request){
        $mchBillNo = $request->input('mchBillNo');
        $res = $this->luckyMoney->query($mchBillNo);
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //查询回复规则
    public function reply(){
        $res = $this->reply->current();
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }

    //获取所有客服账号列表
    public function stafflists(){
        $res = $this->staff->lists();
        var_dump($res);
    }
    
    //获取所有在线的客服账号列表
    public function staffonlines(){
        $res = $this->staff->onlines();
        var_dump($res);
    }

    //添加客服帐号
    public function staffcreate(Request $request){
        $name = $request->input('name');
        $nickname = $request->input('nickname');
        $this->staff->create($name,$nickname);
    }
    
    //修改客服帐号
    public function staffupdate(Request $request){
        $name = $request->input('name');
        $nickname = $request->input('nickname');
        $this->staff->update($name,$nickname);
    }
    //删除客服帐号
    public function staffdelete(Request $request){
        $name = $request->input('name');
        $this->staff->delete($name);
    }
    
    //设置客服帐号的头像
    public function staffavatar(Request $request){
        $name = $request->input('name');
        $avatarPath = $request->input('avatarPath');
        $this->staff->avatar($name, $avatarPath);
    }
    //获取客服聊天记录
    public function records(Request $request){
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');
        $pageIndex = $request->input('pageIndex');
        $pageSize = $request->input('pageSize');
        $this->staff->records($startTime, $endTime, $pageIndex, $pageSize);
    }
    
    //主动发送消息给用户
    public function send(Request $request){
        $this->staff->message($message)->to($openId);
        //////////
    }

    //指定客服发送消息
    public function osend(Request $request){
        $res = $this->staff->message($message)->by('account@test')->to($openId)->send();
        return $res;
    }

    //创建会话
    public function sessioncreate(Request $request){
        $openid = $request->input('openid');        
        $res = $this->session->create('test1@test', $openid);
        var_dump($res);
    }
    
    //关闭会话
    public function close(Request $request){
        $this->session->close('test1@test', 'OPENID');
    }

    //获取客户会话状态
    public function get(Request $request){
        $openid = $request->input('openid');
        $res = $this->session->get($openid);
        var_dump($res);        
    }
    
    //获取客服会话列表
    public function sessionlists(Request $request){
        $openid = $request->input('openid');
        $res = $this->session->lists($openid);
        var_dump($res); 
    }

    //获取未接入会话列表
    public function waiters(){
        $this->session->waiters();
    }

    public function phpconf(){
        return view('conf');
    }
}