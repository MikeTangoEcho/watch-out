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
    protected $description = 'Parse cluster, check timecodes consistency and can repair them';

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
     * @return void
     */
    public function handle()
    {
        $stream = Storage::readStream($this->argument('file'));
        $webm = new Webm(true);
        
        rewind($stream);
        if ($this->option('check')) {
            if ($webm->needRepairCluster($stream)) {
                $this->info('Cluster need to be repaired');
            } else {
                $this->info('Cluster is ok!');
            }
        } else {            
            $repair = $webm->repairCluster($stream);
            Storage::put($this->argument('file') . '-forged', $repair);
            fclose($repair);
        }
        fclose($stream);
    }
}
