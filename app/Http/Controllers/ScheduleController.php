<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Jobs\Schedule;

class ScheduleController extends Controller
{
    public function __construct(Schedule $service)
    {
        $this->service = $service;
    }

    public function update(){
        try{
            $this->service->updateTag();
            $this->service->updateUser();
            $this->service->updateMaterial();
            $this->service->updateFile();
            $this->service->updateStaff();
        }
        catch(\Exception $e)
        {
            $res = $this->service->error($e,'同步微信数据出错');
            echo '同步出错'.$res['errmsg'];
        }
    }
}
