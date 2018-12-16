<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StreamController extends Controller
{
    public function index()
    {
        return view('stream');
    }
    
    public function record()
    {
        return view('record');
    }

    public function push(Request $request)
    {
        //https://axel.isouard.fr/blog/2016/05/24/streaming-webm-video-over-html5-with-media-source
        Log::debug('Receiving stream');
        Storage::append('stream-' . $request->header('X-Block-Chunk-Id') . '.webm', $request->getContent());
    }

    public function pull()
    {
        // Change header of stream ?
        // Inject ADS in init segment
        return response()->stream(function() {
            // Forge file
            //https://chromium.googlesource.com/webm/libvpx/+/master/webmdec.h
            // https://www.w3.org/TR/media-source/#init-segment
            // TODO: query DB to b check if need to switch file
            Log::debug("Start streaming");
            for ($i = 1; $i < 5; $i++) {
                $stream = Storage::readStream("stream-" . $i . ".webm");    
                fpassthru($stream);
                fclose($stream);    
            }
        }, 200, [
            'Cache-Control'         => 'must-revalidate, no-cache, no-store',
            'Content-Type'          => 'video/webm',
            'Pragma'                => 'public',
        ]);
    }
}

