<?php

namespace App\Imports;

use App\Models\Tenant\Person;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Factcolombia1\Models\Tenant\TypePerson;
use Modules\Factcolombia1\Models\Tenant\TypeRegime;
use Modules\Factcolombia1\Models\TenantService\TypeDocumentIdentification;
use Modules\Factcolombia1\Models\Tenant\City;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Factcolombia1\Models\Tenant\TypeObligation;

class PersonsImport implements ToCollection, WithMultipleSheets, WithHeadingRow
{
    use Importable;

    protected $data;
    protected $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function sheets(): array
    {
        // Procesar solo la primera hoja
        return [
            0 => $this, // La hoja 0 (primera)
        ];
    }

    private function throwException($message)
    {
        throw new Exception($message);
    }

    private function validateTypePerson($value, $registered)
    {
        $value = trim(str_replace('_x000D_', '', $value));
        
        // Si es numérico, buscar directamente por ID
        if (ctype_digit($value)) {
            $type_person = TypePerson::find($value);
            if ($type_person) {
                return $type_person->id;
            }
        }
        
        // Si contiene "Persona Natural"
        if (stripos($value, 'Persona Natural') !== false) {
            return 2; // ID para Persona Natural
        }
        
        // Si contiene "Persona Jurídica"
        if (stripos($value, 'Persona Juridica') !== false) {
            return 1; // ID para Persona Jurídica
        }

        throw new Exception("Registro nro.: {$registered}, Tipo de persona no válido: {$value}");
    }

    public function collection(Collection $rows)
    {
        $registered = 0;
        $total = 0;

        foreach ($rows as $row) {
            // Verificamos que la fila no esté vacía
            if (empty(array_filter($row->toArray()))) continue;

            $registered += 1;
            $type = request()->input('type');

            try {
                // Validar que los campos necesarios existan
                if (!isset($row['codigo_tipo_de_persona']) || !isset($row['codigo_tipo_de_regimen']) || 
                    !isset($row['codigo_tipo_de_obligacion']) || !isset($row['codigo_tipo_de_documento'])) {
                    throw new Exception("Registro nro.: {$registered}, Formato de archivo inválido o campos faltantes");
                }

                // Tipo de persona
                $type_person_id = $this->validateTypePerson($row['codigo_tipo_de_persona'], $registered);

                // Tipo de régimen - Modificación para aceptar tanto ID como nombre
                $regime_value = trim(str_replace('_x000D_', '', $row['codigo_tipo_de_regimen']));
                if (is_numeric($regime_value)) {
                    $type_regime = TypeRegime::find($regime_value);
                } else {
                    $type_regime = TypeRegime::where('name', 'like', '%'.$regime_value.'%')->first();
                }
                
                if (!$type_regime) {
                    throw new Exception("Registro nro.: {$registered}, Régimen no encontrado: {$regime_value}");
                }

                // Tipo de obligación
                $obligation_value = trim(str_replace('_x000D_', '', $row['codigo_tipo_de_obligacion']));
                if (is_numeric($obligation_value)) {
                    $type_obligation = TypeObligation::find($obligation_value);
                } else {
                    $type_obligation = TypeObligation::where('name', 'like', '%'.$obligation_value.'%')->first();
                }
                
                if (!$type_obligation) {
                    throw new Exception("Registro nro.: {$registered}, Obligación no encontrada: {$obligation_value}");
                }

                // Tipo de documento - Modificación para aceptar tanto código como nombre
                $doc_type_value = trim(str_replace('_x000D_', '', $row['codigo_tipo_de_documento']));
                if (is_numeric($doc_type_value)) {
                    $type_doc = TypeDocumentIdentification::find($doc_type_value);
                } else {
                    $type_doc = TypeDocumentIdentification::where('name', 'like', '%'.$doc_type_value.'%')->first();
                }
                
                if (!$type_doc) {
                    throw new Exception("Registro nro.: {$registered}, Tipo de documento no encontrado: {$doc_type_value}");
                }

                $city = City::find($row['codigo_de_ciudad']);
                if (!$city) {
                    throw new Exception("Registro nro.: {$registered}, Ciudad no encontrada: {$row['codigo_de_ciudad']}");
                }

                Person::updateOrCreate(
                    [
                        'type' => $type,
                        'identity_document_type_id' => $type_doc->id,
                        'number' => $row['numero_de_identificacion']
                    ],
                    [
                        'type_person_id' => $type_person_id,
                        'type_regime_id' => $type_regime->id,
                        'type_obligation_id' => $type_obligation->id,
                        'dv' => $row['dv'],
                        'code' => $row['codigo_interno'],
                        'name' => $row['nombre_completo'],
                        'country_id' => 47,
                        'department_id' => $city->department_id,
                        'city_id' => $city->id,
                        'address' => $row['direccion'],
                        'telephone' => $row['telefono'],
                        'email' => $row['correo_electronico'],
                    ]
                );

            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            $total++;
        }

        $this->data = compact('total', 'registered');
    }

    public function getData()
    {
        return $this->data;
    }
}
