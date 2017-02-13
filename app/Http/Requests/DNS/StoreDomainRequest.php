<?php

namespace App\Http\Requests\DNS;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class StoreDomainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Anyone can store a new record
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
            'name' => 'required|domain'
        ];
    }
}
