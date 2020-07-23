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
            'type' => 'posts',
            'id' => $this->id,
            'attributes' => [
                'body' => $this->body,
                'image' => $this->image,
                'posted_at' => $this->created_at->diffForHumans()
            ],
            'relationships' => $this->when(! empty($this->getRelations()), function () {
                return [
                    'user' => $this->whenLoaded('user', function () {
                        return [
                            'data' => [
                                'type' => 'users',
                                'id' => $this->user->id
                            ],
                            'links' => [
                                'related' => url('/posts/' . $this->id . '/user')
                            ]
                        ];
                    })
                ];
            }),
            'links' => [
                'self' => url('/posts/' . $this->id)
            ]
        ];
    }

    public function with($request)
    {
        return [
            'included' => $this->when(! empty($this->getRelations()), function () {
                return [
                    $this->whenLoaded('user', function () {
                        return new User($this->user);
                    })
                ];
            })
        ];
    }
}
