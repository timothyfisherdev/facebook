<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreUserRelationship extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data.attributes.related_user_id' => 'exists:users,id|valid_user_relationship'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'data.attributes.related_user_id.exists' => 'Unable to find the requested user.',
            'data.attributes.related_user_id.valid_user_relationship' => 'Relationship already exists'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $failedRules = $validator->failed();

        if (isset($failedRules['data.attributes.related_user_id']['Exists'])) {
            $status = '404';
            $title = 'Requested User Not Found';
            $detail = $this->messages()['data.attributes.related_user_id.exists'];
        } elseif (isset($failedRules['data.attributes.related_user_id']['ValidUserRelationship'])) {
            $status = '409';
            $title = 'Relationship Already Exists';
            $detail = '';
        }
        
        throw new ValidationException($validator, new JsonResponse([
            'errors' => [
                'status' => $status,
                'title' => $title,
                'detail' => $detail
            ]
        ], $status));
    }
}
