<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;

class RelationshipDoesntExist implements Rule
{
    private $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return ! $this->user->hasRelationshipWith($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A relationship already exists with the selected :attribute.';
    }
}
