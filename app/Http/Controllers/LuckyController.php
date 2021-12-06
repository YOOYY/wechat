<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Lucky;

class LuckyController extends Controller
{
    public function __construct(Lucky $service)
    {
        $this->service = $service;
        $this->middleware('auth');
    }
        // $options = [
        //     // payment
        //     'payment' => [
        //         'merchant_id'        => 'your-mch-id',
        //         'key'                => 'key-for-signature',
        //         'cert_path'          => 'path/to/your/cert.pem',
        //         'key_path'           => 'path/to/your/key',
        //         // ...
        //     ],
        // ];
    //列表
    public function lists(Request $request){
        try{
            $current = $request->input('start',1);
            $limit = 10;
            $start = $limit*($current-1);
            $page = $this->service->page($limit,'lucky');
            $info = $this->service->getList($start,$limit);
            return view('lucky',['data' => $info,'page' => $page,'current' => $current]);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'获取红包列表失败');
            echo '数据库错误'.$res['errmsg'];
        }
    }

    public function create(Request $request){
        try{
            $input = $request->all();
            $res = $this->service->create($input);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'创建红包失败');
        }
        return response()->json($res);
    }
    
    //普通红包调用接口
    
    // $luckyMoneyData = [
    //     'mch_billno'       => 'xy123456',
    //     'send_name'        => '测试红包',
    //     're_openid'        => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
    //     'total_num'        => 1,  //固定为1，可不传
    //     'total_amount'     => 100,  //单位为分，不小于100
    //     'wishing'          => '祝福语',
    //     'client_ip'        => '192.168.0.1',  //可不传，不传则由 SDK 取当前客户端 IP
    //     'act_name'         => '测试活动',
    //     'remark'           => '测试备注'
    // ];

    public function normallucky(Request $request){
        try{
            $luckyMoneyData = $request->input('luckyMoneyData');
            $result = $this->service->sendNormal($luckyMoneyData);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'发送普通红包失败');
        }
        return response()->json($res);
    }

    //裂变红包调用接口
    // $luckyMoneyData = [
    //     'mch_billno'       => 'xy123456',
    //     'send_name'        => '测试红包',
    //     're_openid'        => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
    //     'total_num'        => 3,  //不小于3
    //     'total_amount'     => 300,  //单位为分，不小于300
    //     'wishing'          => '祝福语',
    //     'act_name'         => '测试活动',
    //     'remark'           => '测试备注',
    //     'amt_type'         => 'ALL_RAND',  //可不传
    // ];

    public function grouplucky(Request $request){
        try{
            $luckyMoneyData = $request->input('luckyMoneyData');
            $res = $this->service->sendGroup($luckyMoneyData);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'发送裂变红包失败');
        }
        return response()->json($res);
    }

    //红包预下单接口
    // $luckyMoneyData = [
    //     'hb_type'          => 'NORMAL',  //NORMAL 或 GROUP
    //     'mch_billno'       => 'xy123456',
    //     'send_name'        => '测试红包',
    //     're_openid'        => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
    //     'total_num'        => 1,  //普通红包固定为1，裂变红包不小于3
    //     'total_amount'     => 100,  //单位为分，普通红包不小于100，裂变红包不小于300
    //     'wishing'          => '祝福语',
    //     'client_ip'        => '192.168.0.1',  //可不传，不传则由 SDK 取当前客户端 IP
    //     'act_name'         => '测试活动',
    //     'remark'           => '测试备注',
    //     'amt_type'         => 'ALL_RAND',
    //     // ...
    // ];
    public function prepare(Request $request){
        try{
            $luckyMoneyData = $request->input('luckyMoneyData');
            $res = $luckyMoney->prepare($luckyMoneyData);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'红包预下单失败');
        }
        return response()->json($res);
    }

    //红包查询
    public function query(Request $request){
        try{
            $mchBillNo = $request->input('mchBillNo');
            $result = $this->service->query($mchBillNo);
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'查询红包失败');
        }
        return response()->json($res);
    }
}