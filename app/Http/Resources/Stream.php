<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Stream extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'poster' => asset('images/mire160x120.png'),
            'src' => route('streams.pull', ['stream' => $this->id]),
            'url' => route('streams.show', ['stream' => $this->id]),
            'mime_type' => $this->mime_type,
            'user' => new User($this->user)
        ];
    }
}
