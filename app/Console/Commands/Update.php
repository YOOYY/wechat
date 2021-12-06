<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Schedule;
use Illuminate\Support\Facades\Log;
class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update data';
    protected $service;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Schedule $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            $this->service->updateTag();
            $this->service->updateUser();
            $this->service->updateMaterial();
            $this->service->updateFile();
            $this->service->updateStaff();
        }
        catch(\Exception $e)
        {
            Log::error('同步微信数据出错'.$e);
            $this->service->error($e,'同步微信数据出错');
        }
    }
}
