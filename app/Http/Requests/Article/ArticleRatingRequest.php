<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ArticleRatingRequest extends FormRequest
{
   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rate' => 'required|integer|between:1,5',
        ];
    }


    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->unprocessableEntity('Validation errors', $validator->errors()));
    }
}
