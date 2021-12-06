<?php

namespace App\Jobs;
// use Illuminate\Support\Facades\Redis;
use DB;
use Mail;
use Log;

class Base{

    protected $base;
    protected $mailContent;

    public function __construct()
    {
    }

    public function mail($event){
        $flag = Mail::raw($event.$this->mailContent, function ($message) {
            $to = env('MAIL_ADDRESS','1156864263@qq.com');
            $message ->to($to)->subject(env('TITLE','').'微信后台出错');
        });
        return $flag;
    }

    public function error($e,$event){
        Log::error($event.':'.$e);
        $res['errmsg'] = $e->getMessage();
        $this->mailContent = $e;
        for($i = 0;$i < 3;$i++){
            $flag = $this->mail($event);
            if($flag){break;}
            sleep(10);
        }
        return $res;
        // if(array_key_exists('errcode',$res)){
        //     switch ($res['errcode']) {
        //         case 0:
        //             $msg = $res['errmsg'];
        //             break;
        //         case -1:
        //             $msg = '系统繁忙';
        //             break;
        //         case 45157:
        //             $msg = '标签名非法，请注意不能和其他标签重名';
        //             break;
        //         case 45158:
        //             $msg = '标签名长度超过30个字节';
        //             break;
        //         case 45056:
        //             $msg = '创建的标签数过多，请注意不能超过100个';
        //             break;
        //         case 45058:
        //             $msg = '不能修改0/1/2这三个系统默认保留的标签';
        //             break;
        //         case 45057:
        //             $msg = '该标签下粉丝数超过10w，不允许直接删除';
        //             break;
        //         case 40003:
        //             $msg = '传入非法的openid';
        //             break;
        //         case 45159:
        //             $msg = '非法的标签';
        //             break;
        //         case 40032:
        //             $msg = '每次传入的openid列表个数不能超过50个';
        //             break;
        //         case 45059:
        //             $msg = '有粉丝身上的标签数已经超过限制，即超过20个';
        //             break;
        //         case 49003:
        //             $msg = '传入的openid不属于此AppID';
        //             break;
        //         default:
        //             $msg = '未知错误';
        //             break;
        //     }
        //     $res['errmsg'] = $msg;
        //     return $res;
        // }else{
        //     $arr['errmsg'] = '未知错误';
        //     return $arr;            
        // }

    }

    public function page($limit = 10,$table){
        $num = DB::table($table)->count();
        return ceil($num/$limit);
    }
    
    public function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->object_to_array($v);
            }
        }
        return $obj;
    }

    public function curl($url, $params = false, $ispost = 0, $https = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}
