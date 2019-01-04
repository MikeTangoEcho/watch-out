<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function streams()
    {
        return $this->hasMany('App\Stream');
    }

    public function streamChunkMetrics()
    {
        return $this->hasManyThrough('App\StreamChunkMetric', 'App\Stream');
    }

    public function averageViewers()
    {
        // TODO Use it has relation to group queries
        return ceil($this->streamChunkMetrics()
            ->where('chunk_id', '>', 0)
            ->avg('views'));
    }

    /**
     * Return current Usage of service based on a period
     */
    public function quota($interval)
    {
        // TODO Find a way that this is query is not executed everytime
        // Check Nb of stream since last interval
        // Check Size of Stream since last interval
        $streams = $this->streams();
        $streams = $streams->active();
        if ($interval) {
            $streams = $streams
                ->where('created_at', '>=',
                    Carbon::now()->subMinutes($interval));
        }
        $quota = $streams->selectRaw(
            'count(*) as stream_count, '
            . 'sum(total_size) as stream_size')
            ->first();
        return $quota;        
    }

    public function canBroadcast()
    {
        // TODO handle subscriptions ?
        $constraint = config('watchout.constraint');
        $quota = $this->quota($constraint['interval']);
        return ($constraint['stream_count'] >= $quota['stream_count'])
            && ($constraint['stream_size'] >= $quota['stream_size']);
    }

}
