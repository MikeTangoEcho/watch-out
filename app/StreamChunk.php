<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StreamChunk extends Model
{
    public function stream()
    {
        return $this->belongsTo('App\Stream');
    }

    public function metric()
    {
        // Laravel doesnt easily handle compound keys
        return $this->hasOne('App\StreamChunkMetric', 'stream_id', 'stream_id')
            ->where('chunk_id', '=', $this->chunk_id)
            ->withDefault([
                'stream_id' => $this->stream_id,
                'chunk_id' => $this->chunk_id
            ]);
    }

    public static function getFilename($streamId, $chunkId, $clusterOffset) {
        // TODO Autogenerate from attribute
        return "streams/" . $streamId
            . "/" .  str_pad($chunkId, 8, "0", STR_PAD_LEFT) 
            . ($chunkId == 0 ? "-init" : 
                ( is_null($clusterOffset) ? "-chunk" : "-cluster-" . intval($clusterOffset) )
                )
            . ".webm";
    }

    public function deleteFile()
    {
        if (Storage::exists($this->filename)) {
            Storage::delete($this->filename);
        }
    }
}
