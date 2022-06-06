<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ArticleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if ($this->route()->getName() === 'articles.store') {
            return [
                'title' => 'required|unique:articles|max:255',
                'body' => 'required|max:255',
                'categories'  => 'required|array|min:1',
                'categories.*' => 'exists:categories,title',
            ];
        } else {
            return [
                'searchTerm' => 'sometimes|required',
                'sort.trending'  => 'sometimes|required|array|min:2',
                'sort.trending.*' => 'date_format:Y-m-d',
                'sort.popularity'  => 'sometimes|required|in:rating,view',
                'filter.categories'  => 'sometimes|required|array|min:1',
                'filter.categories.*' => 'exists:categories,title',
                'filter.date'  => 'sometimes|required|array|min:2',
                'filter.date.*' => 'date_format:Y-m-d',
            ];
        }
    }


    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->unprocessableEntity('Validation errors', $validator->errors()));
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (isset($this->sort['trending'])) {
            //$this->replace(['sort' => explode(',', $this->sort['trending'])]);
            $this->replace([
                'sort' => array_merge(
                    $this->sort,
                    [
                        'trending' => explode(',', $this->sort['trending'])
                    ]
                )
            ]);
        }
        if (isset($this->filter['categories'])) {
            $this->replace([
                'filter' => array_merge(
                    $this->filter,
                    [
                        'categories' => explode(',', $this->filter['categories'])
                    ]
                )
            ]);
        }
        if (isset($this->filter['date'])) {
            $this->replace([
                'filter' => array_merge(
                    $this->filter,
                    [
                        'date' => explode(',', $this->filter['date'])
                    ]
                )
            ]);
        }
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'sort.popularity.in' => __('messages.article_validation_wrong_popularity_sort_param'),
            'filter.categories.*.exists' => __('messages.article_validation_wrong_category'),
            'sort.trending.*.date_format' => __('messages.article_validation_wrong_date_format'),
            'filter.date.*.date_format' => __('messages.article_validation_wrong_date_format'),
        ];
    }
}
