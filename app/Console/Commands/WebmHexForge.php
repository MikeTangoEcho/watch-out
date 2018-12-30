<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use App\Lib\Webm;

class WebmHexForge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webm:hex-forge {file} {offset} {length} {hex}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forge a string in hex format in the file';

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
        $tmp = fopen('php://temp', 'wb');
        stream_copy_to_stream($stream, $tmp, $this->argument('offset'));
        fwrite($tmp, hex2bin($this->argument('hex')));
        stream_copy_to_stream($stream, $tmp, -1, $this->argument('offset') + $this->argument('length'));
        fclose($stream);
        Storage::put($this->argument('file') . '-forged', $tmp);
        fclose($tmp);        
    }
}
