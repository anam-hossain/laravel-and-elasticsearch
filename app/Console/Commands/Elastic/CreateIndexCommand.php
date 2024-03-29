<?php

namespace App\Console\Commands\Elastic;

use App\Handlers\IndexHandler;
use Illuminate\Console\Command;

class CreateIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:create-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create ElasticSearch index';

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
     * @return mixed
     */
    public function handle()
    {
        $this->indexHandler->createIndex();
    }
}
