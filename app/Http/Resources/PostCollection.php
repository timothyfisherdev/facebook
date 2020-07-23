<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
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
            'data' => $this->collection,
            'links' => [
                'self' => url('/posts')
            ]
        ];
    }

    public function with($request)
    {
        $with = [];

        if ($request->query('include')) {
            $with['included'] = $this->collection->pluck('user')->unique()->values()
                ->map(function ($user) {
                    return new User($user);
                });
        }

        return $with;
    }
}
