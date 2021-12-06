<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Schedule;
use Illuminate\Support\Facades\Log;
class InitGiftCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initGiftCode {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'initGiftCode';
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
            $name = $this->argument('name');
            $this->service->initGiftCode($name);
        }
        catch(\Exception $e)
        {
            Log::error('初始化礼包码失败'.$e);
            $this->service->error($e,'初始化礼包码失败');
        }
    }
}
