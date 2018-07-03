<?php

namespace GeoLV\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadRequest extends FormRequest
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
            'geocode_file' => ['required', 'file'],
            'indexes' => ['required', 'json'],
            'fields' => ['required'],
            'count' => ['required', 'integer', 'min:1'],
            'delimiter' => ['required', Rule::in([',', ';'])]
        ];
    }

    public function messages()
    {
        return [
            'indexes.required' => 'Selecione as colunas com Endereço, Cidade e CEP',
            'fields.required' => 'Selecione ao menos uma coluna de resultado'
        ];
    }


}
