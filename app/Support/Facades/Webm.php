<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Webm extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'webm';
    }
}