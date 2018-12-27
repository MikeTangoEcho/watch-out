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

    public function user() 
    {
        return $this->BelongsTo('App\User');
    }

    public function scopeStreamingSince($query, $interval)
    {
        return $query->whereHas('chunks', function ($subquery) use ($interval) {
            $subquery->where('created_at', '>=', Carbon::now()->subMinutes($interval));
        });
    }

}
