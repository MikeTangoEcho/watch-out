<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterStream extends FormRequest
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
        // TODO use config to set max page size and max excluded ids
        return [
            'per_page' => 'integer|min:1|max:15',
            'excluded_ids' => 'array|max:15',
            'excluded_ids.*' => 'required|integer|min:1'
        ];
    }
}
