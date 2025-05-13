<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfigurationPosRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'prefix' => ['required'],
            'resolution_number' => ['required'],
            'resolution_date' => ['required'],
            'date_from' => ['required'],
            'date_end' => ['required'],
            'from' => ['required'],
            'to' => ['required'],
            'type_resolution' => ['nullable'],
            'electronic' => ['nullable'],
            'generated' => ['required', 'numeric'],
            'plate_number' => ['nullable'],
            'cash_type' => ['nullable'],
            'technical_key' => ['nullable'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('technical_key', 'required', function ($input) {
            return $input->electronic === true && $input->type_resolution === 'Factura Electronica de Venta';
        });

        $validator->sometimes('plate_number', 'required', function ($input) {
            return $input->electronic === true && $input->type_resolution === 'Documento Equivalente POS Electronico';
        });

        $validator->sometimes('cash_type', 'required', function ($input) {
            return $input->electronic === true && $input->type_resolution === 'Documento Equivalente POS Electronico';
        });
    }

    public function messages()
    {
        return [
            'technical_key.required' => 'El campo Clave Técnica es obligatorio cuando el tipo de resolución es "Factura Electronica de Venta".',
            'plate_number.required' => 'El campo Serial Caja es obligatorio cuando el tipo de resolución es "Documento Equivalente POS Electronico".',
            'cash_type.required' => 'El campo Tipo Caja es obligatorio cuando el tipo de resolución es "Documento Equivalente POS Electronico".',
        ];
    }
}
