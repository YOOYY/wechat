<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Oauth;
use Log;
use DB;
class OauthController extends Controller
{
    public function __construct(Oauth $service)
    {
        $this->service = $service;
    }

    //网页授权
    public function oauth(Request $request)
    {
        try{
            return $this->service->oauth();
        }catch(\Exception $e){
            $res = $this->service->error($e,'授权回调出错');
        }
    }

    // {
    //     "id": "o9FqP1rACTswuwnsxbiD5JUnWNo0",
    //     "name": "\u58a8\u7af9\u6447\u66f3",
    //     "nickname": "\u58a8\u7af9\u6447\u66f3",
    //     "avatar": "http:\/\/thirdwx.qlogo.cn\/mmopen\/vi_32\/MdGkWqia0EzQiakFSicib7q5zxy8yI2VN4dgumZlaYJktrol3psianrzrFkE7iblfpg3sSRXh5wpj8Olt6UDY2Bx0UaQ\/132",
    //     "email": null,
    //     "original": {
    //         "openid": "o9FqP1rACTswuwnsxbiD5JUnWNo0",
    //         "nickname": "\u58a8\u7af9\u6447\u66f3",
    //         "sex": 1,
    //         "language": "zh_CN",
    //         "city": "",
    //         "province": "",
    //         "country": "",
    //         "headimgurl": "http:\/\/thirdwx.qlogo.cn\/mmopen\/vi_32\/MdGkWqia0EzQiakFSicib7q5zxy8yI2VN4dgumZlaYJktrol3psianrzrFkE7iblfpg3sSRXh5wpj8Olt6UDY2Bx0UaQ\/132",
    //         "privilege": [],
    //         "unionid": "o3vzSwgr-keXf_4tthFGtQoDa9l4"
    //     },
    //     "token": {
    //         "access_token": "31_hltGXEQsnixJeApnA6k3mQdFbKTnZs-sSbnYTVmLGer-ALaHb-peTvLenJmxPQh3H9f4ZbxIRRpfyet1hUVFbov43gZ4ljiYnerdmce5Iyc",
    //         "expires_in": 7200,
    //         "refresh_token": "31_9IXURvHDK8vYCqidIbhsco0cIlDHWnaHsBMqNvVB4lOX0xWdyjksdkEp4xmPC9XPF2x6DQH3vTT1MRMwvsrTzz_L9tWyQx3qUQHC6HLjlaA",
    //         "openid": "o9FqP1rACTswuwnsxbiD5JUnWNo0",
    //         "scope": "snsapi_userinfo",
    //         "unionid": "o3vzSwgr-keXf_4tthFGtQoDa9l4"
    //     },
    //     "provider": "WeChat"
    // }

    //授权回调页
    public function callback(Request $request)
    {
        try{
            $unionid = $this->service->user()['original']['unionid'];
            $res = DB::table('sign')->where('unionid',$unionid)->first();
            if(isset($res)){
                $res = json_decode(json_encode($res),true);
                Log::error('已注册');
                Log::error($res);
                $request->session()->put('player', $res);
            }else{
                Log::error('未注册');
                $res = $this->service->bind($unionid);
                if($res['error'] === "0"){
                    $playerid = $res['playerid'];
                }else{
                    $playerid = '';
                }
                $request->session()->put('player', array('playerid'=>$playerid,'unionid'=>$unionid));
            }
            header('location:/sign');
        }catch(\Exception $e){
            $res = $this->service->error($e,'授权回调出错');
            echo '亲爱的玩家，获取不到必要的信息哦，请重新在牌缘公众号-每日签到中点击进入';
        }
    }

    //转短链
    public function shorturl(Request $request)
    {
        try{
            $url = $request->input('url');
            $res = $this->service->shortUrl($url);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'转短链出错');
        }
        return response()->json($res);
    }
}
