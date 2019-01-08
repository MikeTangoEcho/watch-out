<?php

namespace App\Http\Controllers\Api;

use App\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stream as StreamResource;
use App\Http\Requests\FilterStream;


class StreamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilterStream $request)
    {
        $filters = $request->validated();
        Log::debug($filters);
        $streams = Stream::orderBy('updated_at', 'desc')
            ->with('user:id,name')
//            ->streamingSince(60)
            ;
        if (isset($filters['excluded_ids']) && $filters['excluded_ids']) { 
            $streams = $streams
                ->whereNotIn('id', $filters['excluded_ids']);
        }
        if (isset($filters['per_page'])) {
            $streams = $streams->paginate($filters['per_page']);
        }

        return StreamResource::collection($streams);
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
     * @param  \App\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function show(Stream $stream)
    {
        return $stream;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stream  $stream
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
}
