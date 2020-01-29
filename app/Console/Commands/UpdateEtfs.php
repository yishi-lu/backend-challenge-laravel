<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\Business\EtfsService;

class UpdateEtfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:etfs';
    private $etfsUpdater;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update all etf symbols';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EtfsService $etfsUpdater)
    {
        parent::__construct();
        $this->etfsUpdater = $etfsUpdater;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $result = $this->etfsUpdater->updateEtfs(env("ETF_TARGET_URL"));
    }
}
