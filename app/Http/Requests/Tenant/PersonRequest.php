<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Se detecta si la request proviene del proceso de factura
        $fromInvoice = $this->input('from_invoice', false);
        $id = $this->input('id');
        $type = $this->input('type');

        // Definir las reglas básicas para los campos sin la verificación de unicidad
        $numberRules = ['required', 'numeric', 'digits_between:1,22'];
        $nameRules   = ['required'];
        $codeRules   = ['required'];

        // Si no se indica que la request proviene de factura, se añaden las reglas de unicidad
        if (!$fromInvoice) {
            $nameRules[]   = Rule::unique('tenant.persons')->where(function ($query) use ($id, $type) {
                return $query->where('type', $type)
                             ->where('id', '<>', $id);
            });
            $codeRules[]   = Rule::unique('tenant.persons')->where(function ($query) use ($id, $type) {
                return $query->where('type', $type)
                             ->where('id', '<>', $id);
            });
        }

        return [
            'number' => ['required'],
            'name' => $nameRules,
            'identity_document_type_id' => ['required'],
            'type_obligation_id' => ['required'],
            'country_id' => ['required'],
            // 'person_type_id' => ['required_if:type,"customers"'],  // Se puede descomentar según se requiera.
            'department_id' => ['required_if:identity_document_type_id,"066"'], // Ejemplo, si aplica.
            'address' => ['required'],
            'email' => ['required', 'email'],
            'telephone' => ['required', 'numeric', 'integer', 'digits_between:7,10'],
            'code' => $codeRules,
            'dv' => ['nullable','numeric', 'digits_between:0,1'],
            'postal_code' => ['nullable', 'numeric'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('dv', 'required', function ($input) {
            return in_array($input->identity_document_type_id, [1, 2, 3, 6, 10]);
        });

        $validator->sometimes('number', ['numeric', 'digits_between:1,15'], function ($input) {
            return in_array($input->identity_document_type_id, [1, 2, 3, 6, 10]);
        });

        $validator->sometimes('number', ['regex:/^[A-Za-z0-9\-]+$/', 'max:20'], function ($input) {
            return in_array($input->identity_document_type_id, [4, 7, 8, 9]);
        });

        // Agregar regla de unicidad solo si NO es desde factura
        if (!$this->input('from_invoice')) {
            $id = $this->input('id');
            $type = $this->input('type');
            $uniqueRule = Rule::unique('tenant.persons')->where(function ($query) use ($id, $type) {
                return $query->where('type', $type)->where('id', '<>', $id);
            });

            $validator->sometimes('number', [$uniqueRule], function () {
                return true;
            });
        }
    }

    public function messages()
    {
        return [
            'dv.required' => 'Para tipos de documentos nacionales, es obligatorio el dígito de verificación.',
            'number.regex' => 'Este tipo de documento permite solo letras, números y guiones.',
            'number.numeric' => 'El numero de identificacion debe ser numérico.',
        ];

    }
}
