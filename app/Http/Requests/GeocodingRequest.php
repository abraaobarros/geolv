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
            'street_name' => ['required_without:cep'],
            'locality' => ['required_with:street_name'],
            'cep' => ['required_without:street_name', 'nullable', 'regex:/(\d{5}\-\d{2})|\d{7}/'],
        ];
    }
}
