<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'type' => 'users',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name
            ],
            'relationships' => $this->when(! empty($this->getRelations()), function () {
                return [
                    'posts' => $this->whenLoaded('posts', function () {
                        return [
                            'data' => $this->posts->map(function ($post) {
                                return [
                                    'type' => 'posts',
                                    'id' => $post->id
                                ];
                            })
                        ];
                    })
                ];
            }),
            'links' => [
                'self' => url('/users/' . $this->id)
            ]
        ];
    }

    public function with($request)
    {
        return [
            'included' => $this->when(! empty($this->getRelations()), function () {
                return $this->whenLoaded('posts', function () {
                    return $this->posts->map(function ($post) {
                        return new Post($post);
                    });
                });
            })
        ];
    }
}
