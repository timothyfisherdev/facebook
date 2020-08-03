<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRelationship extends JsonResource
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
            'type' => 'user-relationships',
            'id' => $this->id,
            'attributes' => [
                'requester_id' => $this->requester_id,
                'requested_id' => $this->requested_id,
                'type' => $this->type
            ],
            'links' => [
                'self' => url("/users/{$this->requester_id}/relationships/{$this->id}")
            ]
        ];
    }
}
