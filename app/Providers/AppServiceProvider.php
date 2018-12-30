<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\StreamChunk;
use App\Observers\StreamChunkObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        StreamChunk::observe(StreamChunkObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
