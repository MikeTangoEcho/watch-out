<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
}
