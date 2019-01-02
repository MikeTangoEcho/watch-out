<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Push Delay in milliseconds
    |--------------------------------------------------------------------------
    |
    | Define the estimate size of each Chunk
    |
    */

    'push_delay' => env('WATCHOUT_PUSH_DELAY', 2000),

    /*
    |--------------------------------------------------------------------------
    | Default Stream Title
    |--------------------------------------------------------------------------
    |
    | Define title of every stream on creation.
    |
    */

    'stream_title' => env('WATCHOUT_STREAM_TITLE', "Do it Live!"),

    /*
    |--------------------------------------------------------------------------
    | MimeType
    |--------------------------------------------------------------------------
    |
    | Define the mimeType for recorder and streamer, Codecs for video and audio
    | are specified to avoid browser to choose
    |
    */

    'mime_type' => env('WATCHOUT_MIME_TYPE', 'video/webm;codecs="opus,vp8"'),

];
