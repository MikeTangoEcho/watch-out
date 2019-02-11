<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use App\Support\Facades\Webm;

class WebmInitSegment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webm:init-segment {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract init segment from webm byte stream';

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

        rewind($stream);
        $this->info('Write Header Webm');
        $header = fopen('php://temp', 'wb');
        stream_copy_to_stream($stream, $header, $ebml['Segment']['elements']['Cluster'][0]['offset']);
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
