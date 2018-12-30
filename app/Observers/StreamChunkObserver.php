<?php

namespace App\Observers;

use App\StreamChunk;

class StreamChunkObserver
{

    /**
     * Handle the stream chunk "deleting" event.
     *
     * @param  \App\StreamChunk  $streamChunk
     * @return void
     */
    public function deleting(StreamChunk $streamChunk)
    {
        $streamChunk->deleteFile();
    }
}
