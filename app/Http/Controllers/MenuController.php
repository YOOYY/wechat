<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\Menu;

class MenuController extends Controller
{
    public function __construct(Menu $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    //查询菜单
    public function lists(Request $request)
    {
        try {

            $info = $this->service->getList();
            $event = $this->service->getEvent();
            $eventList = $this->service->getEventList();
            $message = $this->service->message();
            $limit = 10;
            $page = $this->service->page($limit, 'event');
            $current = $request->input('start', 1);

            return view('menu', ['data' => $info, 'event' => $event, 'eventList' => $eventList, 'message' => $message, 'page' => $page, 'current' => $current]);
        }
        //捕获异常
        catch (\Exception $e) {
            $res = $this->service->error($e, '获取永久素材失败');
            echo '数据库错误' . $res['errmsg'];
        }
    }

    //添加菜单
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

    public function create(Request $request)
    {
        try {
            // $buttons = [
            //     [
            //         "type" => "view",
            //         "name" => "游戏下载",
            //         "url"  => "http://m.52y.com?v=1.0"
            //     ],
            //     [
            //         "name"=> "活动福利",
            //         "sub_button" => [
            //             [
            //                 "type" => "view",
            //                 "name" => "每日签到",
            //                 "url"  => "http://test.com/oauth/oauth"
            //             ],
            //             [
            //                 "type" => "click",
            //                 "name" => "分享有礼",
            //                 "key"  => "share"
            //             ],
            //             [
            //                 "type" => "click",
            //                 "name" => "新手大礼包",
            //                 "key" => "newplayergift"
            //             ],
            //         ]
            //     ],
            //     [
            //         "name"=> "游戏客服",
            //         "sub_button" => [
            //             [
            //                 "type" => "click",
            //                 "name" => "QQ客服",
            //                 "key"  => "QQ"
            //             ],
            //             [
            //                 "type" => "click",
            //                 "name" => "电话客服",
            //                 "key" => "phone"
            //             ]
            //         ]
            //     ]
            // ];
            $buttons = $request->input('buttons');
            $matchRule = $request->input('matchRule', []);
            $res = $this->service->create($buttons, $matchRule);
        }
        //捕获异常
        catch (\Exception $e) {
            $res = $this->service->error($e, '创建菜单失败');
        }
        return response()->json($res);
    }

    //删除菜单
    public function delete(Request $request)
    {
        try {
            $id = $request->input('id');
            return $this->service->delete($id);
        } catch (\Exception $e) {
            $res = $this->service->error($e, '删除菜单失败');
        }
        return response()->json($res);
    }

    //测试个性化菜单
    public function test(Request $request)
    {

        try {
            $userid = $request->input('userid');
            return $this->service->test($userid);
        }
        //捕获异常
        catch (\Exception $e) {
            $res = $this->service->error($e, '测试菜单失败');
        }
        return response()->json($res);
    }
}
