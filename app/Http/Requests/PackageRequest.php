<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class PackageRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
//		return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cpu_number' => 'required',
            'ram' => 'required',
            'disk_size' => 'required',
            'disk_type' => 'required',
            'price' => 'required',
            'tic' => 'required',
            'paymentTypeOverride' => 'required'
        ];
    }
}
