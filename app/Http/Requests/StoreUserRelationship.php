<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Validation\Rule;
use App\Rules\RelationshipDoesntExist;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRelationship extends FormRequest
{
    /**
     * Ensure that the user making the request is the one that
     * is currently authenticated with our API (passport).
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->route('requester')->is($this->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->user();

        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                'not_in:' . $user->id,
                new RelationshipDoesntExist($user)
            ]
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => 'The selected user id does not exist.',
            'user_id.not_in' => 'The selected user id cannot be your own.'
        ];
    }
}
