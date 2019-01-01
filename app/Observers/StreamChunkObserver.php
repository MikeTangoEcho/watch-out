<?php

namespace App\Observers;

use App\StreamChunk;

class StreamChunkObserver
{

    /**
     * Handle the stream chunk "created" event.
     *
     * @param  \App\StreamChunk  $streamChunk
     * @return void
     */
    public function created(StreamChunk $streamChunk)
    {
        // Initialize metrics to avoid upsert check on each chunk access
        $streamChunk->metric->save();
    }

    /**
     * Handle the stream chunk "deleting" event.
     *
     * @param  \App\StreamChunk  $streamChunk
     * @return void
     */
    public function deleting(StreamChunk $streamChunk)
    {
        // Delete file to clean space
        $streamChunk->deleteFile();
    }
}
