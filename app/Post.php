<?php

namespace App;

use App\User;
use App\Scopes\MostRecentFirst;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
    	parent::boot();

    	static::addGlobalScope(new MostRecentFirst());
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
