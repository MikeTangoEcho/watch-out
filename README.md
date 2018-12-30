# Watch OUT

Watch Out is a cheap (hassle-free single-server low cpu/bandwidth) live streaming platform.

It uses:
* Media Source Extensions implemented (pseudo-correctly) in 2 browsers (Chrome/Firefox)
* Open format webm.
* https://www.w3.org/TR/mse-byte-stream-format-webm/

Why not using WebRTC ?
* I don't want to maintain a STUN/TURN server and bother with ICE

Why not using RTMP + Transcoder? + HLS ?
* I don't want to force streamers to install softwares (beside compatible browsers) or plugins like flash
* I don't want to maintain (and pay) transcoder, or streaming server

Why you need WatchOut ?
* You like low quality stream
* You like low cost servers
* You like low hassle (user friendly button in progress)

# Installation

* Download master archive of clone the git
* Since its a Laravel application https://laravel.com/docs/5.7/installation
```
    composer install
    php artisan key:generate --force
    php artisan serve
    php artisan migrate
```

# Using it

* Watch streams: /streams
* Start streaming: /record

# About WatchOut

I like simple, stupid and cheap things.
I wanted to stream, and my bored mind built WatchOut.
If you like it, use it.




