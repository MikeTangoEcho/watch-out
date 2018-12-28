<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Lib\Webm;

class WebmClusterRepair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webm:cluster-repair {file} {--check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse cluster and repair timecodes';

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
        $webm->debug = true;

        rewind($stream);
        if ($this->option('check')) {
            $webm->repairChunk($stream, true);
        } else {            
            $repair = $webm->repairChunk($stream);
            Storage::put($this->argument('file') . '-forged', $repair);
            fclose($repair);
        }
        fclose($stream);
    }
}
