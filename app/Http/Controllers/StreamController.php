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

    public function full(Request $request)
    {
        $filesToStream = ["stream-header.webm"] +  preg_grep("/stream-\d+.webm/", Storage::files());

        return response()->stream(function() use ($filesToStream) {
            // Forge file
            foreach ($filesToStream as $file) {
                if (Storage::exists($file)) {
                    $stream = Storage::readStream($file);
                    fpassthru($stream);
                    fclose($stream);
                }    
            }
        }, 200, [
            'Cache-Control'         => 'must-revalidate, no-cache, no-store',
            'Content-Type'          => 'video/webm',
            'Pragma'                => 'public',
        ]);
    }

    public function pull(Request $request)
    {
        $filesToStream = [];
        // Change header of stream ?
        // Inject ADS in init segment
        // TODO Send blocks until last
        if (intval($request->header('X-Block-Chunk-Id'))) {
            $current_id = intval($request->header('X-Block-Chunk-Id'));
        } else {
            $filesToStream[] = "stream-header.webm";
            $files = preg_grep("/stream-\d+.webm/", Storage::files());
            if (!$files) {
                Log::debug("No segments found");
                return;
            }
            $matches = [];
            if (preg_match("/stream-(?P<id>\d+).webm/", end($files), $matches)) {
                $current_id = $matches['id'];
            }
            // TEST
            $current_id = 1;
            Log::debug("Segments catchup " . $current_id);
        }
        // Get Asked segment
        $filesToStream[] = "stream-" . $current_id . ".webm";
        $filesToStream = ['stream-full.webm'];
        return response()->stream(function() use ($filesToStream) {
            // Forge file
            //https://chromium.googlesource.com/webm/libvpx/+/master/webmdec.h
            // https://www.w3.org/TR/media-source/#init-segment
            // TODO: query DB to b check if need to switch file
            foreach ($filesToStream as $file) {
                if (Storage::exists($file)) {
                    $stream = Storage::readStream($file);
                    fpassthru($stream);
                    fclose($stream);
                } else {
                    abort(404);
                }
            }
        }, 200, [
            'Cache-Control'         => 'must-revalidate, no-cache, no-store',
            'Content-Type'          => 'video/webm',
            'Pragma'                => 'public',
            'X-Block-Chunk-Id'      => $current_id
        ]);
    }
}

