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

    private function getStreamName($id, $hasCluster=false) {
        return "stream-" . str_pad($id, 8, "0", STR_PAD_LEFT) . ($hasCluster ? "-cluster" : null) . ".webm";
    }

    public function push(Request $request)
    {
        // https://w3c.github.io/media-source/webm-byte-stream-format.html
        //https://axel.isouard.fr/blog/2016/05/24/streaming-webm-video-over-html5-with-media-source
        // TODO Check if stream is in progress
        // TODO Split stream by cluster
        // TODO Manage manifest of stream
        if ($request->header('X-Block-Chunk-Id') == 1) {
            $webm = new Webm();
            $stream = fopen('php://temp', 'rwb');
            fwrite($stream, $request->getContent());
            rewind($stream);
            $ebml = $webm->parse($stream);
            rewind($stream);
            
            $header = fopen('php://temp', 'wb');
            stream_copy_to_stream($stream, $header, $ebml['Segment']['elements']['Cluster'][0]['offset']);
            Storage::put('stream-header.webm', $header);
            fclose($header);
            Log::debug('Stream Header');
            
            $cluster = fopen('php://temp', 'wb');
            stream_copy_to_stream($stream, $cluster);
            Storage::put($this->getStreamName($request->header('X-Block-Chunk-Id'), true), $cluster);
            fclose($cluster);
            fclose($stream);    
            Log::debug('Stream First Cluster');
        } else {
            $webm = new Webm();
            $stream = fopen('php://temp', 'rwb');
            fwrite($stream, $request->getContent());
            rewind($stream);
            $pos = $webm->seekNextId($stream, '1f43b675');
            Storage::put($this->getStreamName($request->header('X-Block-Chunk-Id'), $pos), $stream);
            fclose($stream);
            Log::debug('Received Chunk ' . $request->header('X-Block-Chunk-Id') . ($pos ? ' cluster' : null));
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
        $next_chunck_id = 0;
        $sequence_chunk =  intval($request->header('X-Sequence-Chunk-Id'));
        $chunck_id = intval($request->header('X-Block-Chunk-Id'));
        if ($chunck_id) {
            if (Storage::exists($this->getStreamName($chunck_id)))
                $filesToStream[] = $this->getStreamName($chunck_id);
            else
                $filesToStream[] = $this->getStreamName($chunck_id, true);
            $next_chunck_id = $chunck_id + 1;
        } else {
            $filesToStream[] = "stream-header.webm";
            // Get Last Id
            $files = preg_grep("/stream-\d+-cluster.webm/", Storage::files());
            if (!$files) {
                Log::debug("No segments found");
                return;
            }
            $matches = [];
            if (preg_match("/stream-(?P<id>\d+)-cluster.webm/", end($files), $matches)) {
                $next_chunck_id = intval($matches['id']);
            }
            //$next_chunck_id = 49;
            Log::debug("Sent Init Segments with next Chunk " . $next_chunck_id);
        }
        
        return response()->stream(function() use ($filesToStream, $sequence_chunk) {
            //https://chromium.googlesource.com/webm/libvpx/+/master/webmdec.h
            // https://www.w3.org/TR/media-source/#init-segment
            // TODO: query DB to b check if need to switch file
            foreach ($filesToStream as $file) {
                if (Storage::exists($file)) {
                    $stream = Storage::readStream($file);
                    Log::debug('Sent ' . $file);
                    if ($sequence_chunk == 2) {
                        // Seek first cluster because fuck chrome
                        // Its Buffer management sucks
                        $webm = new Webm();
                        $pos = $webm->seekNextId($stream, '1f43b675');
                        Log::debug('Seek Cluster at ' . $pos);
                        fseek($stream, $pos, SEEK_SET);
                    }                    
                    fpassthru($stream);
                    fclose($stream);
                } else {
                    abort(204);
                }
            }
        }, 200, [
            'Cache-Control'         => 'must-revalidate, no-cache, no-store',
            'Content-Type'          => 'video/webm',
            'Pragma'                => 'public',
            'X-Block-Chunk-Id'      => $chunck_id,
            'X-Block-Next-Chunk-Id' => $next_chunck_id,
            'Retry-After'           => 2
        ]);
    }
}

