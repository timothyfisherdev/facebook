<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreFriendRequest extends FormRequest
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
            'data.attributes.user_id' => 'exists:users,id'
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
            'data.attributes.user_id.exists' => 'Unable to find the requested user.'
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

        if (isset($failedRules['data.attributes.user_id']['Exists'])) {
            $status = '404';
            $title = 'Requested User Not Found';
            $detail = $this->messages()['data.attributes.user_id.exists'];
        }
        
        throw new ValidationException($validator, new JsonResponse([
            'errors' => [
                'status' => $status,
                'title' => $title,
                'detail' => $detail
            ]
        ], 404));
    }
}
