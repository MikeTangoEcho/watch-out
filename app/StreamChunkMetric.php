<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StreamChunkMetric extends Model
{
    public function scopeIgnoreInitSegment($query)
    {
        return $query->where('chunk_id', '>', 0);
    }

    public function scopeCreatedSince($query, $interval)
    {
        return $query->where('updated_at', '>=', Carbon::now()->subMinutes($interval));
    }

}
