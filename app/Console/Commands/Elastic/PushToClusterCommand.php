<?php

namespace App\Console\Commands\Elastic;

use App\Country;
use App\Handlers\IndexHandler;
use App\Jobs\PushToSearchClusterJob;
use Illuminate\Console\Command;

class PushToClusterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:push-to-cluster';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push all data to search cluster';

    /**
     * @var \App\Handlers\IndexHandler
     */
    protected $indexHandler;

    /**
     * Create a new command instance.
     *
     * @param  \App\Handlers\IndexHandler  $indexHandler
     * @return void
     */
    public function __construct(IndexHandler $indexHandler)
    {
        parent::__construct();

        $this->indexHandler = $indexHandler;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Country::with('cities', 'languages')
            ->orderBy('Code')
            ->chunk(100, function ($countries) {
                foreach ($countries as $country) {
                    dispatch(new PushToSearchClusterJob($country->Code, $country->toArray()));
                }
            });
    }
}
