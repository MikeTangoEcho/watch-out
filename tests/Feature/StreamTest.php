<?php

namespace Tests\Feature;

use App;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Assert as PHPUnit;

class StreamTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Listing of last streams
     * 
     * @return void
     */
    public function testHistory()
    {
        $user = factory(App\User::class)->create();
        $streams = factory(App\Stream::class, 8)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/history');

        $response->assertOk();

        $streams_url = $streams->map(function ($stream) {
                return route('streams.full', ['stream' => $stream->id]);
            })->reverse()->all();
        $response->assertSeeInOrder($streams_url);
    }

    /**
     * Test download of all the chunks
     * 
     * @return void
     */
    public function testStreamFull()
    {
        Storage::fake();

        $user = factory(App\User::class)->create();
        $stream = factory(App\Stream::class)->create(['user_id' => $user->id]);
        $streamContent = '';
        $stream_chunks = collect(range(0, 10))->map(
            function ($chunk_id) use ($stream, &$streamContent) {
                $filename = App\StreamChunk::getFilename($stream->id, $chunk_id, 0);
                Storage::put($filename, $chunk_id);
                $streamContent .= $chunk_id;

                return factory(App\StreamChunk::class)->create([
                    'stream_id' => $stream->id,
                    'chunk_id' => $chunk_id,
                    'filename' => $filename
                ]);
            }
        );

        $response = $this->actingAs($user)
            ->get('/streams/' . $stream->id . '/full')
            ->assertOk()
            ->assertHeader('Content-Type', $stream->mime_type);
        
        PHPUnit::assertEquals($streamContent, $response->streamedContent());

        $anotherUser = factory(App\User::class)->create();

        $this->actingAs($anotherUser)
            ->get('/streams/' . $stream->id . '/full')
            ->assertForbidden();
    }

    /**
     * Test upload of chunk of webm stream
     * 
     * @return void
     */
    public function testStreamPush()
    {
        Storage::fake();

        $user = factory(App\User::class)->create();
        $stream = factory(App\Stream::class)->create(['user_id' => $user->id]);

        // Push Invalid Chunk Order
        $this->actingAs($user)
            ->call('POST', '/streams/' . $stream->id . '/chunks',
                [], [], [],
                $this->transformHeadersToServerVars(['X-Chunk-Order' => -2]),
                hex2bin('1a45dfa3'))
            ->assertStatus(400);

        // Push Invalid first Chunk
        $this->actingAs($user)
            ->call('POST', '/streams/' . $stream->id . '/chunks',
                [], [], [],
                $this->transformHeadersToServerVars(['X-Chunk-Order' => 1]),
                hex2bin('1a45dfa4')) // False TAG
            ->assertStatus(400);

        // Push pseudo-valid Chunks
        collect(range(1, 10))->map(function ($chunk_id) use ($user, $stream) {
            // TODO Mock webm file
            $data = ($chunk_id == 1 ? hex2bin('1a45dfa3') : '') . random_bytes(42);

            $this->actingAs($user)
                ->call('POST', '/streams/' . $stream->id . '/chunks',
                    [], [], [],
                    $this->transformHeadersToServerVars(['X-Chunk-Order' => $chunk_id]),
                    $data)
                // "post" fn doesnt support full payload.
                // Will change once i moved logic into api
                // ->post('/streams/' . $stream->id . '/chunks',
                //     [$data],
                //     ['X-Chunk-Order' => $chunk_id])
                ->assertOk()
                ->assertHeader('X-Views');

            $this->assertDatabaseHas('stream_chunks', [
                'stream_id' => $stream->id,
                'chunk_id' => ($chunk_id == 1 ? 0 : $chunk_id)
            ]);

            // TODO Assert Storage file exists
        });

        // Push Chunk on unowned stream
        $anotherUser = factory(App\User::class)->create();

        $this->actingAs($anotherUser)
            ->call('POST', '/streams/' . $stream->id . '/chunks',
                [], [], [],
                $this->transformHeadersToServerVars(['X-Chunk-Order' => 10]),
                random_bytes(42)) // False TAG
            ->assertForbidden();
    }

    /**
     * Test ddl of chunk of webm stream
     * 
     * @return void
     */
    public function testStreamPull()
    {
        Storage::fake();

        $user = factory(App\User::class)->create();
        $stream = factory(App\Stream::class)->create(['user_id' => $user->id]);
        $streamContent = [];
        $stream_chunks = collect(range(0, 10))->map(
            function ($chunk_id) use ($stream, &$streamContent) {
                $filename = App\StreamChunk::getFilename($stream->id, $chunk_id, 0);
                $streamContent[$chunk_id] = random_bytes(42);
                Storage::put($filename, $streamContent[$chunk_id]);

                return factory(App\StreamChunk::class)->create([
                    'stream_id' => $stream->id,
                    'chunk_id' => $chunk_id,
                    'filename' => $filename,
                     // First Cluster at ChunkId 1 => should chain every chunks
                    'cluster_offset' => ($chunk_id == 1 ? 0 : null)
                ]);
            }
        );

        $anotherUser = factory(App\User::class)->create();

        // Get EBMLHeader
        $response = $this->actingAs($anotherUser)
            ->get('/streams/' . $stream->id . '/chunks', ['X-Chunk-Order' => 0])
            ->assertOk()
            ->assertHeader('X-Chunk-Order', 0)
            ->assertHeader('X-Next-Chunk-Order', -1)
            ->assertHeader('X-Views')
            ;
        PHPUnit::assertEquals($streamContent[0], $response->streamedContent());

        // Get First Cluster
        $response = $this->actingAs($anotherUser)
            ->get('/streams/' . $stream->id . '/chunks', ['X-Chunk-Order' => -1])
            ->assertOk()
            ->assertHeader('X-Chunk-Order', 1)
            ->assertHeader('X-Next-Chunk-Order', 2)
            ->assertHeader('X-Views')
            ;
        PHPUnit::assertEquals($streamContent[1], $response->streamedContent());

        // Validate the chain
        // TODO Remove chunks in the chain to validate order preservation
        for ($chunkId = 2; $chunkId <= 10; $chunkId++) {
            $response = $this->actingAs($anotherUser)
                ->get('/streams/' . $stream->id . '/chunks', ['X-Chunk-Order' => $chunkId])
                ->assertOk()
                ->assertHeader('X-Chunk-Order', $chunkId)
                ->assertHeader('X-Next-Chunk-Order', $chunkId + 1)
                ->assertHeader('X-Views')
                ;
            PHPUnit::assertEquals($streamContent[$chunkId], $response->streamedContent());
        }

        $response = $this->actingAs($anotherUser)
            ->get('/streams/' . $stream->id . '/chunks', ['X-Chunk-Order' => 11])
            ->assertStatus(204)
            ->assertHeader('X-Chunk-Order', 11)
            ->assertHeader('X-Next-Chunk-Order', -1)
            ->assertHeader('X-Views')
            ;
    }

}
