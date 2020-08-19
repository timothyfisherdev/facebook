<?php

namespace App\Http\Resources\API\REST\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'image' => $this->image,
            'posted_at' => $this->created_at->diffForHumans(),
            'posted_by' => User::make($this->whenLoaded('user'))
        ];
    }
}
