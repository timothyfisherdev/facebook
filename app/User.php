<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Database\Eloquent\Concerns\HasSelfReferencingManyToMany;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasSelfReferencingManyToMany;

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

    public function relationships(string $foreignKey = 'requester_id', string $relatedKey = 'addressee_id', string $table = 'users_relationships')
    {
        return $this->belongsToManySelf(User::class, $table, $foreignKey, $relatedKey)->withTimestamps();
    }

    public function relationshipsWithStatus(string $foreignKey = 'requester_id', string $relatedKey = 'addressee_id')
    {
        return $this->relationships($foreignKey, $relatedKey, 'users_relationships_status')->withPivot('status_code');
    }

    public function requestRelationshipWith($user) : void
    {
        $this->relationships()->attach($user);
        $this->attachRelationshipStatus($user, RelationshipStatusCode::REQUESTED);
    }

    public function acceptRelationshipRequestFrom($user) : void
    {
        $this->attachRelationshipStatus($user, RelationshipStatusCode::ACCEPTED, 'addressee_id', 'requester_id');
    }

    public function declineRelationshipRequestFrom($user) : void
    {
        $this->attachRelationshipStatus($user, RelationshipStatusCode::DECLINED, 'addressee_id', 'requester_id');
    }

    public function hasRelationshipRequestFrom($user) : bool
    {
        return ($result = $this->relationshipsWithStatus('addressee_id', 'requester_id')->where('requester_id', $this->getUserId($user))->latest()->first())
            ? $result->pivot->status_code === RelationshipStatusCode::REQUESTED
            : false;
    }

    public function hasRelationshipWith($user) : bool
    {
        return $this->relationships()->newPivotStatementForId($user)->exists();
    }

    private function attachRelationshipStatus($user, string $statusCode, string $foreignKey = 'requester_id', string $relatedKey = 'addressee_id') : void
    {
        $this->relationshipsWithStatus($foreignKey, $relatedKey)->attach($user, [
            'specifier_id' => $this->id,
            'status_code' => $statusCode
        ]);
    }

    private function getUserId($user) : int
    {
        return $user instanceof User ? $user->id : (int) $user;
    }
}
