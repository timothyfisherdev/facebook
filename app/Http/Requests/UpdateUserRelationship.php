<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRelationship extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->route('addressee')->is($this->user())
            && $this->route('addressee')->hasRelationshipRequestFrom($this->route('requester'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action' => [
                'required',
                'string',
                'in:accept,decline'
            ]
        ];
    }
}
