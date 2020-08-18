<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelationshipStatusCode extends Model
{
    public const REQUESTED = 'R';

    public const ACCEPTED = 'A';

    public const DECLINED = 'D';

    public const BLOCKED = 'B';

    protected $guarded = [];
}
