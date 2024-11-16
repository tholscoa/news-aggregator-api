<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ResponseHelper;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * You can adjust this based on your authentication needs.
     */
    public function authorize()
    {
        return true; // Allow all users for now
    }

    /**
     * Define the validation rules for storing or updating an article.
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(
            ResponseHelper::error('Validation error', 422, $errors)
        );
    }
}
