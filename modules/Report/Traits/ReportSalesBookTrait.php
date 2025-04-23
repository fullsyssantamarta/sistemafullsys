<?php

namespace Modules\Report\Traits;

use App\Models\Tenant\{
    Document,
    DocumentPos
};
use DB;
use Modules\Factcolombia1\Models\Tenant\Tax;


trait ReportSalesBookTrait
{


    /**
     *
     * Registros
     *
     * @param  Request $request
     * @return Collection
     */
    public function getData($request)
    {
        $document_type_id = $request->document_type_id ?? null;

        $data = [
            'documents' => [],
            'documents_pos' => [],
            'credit_notes' => [],
            'pos_credit_notes' => [],
            'records' => [],
        ];

        switch ($document_type_id)
        {
            case 'documents':
            case 'credit_notes':
            case 'debit_notes':
                $documents = $this->getDocuments($request, $document_type_id);
                $data['records'] = $documents;
                $data['documents'] = $documents;
                break;

            case 'documents_pos':
                // Los documentos POS vienen de otra tabla
                $documents_pos = $this->getDocumentPos($request);
                $data['records'] = $documents_pos;
                $data['documents_pos'] = $documents_pos;
                break;

            case 'pos_credit_notes':
                $pos_credit_notes = $this->getDocumentPos($request);
                $data['records'] = $pos_credit_notes;
                $data['pos_credit_notes'] = $pos_credit_notes;
                break;

            default:
                // Al mostrar todos, incluimos los 4 tipos
                $documents = $this->getDocuments($request, 'documents');
                $credit_notes = $this->getDocuments($request, 'credit_notes');
                $debit_notes = $this->getDocuments($request, 'debit_notes');
                $documents_pos = $this->getDocumentPos($request);
                $pos_credit_notes = $this->getDocuments($request, 'pos_credit_notes');
                
                $data['records'] = $documents->concat($credit_notes)
                                           ->concat($debit_notes)
                                           ->concat($documents_pos)
                                           ->concat($pos_credit_notes)
                                           ->sortBy('date_of_issue');
                $data['documents'] = $documents;
                $data['documents_pos'] = $documents_pos;
                $data['credit_notes'] = $credit_notes;
                $data['pos_credit_notes'] = $pos_credit_notes;
                break;
        }

        return $data;
    }


    /**
     *
     * @param  Request $request
     * @param  string|null $document_type
     * @return Collection
     */
    private function getDocuments($request, $document_type = null)
    {
        return Document::filterReportSalesBook($request)
                      ->filterInvoiceDocument($document_type)
                      ->get();
    }


    /**
     *
     * @param  Request $request
     * @return Collection
     */
    private function getDocumentPos($request)
    {
        $query = DocumentPos::filterReportSalesBook($request);
        
        switch($request->document_type_id) {
            case 'documents_pos':
                $query->where('state_type_id', '01');
                break;
            case 'pos_credit_notes':
                $query->where('state_type_id', '11');
                break;
            default:
                break;
        }

        return $query->get();
    }


    /**
     *
     * Obtener impuestos de todos los documentos filtrados
     *
     * @param  Collection $documents
     * @return Collection
     */
    public function getTaxesDocuments($documents)
    {
        $all_taxes_id = collect();
        $q = 0;

        foreach ($documents as $document)
        {
            $document_taxes = $document->items->pluck('tax_id')->toArray();
            $all_taxes_id = $all_taxes_id->merge($document_taxes);

            $q += count($document_taxes);
        }

        return Tax::whereIn('id', $all_taxes_id->unique())
                    ->withOut(['type_tax'])
                    ->select(['id', 'name', 'code', 'rate', 'conversion', 'is_percentage', 'is_fixed_value', 'is_retention', 'in_base', 'in_tax', 'type_tax_id'])
                    ->orderBy('id')
                    ->get();
    }


    /**
     *
     * Agregar registros agrupados
     *
     * @param  Collection $summary_records
     * @param  Collection $records
     * @return void
     */
    public function setOrderedData(&$summary_records, $records)
    {
        $group_prefix = $records->groupBy('prefix');

        foreach ($group_prefix as $prefix => $documents)
        {
            $ordered_documents = $documents->sortBy(function ($row) {
                return (int) $row->number;
            });

            $summary_records->push([
                'prefix' => $prefix,
                'ordered_documents' => $ordered_documents,
                'first_document' => $ordered_documents->first(),
                'last_document' => $ordered_documents->last(),
            ]);
        }
    }


    /**
     * Data para reporte resumido
     *
     * @param  array $data
     * @param  Request $request
     * @return Collection
     */
    public function getSummaryRecords($data, $request)
    {
        $document_type_id = $request->document_type_id ?? null;

        $summary_records = collect();

        switch ($document_type_id)
        {
            case 'documents':
                $this->setOrderedData($summary_records, $data['documents']);
                break;

            case 'documents_pos':
                $this->setOrderedData($summary_records, $data['documents_pos']);
                break;

            default:
                $this->setOrderedData($summary_records, $data['documents']);
                $this->setOrderedData($summary_records, $data['documents_pos']);
                break;
        }

        return $summary_records;
    }

}
