<?php

namespace App\Http\Controllers;

use App\Lib\Webm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Stream;
use App\StreamChunk;

class StreamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $streams = Stream::orderBy('updated_at', 'desc')
            ->limit(1)
            ->get();
        return view('streams', ['streams' => $streams]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stream  $Stream
     * @return \Illuminate\Http\Response
     */
    public function show(Stream $stream)
    {
        return view('stream', ['stream' => $stream]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function edit(Stream $stream)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stream  $Stream
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stream $stream)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stream $stream)
    {
        //
    }

    public function record()
    {
        $stream = new Stream();
        $stream->title = "Do it Live!";
        $stream->mime_type = 'video/webm;codecs="opus,vp8"';
        $stream->save();

        return view('record', ['stream' => $stream]);
    }

    public function push(Request $request, Stream $stream)
    {
        // Same RFC, different behaviors, how the fuck ?
        // Firefox:
        // - Split stream by cluster with fixed size on trigger
        // - SimpleBlock have differents timecode counter for each tracks | need to fix it
        // Chrome:
        // - Clusters with infinite size that hold ~11sec that are split on trigger
        // - SimpleBlock of all tracks share the same timecode counter

        // https://w3c.github.io/media-source/webm-byte-stream-format.html
        // https://axel.isouard.fr/blog/2016/05/24/streaming-webm-video-over-html5-with-media-source
        // TODO Check if stream is in progress

        // Start with EBML header => look for next cluter and split
        // Not start with Cluster => look for next cluster and split by timecode, begin will be appended to last file
        $fStream = fopen('php://temp', 'rwb');
        fwrite($fStream, $request->getContent());
        rewind($fStream);
        $webm = new Webm();
        // Get Pos of first Cluster
        $clusterPos = $webm->seekNextId($fStream, '1f43b675');
        rewind($fStream);
        $chunkId = $request->header('X-Chunk-Order');
        // Check if Init
        // TODO Insert if not exists or prevent insertion of Chunk in wrong order
        // Check if first bytes is the EMBL Header
        if ($chunkId == 1) {
            $streamChunk = new StreamChunk();
            $streamChunk->stream_id = $stream->id;
            $streamChunk->chunk_id = 0;
            $streamChunk->filename = StreamChunk::getFilename($stream->id, 0, false);
            $header = fopen('php://temp', 'wb');
            stream_copy_to_stream($fStream, $header, is_null($clusterPos) ? -1 : intval($clusterPos), 0);
            Storage::put($streamChunk->filename, $header);
            fclose($header);
            $streamChunk->save();
            Log::debug('Stream Header');
            // Still has cluster
            if (!is_null($clusterPos)) {
               $clusterPos = 0;
            }
        }
        // If not eof write chunk and flag if cluster
        if (!feof($fStream)) {
            $streamChunk = new StreamChunk();
            $streamChunk->stream_id = $stream->id;
            $streamChunk->chunk_id = $chunkId;
            $streamChunk->filename = StreamChunk::getFilename($stream->id, $chunkId, $clusterPos);
            $streamChunk->cluster_offset = $clusterPos;
            $fChunk = fopen('php://temp', 'wb');
            stream_copy_to_stream($fStream, $fChunk);
            Storage::put($streamChunk->filename, $fChunk);
            fclose($fChunk);
            $streamChunk->save();
            Log::debug('Stream Chunk ' . $chunkId);
        }
        // Forge header to send next block id ?
        fclose($fStream);
    }

    public function pull(Request $request, Stream $stream)
    {
        $filesToStream = [];
        $nextChunkId = -1;
        $seekCluster = false;

        $chunkId = intval($request->header('X-Chunk-Order'));

        $streamChunk = StreamChunk::where('stream_id', $stream->id);
        if ($chunkId == -1) {
            $streamChunk = $streamChunk
                ->whereNotNull('cluster_offset')
                ->orderBy('chunk_id', 'desc');
        } else {
            // TODO Manage rupture in stream with timestamp ?
            $streamChunk = $streamChunk
                ->where('chunk_id', '=', $chunkId);
        }
        $streamChunk = $streamChunk->first();
        if ($streamChunk) {
            if ($chunkId == -1) {
                $chunkId = $streamChunk->chunk_id;
                $seekCluster = true;
            }
            if ($chunkId != 0) {
               $nextChunkId = $chunkId + 1;
            }
        }
        
        return response()->stream(function() use ($streamChunk, $seekCluster) {
            // https://chromium.googlesource.com/webm/libvpx/+/master/webmdec.h
            // https://www.w3.org/TR/media-source/#init-segment
            // TODO: query DB to b check if need to switch file
            if ($streamChunk && Storage::exists($streamChunk->filename)) {
                $stream = Storage::readStream($streamChunk->filename);
                if ($seekCluster && $streamChunk->cluster_offset) {
                    fseek($stream, $streamChunk->cluster_offset);
                    Log::debug('Seek Cluster at ' . $streamChunk->cluster_offset);
                }
                fpassthru($stream);
                fclose($stream);
                Log::debug('Sent ' . $streamChunk->filename);
            } else {
                abort(204);
            }
        }, 200, [
            'Cache-Control'         => 'no-cache, no-store',
            'Pragma'                => 'public',
            'Content-Type'          => $stream->mime_type,
            'X-Chunk-Order'         => $chunkId,
            'X-Next-Chunk-Order'    => $nextChunkId,
            'Retry-After'           => 2
        ]);
    }

    
    public function full(Request $request, Stream $stream)
    {

        // TODO Query all chunk filename
        $filesToStream = $stream->chunks()->orderBy('chunk_id')->pluck('filename');
        return response()->stream(function() use ($filesToStream) {
            // Forge file
            foreach ($filesToStream as $file) {
                if (Storage::exists($file)) {
                    Log::debug('Full sent ' . $file);
                    $stream = Storage::readStream($file);
                    fpassthru($stream);
                    fclose($stream);
                }
            }
        }, 200, [
            'Cache-Control'         => 'no-cache, no-store',
            'Pragma'                => 'public',
            'Content-Type'          => $stream->mime_type,
        ]);
    }

}

