<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use App\Lib\Webm;

class WebmParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webm:parse {file}';

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
        $webm = new Webm();
        $this->info('Read Webm');
        $ebml = $webm->parse($stream);

        //fclose($stream);return;
        rewind($stream);
        $this->info('Write Header Webm');
        $header = fopen('php://temp', 'wb');
        stream_copy_to_stream($stream, $header, $ebml['Cluster']['offset'] - 1);
        Storage::put('stream-header.webm', $header);
        fclose($header);

        $this->info('Write Cluster Webm');
        $cluster = fopen('php://temp', 'wb');
        stream_copy_to_stream($stream, $cluster);
        Storage::put('stream-cluster.webm', $cluster);
        fclose($cluster);

        $this->info('Close stream');
        fclose($stream);
    }
}
