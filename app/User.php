<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function relationships()
    {
        return $this->belongsToMany(
            User::class,
            'relationships_users',
            'requester_id',
            'addressee_id'
        )->withPivot('status_code');
    }

    public function createRelationships(array $userIds)
    {
        $userIds = array_flip($userIds);

        data_set($userIds, '*.status_code', 'R');

        $this->relationships()->syncWithoutDetaching($userIds);
    }
}
