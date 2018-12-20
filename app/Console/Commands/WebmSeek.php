<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use App\Lib\Webm;

class WebmSeek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webm:seek {file} {hextag}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seek Tag in webm file';

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
        $webm = new Webm();
        $webm->debug = True;
        $this->info('Read Webm');
        $ebml = $webm->seekNextId($stream, $this->argument('hextag'));
        fclose($stream);
        var_dump($ebml);
    }
}
