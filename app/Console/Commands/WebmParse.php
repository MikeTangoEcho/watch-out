<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use App\Support\Facades\Webm;

class WebmParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webm:parse {file} {--segment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse webm file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $stream = Storage::readStream($this->argument('file'));
        $this->info('Read Webm');
        $ebml = Webm::withDebug()->parse($stream);
        fclose($stream);
        var_dump($ebml);
    }
}
