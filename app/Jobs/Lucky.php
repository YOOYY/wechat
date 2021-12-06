<?php

namespace App\Jobs;

use App\Jobs\Base;
use EasyWeChat\Foundation\Application;
use DB;
class Lucky extends Base{
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

    public function __construct()
    {
        $app = app('wechat');
        $this->luckyMoney = $app->lucky_money;
    }

    public function getList($tagId){
        return DB::table('lucky')->get();
    }

    public function create($input){
        DB::beginTransaction();
        DB::table('lucky')->insert($input);

        try{

            if($input['type'] == 'normal'){
                $res = $this->sendNormal($input);
            }else{
                $res = $this->sendGroup($input);
            }

            DB::commit();
            return $res;
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e,'创建红包');
        }
    }

    public function sendNormal($luckyMoneyData)
    {
        return $this->luckyMoney->sendNormal($luckyMoneyData);
    }

    public function sendGroup($luckyMoneyData)
    {
        return $this->luckyMoney->sendGroup($luckyMoneyData); 
    }

    public function query($mchBillNo)
    {
        return $this->luckyMoney->query($mchBillNo);
    }
}
