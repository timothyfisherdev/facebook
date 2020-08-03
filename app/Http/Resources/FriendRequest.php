<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FriendRequest extends JsonResource
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
            'type' => 'friend-requests',
            'id' => $this->id,
            'attributes' => [
                'requester_id' => $this->requester_id,
                'requested_id' => $this->requested_id 
            ],
            'links' => [
                'self' => url('/friend-requests/' . $this->id)
            ]
        ];
    }
}
