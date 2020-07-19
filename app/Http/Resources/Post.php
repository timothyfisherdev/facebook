<?php

namespace App\Http\Resources;

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
            'data' => [
                'type' => 'posts',
                'id' => $this->id,
                'attributes' => [
                    'body' => $this->body,
                    'image' => $this->image,
                    'posted_at' => $this->created_at->diffForHumans()
                ],
                'relationships' => $this->whenLoaded('user', function () {
                    return [
                        'user' => new User($this->user)
                    ];
                }),
                'links' => [
                    'self' => url('/posts/' . $this->id)
                ]
            ]
        ];
    }
}
