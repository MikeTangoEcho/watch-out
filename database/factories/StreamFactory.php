<?php

use Faker\Generator as Faker;

$factory->define(App\Stream::class, function (Faker $faker) {
    return [
        'title' => 'Do It Live',
        'mime_type' => 'video/webm',
    ];
});
