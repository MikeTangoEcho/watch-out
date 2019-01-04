<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Stream extends Model
{
    public function chunks()
    {
        return $this->hasMany('App\StreamChunk');
    }

    public function chunkMetrics()
    {
        return $this->hasMany('App\StreamChunkMetric');
    }

    public function user() 
    {
        return $this->BelongsTo('App\User');
    }

    public function firstChunk()
    {
        return $this->hasOne('App\StreamChunk')
            ->where('chunk_id', '=', 0);
    }

    public function lastChunk()
    {
        return $this->hasOne('App\StreamChunk')
            ->orderBy('chunk_id', 'desc');
    }

    /**
     * TODO find best ways to present the views
     */
    public function maxViewers()
    {
        return $this->chunkMetrics()
            // Exclude Init segment
            // InitSegment's views = number of times the stream has been joined
            ->where('chunk_id', '>', 0)
            ->max('views');
    }

    public function scopeActive($query) {
        return $query->where('total_size', '>', 0);
    }
    
    public function scopeStreamingSince($query, $interval)
    {
        return $query->whereHas('chunks', function ($subquery) use ($interval) {
            $subquery->where('created_at', '>=', Carbon::now()->subMinutes($interval));
        });
    }

}
