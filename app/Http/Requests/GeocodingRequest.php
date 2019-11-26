<?php

namespace GeoLV\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeocodingRequest extends FormRequest
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
            'text' => ['required_without:postal_code'],
            //'text' => ['required_without:postal_code', 'required_with:locality'],
            //'locality' => ['required_with:street_name', 'required_without:postal_code'],
            'postal_code' => ['required_without:text', 'nullable'],
        ];
    }

    public function messages()
    {
        return [
            'text.required_without' => 'Informe um endereço ou um CEP'
        ];
    }

}
