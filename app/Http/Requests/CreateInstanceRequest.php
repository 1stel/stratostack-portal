<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateInstanceRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
//		return false;
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
            'name'     => 'required|alpha_dash',
//            'package'  => 'required|exists:packages,id',
            'template' => 'required_without:snapshot|exists:template_groups,id',
            'snapshot' => 'required_without:template',
            'zone'     => 'required',
            'secGroup' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'Please specify an instance name.',
            'template.required' => 'Please select an operating system.',
            'zone.required'     => 'Please select an availability zone.',
            'secGroup.required' => 'Please select a security group.',
        ];
    }
}
