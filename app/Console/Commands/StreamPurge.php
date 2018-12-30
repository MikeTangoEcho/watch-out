<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Stream;
use Carbon\Carbon;

class StreamPurge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stream:purge {since} {--dry-run} {--only-files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all the streams or chunk files based on date';

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
        if ($this->option('dry-run')) {
            $this->info('Running in DRY-RUN');
        }
        $date = new Carbon($this->argument('since'));
        $this->info('Query streams before ' . $date);

        $streams = Stream::where('updated_at', '<=', $date)->get();
        foreach($streams as $stream) {
            $this->info('stream [' . $stream->id . '] updated_at:' . $stream->updated_at);
            if ($this->option('only-files')) {
                foreach($stream->chunks()->get() as $chunk) {
                    if (!$this->option('dry-run')) {
                        $chunk->deleteFile();
                    }
                    $this->info('stream [' . $stream->id 
                        . '] chunk [' . $chunk->chunk_id . '] deleted');
                }
            } else {
                if (!$this->option('dry-run')) {
                    $stream->delete();
                }
            }
            $this->info('stream [' . $stream->id . '] deleted');
        }
    }
}
