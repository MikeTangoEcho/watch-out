<?php

namespace App\Http\Controllers;

use App\Lib\Webm;
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
        // https://w3c.github.io/media-source/webm-byte-stream-format.html
        //https://axel.isouard.fr/blog/2016/05/24/streaming-webm-video-over-html5-with-media-source
        Log::debug('Receiving stream');
        // TODO Check if stream is in progress
        Storage::put('stream-' . $request->header('X-Block-Chunk-Id') . '.webm', $request->getContent());
        if ($request->header('X-Block-Chunk-Id') == 1) {
            $webm = new Webm();
            $webm->debug = True;
            $stream = fopen('php://temp', 'rwb');
            fwrite($stream, $request->getContent());
            rewind($stream);
            $ebml = $webm->parse($stream);
            rewind($stream);
            
            $header = fopen('php://temp', 'wb');
            stream_copy_to_stream($stream, $header, $ebml['Cluster']['offset']);
            Storage::put('stream-header.webm', $header);
            fclose($header);
            
            $cluster = fopen('php://temp', 'wb');
            stream_copy_to_stream($stream, $cluster);
            Storage::put('stream-' . $request->header('X-Block-Chunk-Id') . '.webm', $cluster);
            fclose($cluster);
            fclose($stream);    
        }        
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
            Log::debug("Send stream header");
            if (Storage::exists("stream-header.webm")) {
                $stream = Storage::readStream("stream-header.webm");
                fpassthru($stream);
                fclose($stream);
            } else {
                Log::debug('No stream found');
                return;
            }

            if ($request->header('X-Block-Chunk-Id')) {
                $current_id = $request->header('X-Block-Chunk-Id');
            } else {
                $files = preg_grep("/stream-\d+.webm/", Storage::files());
                if (!$files) {
                    Log::debug("No segments found");
                    return;
                }
                Log::debug($files);
                $matches = [];
                if (preg_match("/stream-(?P<id>\d+).webm/", end($files), $matches)) {
                    Log::debug("Segments catchup " . $current_id);
                    $current_id = $matches['id'];
                }
            }
            // Get Asked segment
            if (Storage::exists("stream-" . $current_id . ".webm")) {
                Log::debug("Segment sent " . $current_id);
                $stream = Storage::readStream("stream-" . $current_id . ".webm");    
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

