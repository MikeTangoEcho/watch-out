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
        Log::debug('Receiving stream');
        Storage::append('steam.webm', $request->getContent());
    }

    public function pull()
    {
        // Change header of stream ?
        return response()->stream(function() {
            // TODO: query DB to b check if need to switch file
            Log::debug("Start streaming");
            $stream = [];
            $stream[0] = Storage::readStream("oldspice.mp4");
            $stream[1] = Storage::readStream("oldspice2.mp4");
            $stream_id = 0;
            $max_loop = 2;
            $bytesToRead = 1024;
            while ($max_loop > 0) {
                if (feof($stream[$stream_id])) {
                    rewind($stream[$stream_id]);
                    $stream_id = ($stream_id + 1) % 2;
                    $max_loop--;
                    Log::debug("Loop " . $max_loop);
                }
                Log::debug("Streaming " . $stream_id);
                $data = fread($stream[$stream_id], $bytesToRead);
                echo $data;
                flush();
            }
            Log::debug("End of streaming ");
            fclose($stream[0]);
            fclose($stream[1]);
        }, 206, [
            'Cache-Control'         => 'must-revalidate, no-cache, no-store',
            'Content-Type'          => Storage::mimeType("oldspice2.mp4"),
            'Pragma'                => 'public',
        ]);
    }
}

