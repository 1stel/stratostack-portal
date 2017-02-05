<?php

namespace App\Http\Requests\DNS;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class UpdateRecordRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Auth is handled by the DNS facade.
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
            'type'     => 'required|in:A,AAAA,CNAME,MX,NS,TXT,SPF,WKS,SRV,LOC',
            'priority' => 'sometimes|numeric',
            'port'     => 'sometimes|numeric',
            'weight'   => 'sometimes|numeric'
        ];
    }
}
