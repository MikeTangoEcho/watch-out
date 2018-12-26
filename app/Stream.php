<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    public function chunks()
    {
        return $this->hasMany('App\StreamChunk');
    }

}
