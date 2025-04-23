<?php

namespace App\Services;

use App\Models\Tenant\{
    Item,
    User,
    Person,
};
use Modules\Item\Models\{
    Brand
};


class ViewFiltersService
{
    
    /**
     *
     * @param  string $model
     * @param  int $id
     * @return string
     */
    public function getNameFromModel($model, $id)
    {
        $record = $model::select('name')->findOrFail($id);

        return $record->name;
    }


    /**
     *
     * @param  int $id
     * @return string
     */
    public function getCustomerName($id)
    {
        return $this->getNameFromModel(Person::class, $id);
    }


    /**
     *
     * @param  int $id
     * @return string
     */
    public function getUserName($id)
    {
        return $this->getNameFromModel(User::class, $id);
    }


    /**
     *
     * @param  int $id
     * @return string
     */
    public function getItemName($id)
    {
        return $this->getNameFromModel(Item::class, $id);
    }

    
    /**
     *
     * @param  int $id
     * @return string
     */
    public function getBrandName($id)
    {
        return $this->getNameFromModel(Brand::class, $id);
    }


    /**
     *
     * @param  string $id
     * @return string
     */
    public function getTypeName($id)
    {
        $name = null;

        switch($id) 
        {
            case 'documents':
                $name = 'Factura electrónica';
                break;
            
            case 'documents_pos':
                $name = 'Documento POS';
                break;

            case 'credit_notes':
                $name = 'Notas de crédito';
                break;

            case 'debit_notes':
                $name = 'Notas de débito';
                break;

            case 'pos_credit_notes':
                $name = 'Notas de crédito POS';
                break;

            default:
                $name = 'Todos';
                break;
        }

        return $name;
    }
    
}