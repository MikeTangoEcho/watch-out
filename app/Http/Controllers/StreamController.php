<?php

namespace App\Http\Controllers;

use App\Lib\Webm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\StreamChunk;

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

    private function getStreamName($id, $clusterPos=false) {
        return "stream-" . str_pad($id, 8, "0", STR_PAD_LEFT) . ($clusterPos ? "-cluster-" . $clusterPos : "-chunk") . ".webm";
    }

    public function push(Request $request)
    {
        // https://w3c.github.io/media-source/webm-byte-stream-format.html
        //https://axel.isouard.fr/blog/2016/05/24/streaming-webm-video-over-html5-with-media-source
        // TODO Check if stream is in progress
        // TODO Split stream by cluster
        // TODO Manage manifest of stream
        // Start with EBML header => look for next cluter and split
        // Not start with Cluster => look for next cluster and split by timecode, begin will be appended to last file

        $stream = fopen('php://temp', 'rwb');
        fwrite($stream, $request->getContent());
        rewind($stream);
        $webm = new Webm();
        // Get Pos of first Cluster
        $clusterPos = $webm->seekNextId($stream, '1f43b675');
        rewind($stream);
        $chunkId = $request->header('X-Block-Chunk-Id');
        // Check if Init
        if ($chunkId == 1) {
            // Can have cluster or not
            $header = fopen('php://temp', 'wb');
            stream_copy_to_stream($stream, $header, intval($clusterPos) -1, 0);
            Storage::put('stream-header.webm', $header);
            fclose($header);
            Log::debug('Stream Header');
            $streamChunk = new StreamChunk();
            $streamChunk->stream_id = 1;
            $streamChunk->chunk_id = 0;
            $streamChunk->filename = 'stream-header.webm';
            $streamChunk->save();
            $clusterPos = 1;
        }
        // If not eof write chunk and flag if cluster
        if (!feof($stream)) {
            $chunk = fopen('php://temp', 'wb');
            stream_copy_to_stream($stream, $chunk);
            Storage::put($this->getStreamName($chunkId, $clusterPos), $chunk);
            fclose($chunk);
            Log::debug('Stream Chunk ' . $chunkId);
            $streamChunk = new StreamChunk();
            $streamChunk->stream_id = 1;
            $streamChunk->chunk_id = $chunkId;
            $streamChunk->filename = $this->getStreamName($chunkId, $clusterPos);
            if (is_int($clusterPos))
                $streamChunk->cluster_offset = $clusterPos - 1;
            $streamChunk->save();
        }
        fclose($stream);
    }

    public function full(Request $request)
    {
        $filesToStream = ["stream-header.webm"] +  preg_grep("/stream-\d+-\.+.webm/", Storage::files());

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
        $nextChunkId = 0;
        $clusterPos = 0;
        $sequence_chunk =  intval($request->header('X-Sequence-Chunk-Id'));
        $chunkId = intval($request->header('X-Block-Chunk-Id'));
        if ($chunkId) {
            // TODO Add flag for seeking next cluster
            $streamChunk = StreamChunk::where('stream_id', 1)
                ->where('chunk_id', '=', $chunkId)
                ->first();
            if ($streamChunk) {
                $filesToStream[] = $streamChunk->filename;
                $clusterPos = $streamChunk->cluster_offset;
                $nextChunkId = $chunkId + 1;
            } else {
                // Chunk is not yet Available
                $nextChunkId = $chunkId;
            }
        } else {
            $streamChunk = StreamChunk::where('stream_id', 1)
                ->where('chunk_id', '=', $chunkId)
                ->first();
            if ($streamChunk) {
                $filesToStream[] = $streamChunk->filename;
                // Next Cluster
                $streamChunk = StreamChunk::where('stream_id', 1)
                    ->whereNotNull('cluster_offset')
                    ->orderBy('chunk_id', 'desc')
                    ->first();
                
                if ($streamChunk) {
                    $nextChunkId = $streamChunk->chunk_id;
                }
                Log::debug("Sent Init Segments with next Chunk " . $nextChunkId);
            } else {
                // Stream not started
            }
        }
        
        return response()->stream(function() use ($filesToStream, $sequence_chunk, $clusterPos) {
            //https://chromium.googlesource.com/webm/libvpx/+/master/webmdec.h
            // https://www.w3.org/TR/media-source/#init-segment
            // TODO: query DB to b check if need to switch file
            if ($filesToStream) {
                foreach ($filesToStream as $file) {
                    if (Storage::exists($file)) {
                        $stream = Storage::readStream($file);
                        Log::debug('Sent ' . $file);
                        // TODO Check first loop
                        if ($sequence_chunk == 1) {
                            fseek($stream, $clusterPos);
                            Log::debug('Seek Cluster at ' . $clusterPos);
                        }                    
                        fpassthru($stream);
                        fclose($stream);
                    } else {
                        abort(204);
                    }
                }
            } else {
                abort(204);
            }
        }, 200, [
            'Cache-Control'         => 'must-revalidate, no-cache, no-store',
            'Content-Type'          => 'video/webm',
            'Pragma'                => 'public',
            'X-Block-Chunk-Id'      => $chunkId,
            'X-Block-Next-Chunk-Id' => $nextChunkId,
            'Retry-After'           => 2
        ]);
    }
}

