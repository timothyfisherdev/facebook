<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    protected $guarded = [];

    public function user()
    {
    	return $this->belongsTo(User::class, 'requester_id');
    }
}
