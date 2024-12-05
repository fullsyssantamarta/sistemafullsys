<?php

namespace App\Imports;

use App\Models\Tenant\Person;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Modules\Factcolombia1\Models\Tenant\TypePerson;
use Modules\Factcolombia1\Models\Tenant\TypeRegime;
use Modules\Factcolombia1\Models\TenantService\TypeDocumentIdentification;
use Modules\Factcolombia1\Models\Tenant\City;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PersonsImport implements ToCollection, WithMultipleSheets
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

    public function validateTypePerson($row, $row_number)
    {
        if(empty($row[0])) {
            $this->throwException('Registro nro.: '.$registered.', Código tipo de persona no añadido');
        }
        $type_person_string = trim(str_replace('_x000D_', '', $row[0]));
        if (ctype_digit($type_person_string)) {
            $person_id = (int)$type_person_string;
            if(!TypePerson::find($person_id)) {
                $this->throwException('Registro nro.: '.$registered.', '.$row[6].', Código tipo de persona no se encuentra en la base de datos.');
            }
            $type_person_id = $person_id;
        } else {
            // Busca coincidencias con palabras clave en texto
            $person_types = ['natural' => 2,'juridica' => 1,];
            $explode_person = explode(' ', strtolower($type_person_string));
            $matched_keyword = array_filter(array_keys($person_types), function ($keyword) use ($explode_person) {
                return in_array($keyword, $explode_person);
            });
            if (!empty($matched_keyword)) {
                // Obtén el ID correspondiente al texto encontrado
                $type_person_id = $person_types[reset($matched_keyword)];
            } else {
                $this->throwException('Registro nro.: '.$registered.', '.$row[6].', Tipo de persona no se encuentra en la base de datos.');
            }
        }
        if(!empty($type_person_id)) {
            return $type_person_id;
        } else {
            $this->throwException('Registro nro.: '.$registered.', Inconveniente con el Tipo de persona');
        }
    }

    public function collection(Collection $rows)
    {
            $registered = 0;
            unset($rows[0]);
            $total = count($rows);
            foreach ($rows as $row)
            {
                $registered += 1; // aumenta su valor se actualice o registre
                $type = request()->input('type');
                // row 0
                $type_person_id = $this->validateTypePerson($row, $registered);
                // row 1
                $row_type_regime = trim(str_replace('_x000D_', '', $row[1]));
                $type_regime_id = ctype_digit($row_type_regime) ? (int)$row_type_regime : TypeRegime::where('name', 'like', '%'.str_replace('_x000D_', '', $row[1]).'%')->firstOrFail()->id;
                // row 2
                $row_identity_document_type = trim(str_replace('_x000D_', '', $row[2]));
                $identity_document_type_id = ctype_digit($row_identity_document_type) ? (int)$row_identity_document_type : TypeDocumentIdentification::where('name', 'like', '%'.str_replace('_x000D_', '', $row_identity_document_type).'%')->firstOrFail()->id;

                $number = $row[3];
                $dv = $row[4];
                $code = $row[5];
                $name = $row[6];
                $country_id = 47;
                $city_id = $row[7];
                $department_id = City::where('id', $city_id)->firstOrFail()->department_id; // debe validarse
                $address = $row[8];
                $telephone = $row[9];
                $email = $row[10];

                Person::updateOrCreate(
                    [
                        'type' => $type,
                        'identity_document_type_id' => $identity_document_type_id,
                        'number' => $number
                    ],
                    [
                        'type_person_id' => $type_person_id,
                        'type_regime_id' => $type_regime_id,
                        'dv' => $dv,
                        'code' => $code,
                        'name' => $name,
                        'country_id' => $country_id,
                        'department_id' => $department_id,
                        'city_id' => $city_id,
                        'address' => $address,
                        'telephone' => $telephone,
                        'email' => $email,
                    ]
                );
            }
            $this->data = compact('total', 'registered');
    }

    public function getData()
    {
        return $this->data;
    }
}
