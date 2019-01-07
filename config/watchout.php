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

    'push_delay' => env('WATCHOUT_PUSH_DELAY', 3000),

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

    /*
    |--------------------------------------------------------------------------
    | Stream constraints
    |--------------------------------------------------------------------------
    |
    | Define differents requirements in number, size and interval to avoid
    | being overwhelmed by users and make a paywall to handle the growing infra
    |
    */

    'constraint' => [
        'stream_count' => env('WATCHOUT_CONSTRAINT_STREAM_COUNT', 100),
        'stream_size' => env('WATCHOUT_CONSTRAINT_STREAM_SIZE_BYTE', 10 * 1024000), // 10mo
        'interval' => env('WATCHOUT_CONSTRAINT_INTERVAL_MIN', 24 * 60), // 1 day
    ]
];
